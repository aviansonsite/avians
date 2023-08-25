<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LabourAPIController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/check_login_api', [LoginController::class, 'checkLoginAPI']);
Route::post('/profile_edit_api', [LoginController::class, 'profileEditAPI']);
Route::post('/check_pass_api', [LoginController::class, 'checkPassAPI']);

//Attendance Management
// Route::post('/punch_in_api', [LabourAPIController::class, 'punchInAPI']);
// Route::post('/punch_out_api', [LabourAPIController::class, 'punchOutAPI']);

Route::get('/get_pio_records', [LabourAPIController::class, 'getPIORecords']);
// Route::post('/punch_in', [AttendanceController::class, 'punchIn']);
// Route::post('/punch_out', [AttendanceController::class, 'punchOut']);
// Route::post('webcam', [AttendanceController::class, 'store'])->name('webcam.capture');
// Route::get('/get-pouth-labour', [AttendanceController::class, 'getPoutHLabour']);
// Route::get('/get-pinh-labour', [AttendanceController::class, 'getPinHLabour']);


//Technician Expenses
Route::post('/post_elabour_payment_api', [LabourAPIController::class, 'postExpenseLPaymentAPI']);
Route::post('/delete_expense_api', [LabourAPIController::class, 'deleteExpenseAPI']);

//transfer other technician
Route::post('/post_tlabour_payment_api', [LabourAPIController::class, 'postTransferLPaymentAPI']);
Route::post('/delete_tlabour_payment_api', [LabourAPIController::class, 'trLabourPaymentDeleteAPI']);

//travel expense
Route::post('/post_travel_expense_api', [LabourAPIController::class, 'postTravelExpenseAPI']);
Route::post('/delete_travel_expense_api', [LabourAPIController::class, 'deleteTravelExpenseAPI']);