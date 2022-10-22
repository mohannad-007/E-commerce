<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Wallet;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class WalletController extends Controller
{

    public function index()
    {
        $all_request = \App\Models\Request::query()
            ->get();

        if ($all_request->isEmpty()) {
            return response()->json(['message' => 'There are no requests.']);
        }
        return response()->json($all_request, Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $new_request = \App\Models\Request::query()
            ->create([
                'account_id' => Auth::id(),
                'value' => $request->value
            ]);

        return response()->json($new_request, Response::HTTP_OK);
    }


    public function accept($request_id)
    {
        $request = \App\Models\Request::query()
            ->find($request_id);

        if (!$request) {
            return response()->json(['message' => 'The request is not exist.']);
        }

        $wallet = Wallet::query()
            ->where('account_id', '=', $request->account_id)
            ->first();
        if (!$wallet) {
            $wallet = Wallet::query()
                ->create([
                    'account_id' => $request->account_id,
                    'value' => $request->value
                ]);
        }

        if($request->value >= 2000000){
            $string = str::random(10);

            Coupon::query()
                ->create([
                    'code' => $string,
                    'percent' => 0.02,
                    'account_id'=>$request->account_id
                ]);
        }

        $wallet->update([
                'value'=>$wallet->value + $request->value
            ]);

        Notification::query()
            ->create([
                'account_id'=>$request->account_id,
                'content'=>'تم شحن مبلغ الى محفظتك'
            ]);

        $request->delete();
        return response()->json(['message' => 'The wallet was stored successfully.']);
    }


    public function destroy($request_id)
    {
        $request = \App\Models\Request::query()
            ->find($request_id);

        if (!$request) {
            return response()->json(['message' => 'The request is not exist.']);
        }
        $request->delete();
        return response()->json(['message' => 'The request was deleted successfully.']);
    }

    public function update(Request $request, $request_id)
    {
        $edit_request = \App\Models\Request::query()
            ->find($request_id);

        if (!$edit_request) {
            return response()->json(['message' => 'The request is not exist.']);
        }
        if (Auth::id() == $edit_request->account_id) {
            $edit_request->update($request->all());
            return response()->json($edit_request, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Update is not Allowed.']);

    }
}
