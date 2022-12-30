<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DoctorSchedule;
use App\Http\Controllers\API\UserAppointment;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function () {
    Route::post('register','register');
    Route::post('login','login');
    Route::post('user-profiles', 'userProfiles')->middleware('auth:api');
    Route::post('users-listing', 'usersListing')->middleware('auth:api');
});

Route::controller(DoctorSchedule::class)->group(function () {
    Route::group(['middleware' => ['auth:api']], function (){ 
        Route::post('doctor-schedules','doctorSchedule');
        Route::post('doctor-listing','doctorListing');
        Route::put('update-schedule/{id}','updateSchedule');
        Route::put('delete-schedule/{id}','deleteSchedule');
    });
});

Route::controller(UserAppointment::class)->group(function(){
    Route::post('available-doctor','appointments')->middleware('auth:api');
    Route::post('fix-appointment/{id}','fixAppointment')->middleware('auth:api');
});
