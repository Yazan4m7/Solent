<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Api\Http\Controllers\ApiController;

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

Route::get('/doctor-cases/{id}', [ApiController::class, 'getDoctorCases']);

Route::get('/test',function(){
    return "test success";
});
Route::post('/statement',[ApiController::class,'statementOfAccount']);
Route::post('/get-current-balance',[ApiController::class,'getCurrentBalance']);
Route::post('/get-employees',[ApiController::class,'getEmployees']);
Route::post('/client-info',[ApiController::class,'clientInfo']);
Route::post('/opening-balance',[ApiController::class,'openingBalance']);
Route::post('/get-jobs',[ApiController::class,'getJobs']);
Route::post('/get-in-progress-cases',[ApiController::class,'getInProgressCases']);
Route::post('/get-completed-cases',[ApiController::class,'getCompletedCases']);
Route::post('/check-if-phone-exists',[ApiController::class,'checkPhoneNumber']);
Route::post('/get-gallery-items',[ApiController::class,'getGalleryItems']);
Route::post('/set-notification-token',[ApiController::class,'setNotificationToken']);
Route::post('/remove-token',[ApiController::class,'removeNotificationToken']);

// REPORTS
Route::post('/num-of-units-report',[ApiController::class,'unitsCountReport']);
Route::post('/job-types-report',[ApiController::class,'jobTypesReport']);
Route::post('/QC-report',[ApiController::class,'QCReport']);
Route::post('/implants-report',[ApiController::class,'implantsReport']);

Route::post('/register-login-time',[ApiController::class,'logSignin']);


//AUTH
Route::post('/login',[ApiController::class,'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint not found.',
        'status' => 404,
    ], 404);
});
