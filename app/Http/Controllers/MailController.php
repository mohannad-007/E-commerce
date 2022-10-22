<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request){
        $data = $request->data;

        Mail::send(['text'=>'mail'],$data,function($message){
            $message->to('nasouhhmwi@gmail.com','Nassouh Hamwi')
                ->subject('Complaints');
            $message->from(Account::query()->find(Auth::id())->email,
                Account::query()->find(Auth::id())->FullName);
        });
    }
}
