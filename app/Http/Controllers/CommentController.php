<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{


    public function store(Request $request,$product_id)
    {
        $product = Product::query()->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        $validator = Validator::make($request->all(), [
            'contet' => ['required', 'min:5', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $comments = Comment::query()->create([
            'content' => $request->contet,
            'account_id' => Auth::id(),
            'product_id' => $product_id,
        ]);

        $comments->with('product')
                 ->get();

        Notification::query()
            ->create([
                'account_id'=>$product->account_id,
                'content'=>'تم اضافة تعليق على منتجك'
            ]);

        return response()->json($comments,Response::HTTP_OK);
//        return response()->json(['message' => 'The Comment was Added Successfully.']);

    }


    public function update(Request $request, $comment_id)
    {
        $comment = Comment::query()->find($comment_id);

        if (!$comment) {
            return response()->json(['message' => 'The Comment is not Found.']);
        }

        if (Auth::id() == $comment->account_id) {
            $validator = Validator::make($request->all(), [
                'contet' => ['min:5', 'max:255'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $comment->update([
                'content'=>$request->contet
            ]);

            //return response()->json($comments,Response::HTTP_OK);
            return response()->json(['message' => 'The Comment was Updated Successfully.']);
        }
        return response()->json(['message' => 'Update is not Allowed.']);
    }


    public function destroy($comment_id)
    {
        $comment = Comment::query()->find($comment_id);

        if (!$comment) {
            return response()->json(['message' => 'The Comment is not Found.']);
        }

        if (Auth::id() == $comment->account_id) {
            $comment->replycomments()->delete();
            $comment->delete();
            return response()->json(['message' => 'The Comment was Deleted Successfully.']);
        }
        return response()->json(['message' => 'Delete is not Allowed.']);
    }
}
