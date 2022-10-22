<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ReplyComment;
use App\Http\Requests\StoreReplyCommentRequest;
use App\Http\Requests\UpdateReplyCommentRequest;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ReplyCommentController extends Controller
{
    public function store(Request $request,$comment_id)
    {
        $comment = Comment::query()->find($comment_id);

        if (!$comment) {
            return response()->json(['message' => 'The Comment is not Found.']);
        }

        $validator = Validator::make($request->all(), [
            'contet' => ['required', 'min:5', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $reply = ReplyComment::query()->create([
            'content' => $request->contet,
            'account_id' => Auth::id(),
            'comment_id' => $comment_id,
        ]);

        $reply->with('comment')
            ->get();


        return response()->json($reply,Response::HTTP_OK);
//        return response()->json(['message' => 'The Comment was Added Successfully.']);

    }


    public function update(Request $request, $reply_id)
    {
        $reply = ReplyComment::query()->find($reply_id);

        if (!$reply) {
            return response()->json(['message' => 'The ReplyComment is not Found.']);
        }

        if (Auth::id() == $reply->account_id) {
            $validator = Validator::make($request->all(), [
                'contet' => ['min:5', 'max:255'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $reply->update([
                'content'=>$request->contet
            ]);
            //return response()->json($reply,Response::HTTP_OK);
            return response()->json(['message' => 'The ReplyComment was Updated Successfully.']);
        }
        return response()->json(['message' => 'Update is not Allowed.']);
    }


    public function destroy($reply_id)
    {
        $reply = ReplyComment::query()->find($reply_id);

        if (!$reply) {
            return response()->json(['message' => 'The ReplyComment is not Found.']);
        }

        if (Auth::id() == $reply->account_id) {
            $reply->delete();
            return response()->json(['message' => 'The ReplyComment was Deleted Successfully.']);
        }
        return response()->json(['message' => 'Delete is not Allowed.']);
    }
}
