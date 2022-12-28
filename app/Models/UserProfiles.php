<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfiles extends Model
{
    use HasFactory;
    protected $fillable = ['age', 'gender','image','user_id'];
    public function data(){
       return $this->belongsTo('App\Models\User');
    }
    
}
