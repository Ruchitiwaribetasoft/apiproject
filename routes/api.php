<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DoctorSchedule;

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
});

Route::controller(DoctorSchedule::class)->group(function () {
    Route::post('doctor-schedules','doctorSchedule')->middleware('auth:api');
    Route::post('doctor-listing','doctorListing')->middleware('auth:api');
    Route::put('update-schedule/{id}','updateSchedule')->middleware('auth:api');
    Route::put('delete-schedule/{id}','deleteSchedule')->middleware('auth:api');
});
