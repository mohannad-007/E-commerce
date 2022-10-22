<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\UpdateLikeRequest;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class LikeController extends Controller
{
    public function like($product_id)
    {
        $product = Product::query()->find($product_id);
        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        $like = Like::query()->where('account_id', Auth::id())
            ->where('product_id', $product_id)->first();


        if (!$like) {
            Like::query()->create([
                'product_id' => $product_id,
                'account_id' => Auth::id(),
            ]);
            $product->increment('likes');

            Notification::query()
                ->create([
                    'account_id'=>$product->account_id,
                    'content'=>'تم اضافة اعجاب على منتجك'
                ]);

            return response()->json(['message' => 'You Liked The Product']);
        }

        $product->update([
            'likes' => $product->likes - 1
        ]);
        $like->delete();
        return response()->json(['message' => 'You Disliked The Product ']);
    }

}
