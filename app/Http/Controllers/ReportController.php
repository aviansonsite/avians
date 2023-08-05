<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\TechnicianExpenseModel;
use App\Models\TravelExpenseModel;

use Session;
use Hash;
use DB;
use PDF;

class ReportController extends Controller
{
    public function siteExpReport()
    {
        // return view('report.siteExpensePdf');
     

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
       
    	return view('report.siteExpenseReport',compact('u_obj'));

    }

    public function getExpRecord(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');

        $technicians= array(); //create empty array

        $data=DB::table('technician_expenses as te')
        ->leftjoin('users as u','u.id','te.a_id')
        ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
        ->leftjoin('sales_orders as so','so.id','oth.so_id')
        ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
        ->whereDate('te.exp_date', '>=' ,$from_date)
        ->whereDate('te.exp_date', '<=' ,$to_date)
        ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.status'=>"Approved"])
        ->orderby('u.created_at','DESC')
        ->get();
        foreach($data as $d){
            $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
            foreach($u_obj as $u){
                $d->project_admin = $u->name;
            }

            $u_obj=UserModel::where(['delete'=>0,'id'=>$d->sa_id])->where('role','!=','0')->orderby('created_at','DESC')->get();
            foreach($u_obj as $u){
                $d->super_admin = $u->name;
            }
        }

        // foreach($so_obj as $s){
        //     $technician = array_map('intval', explode(',', $s->labour));      // create array all sub technician
        //     // foreach($technician as $t)
        //     // {   
        //     //     array_push($technicians,$t);        // push sub technician in technicians 
        //     // }
            
        //     $lead_tech = array_map('intval', explode(',', $s->lead_technician));    // lead technician
        //     foreach($lead_tech as $l)
        //     {   
        //         array_push($technicians,$l);        // push lead technician in all technicians
        //     }
        // }



        // return view('report.siteExpensePdf');
        $pdf1 =PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.siteExpensePdf',compact('tech_exp','u_obj1'))->setPaper('a4', 'landscape');
        
        $pdf1->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed'=> TRUE,
                        'verify_peer' => FALSE,
                        'verify_peer_name' => FALSE,
                    ]
                ])
            );
        // $file_name="Expense.pdf";
        // $delOldPDF = "public/files/temp/$file_name";
        // file_put_contents("public/files/temp/$file_name", $pdf1->download());
        
        return $pdf1->download();
    }
}
