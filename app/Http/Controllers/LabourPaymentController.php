<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;
use Carbon\Carbon;
use App\Models\UserModel;
use App\Models\SOModel;
use App\Models\LabourPaymentModel;
use App\Models\TransferPaymentModel;
use App\Models\TechnicianExpenseModel;
use App\Models\TravelExpenseModel;
use Session;
use Hash;
use File;
use DB;

class LabourPaymentController extends Controller
{
    public function labourPaymentList()
    {
    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
    	$s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

    	return view('labour.labourPaymentList',compact('u_obj','s_obj'));

    }

    public function incomeList()
    {
    	$a_id=Session::get('USER_ID');

        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
        $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
        $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
        $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        // Avians account Payment
        $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
        // fot - from other technician
        $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
        $total_wallet = $accountant_payment + $fot;
        
        //Technician Expense
        $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
        //transfer to other technician
        $ttot = TransferPaymentModel::where(['delete'=>0,'a_id'=>$a_id])->sum('amount');
        // dd($ttot);
        $total_expense = $technician_expenses + $ttot;

        //Cleared Payment
        $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');

        //uncleared Payment
        $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');

        $balance = $total_wallet - ($ttot + $cleared_pay);

        // dd();
        //get so number payment wise
        foreach($l_obj as $l){

            $so_id = array_map('intval', explode(',', $l->so_id));
            foreach($so_id as $s){
                $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
            }
            $l->s_obj = $so_obj;
        }

        return view('labour.labourDashboard',compact('u_obj','l_obj','s_obj','data','accountant_payment','fot','total_wallet','technician_expenses','ttot','total_expense','cleared_pay','uncleared_pay','balance'));

    }

