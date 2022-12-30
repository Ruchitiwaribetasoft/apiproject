<?php

namespace App\Http\Controllers\API;
use App\Models\User;
use App\Models\DoctorSchedules;
use App\Models\Appointments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserAppointment extends Controller
{
    public function appointments(Request $request)
    {
        $date = $request->date;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $day = Carbon::createFromFormat('d/m/Y', $date)->format('l');
        $schedule = DoctorSchedules::where([
            'day' => $day,
        ])->where(  
            'start_time', '<=' , $start_time,
        )->where(
            'end_time', '>=', $end_time
        )->get();
        
        if( $schedule){
            return response()->json($schedule);
        }else{
            return response()->json(['error'=>"Schedule not match"]);
        }
    }

    /**
     * [fixAppointment ]
     *
     */

    public function fixAppointment(Request $request){
        $user['user_id'] = Auth::user()->id;
        $doctor['doctor_id'] = $request->id;
        $input =  $request->all();
        $check_availability = Appointments::where([
            'doctor_id' => $doctor['doctor_id'],
            'date'  => $input['date'],
            'start_time' =>$input['start_time'],
            'end_time' => $input['end_time']
        ])->exists();

        if($check_availability){
            return response()->json(['error' =>  "doctor not available at the given time"]);
        }else{
            $fix_appointment=[
                'user_id' => $user['user_id'],
                'doctor_id' => $doctor['doctor_id'],
                'date'  => $input['date'],
                'start_time' =>$input['start_time'],
                'end_time' => $input['end_time']
            ];
            if(Appointments::create($fix_appointment)){
                return response()->json(['success' =>  "Appointment Fix"]);
            }
        }
    }
}
