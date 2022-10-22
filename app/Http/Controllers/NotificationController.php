<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class NotificationController extends Controller
{
    public function index()
    {
        $notification = Notification::query()
            ->where('account_id','=',Auth::id())
            ->get();

        if(!$notification){
            return response()->json(['message' => 'There are no notification.']);
        }

        $notification['numOfNotification']= count($notification);

        return response()->json($notification, Response::HTTP_OK);

    }

    public function destroy($notification_id)
    {
        $notification = Notification::query()
            ->find($notification_id);

        if(!$notification){
            return response()->json(['message' => 'The notification is not found.']);
        }

        return response()->json(['message' => 'The notification was Deleted Successfully.']);

    }
}
