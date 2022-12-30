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
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);  
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($role);
        $oClient = OClient::where('password_client', 1)->first();
        $registertokens= Helper::getTokenAndRefreshToken($oClient,$user->email, $request->password);
        if($registertokens){
            return response()->json($registertokens, 200);
        }else{
            return response()->json([ 
                'status' => 'error',
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
            $logintokens= Helper::getTokenAndRefreshToken($oClient,$user->email, $request->password);
            return response()->json($logintokens, 200);
        }
        else{
            return response()->json([
                'status' =>'error',
            ]);
        }
    }
   
    /**
     * [update user profile]
    */
    
    public function userProfiles(Request $request){
        $validator = Validator::make($request->all(), [
            'age'    => 'required',
            'gender' => 'required',
            'image'  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);  
        }
        $imageupload = $request->file('image');
        $imageName = $imageupload->getClientOriginalName();
        $uploadedFile =   time() .'_'.$imageName;
        $path = $imageupload->storeas('public/images',$uploadedFile);
        $input = $request->all();
        $input['image'] = $uploadedFile;
        $userid['user_id'] = Auth::user()->id;
        $UpdateProfile= UserProfiles::updateOrCreate($userid,$input);
        if($UpdateProfile){
            return response()->json(['success' =>"successfull"], 200);
        }else{
            return response()->json([ 
                'status' => 'error',
            ]);
        }
    }

    /**
     *    UsersListing 
     *
     * @param   Request  $request  [$request description]
     *
     * @return  [json]       
     */
    public function usersListing(Request $request){
        $id    = Auth::user()->id;
        $users = User::find($id);
        $user  = $users->UserProfiles;
        $imagepath = asset('storage/images/'.$user['image']);
        $userdata = [
            'name' =>$users['name'],
            'email'=>$users['email'],
            'age'  => $user['age'],
            'gender' => $user['gender'],
            'image'  => $imagepath,
        ];
        return response()->json($userdata, 200);
    }
}


