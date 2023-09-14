<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
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
use App\Models\PunchInOutModel;
use App\Models\OATLHistoryModel;
use Session;
use Hash;
use File;
use DB;

class LabourAPIController extends Controller
{
    public function getPIORecords(Request $req)
    {
        // $a_id=Session::get('USER_ID');
        $a_id = $req->get('u_id');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        
        if ($from_date == null && $to_date == null) 
        {
            $a_idd [] =Session::get('USER_ID');
 
            // $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
            // $s_obj=SOModel::whereIn('labour',$a_idd)->where(['delete'=>0])->orderby('created_at','DESC')->get();

            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

            $labour = explode(",",$s_obj[0]->labour);
            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->whereIn('id',$labour)->orderby('created_at','DESC')->get();
            $tdate=date("Y-m-d");

            // $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate])->orderby('updated_at','DESC')->count();
            $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate,'pin_u_id'=>$a_id])->orderby('updated_at','DESC')->count();
            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$a_id])->orderby('updated_at','DESC')->get();

            $createdAt = PunchInOutModel::whereNotNull('created_at')->get();
            $updatedAt = PunchInOutModel::whereNotNull('updated_at')->get();

            $p_id = PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate,'pin_u_id'=>$a_id])->orderby('updated_at','DESC')->get();
            foreach($p_id as $p){
                $p_id1 = PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate,'a_id'=>$a_id])->orderby('updated_at','DESC')->get();
                // dd($p_id1);
                $pin_u_ids = [];
                foreach($p_id1 as $p1){
                    array_push($pin_u_ids, $p1->pin_u_id);    //Push user id for attendance
                }
                $p->pin_u_ids = implode(',',$pin_u_ids);
            } 
            // dd($p_obj);
            // $p_id = $p_id[0];
            foreach($p_obj as $p){
                $startTime=$p->created_at;
                $finishTime=$p->updated_at;
                // $totalDuration = $finishTime->diffInMinutes($startTime);
                $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                $p->totalDuration=$totalDuration;
                $p->pin_time=$p->created_at->format('H:i:s');          
                $p->pout_time=$p->updated_at->format('H:i:s');  
                $p->pin_time=$p->created_at->format('H:i:s');          
            }

            if(!empty($p_obj)){
                return json_encode(array('status' => true ,'data' => $p_obj,'u_obj' => $u_obj,'s_obj' => $s_obj,'p_id'=>$p_id,'t_count'=> $t_count ,'message' => 'Data Found'));
            }else{
            return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            return ['status' => false, 'message' => 'No Data Found','from_date'=>$from_date];
            $a_idd [] =Session::get('USER_ID');
            $a_id =Session::get('USER_ID');
            $id =Session::get('USER_ID');
            // $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
            // // $s_obj=SOModel::whereIn('labour',$a_idd)->where(['delete'=>0])->orderby('created_at','DESC')->get();

            // $s_obj=DB::table('oa_tl_history as oth')
            // ->leftjoin('users as u','u.id','oth.lead_technician')
            // ->leftjoin('sales_orders as so','so.id','oth.so_id')
            // ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active')
            // ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            // ->orderby('oth.updated_at','DESC')
            // ->get();

            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

            $labour = explode(",",$s_obj[0]->labour);
            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->whereIn('id',$labour)->orderby('created_at','DESC')->get();
            $tdate=date("Y-m-d");

            // $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate])->orderby('updated_at','DESC')->count();
            $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate,'pin_u_id'=>$a_id])->orderby('updated_at','DESC')->count();
            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$a_id])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            $createdAt = PunchInOutModel::whereNotNull('created_at')->get();
            $updatedAt = PunchInOutModel::whereNotNull('updated_at')->get();

            $p_id = PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate,'a_id'=>$a_id])->orderby('updated_at','DESC')->get();
            // dd($p_obj);
            // $p_id = $p_id[0];
            foreach($p_obj as $p){
                $startTime=$p->created_at;
                $finishTime=$p->updated_at;
                // $totalDuration = $finishTime->diffInMinutes($startTime);
                $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                $p->totalDuration=$totalDuration;
                $p->pin_time=$p->created_at->format('H:i:s');          
                $p->pout_time=$p->updated_at->format('H:i:s');  
            }

            if(!empty($p_obj)){
                return json_encode(array('status' => true ,'data' => $p_obj,'u_obj' => $u_obj,'s_obj' => $s_obj,'fdate' =>$from_date ,'message' => 'Data Found'));
            }else{
            return ['status' => false, 'message' => 'No Data Found'];
            }
        }       

    }

    public function punchInAPI(Request $req)
    {
    	// $a_id=Session::get('USER_ID');
        $a_id = $req->get('u_id');
        $p_in_so=isset($_POST['p_in_so']) ? $_POST['p_in_so'] : "NA";
    	$p_in_labour=isset($_POST['p_in_labour']) ? $_POST['p_in_labour'] : "NA";
    	$p_in_remark=isset($_POST['p_in_remark']) ? $_POST['p_in_remark'] : "NA";
    	$p_in_date=isset($_POST['p_in_date']) ? $_POST['p_in_date'] : "NA";
        $p_in_latitude=isset($_POST['p_in_latitude']) ? $_POST['p_in_latitude'] : "NA";
    	$p_in_longitude=isset($_POST['p_in_longitude']) ? $_POST['p_in_longitude'] : "NA";
        $photo_path_ext=isset($_POST['profile_photo_ext']) ? $_POST['profile_photo_ext'] : null;
        // $photo_path = $req->hasfile('attachment');
        $photo_path = $req->input('attachment') ?$req->input('attachment'): '';

        
        $u_id = strval($a_id); 
        array_push($p_in_labour, $u_id);    //Push user id for attendance
        $tech_count = count($p_in_labour);
        // return ['status' => true,'p_in_so' => $p_in_so,'p_in_labour' => $p_in_labour,'p_in_remark' => $p_in_remark,'p_in_date' => $p_in_date,'p_in_latitude' => $p_in_latitude,'p_in_longitude' => $p_in_longitude,'a_id'=>$a_id,'photo_path_ext'=>$photo_path_ext,'photo_path'=>$photo_path]; 
        // $p_in_so=implode(',',$p_in_so);
        // $p_in_labour=implode(',',$p_in_labour);
        
        if ($p_in_latitude !='' &&  $p_in_longitude !='' && $p_in_date !='' ) 
        {
            $j=0;
            for ($i=1; $i <= count($p_in_labour); $i++)
            { 
                $u_obj=new PunchInOutModel();
                $u_obj->pin_u_id=$p_in_labour[$j];
                $u_obj->pin_oth_id=$p_in_so;
                $u_obj->pin_remark=$p_in_remark;
                $u_obj->pin_date=$p_in_date;
                $u_obj->pin_latitude=$p_in_latitude;
                $u_obj->pin_longitude=$p_in_longitude;
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;

                // return ['status' => true, 'photo_path' => $photo_path]; 
                $destinationPath = 'files/attendance/punchIn/';
                    if($photo_path!="" && str_contains($photo_path, '+'))
                    {         
                             
                        
                        $img = str_replace('data:image/jpg;base64,', '', $photo_path);
                        $img = str_replace(' ', '+', $img);
                        $data = base64_decode($img);
                        // $image_id= uniqid();
                        $filename= $photo_path_ext."_".md5($photo_path. microtime()).'.'.$photo_path_ext;

                        file_put_contents($destinationPath.$filename, $data);
                        $u_obj->pin_img=$filename;
                        
                    }


                $res=$u_obj->save();
                $j++;

            }
            if($res){
                // Session::put('SUCCESS_MESSAGE', 'Punch In Successfully...!');
                return ['status' => true, 'message' => 'Punch In Successfully...!']; 

            }else{
                // Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                return ['status' => false, 'message' => 'Something went wrong. Please try again.']; 
            }
           
        }else{
            // Session::put('SUCCESS_MESSAGE', 'Please Try Again..');
            return ['status' => false, 'message' => 'Please Try Again..']; 
        } 
        // return redirect()->back();

    }

    public function punchOutAPI(Request $req)
    {
        // $a_id=Session::get('USER_ID');
        $a_id = $req->get('u_id');
        // $a_id=!empty($_POST['u_id']) ? $_POST['u_id'] : "" ;                   //punch in today id
        $pout_so=isset($_POST['pout_so']) ? $_POST['pout_so'] : "NA";
    	$pout_labour=isset($_POST['pout_labour']) ? $_POST['pout_labour'] : "NA";
    	$pout_remark=isset($_POST['pout_remark']) ? $_POST['pout_remark'] : "NA";
    	$pout_work_desc=isset($_POST['pout_work_desc']) ? $_POST['pout_work_desc'] : "NA";
    	$pout_date=isset($_POST['pout_date']) ? $_POST['pout_date'] : "NA";
        $pout_latitude=isset($_POST['pout_latitude']) ? $_POST['pout_latitude'] : "NA";
    	$pout_longitude=isset($_POST['pout_longitude']) ? $_POST['pout_longitude'] : "NA";
        $photo_path_ext=isset($_POST['ext']) ? $_POST['ext'] : null;
        $photo_path = $req->input('attachment') ?$req->input('attachment'): '';
        // $photo_path = $req->hasfile('attachment');

        $u_id = strval($a_id); 
        array_push($pout_labour, $u_id);    //Push user id for attendance
        $tech_count = count($pout_labour);
        // dd($tech_count);
        // $pout_so=implode(',',$pout_so);
        // $pout_labour=implode(',',$pout_labour);

        // return ['status' => true,'pout_so' => $pout_so,'pout_labour' => $pout_labour,'pout_remark' => $pout_remark,'pout_work_desc' => $pout_work_desc,'pout_date'=>$pout_date,'pout_latitude' => $pout_latitude,'pout_longitude' => $pout_longitude,'u_id'=>$a_id,'ext'=>$ext,'attachment'=>$photo_path]; 

        if ($pout_latitude !='' && $pout_longitude !='') 
        {
            
            $j=0;
            for ($i=1; $i <= count($pout_labour); $i++)
            {  
                $check=PunchInOutModel::where(['pin_u_id'=>$pout_labour[$j],'pin_date'=>$pout_date])->get();

                if(count($check) > 0){
                    $u_obj=PunchInOutModel::where(['pin_u_id'=>$pout_labour[$j],'pin_date'=>$pout_date]);
                    // $img = $req->pout_img;                        //get image
                    if($photo_path!="" )
                    {   

                        $destinationPath = 'files/attendance/punchOut/';
                            
                            $img = str_replace('data:image/jpg;base64,', '', $photo_path);
                            $img = str_replace(' ', '+', $img);
                            $data = base64_decode($img);
                            // $image_id= uniqid();
                            $filename= $photo_path_ext."_".md5($photo_path. microtime()).'.'.$photo_path_ext;

                            file_put_contents($destinationPath.$filename, $data);
            
                        $u_obj->update([
                            'pout_u_id' => $pout_labour[$j],
                            'pout_oth_id' => $pout_so, 
                            'pout_remark' => $pout_remark,
                            'pout_work_desc' => $pout_work_desc,
                            'pout_date' => $pout_date, 
                            'pout_latitude' => $pout_latitude,
                            'pout_longitude' => $pout_longitude,
                            'delete' => 0, 
                            'a_id' => $a_id,
                            'pout_img' => $filename
                        ]);

                    }else{
                        // Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                        return ['status' => false, 'message' => 'Something went wrong. Please try again.']; 
                    }
                    
                }else{

                    // $img = $req->pout_img;                        //get image
                    if($photo_path!=""){
                    // return ['status' => false, 'message' => 'Please']; 

                        $u_obj=new PunchInOutModel();
                        $u_obj->pout_u_id=$pout_labour[$j];
                        $u_obj->pout_oth_id=$pout_so;
                        $u_obj->pout_remark=$pout_remark;
                        $u_obj->pout_work_desc=$pout_work_desc;
                        $u_obj->pout_date=$pout_date;
                        $u_obj->pout_latitude=$pout_latitude;
                        $u_obj->pout_longitude=$pout_longitude;
                        $u_obj->delete=0;
                        $u_obj->a_id=$a_id;

                            // $folderPath = public_path('files/attendance/punchOut/');     // folder path
                            
                            // $image_parts = explode(";base64,", $img);
                            // $image_type_aux = explode("image/", $image_parts[0]);
                            // $image_type = $image_type_aux[1];
                            
                            // $image_base64 = base64_decode($image_parts[1]);

                            // $fileName= '.png'."_".md5($img. microtime()).'.png';

                            // // $fileName = uniqid() . '.png';
                            // $file = $folderPath . $fileName;
                            // file_put_contents($file, $image_base64);        //move to specific folder

                            $destinationPath = 'files/attendance/punchOut/';
                            if($photo_path!="" && str_contains($photo_path, '+'))
                            {              
                                
                                $img = str_replace('data:image/jpg;base64,', '', $photo_path);
                                $img = str_replace(' ', '+', $img);
                                $data = base64_decode($img);
                                // $image_id= uniqid();
                                $filename= $photo_path_ext."_".md5($photo_path. microtime()).'.'.$photo_path_ext;

                                file_put_contents($destinationPath.$filename, $data);
                                $u_obj->pout_img=$filename;                                
                            }


                        
                        $res=$u_obj->save();
                    }else{
                        // Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                        return ['status' => false, 'message' => 'Something went wrong. Please try again.']; 
                    }
                }
                $j++;
            }    
            
            if($u_obj){
                // Session::put('SUCCESS_MESSAGE', 'Punch Out Successfully...!');
                return ['status' => true, 'message' => 'Punch Out Successfully...!']; 

            }else{
                // Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                return ['status' => false, 'message' => 'Something went wrong. Please try again.']; 
            }
           
        }else{
            // Session::put('SUCCESS_MESSAGE', 'Please Try Again..');
            return ['status' => false, 'message' => 'Please Try Again..']; 

        } 

        return ['status' => false, 'message' => 'Please Try Again..']; 
        // return redirect()->back();

    }

    public function getPinHLabourAPI(Request $req)
    {
        // $pin_date = $req->get('pin_date');
        $pin_oth_id = $req->get('pin_oth_id');
        $pin_date=isset($_POST['pin_date']) ? $_POST['pin_date'] : "NA";

        // $a_id =Session::get('USER_ID');
        // $so_id = explode(",",$req->get('pin_so_id'));
        
        $data=PunchInOutModel::where(['delete'=>0,'pin_date'=>$pin_date])->orderby('updated_at','DESC')->get();


        $l_obj=DB::table('punch_in_out as pio')
            ->leftjoin('users as u','u.id','pio.pin_u_id')
            ->select('u.id','pio.pin_date','pio.delete','u.name','u.delete as u_delete','u.is_active')
            ->where(['pio.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'pio.pin_date'=>$pin_date,'pin_oth_id'=>$pin_oth_id])
            ->orderby('u.created_at','DESC')
            ->get();

        $s_obj=DB::table('oa_tl_history as oth')
        ->leftjoin('users as u','u.id','oth.lead_technician')
        ->leftjoin('sales_orders as so','so.id','oth.so_id')
        ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active')
        ->where(['oth.id'=>$pin_oth_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
        ->orderby('oth.updated_at','DESC')
        ->get();

        // $s_obj=SOModel::where(['delete'=>0,'id'=>$so_id])->orderby('created_at','DESC')->get();
        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
        if(!empty($data)){
           return json_encode(array('status' => true ,'data' => $data,'l_obj'=>$l_obj,'s_obj'=>$s_obj));
        }else{
           return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    public function getPoutHLabourAPI(Request $req)
    {
        $pout_date = $req->get('pout_date');
        $pout_oth_id = $req->get('pout_oth_id');
        // $so_id = explode(",",$req->get('pout_so_id'));
        $data=PunchInOutModel::where(['delete'=>0,'pin_date'=>$pout_date])->orderby('updated_at','DESC')->get();


        $l_obj=DB::table('punch_in_out as pio')
            ->leftjoin('users as u','u.id','pio.pout_u_id')
            ->select('u.id','pio.pout_date','pio.delete','u.name','u.delete as u_delete','u.is_active')
            ->where(['pio.delete'=>0,'u.delete'=>0,'u.is_active'=>0,'pio.pout_date'=>$pout_date,'pout_oth_id'=>$pout_oth_id])
            ->orderby('u.created_at','DESC')
            ->get();

        $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.id'=>$pout_oth_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
        if(!empty($data)){
           return json_encode(array('status' => true ,'data' => $data,'l_obj'=>$l_obj,'s_obj'=>$s_obj));
        }else{
           return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    public function postExpenseLPaymentAPI(Request $req)
    {
        $a_id = $req->get('u_id');
        $exp_edit_id = $req->get('exp_edit_id');
        $exp_desc=isset($_POST['exp_desc']) ? $_POST['exp_desc'] : "NA";
    	$exp_date=isset($_POST['exp_date']) ? $_POST['exp_date'] : "NA";
        $oth_id=isset($_POST['exp_oth_id']) ? $_POST['exp_oth_id'] : "NA";
    	$expense_amnt=isset($_POST['expense_amnt']) ? $_POST['expense_amnt'] : "NA";
    	$exp_type=isset($_POST['exp_type']) ? $_POST['exp_type'] : "NA";
        $photo_path_ext=isset($_POST['photo_path_ext']) ? $_POST['photo_path_ext'] : null;
        $photo_path = $req->input('attachment') ?$req->input('attachment'): '';


        // For File Decoder 
        $destinationPath = 'files/user/expense/';
        if($photo_path!="" && str_contains($photo_path, '+'))
        {         
            $img = str_replace('data:image/jpg;base64,', '', $photo_path);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            // $image_id= uniqid();
            $filename= $photo_path_ext."_".md5($photo_path. microtime()).'.'.$photo_path_ext;

            file_put_contents($destinationPath.$filename, $data);
            
        }

        if($exp_edit_id!=null)
    	{
            if ($expense_amnt !='' && $exp_date !='') 
            {
                    
                $u_obj=TechnicianExpenseModel::find($exp_edit_id);
                $u_obj->exp_type=$exp_type;
                $u_obj->oth_id=$oth_id;
                $u_obj->exp_desc=$exp_desc;
                $u_obj->exp_date=$exp_date;
                $u_obj->amount=$expense_amnt;
                if($photo_path!="" && str_contains($photo_path, '+'))
                {
                    $u_obj->attachment=$filename;
                }
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                
                if($res){
                    return ['status' => true, 'message' => 'Expense Update Successfully'];
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
                if($photo_path!="" && str_contains($photo_path, '+'))
                {
                    $u_obj->attachment=$filename;
                }
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;            
                $res=$u_obj->save();
                
                if($res){
                    return ['status' => true, 'message' => 'Expense add Successfully'];
                }else{
                return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            } 
        }     
        
    }

    public function deleteExpenseAPI(Request $req)
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

    public function getLabourExpenseAPI(Request $req)
    {

        $a_id = $req->get('u_id');
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


    public function postTransferLPaymentAPI(Request $req)
    {

        $a_id = $req->get('u_id');
        $edit_id = $req->get('edit_id');
        $pay_desc=isset($_POST['pay_desc']) ? $_POST['pay_desc'] : "NA";
    	$payment_date=isset($_POST['payment_date']) ? $_POST['payment_date'] : "NA";
        $payment_amnt=isset($_POST['payment_amnt']) ? $_POST['payment_amnt'] : "NA";
    	$labour=isset($_POST['labour']) ? $_POST['labour'] : "NA";                          //receiver technician id
    	$recvr_oth_id=isset($_POST['recvr_oth_id']) ? $_POST['recvr_oth_id'] : "NA";        //receiver oth id
    	$so=isset($_POST['so']) ? $_POST['so'] : "NA";                                      //sender oth id
        // $photo_path_ext=isset($_POST['profile_photo_ext']) ? $_POST['profile_photo_ext'] : null;
        // $photo_path = $req->input('attachment') ?$req->input('attachment'): '';


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

    //GET transfer labour payment
    public function getTransferLabourPaymentAPI(Request $req)
    {

        $a_id = $req->get('u_id');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

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

    public function trLabourPaymentDeleteAPI(Request $req)
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


    public function postTravelExpenseAPI(Request $req)
    {
        $a_id = $req->get('u_id');
        $edit_id = $req->get('edit_id');
        $exp_so=isset($_POST['exp_so']) ? $_POST['exp_so'] : "NA";
    	$mode_travel=isset($_POST['mode_travel']) ? $_POST['mode_travel'] : "NA";
        $from_location=isset($_POST['from_location']) ? $_POST['from_location'] : "NA";
    	$to_location=isset($_POST['to_location']) ? $_POST['to_location'] : "NA";
    	$total_km=isset($_POST['total_km']) ? $_POST['total_km'] : "NA";
        $travel_date=isset($_POST['travel_date']) ? $_POST['travel_date'] : "NA";
        $travel_desc=isset($_POST['travel_desc']) ? $_POST['travel_desc'] : "NA";
    	$travel_amount=isset($_POST['travel_amnt']) ? $_POST['travel_amnt'] : "NA";
    	$no_of_person=isset($_POST['no_of_person']) ? $_POST['no_of_person'] : "NA";
        $photo_path_ext=isset($_POST['photo_path_ext']) ? $_POST['photo_path_ext'] : null;
        $photo_path = $req->input('attachment') ?$req->input('attachment'): '';

        // For File Decoder 
        $destinationPath = 'files/user/travel_expense/';
        if($photo_path!="" && str_contains($photo_path, '+'))
        {         
            $img = str_replace('data:image/jpg;base64,', '', $photo_path);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            // $image_id= uniqid();
            $filename= $photo_path_ext."_".md5($photo_path. microtime()).'.'.$photo_path_ext;

            file_put_contents($destinationPath.$filename, $data);
            
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
                if($photo_path!="" && str_contains($photo_path, '+'))
                {
                    $u_obj->attachment=$filename;
                }
                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                
                if($res){
                    return ['status' => true, 'message' => 'Travel Expense Updated Successfully'];
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
                if($photo_path!="" && str_contains($photo_path, '+'))
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

    public function getTravelExpenseAPI(Request $req)
    {

        $a_id = $req->get('u_id');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
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


            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{


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


           
            if(count($data)>0){
                return json_encode(array('status' => true ,'data' => $data,'s_obj' => $s_obj,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }
        }
    }

    public function deleteTravelExpenseAPI(Request $req)
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


    public function incomeListAPI(Request $req)
    {
    	// $a_id=Session::get('USER_ID');
        $a_id = $req->get('u_id');
        // $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
        // $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->orderby('updated_at','DESC')->get();
        // $s_obj=SOModel::where(['delete'=>0,'lead_technician'=>$a_id])->orderby('created_at','DESC')->get();
        // $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();

        // $s_obj1=DB::table('oa_tl_history as oth')
        // ->leftjoin('users as u','u.id','oth.lead_technician')
        // ->leftjoin('sales_orders as so','so.id','oth.so_id')
        // ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
        // ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
        // ->orderby('oth.updated_at','DESC')
        // ->get();
        
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
        // //get so number payment wise
        // foreach($l_obj as $l){

        //     $so_id = array_map('intval', explode(',', $l->so_id));
        //     foreach($so_id as $s){
        //         $so_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->get();
        //     }
        //     $l->s_obj = $so_obj;
        // }

        if(!empty($u_obj)){
            return json_encode(array('status' => true ,'data' => $u_obj,'accountant_payment'=> $accountant_payment,'fot'=> $fot,'total_wallet'=> $total_wallet,'technician_expenses'=> $technician_expenses,'ttot'=> $ttot,'total_expense'=> $total_expense,'cleared_pay'=> $cleared_pay,'uncleared_pay'=> $uncleared_pay,'balance'=> $balance,'total_tech_expense'=> $total_tech_expense,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }
        
    }

   
    public function getAccPaymentAPI(Request $req)
    {


        $a_id = $req->get('u_id');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {


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

            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();

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

    public function getOtTechPaymentAPI(Request $req)
    {

        $a_id = $req->get('u_id');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {

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



            $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$a_id])->orderby('created_at','DESC')->get();
            $l_obj = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u_obj[0]->id])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            // $data = TransferPaymentModel::where(['delete'=>0,'u_id'=>$a_id])->orderby('updated_at','DESC')->get();
    
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
}
