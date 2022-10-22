<?php


namespace App\Http\Controllers;

use App\Models\Add;
use App\Models\Account;
use App\Models\Company;
use App\Models\Video;
use App\Http\Requests\StoreAddRequest;
use App\Http\Requests\UpdateAddRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class AddController extends Controller
{

    public function index()
    {
        $add = Add::query()->with('video')->get();
        return response()->json($add, Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $account = Account::query()->where('id', Auth::id())->first();
        if ($account->owner_type == 'App\Models\User') {
            return response()->json(['message' => 'Sorry!']);
        }
        $data = $request->all();
        $company = Company::query()->where('id', $account->owner_id)->first();
        $validator = Validator::make($request->all(), [
            'email' => ['string', 'email', 'max:255'],
            'details' => ['required', 'string', 'min:50'],
            'company_name' => ['string'],
            'name' => ['mimes:mp4,ogx,oga,ogv,ogg,webm,wvm,mov,mpeg,3gp,flv,,avi,wmv,ts', 'max:100040']
        ]);
        if ($validator->fails()) {
            return response()->json([$validator->errors()]);
        }
        $add = new Add;

        if ($request->email) {
            $add->email = $request->email;
        } else {
            $add->email = $account->email;
        }
        if ($request->company_name) {
            $add->company_name = $request->company_name;
        } else {
            $add->company_name = $account->FullName;
        }
        $add->details = $request->details;
        $add->company_id = $company->id;
        $add->save();
        if ($request->name) {
            $video = new Video;
            $ve = $data['name'];
            $input = time() . "." . $ve->getClientOriginalExtension();
            $destinationPath = 'videos';
            $ve->move($destinationPath, $input);
            $video->name = 'http://localhost:8000/videos/' . $input;
            $video->add_id = $add->id;
            $video->save();
        }
        return response()->json(['message' => 'Done!']);
    }


    public function show($id)
    {
        $add = Add::query()->with('video')->find($id);
        if (!$add) {
            return response()->json(['message' => 'Sorry!']);
        }
        return response()->json($add, Response::HTTP_OK);
    }


    public function update(Request $request, $id)
    {
        $add = Add::query()->where('id', $id)->first();
        if (!$add) {
            return response()->json(['message' => 'Sorry!']);
        }
        $account = Account::query()->where('id', Auth::id())->first();
        if ($account->owner_type == 'App\Models\User') {
            return response()->json(['message' => 'Sorry!']);
        }
        $company = Company::query()->where('id', $account->owner_id)->first();
        if ($add->company_id != $company->id) {
            return response()->json(['message' => 'Sorry!']);
        }
        $data = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => ['string', 'email', 'max:255'],
            'details' => ['string', 'min:50'],
            'company_name' => ['string'],
            'name' => ['mimes:mp4,ogx,oga,ogv,ogg,webm,wvm,mov,mpeg,3gp,flv,,avi,wmv,ts', 'max:100040']
        ]);
        if ($validator->fails()) {
            return response()->json([$validator->errors()]);
        }
        if ($request->email) {
            $add->email = $request->email;
        }
        if ($request->company_name) {
            $add->company_name = $request->company_name;
        }
        if ($request->details) {
            $add->details = $request->details;
        }
        $add->save();
        if ($request->name) {
            $video = Video::query()->where('add_id', $add->id)->first();
            if (!$video) {
                $video = new Video;
                $ve = $data['name'];
                $input = time() . "." . $ve->getClientOriginalExtension();
                $destinationPath = 'videos';
                $ve->move($destinationPath, $input);
                $video->name = 'http://localhost:8000/videos/' . $input;
                $video->add_id = $add->id;
                $video->save();
            } else {
                $ve = $data['name'];
                $input = time() . "." . $ve->getClientOriginalExtension();
                $destinationPath = 'videos';
                $ve->move($destinationPath, $input);
                $video->name = 'http://localhost:8000/videos/' . $input;
                $video->add_id = $add->id;
                $video->save();
            }
        }
        return response()->json(['message' => 'Done!']);
    }


    public function destroy($id)
    {
        $add = Add::query()->with('video')->find($id);
        if (!$add) {
            return response()->json(['message' => 'Sorry!']);
        }
        $account = Account::query()->where('id', Auth::id())->first();
        if ($account->owner_type == 'App\Models\User') {
            return response()->json(['message' => 'Sorry!']);
        }
        $company = Company::query()->where('id', $account->owner_id)->first();
        if ($add->company_id != $company->id) {
            return response()->json(['message' => 'Sorry!']);
        }
        $video = Video::query()->where('add_id', $add->id)->first();
        if (!$video) {
            $add->delete();
            return response()->json(['message' => 'Done!']);
        }
        $video->delete();
        $add->delete();
        return response()->json(['message' => 'Done!']);
    }
}