    public function getAccPayment(Request $req)
    {
        $a_id=Session::get('USER_ID');

        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {

            $a_id=Session::get('USER_ID');

            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();
    
            // Avians account Payment
            $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
            // fot - from other technician
            $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
            $total_wallet = $accountant_payment + $fot;
            
            //Technician Expense
            $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
            //transfer to other technician
            $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
            $total_expense = $technician_expenses + $ttot;
    
            //Cleared Payment
            $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');
    
            //uncleared Payment
            $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');
    
            $balance = $total_wallet - ($ttot + $cleared_pay);
    
            // dd();
            //get so number payment wise
            foreach($l_obj as $l){
    
                $so_id = array_map('intval', explode(',', $l->so_id));
                foreach($so_id as $s){
                    $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
                }
                $l->s_obj = $so_obj;
            }

            if(!empty($l_obj)){
                return json_encode(array('status' => true ,'data' => $l_obj,'s_obj' => $s_obj ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $a_id=Session::get('USER_ID');

            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();
    
            // Avians account Payment
            $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
            // fot - from other technician
            $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
            $total_wallet = $accountant_payment + $fot;
            
            //Technician Expense
            $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
            //transfer to other technician
            $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
            $total_expense = $technician_expenses + $ttot;
    
            //Cleared Payment
            $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');
    
            //uncleared Payment
            $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');
    
            $balance = $total_wallet - ($ttot + $cleared_pay);
    
            // dd();
            //get so number payment wise
            foreach($l_obj as $l){
    
                $so_id = array_map('intval', explode(',', $l->so_id));
                foreach($so_id as $s){
                    $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
                }
                $l->s_obj = $so_obj;
            }

            if(!empty($l_obj)){
                return json_encode(array('status' => true ,'data' => $l_obj,'s_obj' => $s_obj ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }
        }
    }

    public function getOtTechPayment(Request $req)
    {
        $a_id=Session::get('USER_ID');

        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {

            $a_id=Session::get('USER_ID');

            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();
            foreach($data as $d){
    
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->name = $u->name;
                }
                
            }

            // Avians account Payment
            $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
            // fot - from other technician
            $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
            $total_wallet = $accountant_payment + $fot;
            
            //Technician Expense
            $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
            //transfer to other technician
            $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
            $total_expense = $technician_expenses + $ttot;
    
            //Cleared Payment
            $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');
    
            //uncleared Payment
            $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');
    
            $balance = $total_wallet - ($ttot + $cleared_pay);
    
            // dd();
            //get so number payment wise
            foreach($l_obj as $l){
    
                $so_id = array_map('intval', explode(',', $l->so_id));
                foreach($so_id as $s){
                    $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
                }
                $l->s_obj = $so_obj;
            }

            if(!empty($l_obj)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $a_id=Session::get('USER_ID');

            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();
    
            // Avians account Payment
            $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
            // fot - from other technician
            $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
            $total_wallet = $accountant_payment + $fot;
            
            //Technician Expense
            $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
            //transfer to other technician
            $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
            $total_expense = $technician_expenses + $ttot;
    
            //Cleared Payment
            $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');
    
            //uncleared Payment
            $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');
    
            $balance = $total_wallet - ($ttot + $cleared_pay);
    
            // dd();
            //get so number payment wise
            foreach($l_obj as $l){
    
                $so_id = array_map('intval', explode(',', $l->so_id));
                foreach($so_id as $s){
                    $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
                }
                $l->s_obj = $so_obj;
            }

            if(!empty($l_obj)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }
        }
    }

    public function expenseList()
    {
    	$a_id=Session::get('USER_ID');

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
        $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
        $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
        $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        // Avians account Payment
        $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
        // fot - from other technician
        $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
        $total_wallet = $accountant_payment + $fot;
        
        //Technician Expense
        $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
        //transfer to other technician
        $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
        $total_expense = $technician_expenses + $ttot;

        //Cleared Payment
        $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');

        //uncleared Payment
        $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');

        $balance = $total_wallet - ($ttot + $cleared_pay);

        // dd();
        //get so number payment wise
        foreach($l_obj as $l){

            // $p= array(); //get SO ID
            // array_push($p, $ds->so_id);

            $so_id = array_map('intval', explode(',', $l->so_id));
            foreach($so_id as $s){
                $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
            }
            $l->s_obj = $so_obj;
        }

        return view('labour.expense',compact('u_obj','l_obj','s_obj','data','accountant_payment','fot','total_wallet','technician_expenses','ttot','total_expense','cleared_pay','uncleared_pay','balance'));

    }

    public function transferOtherTechnicianList()
    {
    	$a_id=Session::get('USER_ID');

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
        $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
        $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
        $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        // Avians account Payment
        $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
        // fot - from other technician
        $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
        $total_wallet = $accountant_payment + $fot;
        
        //Technician Expense
        $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
        //transfer to other technician
        $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
        $total_expense = $technician_expenses + $ttot;

        //Cleared Payment
        $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');

        //uncleared Payment
        $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');

        $balance = $total_wallet - ($ttot + $cleared_pay);

        // dd();
        //get so number payment wise
        foreach($l_obj as $l){

            // $p= array(); //get SO ID
            // array_push($p, $ds->so_id);

            $so_id = array_map('intval', explode(',', $l->so_id));
            foreach($so_id as $s){
                $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
            }
            $l->s_obj = $so_obj;
        }

        return view('labour.transferTechnician',compact('u_obj','l_obj','s_obj','data','accountant_payment','fot','total_wallet','technician_expenses','ttot','total_expense','cleared_pay','uncleared_pay','balance'));

    }

    public function postLabourPayment(Request $req)
    {
    	$edit_id=empty($req->get('edit_id')) ? null : $req->get('edit_id');
        $pay_desc = $req->get('pay_desc');
   		$payment_date = $req->get('payment_date');
   		$payment_amnt = $req->get('payment_amnt');
        $labour = $req->get('labour');

        $so = $req->get('so');
        // $so=implode(',',$sos);

    	$a_id=Session::get('USER_ID');

        if($edit_id!=null)
    	{
            if ($payment_amnt !='' && $payment_date !='') 
            {
                $u_obj=LabourPaymentModel::find($edit_id);
                $u_obj->u_id=$labour;
                $u_obj->so_id=$so;
                $u_obj->p_desc=$pay_desc;
                $u_obj->payment_date=$payment_date;
                $u_obj->payment_amnt=$payment_amnt;
                $u_obj->created_by=$a_id;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                
                if($res){
                    return ['status' => true, 'message' => 'Payment Update Successfully'];
                }else{
                    return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   

        }else{       

            if ($payment_amnt !='') 
            {
                $u_obj=new LabourPaymentModel();
                $u_obj->u_id=$labour;
                $u_obj->so_id=$so;
                $u_obj->p_desc=$pay_desc;
                $u_obj->payment_date=$payment_date;
                $u_obj->payment_amnt=$payment_amnt;
                $u_obj->created_by=$a_id;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->save();

                $pers_email="omkar9497@gmail.com";
                $name="test";
                $whatsapp=8551071325;
                $pass="omkar";
                $image=public_path('assets/images/logo-dark.png');
    
                $mail = new PHPMailer(true);
               
                //Server Options  
                $mail->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true)); 
                //Server settings                                 
                // Enable verbose debug output
                $mail->isSMTP();                                      
                // Set mailer to use SMTP
                $mail->Host = config('constants.MAIL_HOST');  
                // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               
                // Enable SMTP authentication
                $mail->Username = config('constants.MAIL_USERNAME');                 
                // SMTP username
                $mail->Password = config('constants.MAIL_PASSWORD');                           
                // SMTP password
                $mail->SMTPSecure = 'tls';                            
                // Enable TLS encryption, `ssl` also accepted
                $mail->Port = config('constants.MAIL_PORT');                                    
                // TCP port to connect to
    
                //Recipients
                $mail->setFrom(config('constants.MAIL_FROM'), "".config('constants.AUTHOR_NAME')." Team");
                $mail->addAddress($pers_email,$name);     
                $mail->addReplyTo(config('constants.MAIL_REPLY'), 'Money Vision');
    
                //Content
                $mail->CharSet = "utf-8";       
                // set charset to utf8
                $mail->isHTML(true); 
                $mail->AddEmbeddedImage($image, 'logo_2u');              
                // Set email format to HTML
                $mail->Subject = config('constants.AUTHOR_NAME')." - User Account Created..";
                $body="
                <div style='background-color:rgb(255,255,255);margin:0;font:12px/16px Arial,sans-serif'>"
                   ." <table style='width: 640px;color: rgb(51,51,51);margin: 0 auto;border-collapse: collapse;'>"
                       ." <tbody>"
                           ." <tr>"
                               ." <td style='padding:0 20px 20px 20px;vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                   ." <table style='width:100%;border-collapse:collapse'>"
                                       ." <tbody>"
                                           ." <tr>"
                                               ." <td style='vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                   ." <table style='width:100%;border-collapse:collapse'>"
                                                       ." <tbody>"
                                                           ." <tr>"
                                                               ." <td rowspan='2' style='width:115px;padding:18px 0 0 0;vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                                   ." <img alt='Money Vision' src='cid:logo_2u' style='border:0;width:115px' class='CToWUd'>" 
                                                               ." </td>
                                                                <td style='text-align:right;padding:5px 0;border-bottom:1px solid rgb(204,204,204);white-space:nowrap;vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                               ." </td>"
                                                               ."<td style='text-align:right;padding:5px 0;border-bottom:1px solid rgb(204,204,204);white-space:nowrap;vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'></td></tr><tr><td colspan='3' style='text-align:right;padding:7px 0 5px 0;vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'></td>"
                                                           ." </tr>"
                                                       ." </tbody>"
                                                   ." </table>"
                                               ." </td>"
                                           ." </tr>"
                                           ." <tr>"
                                               ." <td style='vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                   ." <table style='width:100%;border-collapse:collapse'>"
                                                       ." <tbody>"
                                                           ." <tr>"
                                                               ." <td style='vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'> "
                                                                   ." <h3 style='font-size:15px;color:rgb(204,102,0);margin:15px 0 0 0;font-weight:normal'>"
                                                                       ." <b> Hello ".$name.", </b>"
                                                                   ." </h3> "
                                                                   ." <p style='margin:5px 0 0 0;font:12px/16px Arial,sans-serif'> Here are your login details. Please update password as per your need after login to the system."
                                                                   ." </p> "
                                                               ." </td>"
                                                           ." </tr>"
                                                           ." <tr>"
                                                               ." <td style='vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                               ." </td>"
                                                           ." </tr>"
                                                       ." </tbody>"
                                                   ." </table>"
                                               ." </td>"
                                           ." </tr>"
                                           ." <tr>"
                                               ." <td style='vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                   ." <table style='width:100%;border-collapse:collapse'>"
                                                    ."</table>"
                                               ." </td>"
                                           ." </tr>"
                                           ." <tr>"
                                               ." <td style='vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                    ."<table style='width:100%;border-top:3px solid rgb(45,55,65);border-collapse:collapse'>"
                                                       ." <tbody>"
                                                           ." <tr style='background-color:rgb(239,239,239)'>"
                                                               ." <td style='font-size:14px;padding:11px 18px 18px 18px;width:50%;vertical-align:top;line-height:16px;font-family:Arial,sans-serif'> "
                                                                   ." <p style='margin:2px 0 9px 0;font:12px/16px Arial,sans-serif'> "
                                                                       ." <span style='color:rgb(102,102,102)'>Login Details:</span>"
                                                                       ." <br/><br/>"
                                                                       ." <strong>Login Link:  </strong> <a href='".config('constants.LOGIN_LINK')."'>Click Here.</a>"
                                                                       ." <br> "
                                                                       ." <strong>Mobile:  ".$whatsapp."</strong>"
                                                                       ." <br>"
                                                                       ." <strong>Password:".$pass."</strong>"
                                                                       ." <br>"
                                                                   ." </p>"
                                                               ." </td>"
                                                           ." </tr>"
                                                       ." </tbody>"
                                                   ." </table>"
                                               ." </td>"
                                           ." </tr>"
                                           ." <tr>"
                                               ." <td style='padding:0;vertical-align:top;font-size:12px;line-height:16px;font-family:Arial,sans-serif'>"
                                                   ." <p style='font-size:11px;color:rgb(102,102,102);line-height:16px;margin:0 0 10px 0;font:11px'>This email was sent from a notification-only address that cannot accept incoming email. Please do not reply to this message. "
                                                   ." </p>"
                                                   ." <p style='font-size:11px;color:rgb(102,102,102);line-height:16px;margin:0 0 10px 0;font:11px'>"
                                                       ." <div>"
                                                           ." <b>".config('constants.PROJECT_NAME')." Team.</b>"
                                                           ." <br/>&nbsp;&nbsp;"
                                                           ." <b>"
                                                           ." </b>"
                                                       ." </div>"
                                                       ." <div style='background-color:#ebeef2;color:black;text-align:center;font-size:12px;height:20px;padding-top: 7px;padding-botton: 7px;'>© ".date('Y')." ".config('constants.AUTHOR_URL')."
                                                        </div>"
                                                   ." </p>"
                                               ." </td>"
                                           ." </tr>"
                                      ."  </tbody>"
                                   ." </table>"
                               ." </td>"
                           ." </tr>"
                       ." </tbody>"
                   ." </table>"."
                </div>";  
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                $mail->MsgHTML($body);

                $mail->send();
                
                if($mail){
                    return ['status' => true,'mail'=>$mail, 'message' => 'Payment add Successfully'];
                }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            } 
        }     
        

    }

    public function getLabourPayment(Request $req)
    {
    	$role=Session::get('ROLES');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');

        if ($from_date == null && $to_date == null && $labours == null) 
        {

            $data = LabourPaymentModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
                $d->labour_name = $u_obj[0]->name;
            }

            // User Data
            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
            //SO data
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'s_obj' => $s_obj ,'role'=>$role ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data = LabourPaymentModel::where(['delete'=>0,'u_id'=>$labours])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
                $d->labour_name = $u_obj[0]->name;
            }

            // User Data
            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
            //SO data
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'s_obj' => $s_obj,'role'=>$role ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }
    }

    public function editLabourPayment(Request $req)
    {
        $edit_id = $req->get('edit_id');
        $pay_desc = $req->get('pay_desc');
   		$payment_date = $req->get('payment_date');
   		$payment_amnt = $req->get('payment_amnt');

        $labour = $req->get('labour');
        // $labour=implode(',',$labours);

        $sos = $req->get('so');
        $so=implode(',',$sos);

    	$a_id=Session::get('USER_ID');
        // return ['status' => true, 'message' => "$so_number,$client_name,$project_name,$address,$cp_name,$cp_ph_no,$labour"];
    	if ($payment_amnt !='' && $payment_date !='') 
        {
            $u_obj=LabourPaymentModel::find($edit_id);
    		$u_obj->u_id=$labour;
    		$u_obj->so_id=$so;
    		$u_obj->p_desc=$pay_desc;
    		$u_obj->payment_date=$payment_date;
    		$u_obj->payment_amnt=$payment_amnt;
    		$u_obj->created_by=$a_id;
    		$u_obj->delete=0;
    		$u_obj->a_id=$a_id;
    		$res=$u_obj->update();
       		
            if($res){
                return ['status' => true, 'message' => 'Payment Update Successfully'];
            }else{
               return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            }
        }else{
            return ['status' => false, 'message' => 'Please Try Again..']; 
        }   

    }

    public function LabourPaymentDelete(Request $req)
    {
        $id=$req->get('id');
        $u_obj=LabourPaymentModel::find($id);
        $u_obj->delete=1;
        $res=$u_obj->update();
        
        if($res){
            return ['status' => true, 'message' => 'Labour Payment Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'Labour Payment Unsuccessfull...!'];
        }
    }

    //transfer labour payment
    public function transferLabourPayment(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {
            $data = TransferPaymentModel::where(['delete'=>0,'a_id'=>$a_id])->orderby('updated_at','DESC')->get();
            
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
                $d->labour_name = $u_obj[0]->name;
            }

            // User Data
            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
            //SO data
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'s_obj' => $s_obj ,'message' => 'Data Found'));
            }else{
            return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data = TransferPaymentModel::where(['delete'=>0,'a_id'=>$a_id])->whereDate('p_date', '>=' ,$from_date)->whereDate('p_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
                $d->labour_name = $u_obj[0]->name;
            }

            // User Data
            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
            //SO data
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'s_obj' => $s_obj,'fdate' =>$from_date ,'message' => 'Data Found'));
            }else{
            return ['status' => false, 'message' => 'No Data Found'];
            }
        }       

    }

    public function postTransferLPayment(Request $req)
    {
    	$edit_id=empty($req->get('edit_id')) ? null : $req->get('edit_id');
        $pay_desc = $req->get('pay_desc');
   		$payment_date = $req->get('payment_date');
   		$payment_amnt = $req->get('payment_amnt');
        $labour = $req->get('labour');

        $sos = $req->get('so');
        $so=implode(',',$sos);

    	$a_id=Session::get('USER_ID');

        if($edit_id!=null)
    	{
            if ($payment_amnt !='' && $payment_date !='') 
            {
                $u_obj=TransferPaymentModel::find($edit_id);
                $u_obj->u_id=$labour;
                $u_obj->so_id=$so;
                $u_obj->p_desc=$pay_desc;
                $u_obj->p_date=$payment_date;
                $u_obj->amount=$payment_amnt;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                
                if($res){
                    return ['status' => true, 'message' => 'Payment Update Successfully'];
                }else{
                    return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   

        }else{       

            if ($payment_amnt !='') 
            {
                $u_obj=new TransferPaymentModel();
                $u_obj->u_id=$labour;
                $u_obj->so_id=$so;
                $u_obj->p_desc=$pay_desc;
                $u_obj->p_date=$payment_date;
                $u_obj->amount=$payment_amnt;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->save();
                
                if($res){
                    return ['status' => true, 'message' => 'Payment add Successfully'];
                }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            } 
        }     
        

    }

    public function trLabourPaymentDelete(Request $req)
    {
        $id=$req->get('id');
        $u_obj=TransferPaymentModel::find($id);
        $u_obj->delete=1;
        $res=$u_obj->update();
        
        if($res){
            return ['status' => true, 'message' => 'Labour Payment Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'Labour Payment Unsuccessfull...!'];
        }
    }

    public function postExpenseLPayment(Request $req)
    {
        $edit_id=$req->get('exp_edit_id');
        $exp_desc = $req->get('exp_desc');
   		$exp_date = $req->get('exp_date');
   		$expense_amnt = $req->get('expense_amnt');
        $exp_type = $req->get('exp_type');
        $attachment = $req->get('attachment');
        
        $so = $req->get('exp_so');
        // $so=implode(',',$sos);

    	$a_id=Session::get('USER_ID');


        // For File Decoder 
        if($attachment!='') 
        {

            // $check = PaymentModel::where('p_id',$project_id)->exists();
            // $destinationPath = 'public/files/project-payment/'.$project_id.'/';

            // check folder exits or not
            // if ($check == false) {
            //     $result = File::makeDirectory($destinationPath, 0775, true, true); 
            // }

            // $destinationPath=public_path('/files/loan-receipt/');   //Folder Path
            $image1 = $req->input('attachment');     // encoded File name
            $extension=$req->input('payment_extension');       //File Extension  
            
            $pattern='/^data:.+;base64,/';

            $img = preg_replace($pattern, '', $image1);  //removed $pattern
            $img = str_replace(' ', '+', $img);  //for + sign blank space convert
            $data = base64_decode($img);       //decode All File

            $image_id= uniqid();    // create random name,number
            $file = $image_id . '.'.$extension; // create name for file
            $fp  = $image_id.'.'.$extension;   // send the file to destination path

            file_put_contents(public_path('files/user/expense/').$file, $data); 
        }

        if($edit_id!=null)
    	{
            
            if ($expense_amnt !='' && $exp_date !='') 
            {
                    

                $u_obj=TechnicianExpenseModel::find($edit_id);
                $u_obj->exp_type=$exp_type;
                $u_obj->so_id=$so;
                $u_obj->exp_desc=$exp_desc;
                $u_obj->exp_date=$exp_date;
                $u_obj->amount=$expense_amnt;
                $u_obj->attachment=$fp;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                
                if($res){
                    return ['status' => true, 'message' => 'Payment Update Successfully'];
                }else{
                    return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   

        }else{       

            if ($expense_amnt !='') 
            {
                $u_obj=new TechnicianExpenseModel();
                $u_obj->exp_type=$exp_type;
                $u_obj->so_id=$so;
                $u_obj->exp_desc=$exp_desc;
                $u_obj->exp_date=$exp_date;
                $u_obj->amount=$expense_amnt;
                $u_obj->attachment=$fp;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;

                

                $res=$u_obj->save();
                
                if($res){
                    return ['status' => true, 'message' => 'Payment add Successfully'];
                }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            } 
        }     
        

    }

    public function getLabourExpense(Request $req)
    {
        $a_id=Session::get('USER_ID');

        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {

            $date = Carbon::now()->subDays(60);  // get last 7 days record
            $data = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->where('created_at', '>=', $date)->orderby('updated_at','DESC')->get();

            //SO data
            // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            $s_obj=SOModel::where('labour', 'LIKE', '%'.$a_id.'%')->where(['delete'=>0,])->orderby('created_at','DESC')->get();
            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj ,'a_id' => $a_id ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->whereDate('exp_date', '>=' ,$from_date)->whereDate('exp_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            
            
            //SO data
            // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            $s_obj=SOModel::where('labour', 'LIKE', '%'.$a_id.'%')->where(['delete'=>0,])->orderby('created_at','DESC')->get();
            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj ,'a_id' => $a_id ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }
        }
    }

    public function deleteExpense(Request $req)
    {
        $id=$req->get('id');
        $u_obj=TechnicianExpenseModel::find($id);
        $u_obj->delete=1;
        $res=$u_obj->update();
        
        if($res){
            return ['status' => true, 'message' => 'Expense Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'Expense Delete Unsuccessfull...!'];
        }
    }

    public function managelabourPayment()
    {
    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
    	$s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
    	return view('labour.managelabourPayment',compact('u_obj','s_obj'));

    }

    public function getAllExpense(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $role=Session::get('ROLES');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');

        if ($from_date == null && $to_date == null && $labours == null) 
        {
            $data = TechnicianExpenseModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->labour_name = $u->name;
                    $d->emp_number = $u->emp_number;

                }
            }

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'role'=> $role,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$labours])->whereDate('exp_date', '>=' ,$from_date)->whereDate('exp_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->labour_name = $u->name;
                    $d->emp_number = $u->emp_number;

                }
            }

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'role'=> $role,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }
        }
    }

    
    public function postExpense(Request $req)
    {
        $role=Session::get('ROLES');
        $edit_id=$req->get('exp_edit_id');
        $updated_amnt = $req->get('updated_amnt');
   		$status = $req->get('status');
   		$acc_remark = $req->get('acc_remark');

        $sa_remark = $req->get('sa_remark');
        $sa_updated_amnt = $req->get('sa_updated_amnt');

    	$a_id=Session::get('USER_ID');

        if($edit_id!=null)
    	{
            
            $u_obj=TechnicianExpenseModel::find($edit_id);

            if($role == 0){
                $u_obj->sa_remark=$sa_remark;
                $u_obj->status=$status;
    
                if ($sa_updated_amnt >'0' && $status != '') 
                {
                    $u_obj->amount=$sa_updated_amnt;
                }
    
                $u_obj->delete=0;
                $u_obj->sa_id=$a_id;
                $res=$u_obj->update();
            }else{
                $u_obj->acc_remark=$acc_remark;
                $u_obj->status=$status;
    
                if ($updated_amnt >'0' && $status != 'Cancelled') 
                {
                    $u_obj->amount=$updated_amnt;
                }
    
                $u_obj->delete=0;
                $u_obj->acc_id=$a_id;
                $res=$u_obj->update();
            }
           
            
            if($res){
                return ['status' => true, 'message' => 'Expense Update Successfully'];
            }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            }
            

        }else{
            return ['status' => false, 'message' => 'Data Not Found Please Try Again..']; 
        }  
        

    }

    public function travelExpense()
    {
    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
    	$s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
    	return view('labour.travelExpense',compact('u_obj','s_obj'));

    }

    public function postTravelExpense(Request $req)
    {
        $edit_id=$req->get('exp_edit_id');
        $mode_travel = $req->get('mode_travel');
   		$from_location = $req->get('from_location');
   		$to_location = $req->get('to_location');
        $total_km = $req->get('total_km');
        $attachment = $req->get('attachment');
        $travel_date = $req->get('travel_date');
        $travel_desc = $req->get('travel_desc');
        $travel_amount = $req->get('travel_amnt');

    	$a_id=Session::get('USER_ID');

        // For File Decoder 
        if($attachment!='') 
        {

            // $check = PaymentModel::where('p_id',$project_id)->exists();
            // $destinationPath = 'public/files/project-payment/'.$project_id.'/';

            // check folder exits or not
            // if ($check == false) {
            //     $result = File::makeDirectory($destinationPath, 0775, true, true); 
            // }

            // $destinationPath=public_path('/files/loan-receipt/');   //Folder Path
            $image1 = $req->input('attachment');     // encoded File name
            $extension=$req->input('payment_extension');       //File Extension  
            
            $pattern='/^data:.+;base64,/';

            $img = preg_replace($pattern, '', $image1);  //removed $pattern
            $img = str_replace(' ', '+', $img);  //for + sign blank space convert
            $data = base64_decode($img);       //decode All File

            $image_id= uniqid();    // create random name,number
            $file = $image_id . '.'.$extension; // create name for file
            $fp  = $image_id.'.'.$extension;   // send the file to destination path

            file_put_contents(public_path('files/user/travel_expense/').$file, $data); 
        }

        if($edit_id!=null)
    	{
            
            if ($travel_amount !='' && $travel_date !='') 
            {
                $u_obj=TravelExpenseModel::find($edit_id);
                $u_obj->mode_travel=$mode_travel;
                $u_obj->from_location=$from_location;
                $u_obj->to_location=$to_location;
                $u_obj->total_km=$total_km;
                $u_obj->travel_date=$travel_date;
                $u_obj->travel_desc=$travel_desc;
                $u_obj->travel_amount=$travel_amount;
                if($attachment!='') 
                {
                     $u_obj->attachment=$fp;
                }
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                
                if($res){
                    return ['status' => true, 'message' => 'Travel Expense Successfully'];
                }else{
                    return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   

        }else{       

            if ($travel_amount !='' && $mode_travel !='' && $from_location !='' && $to_location !='') 
            {
                $u_obj=new TravelExpenseModel();
                $u_obj->mode_travel=$mode_travel;
                $u_obj->from_location=$from_location;
                $u_obj->to_location=$to_location;
                $u_obj->total_km=$total_km;
                $u_obj->travel_date=$travel_date;
                $u_obj->travel_desc=$travel_desc;
                $u_obj->travel_amount=$travel_amount;
                $u_obj->attachment=$fp;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->save();
                
                if($res){
                    return ['status' => true, 'message' => 'Travel Expense add Successfully'];
                }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            } 
        }     
        
    }

    public function getTravelExpense(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $role=Session::get('ROLES');

        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');
        if ($from_date == null && $to_date == null) 
        {
            if($role == 3){
                $date = Carbon::now()->subDays(60);  // get last 7 days record
                $data = TravelExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->where('created_at', '>=', $date)->orderby('updated_at','DESC')->get();
                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->labour_name = $u->name;
                        $d->emp_number = $u->emp_number;
    
                    }
                }
            }else{
                $date = Carbon::now()->subDays(60);  // get last 7 days record
                $data = TravelExpenseModel::where(['delete'=>0])->where('created_at', '>=', $date)->orderby('updated_at','DESC')->get();
                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->labour_name = $u->name;
                        $d->emp_number = $u->emp_number;
    
                    }
                }
            }

          

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'role'=>$role,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            if($role == 3){
                $data = TravelExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->whereDate('travel_date', '>=' ,$from_date)->whereDate('travel_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->labour_name = $u->name;
                        $d->emp_number = $u->emp_number;
    
                    }
                }
            }else{
                $data = TravelExpenseModel::where(['delete'=>0,'a_id'=>$labours])->whereDate('travel_date', '>=' ,$from_date)->whereDate('travel_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->labour_name = $u->name;
                        $d->emp_number = $u->emp_number;
    
                    }
                }
            }
           
            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'role'=>$role,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }
        }
    }

    public function deleteTravelExpense(Request $req)
    {
        $id=$req->get('id');
        $u_obj=TravelExpenseModel::find($id);
        $u_obj->delete=1;
        $res=$u_obj->update();
        
        if($res){
            return ['status' => true, 'message' => 'Expense Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'Expense Delete Unsuccessfull...!'];
        }
    }

    public function updateTravelExpenses(Request $req)
    {
        $role=Session::get('ROLES');
        $edit_id=$req->get('exp_edit_id');
        $updated_amnt = $req->get('updated_amnt');
   		$status = $req->get('status');
   		$acc_remark = $req->get('acc_remark');

        $sa_remark = $req->get('sa_remark');
        $sa_updated_amnt = $req->get('sa_updated_amnt');

    	$a_id=Session::get('USER_ID');

        if($edit_id!=null)
    	{
            
            $u_obj=TravelExpenseModel::find($edit_id);

            if($role == 0){
                $u_obj->sa_remark=$sa_remark;
                $u_obj->status=$status;
    
                if ($sa_updated_amnt >'0' && $status != '') 
                {
                    $u_obj->travel_amount=$sa_updated_amnt;
                }
    
                $u_obj->delete=0;
                $u_obj->sa_id=$a_id;
                $res=$u_obj->update();
            }else{
                $u_obj->ad_remark=$acc_remark;
                $u_obj->status=$status;
    
                if ($updated_amnt >'0' && $status != 'Cancelled') 
                {
                    $u_obj->travel_amount=$updated_amnt;
                }
    
                $u_obj->delete=0;
                $u_obj->ad_id=$a_id;
                $res=$u_obj->update();
            }
           
            
            if($res){
                return ['status' => true, 'message' => 'Expense Update Successfully'];
            }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            }
            

        }else{
            return ['status' => false, 'message' => 'Data Not Found Please Try Again..']; 
        }  
        

    }
}
