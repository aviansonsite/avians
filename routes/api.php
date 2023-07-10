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

Route::post('/checkLoginAPI', [LoginController::class, 'check_login_api']);
Route::post('/profile_edtAPI', [LoginController::class, 'profile_edit_api']);
Route::post('/check_passAPI', [LoginController::class, 'check_pass_api']);

//Attendance Management
Route::post('/punch_in_api', [LabourAPIController::class, 'punchInAPI']);
Route::post('/punch_out_api', [LabourAPIController::class, 'punchOutAPI']);

//Technician Expenses
Route::post('/post_elabour_payment_api', [LabourAPIController::class, 'postExpenseLPaymentAPI']);
Route::post('/delete_expense_api', [LabourAPIController::class, 'deleteExpenseAPI']);

//transfer other technician
Route::post('/post_tlabour_payment_api', [LabourAPIController::class, 'postTransferLPaymentAPI']);
Route::post('/delete_tlabour_payment_api', [LabourAPIController::class, 'trLabourPaymentDeleteAPI']);

//travel expense
Route::post('/post_travel_expense_api', [LabourAPIController::class, 'postTravelExpenseAPI']);
Route::post('/delete_travel_expense_api', [LabourAPIController::class, 'deleteTravelExpenseAPI']);