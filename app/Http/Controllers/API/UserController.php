<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfiles;
use App\Models\DoctorSchedules;
use Validator;
use Illuminate\Support\Facades\Input;
use Hash;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;
use Helper;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $role = $request->role;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($role);
        $oClient = OClient::where('password_client', 1)->first();
        $registertokens= Helper::getTokenAndRefreshToken($oClient,$user->email, $request->password);
        return $registertokens;
    }

    ///login
    public function login(Request $request)
    {
        $authCheck = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if($authCheck){
            $user = Auth::user();
            $oClient = OClient::where('password_client', 1)->first();
            $helpertokens= Helper::getTokenAndRefreshToken($oClient,$user->email, $request->password);
            return response()->json($helpertokens, 200);
        }
        else{
            return response()->json([
                'status' => false,
            ], 500);
        }
    }

    //update user profile
    public function userprofiles(Request $request){
        $validator = Validator::make($request->all(), [
            'age' => 'required',
            'gender' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $imageupload = $request->file('image')->store('images');
        $input = $request->all();
        $input['image'] = $imageupload;
        $userid['user_id'] = Auth::user()->id;
        $post= UserProfiles::updateOrCreate($userid,$input);
        return "successfull";
    }

    //doctor schedule
    public function doctorschedule(Request $request){
        $validator = Validator::make($request->all(), [
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $userid['user_id'] = Auth::user()->id;
        $post= DoctorSchedules::updateOrCreate($userid,$input);
        return "Doctor Schedule Inserted";
    }
}


