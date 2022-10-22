<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Company;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            //throw new AuthenticationException();
            return response()->json(['message' => 'There is an Error in the Email or Password.']);

        }

        $account = $request->user();

        $tokenResult = $account->createToken('Personal Access Token');

        $data["account"] = $account;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json($data, Response::HTTP_OK);
        //return response()->json(['message'=>'You are Logged in Successfully.']);


    }

    public function createAccount(Request $request, $type_account)
    {
        $validator = Validator::make($request->all(), [
            'FullName' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('accounts', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            //return $validator->errors()->all();
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        $request['password'] = Hash::make($request['password']);

        $userOrCom = null;

        if ($type_account == 'user') {
            $userOrCom = User::query()->create([
                'region_id' => $request->region_id,
                'level' => $request->level,
                'gender' => $request->gender,
                'birthdate' => $request->birthdate,
            ]);
        } else if ($type_account == 'company') {
            $userOrCom = Company::query()->create([
                'region_id' => $request->region_id,
                'level' => $request->level,
                'branch' => $request->branch,
                'owner' => $request->owner,
            ]);
        } else {
            return response()->json(['message' => 'You must choice kind of account.']);
        }

        $account = Account::query()->create([
            'FullName' => $request->FullName,
            'email' => $request->email,
            'password' => $request->password,
            'owner_id' => $userOrCom->id,
            'owner_type' => get_class($userOrCom)
        ]);

        $tokenResult = $account->createToken('Personal Access Token');

        $data["account"] = $account;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;


        $string = str::random(10);

        Coupon::query()
            ->create([
                'code' => $string,
                'percent' => 0.05,
                'account_id'=>$userOrCom->id
            ]);

        Notification::query()
            ->create([
                'account_id'=>$account->id,
                'content'=>'تم انشاء حساب لك في الموقع بنجاح'
            ]);
        Notification::query()
            ->create([
                'account_id'=>$account->id,
                'content'=>'تم اهدائك كوبون من قبل فريق الموقع لانضمامك الى عائلتنا'
            ]);

        return response()->json($data, Response::HTTP_OK);
//        return response()->json(['message'=>'The Account was Created Successfully.']);

    }
}
