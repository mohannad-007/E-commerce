<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;



class CartController extends Controller
{

    public function index()
    {
        $cart=Cart::query()->where('account_id',Auth::id())->first();

        if (!$cart) {
            return response()->json(['message' => 'The cart is not found.']);
        }

        $cart->products->pluck('id');

        return response()->json($cart, Response::HTTP_OK);
    }


    public function store($product_id)
    {
        $product = Product::query()->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The product is not found.']);
        }

        $cart=Cart::query()->where('account_id',Auth::id())->get();

        if ($cart->isEmpty()){
            $cart = Cart::query()->create([
                'account_id' => Auth::id()
            ]);
        }

        $product->carts()->attach($cart);

        return response()->json(['message' => 'The product added to cart successfully.']);
    }


    public function edit($cart_id,$product_id)
    {
        $cart = Cart::query()->find($cart_id);

        if (!$cart) {
            return response()->json(['message' => 'The cart is not found.']);
        }

        $product = Product::query()->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The product is not found.']);
        }

        if (Auth::id() == $cart->account_id) {

//            $product->carts()->detach();

            DB::table('cart_product')
                ->where('cart_id', '=', $cart_id)
                ->where('product_id', '=', $product_id)
                ->delete();

            return response()->json(['message' => 'The product removed successfully.']);

        }
        return response()->json(['message' => 'removed is not Allowed.']);

    }


    public function destroy($cart_id)
    {
        $cart = Cart::query()->find($cart_id);

        if (!$cart) {
            return response()->json(['message' => 'The cart is not found.']);
        }

        if (Auth::id() == $cart->account_id) {

            $cart->products()->detach();

            $cart->delete();

            return response()->json(['message' => 'The cart deleted successfully.']);

        }

        return response()->json(['message' => 'Delete is not Allowed.']);
    }
}
