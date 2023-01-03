<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\DoctorSchedule;
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
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);  
        }
        $input = $request->all();
        $role = $input['role'];
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($role);
        $oClient = OClient::where('password_client', 1)->first();
        $registertokens= Helper::getTokenAndRefreshToken($oClient,$user->email, $request->password);
        if($registertokens){
            return response()->json([
                'status' => true,
                'Tokens' =>  $registertokens,
            ], 200);
        }else{
            return response()->json([ 
                'status'  => false,
                'message' => "error",
            ]);
        }
    }

    /**
     * [ login ]
    */
    public function login(Request $request)
    {
        $authCheck = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if($authCheck){
            $user = Auth::user();
            $oClient = OClient::where('password_client', 1)->first();
            $logintoken = Helper::getTokenAndRefreshToken($oClient,$user->email, $request->password);
            return response()->json([
                'status'      =>  true,
                'message'     => "Login Token",
                'loginTokens' =>  $logintoken,

            ], 200);
        }else{
            return response()->json([
                'status'  =>  false,
                'message' => "User Not found"
            ]);
        }
    }
   
    /**
     * [update user profile]
    */
    public function updateProfiles(Request $request){
        $validator = Validator::make($request->all(), [
            'age'    => 'required',
            'gender' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);  
        }
        $imageupload  = $request->file('image');
        $imageName    = $imageupload->getClientOriginalName();
        $uploadedFile =  time() .'_'.$imageName;
        $path  = $imageupload->storeas('public/images',$uploadedFile);
        $input = $request->all();
        $input['image']    = $uploadedFile;
        $userid['user_id'] = Auth::user()->id;
        $UpdateProfile = UserProfile::updateOrCreate($userid,$input);
        if($UpdateProfile){
            return response()->json([
                'status'  => true,
                'message' => "Successfully updated"
            ], 200);
        }else{
            return response()->json([ 
                'status'  =>  false,
                'message' => "error"
            ]);
        }
    }

    /**
     *    UsersListing 
     *
     * @param   Request  $request  [$request]
     *
     * @return  [json]       
     */
    public function usersData(Request $request){
        $id   = Auth::user()->id;
        $user = User::find($id);
        if($user){
            $profile_data = $user->UserProfile;
            $user_image   = asset('storage/images/'.$profile_data['image']);
            $userdata = [
                'name'   => $user['name'],
                'email'  => $user['email'],
                'age'    => $profile_data['age'],
                'gender' => $profile_data['gender'],
                'image'  => $user_image,
            ];
            return response()->json([
                'status'   => true,
                'message'  => "User Data",
                'UserData' => $userdata,
            ],200);
        }else{
            return response()->json([ 
                'status'  =>  false,
                'message' => "User not found"
            ]);
        }
    }
}


