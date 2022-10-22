<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\WishList;
use App\Http\Requests\StoreWishListRequest;
use App\Http\Requests\UpdateWishListRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class WishListController extends Controller
{

    public function index()
    {
        $wishList = WishList::query()->where('account_id', Auth::id())->first();

        if (!$wishList) {
            return response()->json(['message' => 'The wishList is Empty.']);
        }

        foreach ($wishList->products as $products) {
            $products->pivot;
        }

        return response()->json($wishList, Response::HTTP_OK);
    }


    public function store($product_id)
    {
        $product = Product::query()->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The product is not found.']);
        }

        $wishList = WishList::query()->where('account_id', Auth::id())->get();

        if ($wishList->isEmpty()) {
            $wishList = WishList::query()->create([
                'account_id' => Auth::id()
            ]);
        }

        foreach ($wishList as $wishLists) {
            $wishLists->id;
        }

        $wish = DB::table('wish_list_product')
            ->where('wish_list_id', '=', $wishLists->id)
            ->where('product_id', '=', $product_id)
            ->first();

        if (!$wish) {
            $product->wishLists()->attach($wishList);

            return response()->json(['message' => 'The product added to wishList successfully.']);
        }
        return response()->json(['message' => 'The product has already been added in wishList .']);
    }


    public function edit($wishList_id, $product_id)
    {
        $wishList = WishList::query()->find($wishList_id);

        if (!$wishList) {
            return response()->json(['message' => 'The wishList is Empty.']);
        }

        $product = Product::query()->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The product is not found.']);
        }

        if (Auth::id() == $wishList->account_id) {

//            $product->wishLists()->detach();
            DB::table('wish_list_product')
                ->where('wish_list_id', '=', $wishList_id)
                ->where('product_id', '=', $product_id)
                ->delete();

            return response()->json(['message' => 'The product removed successfully.']);

        }
        return response()->json(['message' => 'removed is not Allowed.']);

    }


    public function destroy($wishList_id)
    {
        $wishList = WishList::query()->find($wishList_id);

        if (!$wishList) {
            return response()->json(['message' => 'The wishList is Empty.']);
        }

        if (Auth::id() == $wishList->account_id) {

            $wishList->products()->detach();

            $wishList->delete();

            return response()->json(['message' => 'The wishList has been dispired successfully.']);

        }

        return response()->json(['message' => 'Delete is not Allowed.']);
    }
}
