<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Product;
use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function store(Request $request, $product_id)
    {
        $product = Product::query()
            ->with('account', 'category', 'comments')
            ->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        Rate::query()
            ->create([
                'account_id' => $product->account_id,
                'product_id' => $product_id,
                'value' => $request->value
            ]);

        $rate = Rate::query()
            ->where('product_id', '=', $product_id)
            ->get();

        $allRate = $rate->pluck('value');

        $numOfRate = count($allRate);

        $rates = 0;

        foreach ($allRate as $oneRate) {
            $rates = $rates + $oneRate;
        }

        $product->update([
            'rate' => round($rates / $numOfRate)
        ]);

        $rate2 = Rate::query()
            ->where('account_id', '=', $product->account_id)
            ->get();

        $allRate2 = $rate2->pluck('value');

        $numOfRate2 = count($allRate2);

        $rates = 0;

        foreach ($allRate2 as $oneRate) {
            $rates = $rates + $oneRate;
        }

        $account = Account::query()
            ->find($product->account_id);

        if ($account->owner_type == 'App\Models\User') {
            $user = User::query()
                ->find($account->owner_id);
            $user->update([
                'level' => round($rates / $numOfRate2)
            ]);
        }
        if ($account->owner_type == 'App\Models\Company') {
            $company = Company::query()
                ->find($account->owner_id);
            $company->update([
                'level' => round($rates / $numOfRate2)
            ]);
        }

        return response()->json(['message' => 'The product was rated successfully.']);

    }
}
