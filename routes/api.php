<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DoctorScheduleController;
use App\Http\Controllers\API\UserAppointmentController;

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
    Route::group(['middleware' => ['auth:api']], function (){ 
        Route::post('update-profiles', 'updateProfiles');
        Route::post('users-data', 'usersData');
    });
});

Route::controller(DoctorScheduleController::class)->group(function () {
    Route::group(['middleware' => ['auth:api']], function (){ 
        Route::post('doctor-schedules','doctorSchedule');
        Route::post('doctor-data','doctorData');
        Route::put('update-schedule/{id}','updateSchedule');
        Route::put('delete-schedule/{id}','deleteSchedule');
    });
});

Route::controller(UserAppointmentController::class)->group(function(){
    Route::group(['middleware' => ['auth:api']], function (){ 
        Route::post('available-doctor','checkAvailability');
        Route::post('fix-appointment/{id}','fixAppointment');
    });
});
