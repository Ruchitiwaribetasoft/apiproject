<?php

namespace App\Http\Controllers\API;
use App\Models\User;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserAppointmentController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $date       = $request->date;
        $start_time = $request->start_time;
        $end_time   = $request->end_time;
        $day = Carbon::createFromFormat('d/m/Y', $date)->format('l');
        $schedule = DoctorSchedule::where([
            'day' => $day,
        ])->where(  
            'start_time','<=',$start_time,
        )->where(
            'end_time','>=',$end_time
        )->get();
        
        if($schedule){
            return response()->json([
                'status'     => true,
                'message'    => "Available Schedule",
                'DoctorData' => $schedule,
            ],200);
        }else{
            return response()->json([
                'status'  => false,
                'message' => "Schedule not match"
            ]);
        }
    }

    /**
     * [fixAppointment]
     *
     */
    public function fixAppointment(Request $request, $id){
        $input =  $request->all();
        $user = Auth::user()->id;
        $checkUser = Appointment::where([
            'user_id' => $user
        ])->exists();
        if($checkUser){
            return response()->json([
                'status'  => false,
                'message' =>  "User appointment already exist"
            ]);
        }else{
            $check_appointments =  Appointment::where([
                    'doctor_id' => $id,
                    'date'      => $input['date'],
                ])->where(
                    'start_time','<=', $input['start_time'],
                )->where(
                    'end_time' ,'>=', $input['end_time']
                )->exists();
            if ($check_appointments){
                return response()->json([
                    'status'  => false,
                    'message' =>  "doctor not available at the given time"
                ]);
            }else{
                $fix_appointment=[
                    'user_id'    => $user,
                    'doctor_id'  => $id,
                    'date'       => $input['date'],
                    'start_time' => $input['start_time'],
                    'end_time'   => $input['end_time']
                ];
                if(Appointment::create($fix_appointment)){
                    return response()->json([
                        'status'  => true,
                        'message' => "Appointment fix At", 'Time'=>$fix_appointment['start_time']
                    ]);
                }else{
                    return response()->json([ 
                        'status'  => false,
                        'message' => "Error"
                    ]);
                }
            }   
        }
    }
}


