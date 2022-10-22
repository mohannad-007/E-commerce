<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchShipping;
use App\Models\Product;
use App\Models\Shipping;
use App\Http\Requests\StoreShippingRequest;
use App\Http\Requests\UpdateShippingRequest;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;


class ShippingController extends Controller
{

    public function showAll()
    {
        $shipping = Shipping::query()
            ->first();

        if (!$shipping) {
            return response()->json(['message' => 'There are no shipments.']);
        }

        $shipping->products->pluck('id');

        return response()->json($shipping, Response::HTTP_OK);
    }


    public function receive()
    {
        $shipping = Shipping::query()
            ->where('receive', '!=', true)
            ->get();

        if ($shipping->isEmpty()) {
            return response()->json(['message' => 'There are no shipments.']);
        }

        return response()->json($shipping, Response::HTTP_OK);
    }


    public function delivery()
    {
        $shipping = Shipping::query()
            ->with('branchShipping')
            ->where('receive', '=', true)
            ->where('delivery', '!=', true)
            ->get();

        if ($shipping->isEmpty()) {
            return response()->json(['message' => 'There are no shipments.']);
        }

        return response()->json($shipping, Response::HTTP_OK);
    }

    public function receiveDone($shipping_id, Request $request)
    {
        $shipping = Shipping::query()
            ->where('receive', '!=', true)
            ->find($shipping_id);

        if (!$shipping) {
            return response()->json(['message' => 'There are no shipments.']);
        }

        if ($request->query('branch') == 'mydan') {
            $branch = 1;
        } elseif ($request->query('branch') == 'mazah') {
            $branch = 2;
        } elseif ($request->query('branch') == 'tigara') {
            $branch = 3;
        } elseif ($request->query('branch') == 'shaalan') {
            $branch = 4;
        } else {
            $branch = 5;
        }
        BranchShipping::query()
            ->create([
                'branch_id' => $branch,
                'shipping_id' => $shipping_id
            ]);

        $shipping->update([
            'receive' => true
        ]);

        while (true) {
            $product = DB::table('shipping_products')
                ->where('shipping_id', '=', $shipping_id)
                ->first();
            if (!$product) {
                return response()->json($shipping, Response::HTTP_OK);
            }
            $pro = $product->product_id;
            $products = Product::query()
                ->find($pro);
            $user = $products->account_id;
            $walletUser = Wallet::query()->where('account_id', '=', $user)->first();
            if (!$walletUser) {
                $walletUser = Wallet::query()
                    ->create([
                        'account_id' => $user,
                        'value' => 0
                    ]);
            }
            $newValue = ($walletUser->value) + ((($products->price) * (93)) / 100);

            $walletUser->update([
                'value' => $newValue
            ]);
            $wallet = Wallet::query()->find(1);
            $wallet->update([
                'value' => ($wallet->value) - ((($products->price) * (93)) / 100)
            ]);
            DB::table('shipping_products')
                ->where('id', '=', $product->id)
                ->delete();
        }

    }


    public function deliveryDone($shipping_id)
    {
        $shipping = Shipping::query()
            ->where('receive', '=', true)
            ->where('delivery', '!=', true)
            ->find($shipping_id);

        if (!$shipping) {
            return response()->json(['message' => 'There are no shipments.']);
        }

        $shipping->update([
            'delivery' => true
        ]);

        $walletUser = Wallet::query()->where('account_id', '=', $shipping->account_id)->first();
        $walletUser->update([
            'value' => ($walletUser->value) - (($shipping->cost) / 2)
        ]);
        $wallet = Wallet::query()->find(1);
        $wallet->update([
            'value' => ($wallet->value) + (($shipping->cost) / 2)
        ]);

        return response()->json($shipping, Response::HTTP_OK);
    }

    public function destroy($shipping_id)
    {
        $shipping = Shipping::query()
            ->find($shipping_id);

        if (!$shipping) {
            return response()->json(['message' => 'There are no shipments.']);
        }

        $shipping->delete();

        DB::table('shipping_products')
            ->where('shipping_id', '=', $shipping_id)
            ->delete();

        return response()->json(['message' => 'The shipments were Deleted Successfully.']);
    }

    public function updateBranch($branch_id, Request $request)
    {
        $newBranch = BranchShipping::query()
            ->find($branch_id);

        if (!$newBranch) {
            return response()->json(['message' => 'There are no shipments in this area.']);
        }

        if ($request->query('branch') == 'mydan') {
            $branch = 1;
        } elseif ($request->query('branch') == 'mazah') {
            $branch = 2;
        } elseif ($request->query('branch') == 'tigara') {
            $branch = 3;
        } elseif ($request->query('branch') == 'shaalan') {
            $branch = 4;
        } else {
            $branch = 5;
        }

        $newBranch->update([
            'branch_id' => $branch,
            'shipping_id' => $newBranch->shipping_id
        ]);

        return response()->json($newBranch, Response::HTTP_OK);

    }

}
