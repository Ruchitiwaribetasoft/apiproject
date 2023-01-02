<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    protected $fillable = ['age', 'gender','image','user_id'];
    public function extraData(){
       return $this->belongsTo('App\Models\User');
    }
    
}
