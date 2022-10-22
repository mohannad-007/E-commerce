<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cart;
use App\Models\Coupon;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{

    public function index()
    {
        $coupon = Coupon::query()->where('account_id', Auth::id())->with('account')->get();

        if (!$coupon) {
            return response()->json(['message' => 'There are no coupons.']);
        }

        return response()->json($coupon, Response::HTTP_OK);
    }

    public function all()
    {
        $coupon = Coupon::query()->with('account')->get();

        if (!$coupon) {
            return response()->json(['message' => 'There are no coupons.']);
        }

        return response()->json($coupon, Response::HTTP_OK);
    }

    public function create(Request $request, $account_id)
    {
        $account = Account::query()->find($account_id);

        if (!$account) {
            return response()->json(['message' => 'The account is not found.']);
        }

        $string = str::random(10);

        $coupon = Coupon::query()
            ->create([
                'code' => $string,
                'percent' => $request->percent,
                'account_id' => $account->id
            ]);

        Notification::query()
            ->create([
                'account_id'=>$account->id,
                'content'=>'تم اهدائك كوبون من قبل الموقع'
            ]);

        $coupon->with('account');

        return response()->json($coupon, Response::HTTP_OK);
    }

    public function show($coupon_id)
    {
        $coupon = Coupon::query()->with('account')->find($coupon_id);

        if (!$coupon) {
            return response()->json(['message' => 'The coupon is not found.']);
        }

        $coupon->with('account');

        return response()->json($coupon, Response::HTTP_OK);
    }


    public function update(Request $request,$coupon_id)
    {
        $coupon = Coupon::query()->find($coupon_id);

        if (!$coupon) {
            return response()->json(['message' => 'The coupon is not found.']);
        }

        $coupon->update([
                'percent' => $request->percent,
            ]);

        return response()->json($coupon, Response::HTTP_OK);
    }

    public function destroy($coupon_id)
    {
        $coupon = Coupon::query()->find($coupon_id);

        if (!$coupon) {
            return response()->json(['message' => 'The coupon is not found.']);
        }

        $coupon->delete();

        return response()->json(['message' => 'The coupon was Deleted Successfully.']);
    }
}
