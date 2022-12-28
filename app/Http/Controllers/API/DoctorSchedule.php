<?php

namespace App\Http\Controllers\API;
use App\Models\User;
use App\Models\DoctorSchedules;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class DoctorSchedule extends Controller
{
    public function doctorSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);  
        }
        $id  =  Auth::user()->id;
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $day = $request->day;
        $dataexist = DoctorSchedules::where('user_id','=',$input['user_id'])->where('day','=',$day )->exists();
        if($dataexist){
            return response()->json(['error' =>"Data Already Exist"], 200);
        }else{
            $DoctorSchedules= DoctorSchedules::create($input);
            if($DoctorSchedules){
                return response()->json(['success' =>"successfull"], 200);
            }else{
                return response()->json([ 
                    'status' => 'error',
                ]);
            }
        }
    }

    /**
     * [doctorListing]
     *
     */
    public function doctorListing() {
        $id    = Auth::user()->id;
        $users = User::find($id);
        $user  = $users->DoctorSchedules;
        return $user;
    }
    
    /**
     * [Edit Schedule]
     */
    public function updateSchedule(Request $request) {
        $validator = Validator::make($request->all(), [
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);  
        }
        $id = $request->id;
        $requestedData =  $request->all();
        $requestedData['id'] = $request->id;
        $data['user_id'] = Auth::user()->id;
        $updateSchedule = DoctorSchedules::updateOrCreate($data, $requestedData);
        if($updateSchedule){
            return response()->json(['success' =>"Update successfull"], 200);
        }else{
            return response()->json([ 
                'status' => 'error',
            ]);
        }
    }

    /**
     * [delete]
     */
    public function deleteSchedule(Request $request){
        $id = $request->id;
        $deleteSchedule = DoctorSchedules::find($id);
        $deleteSchedule->delete();
        if($deleteSchedule){
            return response()->json(['success' =>"deleted successfull"], 200);
        }
    }

}
