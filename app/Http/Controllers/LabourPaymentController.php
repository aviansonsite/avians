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

        $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
        $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
        $s_obj=SOModel::where(['delete'=>0,'lead_technician'=>$a_id])->orderby('created_at','DESC')->get();
        $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        $s_obj1=DB::table('oa_tl_history as oth')
        ->leftjoin('users as u','u.id','oth.lead_technician')
        ->leftjoin('sales_orders as so','so.id','oth.so_id')
        ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
        ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
        ->orderby('oth.updated_at','DESC')
        ->get();
        
        // Avians account Payment
        $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
        // fot - from other technician
        $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
        $total_wallet = $accountant_payment + $fot;
        
        //Technician Expense
        $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');

        //Travel Expense
        $travel_expense = TravelExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('travel_amount');

        $total_tech_expense = $technician_expenses + $travel_expense;

        //transfer to other technician
        $ttot = TransferPaymentModel::where(['delete'=>0,'a_id'=>$a_id])->sum('amount');
        // dd($ttot);
        $total_expense = $technician_expenses + $travel_expense + $ttot;


        //Cleared Payment
        $aprvd_technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('aprvd_amount');
    
        //Cleared Payment
        $apprvd_travel_expense = TravelExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('aprvd_amount');
           
        $cleared_pay = $aprvd_technician_expenses +  $apprvd_travel_expense;

        //uncleared Payment
        $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->where('status', '!=','Approved')->sum('amount');

        $balance = $total_wallet - $total_expense;

        // dd();
        //get so number payment wise
        foreach($l_obj as $l){

            $so_id = array_map('intval', explode(',', $l->so_id));
            foreach($so_id as $s){
                $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
            }
            $l->s_obj = $so_obj;
        }

        return view('labour.labourDashboard',compact('u_obj','l_obj','s_obj','s_obj1','data','accountant_payment','fot','total_wallet','technician_expenses','ttot','total_expense','cleared_pay','us_obj','uncleared_pay','balance','total_tech_expense'));

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
            // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();
            
            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();
            
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
            $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('aprvd_amount');
            //uncleared Payment
            $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('aprvd_amount');
    
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
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();

            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->whereDate('p_date', '>=' ,$from_date)->whereDate('p_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            foreach($data as $d){
    
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->name = $u->name;
                }
                
            }

    
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
        $s_obj=SOModel::where(['delete'=>0,'lead_technician'=>$a_id])->orderby('created_at','DESC')->get();
        $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
        $s_obj1=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
                ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('oth.updated_at','DESC')
                ->get();

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

            $so_id = array_map('intval', explode(',', $l->so_id));      // create array
            foreach($so_id as $s){
                $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
            }
            $l->s_obj = $so_obj;
        }

        return view('labour.expense',compact('u_obj','l_obj','s_obj','us_obj','s_obj1','data','accountant_payment','fot','total_wallet','technician_expenses','ttot','total_expense','cleared_pay','uncleared_pay','balance'));

    }

    public function transferOtherTechnicianList()
    {
    	$a_id=Session::get('USER_ID');

    	$u_obj1=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();

        $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj1[0]->id])->orderby('updated_at','DESC')->get();

        // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
        $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();
        // dd()

        $u_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.id as u_id','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

        $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
        $s_obj1=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
                ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('oth.updated_at','DESC')
                ->get();

        $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        // // Avians account Payment
        // $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('payment_amnt');
        // // fot - from other technician
        // $fot = TransferPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->sum('amount');
        // $total_wallet = $accountant_payment + $fot;
        
        // //Technician Expense
        // $technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id])->sum('amount');
        // //transfer to other technician
        // $ttot = TransferPaymentModel::where(['delete'=>0])->where('u_id', '!=', $u_obj[0]->id)->sum('amount');
        // $total_expense = $technician_expenses + $ttot;

        // //Cleared Payment
        // $cleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'Approved'])->sum('amount');

        // //uncleared Payment
        // $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$u_obj[0]->id,'status'=>'uncleared'])->sum('amount');

        // $balance = $total_wallet - ($ttot + $cleared_pay);

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

        return view('labour.transferTechnician',compact('u_obj','l_obj','s_obj','data','s_obj1','us_obj'));

    }

    public function postLabourPayment(Request $req)
    {
    	$edit_id=empty($req->get('edit_id')) ? null : $req->get('edit_id');
        $so_id = $req->get('so');
        $pay_desc = $req->get('pay_desc');
   		$payment_date = $req->get('payment_date');
   		$payment_amnt = $req->get('payment_amnt');
        $labour = $req->get('labour');
        $oth_id = $req->get('oth_id');
    	$a_id=Session::get('USER_ID');

        if($edit_id!=null)
    	{
            if ($payment_amnt !='' && $payment_date !='' && $labour !='' && $so_id !='') 
            {
                $u_obj=LabourPaymentModel::find($edit_id);
                $u_obj->u_id=$labour;
                $u_obj->so_id=$so_id;
                $u_obj->oth_id=$oth_id;
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

            if ($payment_amnt !='' && $payment_date !='' && $labour !='' && $so_id !='') 
            {
                $u_obj=new LabourPaymentModel();
                $u_obj->u_id=$labour;
                $u_obj->so_id=$so_id;
                $u_obj->oth_id=$oth_id;
                $u_obj->p_desc=$pay_desc;
                $u_obj->payment_date=$payment_date;
                $u_obj->payment_amnt=$payment_amnt;
                $u_obj->created_by=$a_id;
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

    public function getLabourPayment(Request $req)
    {
    	$role=Session::get('ROLES');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');

        if ($from_date == null && $to_date == null && $labours == null) 
        {

            // $data = LabourPaymentModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();
            // foreach($data as $d){
            //     $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();

            //     foreach($u_obj as $u){
            //         $d->labour_name = $u->name;
            //     }
            // }


            $data=DB::table('labour_payments as lp')
                ->leftjoin('users as u','u.id','lp.u_id')
                ->leftjoin('sales_orders as so','so.id','lp.so_id')
                ->select('lp.id','lp.oth_id','lp.so_id','lp.p_desc','lp.payment_date','lp.payment_amnt','lp.created_by','lp.delete','lp.updated_at','lp.created_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name as labour_name','u.delete as u_delete','u.is_active','u.role')
                ->where(['u.delete'=>0,'u.is_active'=>0,'u.role'=>3,'so.delete'=>0,'lp.delete'=>0])
                ->orderby('lp.updated_at','DESC')
                ->get();

                foreach($data as $d){           //for 24 hrs , time duration calculate
                    // $startTime=$d->created_at;
                    // $nowTime = Carbon::now();       //get current time
                    // $currentTime = $nowTime->toDateTimeString();    //get format "2023-07-03 08:40:11"
                    // // $totalDuration = $finishTime->diffInMinutes($startTime);
                    // $totalDuration = $startTime->diff($currentTime)->format('%H:%I:%S');    
                    // $d->totalDuration=$totalDuration;

                    $now = Carbon::now();
                    $created_at = Carbon::parse($d->created_at);
                    $diffHuman = $created_at->diffForHumans($now);  // 3 Months ago
                    $diffHours = $created_at->diffInHours($now);  // 3 
                    $diffMinutes = $created_at->diffInMinutes($now);   // 180
                    $d->diffHuman=$diffHuman;
                    $d->diffHours=$diffHours;
                    $d->diffMinutes=$diffMinutes;
                }   

            // User Data
            // $u_obj=DB::table('oa_tl_history as oth')
            // ->leftjoin('users as u','u.id','oth.lead_technician')
            $u_obj=DB::table('users as u')
                ->leftjoin('oa_tl_history as oth','oth.lead_technician','u.id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','u.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active','u.role')
                ->where(['u.delete'=>0,'u.is_active'=>0,'u.role'=>3])
                ->orderby('u.updated_at','DESC')
                ->get();

            //SO data
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'s_obj' => $s_obj ,'role'=>$role ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            // $data = LabourPaymentModel::where(['delete'=>0,'u_id'=>$labours])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            // foreach($data as $d){
            //     $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
            //     $d->labour_name = $u_obj[0]->name;
            // }

            $data=DB::table('labour_payments as lp')
                ->leftjoin('users as u','u.id','lp.u_id')
                ->leftjoin('sales_orders as so','so.id','lp.so_id')
                ->select('lp.id','lp.oth_id','lp.u_id','lp.so_id','lp.p_desc','lp.payment_date','lp.payment_amnt','lp.created_by','lp.delete','lp.updated_at','lp.created_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name as labour_name','u.delete as u_delete','u.is_active','u.role')
                ->where(['u.delete'=>0,'u.is_active'=>0,'u.role'=>3,'so.delete'=>0,'lp.delete'=>0,'lp.u_id'=>$labours])
                ->whereDate('lp.payment_date', '>=' ,$from_date)
                ->whereDate('lp.payment_date', '<=' ,$to_date)
                ->orderby('lp.updated_at','DESC')
                ->get();


                foreach($data as $d){           //for 24 hrs , time duration calculate
                    // $startTime=$d->created_at;
                    // $nowTime = Carbon::now();       //get current time
                    // $currentTime = $nowTime->toDateTimeString();    //get format "2023-07-03 08:40:11"
                    // // $totalDuration = $finishTime->diffInMinutes($startTime);
                    // $totalDuration = $startTime->diff($currentTime)->format('%H:%I:%S');    
                    // $d->totalDuration=$totalDuration;

                    $now = Carbon::now();
                    $created_at = Carbon::parse($d->created_at);
                    $diffHuman = $created_at->diffForHumans($now);  // 3 Months ago
                    $diffHours = $created_at->diffInHours($now);  // 3 
                    $diffMinutes = $created_at->diffInMinutes($now);   // 180
                    $d->diffHuman=$diffHuman;
                    $d->diffHours=$diffHours;
                    $d->diffMinutes=$diffMinutes;
                }   

            // User Data
            // $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
            $u_obj=DB::table('users as u')
                ->leftjoin('oa_tl_history as oth','oth.lead_technician','u.id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','u.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active','u.role')
                ->where(['u.delete'=>0,'u.is_active'=>0,'u.role'=>3])
                ->orderby('u.updated_at','DESC')
                ->get();
            //SO data
            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

            if(count($data)>0){
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
        $role=Session::get('ROLES');
        if ($from_date == null && $to_date == null) 
        {

            
                
            $data = TransferPaymentModel::where(['delete'=>0,'a_id'=>$a_id])->orderby('updated_at','DESC')->get();
            
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
                $d->labour_name = $u_obj[0]->name;

                //SO data
                // $s_obj=SOModel::where(['delete'=>0,'id'=>$d->so_id])->orderby('created_at','DESC')->get();  
                // $d->so_number = $s_obj[0]->so_number;
                // $d->client_name = $s_obj[0]->client_name;

                $s_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.updated_at','so.delete','so.labour','so.so_number')
                ->where(['oth.id'=>$d->recvr_oth_id,'so.delete'=>0])
                ->orderby('so.updated_at','DESC')
                ->get();

                foreach($s_obj as $s){
                    $d->so_number = $s->so_number;
                }
            }

           
            $s_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
                ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('so.updated_at','DESC')
                ->get();
        
            $u_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active','u.created_at')
                ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('u.created_at','ASC')
                ->get();    

            // $u_obj=DB::table('oa_tl_history as oth')
            //     ->leftjoin('users as u','u.id','oth.lead_technician')
            //     ->leftjoin('sales_orders as so','so.id','oth.so_id')
            //     ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.id as u_id','u.name','u.delete as u_delete','u.is_active')
            //     ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            //     
            //     ->orderby('oth.updated_at','DESC')
            //     ->get();

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'s_obj' => $s_obj ,'a_id' => $a_id ,'message' => 'Data Found'));
            }else{
            return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data = TransferPaymentModel::where(['delete'=>0,'a_id'=>$a_id])->whereDate('p_date', '>=' ,$from_date)->whereDate('p_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->u_id])->orderby('created_at','DESC')->get();
                $d->labour_name = $u_obj[0]->name;

                $s_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.updated_at','so.delete','so.labour','so.so_number')
                ->where(['oth.id'=>$d->recvr_oth_id,'so.delete'=>0])
                ->orderby('so.updated_at','DESC')
                ->get();

                foreach($s_obj as $s){
                    $d->so_number = $s->so_number;
                }
                
            }

            // User Data
            // $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
            //SO data
            // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            $s_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active')
                ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('so.updated_at','DESC')
                ->get();

            $u_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active','u.created_at')
                ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('u.created_at','ASC')
                ->get(); 
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
        $labour = $req->get('labour');                      //receiver technician id
        $recvr_oth_id = $req->get('oth_id');                //receiver oth id
        $so = $req->get('so');                              //sender oth id
        // $so=implode(',',$sos);

    	$a_id=Session::get('USER_ID');

        if($edit_id!=null)
    	{
            if ($payment_amnt !='' && $payment_date !='') 
            {
                $u_obj=TransferPaymentModel::find($edit_id);
                $u_obj->u_id=$labour;
                $u_obj->oth_id=$so;
                $u_obj->recvr_oth_id=$recvr_oth_id;
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

            if ($payment_amnt !='' && $payment_date !='') 
            {
                $u_obj=new TransferPaymentModel();
                $u_obj->u_id=$labour;
                $u_obj->oth_id=$so;
                $u_obj->recvr_oth_id=$recvr_oth_id;
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
            return ['status' => true, 'message' => 'Technician Payment Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'Technician Payment Unsuccessfull...!'];
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
        
        $oth_id = $req->get('exp_so');
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
            $extension = $req->input('payment_extension');       //File Extension  
            
            $pattern='/^data:.+;base64,/';

            $img = preg_replace($pattern, '', $image1);  //removed $pattern
            $img = str_replace(' ', '+', $img);  //for + sign blank space convert
            $data = base64_decode($img);       //decode All File
            
            $filename= $extension."_".md5($image1. microtime()).'.'.$extension;

            // $image_id= uniqid();    // create random name,number
            // $file = $image_id . '.'.$extension; // create name for file
            // $fp  = $image_id.'.'.$extension;   // send the file to destination path

            file_put_contents(public_path('files/user/expense/').$filename,$data); 
        }

        if($edit_id!=null)
    	{
            
            if ($expense_amnt !='' && $exp_date !='') 
            {
                    

                $u_obj=TechnicianExpenseModel::find($edit_id);
                $u_obj->exp_type=$exp_type;
                $u_obj->oth_id=$oth_id;
                $u_obj->exp_desc=$exp_desc;
                $u_obj->exp_date=$exp_date;
                $u_obj->amount=$expense_amnt;
                if($attachment!='') 
                {
                    $u_obj->attachment=$filename;
                }
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
                $u_obj->oth_id=$oth_id;
                $u_obj->exp_desc=$exp_desc;
                $u_obj->exp_date=$exp_date;
                $u_obj->amount=$expense_amnt;
                if($attachment!='') 
                {
                    $u_obj->attachment=$filename;
                }
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


            foreach($data as $d){           //for 24 hrs , time duration calculate
                $now = Carbon::now();
                $created_at = Carbon::parse($d->created_at);
                $diffHuman = $created_at->diffForHumans($now);  // 3 Months ago
                $diffHours = $created_at->diffInHours($now);  // 3 
                $diffMinutes = $created_at->diffInMinutes($now);   // 180
                $d->diffHuman=$diffHuman;
                $d->diffHours=$diffHours;
                $d->diffMinutes=$diffMinutes;
            }   

            //SO data
            // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $s_obj=SOModel::where(['delete'=>0,'lead_technician'=>$a_id])->orderby('created_at','DESC')->get();
            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

            

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj ,'a_id' => $a_id,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->whereDate('exp_date', '>=' ,$from_date)->whereDate('exp_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
            
            foreach($data as $d){           //for 24 hrs , time duration calculate
                // $startTime=$d->created_at;
                // $nowTime = Carbon::now();       //get current time
                // $currentTime = $nowTime->toDateTimeString();    //get format "2023-07-03 08:40:11"
                // // $totalDuration = $finishTime->diffInMinutes($startTime);
                // $totalDuration = $startTime->diff($currentTime)->format('%H:%I:%S');    
                // $d->totalDuration=$totalDuration;

                $now = Carbon::now();
                $created_at = Carbon::parse($d->created_at);
                $diffHuman = $created_at->diffForHumans($now);  // 3 Months ago
                $diffHours = $created_at->diffInHours($now);  // 3 
                $diffMinutes = $created_at->diffInMinutes($now);   // 180
                $d->diffHuman=$diffHuman;
                $d->diffHours=$diffHours;
                $d->diffMinutes=$diffMinutes;
            }   

            //SO data
            // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $s_obj=SOModel::where('labour', 'LIKE', '%'.$a_id.'%')->where(['delete'=>0,])->orderby('created_at','DESC')->get();
            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();
            
            
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
        $a_id=Session::get('USER_ID');
        $role=Session::get('ROLES');
        if($role == 0){
    	    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        }else{

            $s_obj=SOModel::where(['delete'=>0,'a_id'=>$a_id])->orderby('created_at','DESC')->get();  // get admin created all oa

            $technicians= array(); //create empty array

            foreach($s_obj as $s){
                $technician = array_map('intval', explode(',', $s->labour));      // create array all sub technician
                // foreach($technician as $t)
                // {   
                //     array_push($technicians,$t);        // push sub technician in technicians 
                // }
                
                $lead_tech = array_map('intval', explode(',', $s->lead_technician));    // lead technician
                foreach($lead_tech as $l)
                {   
                    array_push($technicians,$l);        // push lead technician in all technicians
                }
            }

            $all_technician = array_unique($technicians);           //remove duplicate technician id
            

    	    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->whereIn('id',$all_technician)->orderby('created_at','DESC')->get();
            // dd($s_obj);
        }
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

            if($role == 0){
                $data=DB::table('technician_expenses as te')
                ->leftjoin('users as u','u.id','te.a_id')
                ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
                ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.delete'=>0])
                ->orderby('u.created_at','DESC')
                ->get();

                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->project_admin = $u->name;
                    }
                }
    
            }else{

                // $data=DB::table('technician_expenses as te')
                // ->leftjoin('users as u','u.id','te.a_id')
                // ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount')
                // ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'u.a_id'=>$a_id])
                // ->orderby('u.created_at','DESC')
                // ->get();

                $data=DB::table('technician_expenses as te')
                ->leftjoin('users as u','u.id','te.a_id')
                ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
                ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.delete'=>0,'so.a_id'=>$a_id])
                ->orderby('u.created_at','DESC')
                ->get();

                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->project_admin = $u->name;
                    }
                }
            }

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'role'=> $role,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            $data=DB::table('technician_expenses as te')
                ->leftjoin('users as u','u.id','te.a_id')
                ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
                ->whereDate('te.exp_date', '>=' ,$from_date)
                ->whereDate('te.exp_date', '<=' ,$to_date)
                ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.delete'=>0,'te.a_id'=>$labours])
                ->orderby('u.created_at','DESC')
                ->get();

            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->project_admin = $u->name;
                }
            }


            if(count($data)>0){
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
                    $u_obj->aprvd_amount=$sa_updated_amnt;
                }
                $u_obj->delete=0;
                $u_obj->sa_id=$a_id;
                $res=$u_obj->update();

            }else{

                $u_obj->acc_remark=$acc_remark;
                $u_obj->status=$status;
    
                if ($updated_amnt >'0' && $status != 'Disapproved') 
                {
                    $u_obj->aprvd_amount=$updated_amnt;
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
        $role=Session::get('ROLES');
        $a_id=Session::get('USER_ID');
        if($role == 0){
    	    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
    	    $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
            $s_obj1=DB::table('oa_tl_history as oth')
                    ->leftjoin('users as u','u.id','oth.lead_technician')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
                    ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                    ->orderby('oth.updated_at','DESC')
                    ->get();

        }else{

            $so_obj=SOModel::where(['delete'=>0,'a_id'=>$a_id])->orderby('created_at','DESC')->get();  // get admin created all oa

            $technicians= array(); //create empty array

            foreach($so_obj as $s){
                $technician = array_map('intval', explode(',', $s->labour));      // create array all sub technician
                // foreach($technician as $t)
                // {   
                //     array_push($technicians,$t);        // push sub technician in technicians 
                // }
                
                $lead_tech = array_map('intval', explode(',', $s->lead_technician));    // lead technician
                foreach($lead_tech as $l)
                {   
                    array_push($technicians,$l);        // push lead technician in all technicians
                }
            }

            $all_technician = array_unique($technicians);           //remove duplicate technician id
            

    	    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->whereIn('id',$all_technician)->orderby('created_at','DESC')->get();
            // dd($s_obj);



    	    // $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'a_id'=>$a_id])->orderby('created_at','DESC')->get();
    	    $s_obj=SOModel::where(['delete'=>0,'lead_technician'=>$a_id])->orderby('created_at','DESC')->get();
            $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
            $s_obj1=DB::table('oa_tl_history as oth')
                    ->leftjoin('users as u','u.id','oth.lead_technician')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
                    ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                    ->orderby('oth.updated_at','DESC')
                    ->get();

       
        }
    	return view('labour.travelExpense',compact('u_obj','s_obj','us_obj','s_obj1'));

    }

    public function postTravelExpense(Request $req)
    {
        $edit_id=$req->get('exp_edit_id');
        $exp_so = $req->get('exp_so');
        $mode_travel = $req->get('mode_travel');
   		$from_location = $req->get('from_location');
   		$to_location = $req->get('to_location');
        $total_km = $req->get('total_km');
        $attachment = $req->get('attachment');
        $travel_date = $req->get('travel_date');
        $travel_desc = $req->get('travel_desc');
        $travel_amount = $req->get('travel_amnt');
        $no_of_person = $req->get('no_of_person');
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

            $filename= $extension."_".md5($image1. microtime()).'.'.$extension;

            // $image_id= uniqid();    // create random name,number
            // $file = $image_id . '.'.$extension; // create name for file
            // $fp  = $image_id.'.'.$extension;   // send the file to destination path

            file_put_contents(public_path('files/user/travel_expense/').$filename, $data); 
        }

        if($edit_id!=null)
    	{
            
            if ($travel_amount !='' && $travel_date !='') 
            {
                $u_obj=TravelExpenseModel::find($edit_id);
                $u_obj->mode_travel=$mode_travel;
                $u_obj->oth_id=$exp_so;
                $u_obj->from_location=$from_location;
                $u_obj->to_location=$to_location;
                $u_obj->total_km=$total_km;
                $u_obj->travel_date=$travel_date;
                $u_obj->travel_desc=$travel_desc;
                $u_obj->travel_amount=$travel_amount;
                $u_obj->no_of_person=$no_of_person;

                if($attachment!='') 
                {
                    $u_obj->attachment=$filename;
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
                $u_obj->oth_id=$exp_so;
                $u_obj->from_location=$from_location;
                $u_obj->to_location=$to_location;
                $u_obj->total_km=$total_km;
                $u_obj->travel_date=$travel_date;
                $u_obj->travel_desc=$travel_desc;
                $u_obj->travel_amount=$travel_amount;
                $u_obj->no_of_person=$no_of_person;
                if($attachment!='') 
                {
                    $u_obj->attachment=$filename;
                }
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
            if($role == 3)      // for technician
            {
                $date = Carbon::now()->subDays(60);  // get last 7 days record
                $data = TravelExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->where('created_at', '>=', $date)->orderby('updated_at','DESC')->get();
                foreach($data as $d)
                {
                    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u)
                    {
                        $d->labour_name = $u->name;
                        $d->emp_number = $u->emp_number;
    
                    }

                    $now = Carbon::now();
                    $created_at = Carbon::parse($d->created_at);
                    $diffHuman = $created_at->diffForHumans($now);  // 3 Months ago
                    $diffHours = $created_at->diffInHours($now);  // 3 
                    $diffMinutes = $created_at->diffInMinutes($now);   // 180
                    $d->diffHuman=$diffHuman;
                    $d->diffHours=$diffHours;
                    $d->diffMinutes=$diffMinutes;
                }

                $s_obj=DB::table('oa_tl_history as oth')
                    ->leftjoin('users as u','u.id','oth.lead_technician')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
                    ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                    ->orderby('oth.updated_at','DESC')
                    ->get();

            }
            else
            {
                if($role == 0)      // for super admin
                {
                    
                    $date = Carbon::now()->subDays(60);  // get last 7 days record

                    $data=DB::table('travel_expenses as te')
                        ->leftjoin('users as u','u.id','te.a_id')
                        ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                        ->leftjoin('sales_orders as so','so.id','oth.so_id')
                        ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id','u.emp_number','u.a_id as u_a_id','te.id','te.oth_id','te.ad_id','te.sa_id','te.mode_travel','te.from_location','te.to_location','te.total_km','te.travel_date','te.travel_desc','te.ad_remark','te.sa_remark','te.attachment','te.no_of_person','te.travel_amount','te.aprvd_amount','te.status','te.a_id','te.delete','te.created_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
                        ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                        ->where('te.created_at', '>=', $date)
                        ->orderby('te.updated_at','DESC')
                        ->get();

                    foreach($data as $d)
                    {
                        
                        $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $d->project_admin = $u->name;
                        }
                    }


                    $s_obj=DB::table('oa_tl_history as oth')
                        ->leftjoin('users as u','u.id','oth.lead_technician')
                        ->leftjoin('sales_orders as so','so.id','oth.so_id')
                        ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
                        ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                        ->orderby('oth.updated_at','DESC')
                        ->get();
                    

                }else{      //admin
                    
                    $date = Carbon::now()->subDays(60);  // get last 7 days record


                    $data=DB::table('travel_expenses as te')
                        ->leftjoin('users as u','u.id','te.a_id')
                        ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                        ->leftjoin('sales_orders as so','so.id','oth.so_id')
                        ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','te.id','te.oth_id','te.ad_id','te.sa_id','te.mode_travel','te.from_location','te.to_location','te.total_km','te.travel_date','te.travel_desc','te.ad_remark','te.sa_remark','te.attachment','te.no_of_person','te.travel_amount','te.aprvd_amount','te.status','te.a_id','te.delete','te.created_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
                        ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.a_id'=>$a_id])
                        ->where('te.created_at', '>=', $date)
                        ->orderby('te.updated_at','DESC')
                        ->get();

                        foreach($data as $d)
                        {
                            $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                            foreach($u_obj as $u){
                                $d->project_admin = $u->name;
                            }
                        }

                    $s_obj=DB::table('oa_tl_history as oth')
                    ->leftjoin('users as u','u.id','oth.lead_technician')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active','u.a_id')
                    ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                    ->orderby('oth.updated_at','DESC')
                    ->get();  
                }
            }

          

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj,'role'=>$role,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            if($role == 3)
            {
                $data = TravelExpenseModel::where(['delete'=>0,'a_id'=>$a_id])->whereDate('travel_date', '>=' ,$from_date)->whereDate('travel_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
                foreach($data as $d){
                    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->a_id])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $d->labour_name = $u->name;
                        $d->emp_number = $u->emp_number;
    
                    }

                    $now = Carbon::now();
                    $created_at = Carbon::parse($d->created_at);
                    $diffHuman = $created_at->diffForHumans($now);  // 3 Months ago
                    $diffHours = $created_at->diffInHours($now);  // 3 
                    $diffMinutes = $created_at->diffInMinutes($now);   // 180
                    $d->diffHuman=$diffHuman;
                    $d->diffHours=$diffHours;
                    $d->diffMinutes=$diffMinutes;
                }

                $s_obj=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active')
                ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('oth.updated_at','DESC')
                ->get();

            }else{

                if($role == 0){

                    $data=DB::table('travel_expenses as te')
                    ->leftjoin('users as u','u.id','te.a_id')
                    ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id','u.emp_number','u.a_id as u_a_id','te.id','te.oth_id','te.ad_id','te.sa_id','te.mode_travel','te.from_location','te.to_location','te.total_km','te.travel_date','te.travel_desc','te.ad_remark','te.sa_remark','te.attachment','te.no_of_person','te.travel_amount','te.aprvd_amount','te.status','te.a_id','te.delete','te.created_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
                        ->whereDate('travel_date', '>=' ,$from_date)
                        ->whereDate('travel_date', '<=' ,$to_date)
                        ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'te.a_id'=>$labours])
                        ->orderby('te.updated_at','DESC')
                        ->get();

                    foreach($data as $d)
                    {
                        
                        $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $d->project_admin = $u->name;
                        }
                    }

                    $s_obj=DB::table('oa_tl_history as oth')
                    ->leftjoin('users as u','u.id','oth.lead_technician')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active')
                    ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                    ->orderby('oth.updated_at','DESC')
                    ->get();

                }
                else
                {
                    $data=DB::table('travel_expenses as te')
                        ->leftjoin('users as u','u.id','te.a_id')
                        ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                        ->leftjoin('sales_orders as so','so.id','oth.so_id')
                        ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','te.id','te.oth_id','te.ad_id','te.sa_id','te.mode_travel','te.from_location','te.to_location','te.total_km','te.travel_date','te.travel_desc','te.ad_remark','te.sa_remark','te.attachment','te.no_of_person','te.travel_amount','te.aprvd_amount','te.status','te.a_id','te.delete','te.created_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
                        ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'te.a_id'=>$labours])
                        ->whereDate('travel_date', '>=' ,$from_date)
                        ->whereDate('travel_date', '<=' ,$to_date)
                        ->orderby('te.updated_at','DESC')
                        ->get();

                    foreach($data as $d)
                    {
                        $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $d->project_admin = $u->name;
                        }
                    }

                    $s_obj=DB::table('oa_tl_history as oth')
                    ->leftjoin('users as u','u.id','oth.lead_technician')
                    ->leftjoin('sales_orders as so','so.id','oth.so_id')
                    ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active','u.a_id')
                    ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                    ->orderby('oth.updated_at','DESC')
                    ->get();
                }
            }
           
            if(count($data)>0){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj,'role'=>$role,'message' => 'Data Found'));
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
        
        // return ['status' => true,'sa_remark' => $sa_remark ,'a_id' => $a_id,'message' => 'found']; 

        if($edit_id!=null)
    	{
            
            $u_obj=TravelExpenseModel::find($edit_id);

            if($role == 0){
                $u_obj->sa_remark=$sa_remark;
                $u_obj->status=$status;
    
                if ($sa_updated_amnt >'0' && $status != '') 
                {
                    $u_obj->aprvd_amount=$sa_updated_amnt;
                }
    
                $u_obj->delete=0;
                $u_obj->sa_id=$a_id;
                $res=$u_obj->update();
            }else{
                $u_obj->ad_remark=$acc_remark;
                $u_obj->status=$status;
    
                if ($updated_amnt >'0' && $status != 'Disapproved') 
                {
                    $u_obj->aprvd_amount=$updated_amnt;
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
    public function aprvdCheckExp(Request $req)
    {
        $role=Session::get('ROLES');
        $a_id=Session::get('USER_ID');
        $check_exp = $req->get('check_exp');
        // dd($check_exp);
        $j=0;
        for ($i=1; $i <= count($check_exp); $i++)
        {  
            $u_obj=TechnicianExpenseModel::find($check_exp[$j]);
            $u_obj->sa_id=$a_id;
            $u_obj->status="Approved";
            $res=$u_obj->update();
        }  
        if($res){
            Session::put('SUCCESS_MESSAGE', "Technician Expenses Approved Successfully.");
        }else{
            Session::put('ERROR_MESSAGE',"Technician Expenses Approved Unsuccessfully...!");
        }
        return redirect()->back();
    }
    
    public function aprvdCheckTravelExp(Request $req)
    {
        $role=Session::get('ROLES');
        $a_id=Session::get('USER_ID');
        $check_exp = $req->get('check_exp');
        // dd($check_exp);
        $j=0;
        for ($i=1; $i <= count($check_exp); $i++)
        {  
            $u_obj=TravelExpenseModel::find($check_exp[$j]);
            $u_obj->sa_id=$a_id;
            $u_obj->status="Approved";
            $res=$u_obj->update();
        }  
        if($res){
            Session::put('SUCCESS_MESSAGE', "Technician Expenses Approved Successfully.");
        }else{
            Session::put('ERROR_MESSAGE',"Technician Expenses Approved Unsuccessfully...!");
        }
        return redirect()->back();
    }


}
