<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Coupon;
use App\Models\CouponGift;
use App\Http\Requests\StoreCouponGiftRequest;
use App\Http\Requests\UpdateCouponGiftRequest;
use App\Models\Notification;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponGiftController extends Controller
{
    public function index()
    {
        $coupon = CouponGift::query()->where('account_id', Auth::id())->with('account')->get();

        if ($coupon->isEmpty()) {
            return response()->json(['message' => 'There are no coupons.']);
        }

        return response()->json($coupon, Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $account = Account::query()
            ->where('email', '=', $request->email)
            ->first();

        if (!$account) {
            return response()->json(['message' => 'The account is not found.']);
        }

        $string = str::random(10);

        $coupon = CouponGift::query()
            ->create([
                'code' => $string,
                'money' => $request->money,
                'account_id' => $account->id
            ]);

        $wallet = Wallet::query()
            ->where('account_id', '=', Auth::id())
            ->first();

        $wallet->update([
            'value' => ($wallet->value) - $request->money
        ]);

        Notification::query()
            ->create([
                'account_id' => $account->id,
                'content' => 'تم اهدائك كوبون من قبل حساب اخر'
            ]);

        $coupon->with('account');

        return response()->json($coupon, Response::HTTP_OK);
    }
}
