<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
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

    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'FullName' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('accounts', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'the_mission' => ['required', 'string', 'min:5', 'max:255'],
            'year_of_employment' => ['required', 'date'],
            'gender' => ['required', 'string', 'min:4', 'max:5'],
            'salary' => ['required', 'numeric', 'max:5000000'],
            'birthdate' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        $request['password'] = Hash::make($request['password']);

        $admin = Admin::query()->create([
            'the_mission' => $request->the_mission,
            'year_of_employment' => $request->year_of_employment,
            'gender' => $request->gender,
            'salary' => $request->salary,
            'birthdate' => $request->birthdate,
        ]);


        $account = Account::query()->create([
            'FullName' => $request->FullName,
            'email' => $request->email,
            'password' => $request->password,
            'owner_id' => $admin->id,
            'owner_type' => get_class($admin)
        ]);

        $tokenResult = $account->createToken('Personal Access Token');

        $data["account"] = $account;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;


        return response()->json($data, Response::HTTP_OK);
//        return response()->json(['message'=>'The Account was Created Successfully.']);
    }
}
