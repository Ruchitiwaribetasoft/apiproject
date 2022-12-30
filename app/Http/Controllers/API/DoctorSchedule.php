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
    /**
     *  DoctorSchedule 
     *
     * @param   Request  $request
     *
     * @return  [json]     
     */

    public function doctorSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error' => $validator->errors()], 401);  
        }

        $input  = $request->all();
        $input['user_id'] = Auth::user()->id;
        $day = $input['day'];
        $user_id = $input['user_id'];
        $dataexist = DoctorSchedules::where([
            'user_id' => $user_id,
            'day'     => $day
        ])->exists();
        if($dataexist){ 
            return response()->json(['error' =>  "Data Already Exist"]);
        }else{
            $Schedules = DoctorSchedules::create($input);
            if($Schedules){
                return response()->json(['success' => "successfull"], 200);
            }else{
                return response()->json([ 
                    'status' => 'error',
                ]);
            }
        }
    }

    /**
     *   DoctorListing
     *
     */

    public function doctorListing() {
        $id    = Auth::user()->id;
        $users = User::find($id);
        $doctorlisting  = $users->DoctorSchedules;
        return response()->json($doctorlisting, 200);
    }
    
    /**
     *   Edit Schedule
    */

    public function updateSchedule(Request $request) {
        $validator = Validator::make($request->all(), [
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error' => $validator->errors()], 401);  
        }
        $input =  $request->all();
        $dataexist = DoctorSchedules::where([
            'day'     => $input['day'],
            'start_time' => $input['start_time'],
            'end_time' => $input['end_time'],
        ])->exists();
        if($dataexist){
            return response()->json(['error' =>  "Data Already Exist"]);
        }else{
            $input =  $request->all();
            $input['user_id'] = Auth::user()->id;
            $id = $request->id;
            $findUser = DoctorSchedules::find($id);
            if($findUser->update($input)){
                return response()->json(['success' => "Update Successfull"], 200);
            }else{
                return response()->json([ 
                    'status' => 'error',
                ]);
            }
        }
     }

    /**
     *   Delete
    */
    public function deleteSchedule(Request $request){
        $id = $request->id;
        $deleteSchedule = DoctorSchedules::find($id);
        if($deleteSchedule->delete()){
            return response()->json(['success' => "deleted successfull"], 200);
        }else{
            return response()->json(['error' => "Not deleted"]);
        }
    }

}
