<?php
namespace App\Http\Controllers\API;
use App\Models\User;
use App\Models\DoctorSchedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class DoctorScheduleController extends Controller
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
        $schedule_exist = DoctorSchedule::where([
            'user_id' => $input['user_id'],
            'day'     => $day
        ])->exists();
        if($schedule_exist){ 
            return response()->json(['error' =>  "Data Already Exist"]);
        }else{
            $Doctor_Schedules = DoctorSchedule::create($input);
            if($Doctor_Schedules){
                return response()->json([
                    'status'  => true,
                    'message' => "Successfully Schedule Created"
                ], 200);
            }else{
                return response()->json([ 
                    'status'  => false,
                    'message' => "Error"
                ]);
            }
        }
    }

    /**
     *   Doctor Schedule
     *
     */
    public function doctorData() {
        $id    = Auth::user()->id;
        $login_user = User::find($id);
        if($login_user){
            $doctor_data = $login_user->DoctorSchedule;
            return response()->json([
                'status'     => true,
                'message'    => "Doctor Data",
                'DoctorData' => $doctor_data,
            ],200);
        }else{
            return response()->json([ 
                'status'  => false,
                'message' => "User not found"
            ]);
        }
    }
    
    /**
     *   Edit Schedule
    */
    public function updateSchedule(Request $request,$id) {
        $validator = Validator::make($request->all(), [
            'day'        => 'required',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json([
                'error' => $validator->errors()
            ], 401);  
        }
        $input =  $request->all();
        $check_data = DoctorSchedule::where([
            'day'        => $input['day'],
            'start_time' => $input['start_time'],
            'end_time'   => $input['end_time'],
        ])->exists();
        if($check_data){
            return response()->json([
                'status'  => false,
                'message' =>  "Data Already Exist"
            ]);
        }else{
            $input =  $request->all();
            $input['user_id'] = Auth::user()->id;
            $findUser = DoctorSchedule::find($id);
            if($findUser->update($input)){
                return response()->json([
                    'status'  => true,
                    'message' => "Update Successfull"
                ], 200);
            }else{
                return response()->json([ 
                    'status'  => false,
                    'message' => "Error"
                ]);
            }
        }
     }

    /**
     *   Delete
    */
    public function deleteSchedule(Request $request){
        $id = $request->id;
        $deleteSchedule = DoctorSchedule::find($id);
        if($deleteSchedule->delete()){
            return response()->json([
                'status'  => true,
                'message' => "deleted successfull"
            ], 200);
        }else{
            return response()->json([
                'status'  => false,
                'message' => "Not deleted"
            ]);
        }
    }
}
