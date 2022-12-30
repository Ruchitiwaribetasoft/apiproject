<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedules extends Model
{
    use HasFactory;
    protected $fillable = ['day', 'start_time','end_time','user_id'];
    public function doctorSchedule(){
        return $this->belongsTo('App\Models\User');
    }

    
}
