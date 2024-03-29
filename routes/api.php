<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LabourAPIController;
use App\Http\Controllers\AdminAPIController;


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

//User Management
Route::post('/users', [AdminAPIController::class, 'users']);
Route::post('/post_user', [AdminAPIController::class, 'postUser']);
Route::post('/change_status', [AdminAPIController::class, 'change_status']);
Route::post('/user_del', [AdminAPIController::class, 'user_delete']);
Route::post('/res_pass', [AdminAPIController::class, 'resPass']);

//oa Management
Route::post('/check_tl_status', [AdminAPIController::class, 'checkTlStatus']);
Route::post('/post_oa', [AdminAPIController::class, 'postOA']);
Route::post('/get_so', [AdminAPIController::class, 'getSO']);
Route::post('/get_so_technician', [AdminAPIController::class, 'getSOTechnician']);
Route::post('/remove_tl', [AdminAPIController::class, 'removeTL']);
Route::post('/delete_so', [AdminAPIController::class, 'soDelete']);


//Manage Expense Requests
Route::post('/manage_exp_tech', [AdminAPIController::class, 'manageExpTechnicians']);       
Route::post('/get_all_expenses', [AdminAPIController::class, 'getAllExpense']);
Route::post('/post_expense', [AdminAPIController::class, 'postExpense']);
Route::post('/aprvd_check_exp', [AdminAPIController::class, 'aprvdCheckExp']);
Route::post('/admin_cleared_exp', [AdminAPIController::class, 'adminClearedExp']);


//Travel Expense
Route::post('/travel_exp_tech', [AdminAPIController::class, 'travelExpTechnicians']);       
Route::post('/get_travel_expenses', [AdminAPIController::class, 'getTravelExpense']);
Route::post('/update_travel_expense', [AdminAPIController::class, 'updateTravelExpenses']);
Route::post('/admin_cleared_travelexp', [AdminAPIController::class, 'adminClearedTravelexp']);
Route::post('/aprvd_check_travelexp', [AdminAPIController::class, 'aprvdCheckTravelExp']);

//Technician Payment
Route::post('/tech_paymets', [AdminAPIController::class, 'techniciansPayments']);
Route::post('/post_labour_payment', [AdminAPIController::class, 'postLabourPayment']);
Route::post('/get_labour_payment', [AdminAPIController::class, 'getLabourPayment']);
Route::post('/delete_labour_payment', [AdminAPIController::class, 'LabourPaymentDelete']);

//Technician Payment
Route::post('/view_oa_payment_history', [AdminAPIController::class, 'viewOAPaymentHistory']);

//site expense Report
Route::post('/site_exp_technicians', [AdminAPIController::class, 'siteExpTechnicians']);
Route::post('/get_tech_so', [AdminAPIController::class, 'getTechSO']);
Route::post('/get_exp_record', [AdminAPIController::class, 'getExpRecord']);;
Route::post('/generate_pdf', [AdminAPIController::class, 'generatePdf']);

//work Report 
Route::post('/work_report', [AdminAPIController::class, 'workReport']);
Route::post('/get_work_record', [AdminAPIController::class, 'getWorkRecord']);
Route::post('/generate-work-pdf', [AdminAPIController::class, 'generateWorkPdf']);

//attendance Report
Route::post('/tech_attendance_report', [AdminAPIController::class, 'techAttendanceReport']);
Route::post('/tech_daily_att_record', [AdminAPIController::class, 'techDailyAttRecord']);
Route::post('/generate-attendance-pdf', [AdminAPIController::class, 'generateAttendancePdf']);

//Technician Attendance Regularise
Route::post('/technician_attendance', [AdminAPIController::class, 'technicianAttendance']);
Route::post('/get_labour', [AdminAPIController::class, 'getLabour']);
Route::post('/tech_att_record', [AdminAPIController::class, 'techAttRecord']);
Route::post('/regularise-attendance', [AdminAPIController::class, 'regulariseAttendance']);
Route::post('/update-tech-time', [AdminAPIController::class, 'updateTechTime']);
Route::post('/get_regularise_history', [AdminAPIController::class, 'getRegulariseHistory']);




// ---------------------------------------- LABOUR API -------------------------------- 
//Attendance Management
Route::post('/get_pio_records', [LabourAPIController::class, 'getPIORecords']);

Route::post('/punch_in_api', [LabourAPIController::class, 'punchInAPI']);
Route::post('/punch_out_api', [LabourAPIController::class, 'punchOutAPI']);
Route::post('/get_pinh_labour', [LabourAPIController::class, 'getPinHLabourAPI']);
Route::post('/get_pouth_labour', [LabourAPIController::class, 'getPoutHLabourAPI']);

// Route::post('webcam', [AttendanceController::class, 'store'])->name('webcam.capture');


//Technician Expenses
Route::post('/post_elabour_payment_api', [LabourAPIController::class, 'postExpenseLPaymentAPI']);
Route::post('/delete_expense_api', [LabourAPIController::class, 'deleteExpenseAPI']);
Route::post('/get_labour_expenses_api', [LabourAPIController::class, 'getLabourExpenseAPI']);

//transfer other technician
Route::post('/post_tlabour_payment_api', [LabourAPIController::class, 'postTransferLPaymentAPI']);
Route::post('/get_tran_lab_pay_api', [LabourAPIController::class, 'getTransferLabourPaymentAPI']);
Route::post('/delete_tlabour_payment_api', [LabourAPIController::class, 'trLabourPaymentDeleteAPI']);

//travel expense
Route::post('/post_travel_expense_api', [LabourAPIController::class, 'postTravelExpenseAPI']);
Route::post('/get_travel_expenses_api', [LabourAPIController::class, 'getTravelExpenseAPI']);
Route::post('/delete_travel_expense_api', [LabourAPIController::class, 'deleteTravelExpenseAPI']);

//labour dashboard
Route::post('/income_api', [LabourAPIController::class, 'incomeListAPI']);
Route::post('/get_acc_payment_api', [LabourAPIController::class, 'getAccPaymentAPI']);
Route::post('/get_ot_tech_payment_api', [LabourAPIController::class, 'getOtTechPaymentAPI']);