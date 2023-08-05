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
                                                                                                                                         
        //Technician Expense
        $exp_date = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$labours]) ->whereDate('exp_date', '>=' ,$from_date)
        ->whereDate('exp_date', '<=' ,$to_date)->groupBy('exp_date')->get('exp_date');
        $technicianss= array(); //create empty array
        foreach($exp_date as $ed){

            $data=DB::table('technician_expenses as te')
            ->leftjoin('users as u','u.id','te.a_id')
            ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
            ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.status'=>"Approved",'te.exp_date'=>$ed->exp_date])
            ->orderby('u.created_at','DESC')
            ->get();

            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->project_admin = $u->name;
                }

                $u_obj=UserModel::where(['delete'=>0,'id'=>$d->sa_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->super_admin = $u->name;
                }
            }
             $exp_objj= array(); //create empty array

            // $technicians= array(); //create empty array
            $exp_obj=UserModel::select('id','name','emp_number','mobile')->where(['delete'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
            
            $hotel=$daily_allowance=$material_purchase=$other=$total_amount= 0;
            $approval_admin = "";
            $approval_super_admin = "";
            $oa_number = "";
            $exp_date = $ed->exp_date;
            foreach($data as $d){
                
                $approval_admin = $d->project_admin;
                $approval_super_admin = $d->super_admin;
                $oa_number = $d->so_number;
                if($d->exp_type == "Hotel"){
                    $hotel += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }else if($d->exp_type == "Daily Allowance"){
                    $daily_allowance += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }else if($d->exp_type == "Material_Purchase"){
                    $material_purchase += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }else{
                    $other += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }

            }
            $exp_objj["hotel"] = $hotel;
            $exp_objj["daily_allowance"] = $daily_allowance;
            $exp_objj["material_purchase"] = $material_purchase;
            $exp_objj["other"] = $other;
            $exp_objj["total_amount"] = $total_amount;
            $exp_objj["approval_admin"] = $approval_admin;
            $exp_objj["approval_super_admin"] = $approval_super_admin;
            $exp_objj["oa_number"] = $oa_number;
            $exp_objj["exp_date"]= $ed->exp_date;
            $travel_expense = TravelExpenseModel::where(['delete'=>0,'a_id'=>$labours,'travel_date'=>$ed->exp_date])->sum('aprvd_amount');
            $exp_objj["travel_expense"]= $travel_expense;
          
            array_push($technicianss,$exp_objj);

        }

        if(count($data)>0){
            return json_encode(array('status' => true ,'data' => $technicianss,'exp_date' => $exp_date,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    public function generatePdf(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('pdf_from_date');
        $to_date = $req->get('pdf_to_date');
        $labours = $req->get('pdf_labours');
        // dd($from_date);
        $u_obj1=UserModel::where(['delete'=>0,'id'=>$labours])->where('role','!=','0')->orderby('created_at','DESC')->get();
        foreach($u_obj1 as $u){
            $u->from_date = date('d-m-Y', strtotime($from_date));
            $u->to_date = date('d-m-Y', strtotime($to_date));

        }

        //Technician Expense
        $exp_date = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$labours]) ->whereDate('exp_date', '>=' ,$from_date)
        ->whereDate('exp_date', '<=' ,$to_date)->groupBy('exp_date')->get('exp_date');
        $tech_exp= array(); //create empty array
        foreach($exp_date as $ed){

            $data=DB::table('technician_expenses as te')
            ->leftjoin('users as u','u.id','te.a_id')
            ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
            ->where(['te.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.status'=>"Approved",'te.exp_date'=>$ed->exp_date])
            ->orderby('u.created_at','DESC')
            ->get();

            foreach($data as $d){
                $u_obj=UserModel::where(['delete'=>0,'id'=>$d->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->project_admin = $u->name;
                }

                $u_obj=UserModel::where(['delete'=>0,'id'=>$d->sa_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $d->super_admin = $u->name;
                }
            }
             $exp_objj= array(); //create empty array

            // $technicians= array(); //create empty array
            $exp_obj=UserModel::select('id','name','emp_number','mobile')->where(['delete'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
            
            $hotel=$daily_allowance=$material_purchase=$other=$total_amount= 0;
            $approval_admin = "";
            $approval_super_admin = "";
            $oa_number = "";
            $exp_date = $ed->exp_date;
            foreach($data as $d){
                
                $approval_admin = $d->project_admin;
                $approval_super_admin = $d->super_admin;
                $oa_number = $d->so_number;
                if($d->exp_type == "Hotel"){
                    $hotel += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }else if($d->exp_type == "Daily Allowance"){
                    $daily_allowance += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }else if($d->exp_type == "Material_Purchase"){
                    $material_purchase += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }else{
                    $other += $d->aprvd_amount;
                    $total_amount += $d->aprvd_amount;
                }

            }
            $date = date('d-m-Y', strtotime($ed->exp_date));
            $exp_objj["hotel"] = $hotel;
            $exp_objj["daily_allowance"] = $daily_allowance;
            $exp_objj["material_purchase"] = $material_purchase;
            $exp_objj["other"] = $other;
            $exp_objj["total_amount"] = $total_amount;
            $exp_objj["approval_admin"] = $approval_admin;
            $exp_objj["approval_super_admin"] = $approval_super_admin;
            $exp_objj["oa_number"] = $oa_number;
            $exp_objj["exp_date"]= $date;
            $travel_expense = TravelExpenseModel::where(['delete'=>0,'a_id'=>$labours,'travel_date'=>$ed->exp_date])->sum('aprvd_amount');
            $exp_objj["travel_expense"]= $travel_expense;
            $exp_objj["exp_total_amount"] = $total_amount + $travel_expense;
            array_push($tech_exp,$exp_objj);

        }



        // return view('report.siteExpensePdf');

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
