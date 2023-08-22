<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SOController;
use App\Http\Controllers\LabourPaymentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'login'])->name('login.page');

Route::post('/checkLogin', [LoginController::class, 'check_login']);
Route::group(['middleware' => 'login'], function () 
{
    // DASHBOARD
    Route::get('/dashboard', [LoginController::class, 'index'])->name('dashboard');

    Route::get('/logout', function(){

        Session::flush(); // removes all session data
        Auth::logout(); // logs out the user
        return redirect()->route('login.page');
    })->name('admin.logout');

    //Profile Management
    Route::get('/profile', [LoginController::class, 'profile'])->name('profile.page');
    Route::post('/profile_edt', [LoginController::class, 'profile_edit']);
    Route::post('/check_pass', [LoginController::class, 'check_pass']);

    //Company Management
    Route::get('/company', [CompanyProfileController::class, 'company_profile'])->name('company.profile.page')->middleware('check');
    Route::post('/edit_company', [CompanyProfileController::class, 'edit_company'])->middleware('check');

    //User Management
    Route::get('/users', [UserController::class, 'user_list'])->name('user.page')->middleware('check');
    Route::post('/post_user', [UserController::class, 'postUser'])->middleware('check');
    Route::post('/edit_users', [UserController::class, 'edit_users'])->middleware('check');
    Route::get('/change_status/{id}', [UserController::class, 'change_status'])->middleware('check');
    Route::get('/user_del', [UserController::class, 'user_delete'])->middleware('check');
    Route::get('/res_pass', [UserController::class, 'resPass']);

    //OA Management
    Route::get('/so', [SOController::class, 'so_list'])->name('so.page');
    Route::post('/post_oa', [SOController::class, 'postOA']);
    Route::get('/get_so', [SOController::class, 'getSO']);
    Route::get('/edit-so', [SOController::class, 'editSO']);
    Route::get('/delete_so', [SOController::class, 'soDelete']);
    Route::get('/check_tl_status', [SOController::class, 'checkTlStatus']);
    Route::post('/remove_tl', [SOController::class, 'removeTL']);

    //Visit OA Management
    Route::get('/visit_so', [SOController::class, 'visitSoList'])->name('visit_so.page');

    //Expense
    Route::get('/site_exp_report', [ReportController::class, 'siteExpReport'])->name('site_exp_report.page');
    Route::get('/get-exp-record', [ReportController::class, 'getExpRecord']);
    Route::post('/generate-pdf', [ReportController::class, 'generatePdf']);
    // ****** Start Accountant **********

    //Accountant Management
    Route::get('/labour_payment', [LabourPaymentController::class, 'labourPaymentList'])->name('labour_payment.page');
    Route::get('/post_labour_payment', [LabourPaymentController::class, 'postLabourPayment']);
    Route::get('/get_labour_payment', [LabourPaymentController::class, 'getLabourPayment']);
    Route::get('/edit-labour-payment', [LabourPaymentController::class, 'editLabourPayment']);
    Route::get('/delete_labour_payment', [LabourPaymentController::class, 'LabourPaymentDelete']);

    //attendance regularise 
    Route::get('/technician_attendance', [AttendanceController::class, 'technicianAttendance'])->name('technician_attendance.page');
    Route::get('/tech-att-record', [AttendanceController::class, 'techAttRecord']);
    Route::get('/get-labour', [AttendanceController::class, 'getLabour']);
    Route::get('/regularise-attendance', [AttendanceController::class, 'regulariseAttendance']);


    // Accountant Manage Payment
    Route::get('/manage_labour_payment', [LabourPaymentController::class, 'managelabourPayment'])->name('manage_labour_payment.page');
    Route::get('/get_all_expenses', [LabourPaymentController::class, 'getAllExpense']);
    Route::get('/post_expense', [LabourPaymentController::class, 'postExpense']);


    // Accountant Manage Payment
    Route::get('/SO_payment_history', [SOController::class, 'SOPaymentHistory'])->name('SO_payment_history.page');
    Route::get('/view_oa_payment_history/{id}', [SOController::class, 'viewOAPaymentHistory']);
    // Route::get('/get_all_expenses', [LabourPaymentController::class, 'getAllExpense']);
    // Route::get('/post_expense', [LabourPaymentController::class, 'postExpense']);


    // ****** End Accountant **********



    // ****** Start Labour **********

        //Attendance Management
        Route::get('/attendance', [AttendanceController::class, 'attendanceList'])->name('attendance.page');
        Route::get('/get_pio_records', [AttendanceController::class, 'getPIORecords']);
        Route::post('/punch_in', [AttendanceController::class, 'punchIn']);
        Route::post('/punch_out', [AttendanceController::class, 'punchOut']);
        Route::post('webcam', [AttendanceController::class, 'store'])->name('webcam.capture');
        Route::get('/get-pouth-labour', [AttendanceController::class, 'getPoutHLabour']);
        Route::get('/get-pinh-labour', [AttendanceController::class, 'getPinHLabour']);


        Route::get('/income', [LabourPaymentController::class, 'incomeList'])->name('income.page');
        Route::get('/get_acc_payment', [LabourPaymentController::class, 'getAccPayment']);
        Route::get('/get_ot_tech_payment', [LabourPaymentController::class, 'getOtTechPayment']);


        //labour expense
        Route::get('/expense', [LabourPaymentController::class, 'expenseList'])->name('expense.page');
        Route::post('/post_elabour_payment', [LabourPaymentController::class, 'postExpenseLPayment']);
        Route::get('/get_labour_expenses', [LabourPaymentController::class, 'getLabourExpense']);
        Route::get('/delete_expense', [LabourPaymentController::class, 'deleteExpense']);

        //transfer other technician
        Route::get('/transfer_other_technician', [LabourPaymentController::class, 'transferOtherTechnicianList'])->name('transfer_other_technician.page');
        Route::get('/transfer_labour_payment', [LabourPaymentController::class, 'transferLabourPayment']);
        Route::get('/post_tlabour_payment', [LabourPaymentController::class, 'postTransferLPayment']);
        Route::get('/delete_tlabour_payment', [LabourPaymentController::class, 'trLabourPaymentDelete']);

        //travel expense
        Route::get('/travel_expense', [LabourPaymentController::class, 'travelExpense'])->name('travel_expense.page');
        Route::POST('/post_travel_expense', [LabourPaymentController::class, 'postTravelExpense']);
        Route::get('/get_travel_expenses', [LabourPaymentController::class, 'getTravelExpense']);
        Route::get('/delete_travel_expense', [LabourPaymentController::class, 'deleteTravelExpense']);
        Route::get('/update_travel_expense', [LabourPaymentController::class, 'updateTravelExpenses']);
        
    // ****** End Labour **********

       

});    