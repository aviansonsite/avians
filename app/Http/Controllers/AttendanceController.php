<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\SOModel;
use App\Models\PunchInOutModel;
use App\Models\OATLHistoryModel;
use Carbon\Carbon;
use Session;
use Hash;
use File;
use DB;

class AttendanceController extends Controller
{
    public function attendanceList()
    {
        $a_idd [] =Session::get('USER_ID');
        $a_id =Session::get('USER_ID');

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();

        //for history modal
        $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
    	// $s_obj=SOModel::where('lead_technician', 'LIKE', '%'.$a_id.'%')->where(['delete'=>0,])->orderby('created_at','DESC')->get();
    	// $s_obj=SOModel::whereIn('lead_technician',$a_idd)->where(['delete'=>0])->orderby('created_at','DESC')->get();

        $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.lead_technician'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

        $s_obj1=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();


        // dd($s_obj);
        $tdate=date("Y-m-d");

        $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate,'pin_u_id'=>$a_id])->orderby('updated_at','DESC')->count();
        $p_obj=PunchInOutModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();
        // dd($p_obj);
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
        
    	// return view('labour.test',compact('u_obj','s_obj'));
    	return view('labour.attendance',compact('u_obj','s_obj','s_obj1','t_count','p_obj','p_id','us_obj'));

    }

    public function store(Request $request)
    {
        $img = $request->image;
        $folderPath = public_path('files/attendance/punchIn/');
        
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.png';
        
        $file = $folderPath . $fileName;

        file_put_contents($file, $image_base64);

        // Storage::put($file, $image_base64);
        
        dd('Image uploaded successfully: '.$fileName);
    }

    public function punchIn(Request $req)
    {
    	$a_id=Session::get('USER_ID');
        $p_in_so=isset($_POST['p_in_so']) ? $_POST['p_in_so'] : "NA";
    	$p_in_labour=isset($_POST['p_in_labour']) ? $_POST['p_in_labour'] : "NA";
    	$p_in_remark=isset($_POST['p_in_remark']) ? $_POST['p_in_remark'] : "NA";
    	$p_in_date=isset($_POST['p_in_date']) ? $_POST['p_in_date'] : "NA";
        $p_in_latitude=isset($_POST['p_in_latitude']) ? $_POST['p_in_latitude'] : "NA";
    	$p_in_longitude=isset($_POST['p_in_longitude']) ? $_POST['p_in_longitude'] : "NA";

        $u_id = strval($a_id); 
        array_push($p_in_labour, $u_id);    //Push user id for attendance
        $p_in_labour = array_unique($p_in_labour);
        $tech_count = count($p_in_labour);
        
        // $p_in_so=implode(',',$p_in_so);
        // $p_in_labour=implode(',',$p_in_labour);

        if ($p_in_latitude !='' &&  $p_in_longitude !='') 
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

                    $img = $req->pin_img;                        //get image
                    $folderPath = public_path('files/attendance/punchIn/');     // folder path
                    
                    $image_parts = explode(";base64,", $img);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    
                    $image_base64 = base64_decode($image_parts[1]);

                    $fileName= '.png'."_".md5($img. microtime()).'.png';
                    // $fileName = uniqid() . '.png';
                    $file = $folderPath . $fileName;
                    file_put_contents($file, $image_base64);        //move to specific folder

                $u_obj->pin_img=$fileName;
                $res=$u_obj->save();
                $j++;
            }
            if($res){
                Session::put('SUCCESS_MESSAGE', 'Punch In Successfully...!');
            }else{
                Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
            }
           
        }else{
            Session::put('SUCCESS_MESSAGE', 'Please Try Again..');
        } 
        return redirect()->back();

    }

    public function punchOut(Request $req)
    {

        $a_id=Session::get('USER_ID');
        $pin_id=!empty($_POST['pin_id']) ? $_POST['pin_id'] : "" ;                   //punch in today id
        $pout_so=isset($_POST['pout_so']) ? $_POST['pout_so'] : "NA";
    	$pout_labour=isset($_POST['pout_labour']) ? $_POST['pout_labour'] : "NA";
    	$pout_remark=isset($_POST['pout_remark']) ? $_POST['pout_remark'] : "NA";
    	$pout_work_desc=isset($_POST['pout_work_desc']) ? $_POST['pout_work_desc'] : "NA";
    	$pout_date=isset($_POST['pout_date']) ? $_POST['pout_date'] : "NA";
        $pout_latitude=isset($_POST['pout_latitude']) ? $_POST['pout_latitude'] : "NA";
    	$pout_longitude=isset($_POST['pout_longitude']) ? $_POST['pout_longitude'] : "NA";
        $u_id = strval($a_id); 
        array_push($pout_labour, $u_id);    //Push user id for attendance
        $pout_labour = array_unique($pout_labour);
        $tech_count = count($pout_labour);
        // dd($tech_count);

        // $pout_so=implode(',',$pout_so);
        // $pout_labour=implode(',',$pout_labour);

        $of1=null;

        if($req->hasfile('work_attachment'))
        {
            // dd($req->hasfile('work_attachment'));
            
            $work_attachment = $req->file('work_attachment');
            // $of1 = rand(1,999).'.'.$work_attachment->getClientOriginalExtension(); 
            $fileExtension = $work_attachment->getClientOriginalExtension();
            $of1 = $fileExtension.'_'.md5($work_attachment. microtime()).'.'.$fileExtension; 

            // dd($of1);
            $work_attachment->move(public_path('files/attendance/workAttachments/'), $of1);
            // $u_obj->work_attachment=$of1;
            // $u_obj->update(['work_attachment' => $of1]);

        }
        if ($pout_latitude !='' && $pout_longitude !='') 
        {
            $j=0;
            for ($i=1; $i <= count($pout_labour); $i++)
            {  
                $check=PunchInOutModel::where(['pin_u_id'=>$pout_labour[$j],'pin_date'=>$pout_date])->get();

                if(count($check) > 0){
                    $u_obj=PunchInOutModel::where(['pin_u_id'=>$pout_labour[$j],'pin_date'=>$pout_date]);
                    $img = $req->pout_img;                        //get image
                    if($img != ""){

                        $folderPath = public_path('files/attendance/punchOut/');     // folder path
                    
                        $image_parts = explode(";base64,", $img);
                        $image_type_aux = explode("image/", $image_parts[0]);
                        $image_type = $image_type_aux[1];
                        
                        $image_base64 = base64_decode($image_parts[1]);
                        $fileName= 'png'."_".md5($img. microtime()).'.png';
                        // $fileName = uniqid() . '.png';
                        $file = $folderPath . $fileName;
                        file_put_contents($file, $image_base64);        //move to specific folder

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
                            'pout_img' => $fileName,
                            'work_attachment' => $of1,
                        ]);


                        
		
                        

                    }else{
                        Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                    }
                    
                }else{

                    $img = $req->pout_img;                        //get image
                    if($img != ""){
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

                            $folderPath = public_path('files/attendance/punchOut/');     // folder path
                            
                            $image_parts = explode(";base64,", $img);
                            $image_type_aux = explode("image/", $image_parts[0]);
                            $image_type = $image_type_aux[1];
                            
                            $image_base64 = base64_decode($image_parts[1]);

                            $fileName= 'png'."_".md5($img. microtime()).'.png';

                            // $fileName = uniqid() . '.png';
                            $file = $folderPath . $fileName;
                            file_put_contents($file, $image_base64);        //move to specific folder

                        $u_obj->pout_img=$fileName;
                        $res=$u_obj->save();
                    }else{
                        Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                    }
                }
                $j++;
            }    
            
            if($u_obj){
                Session::put('SUCCESS_MESSAGE', 'Punch Out Successfully...!');
            }else{
                Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
            }
           
        }else{
            Session::put('SUCCESS_MESSAGE', 'Please Try Again..');
        } 

        return redirect()->back();

    }

    //get punch in out records
    public function getPIORecords(Request $req)
    {
        $a_id=Session::get('USER_ID');
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

            $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate])->orderby('updated_at','DESC')->count();
            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$a_id])->orderby('updated_at','DESC')->get();

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
                $p->pin_time=$p->created_at->format('H:i:s');          
            }

            if(!empty($p_obj)){
                return json_encode(array('status' => true ,'data' => $p_obj,'u_obj' => $u_obj,'s_obj' => $s_obj ,'message' => 'Data Found'));
            }else{
            return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

            
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

            $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate])->orderby('updated_at','DESC')->count();
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

    public function technicianAttendance()
    {
        $a_idd [] =Session::get('USER_ID');
        $a_id =Session::get('USER_ID');
        $role =Session::get('ROLES');

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
    	// $s_obj=SOModel::whereIn('labour',$a_idd)->where(['delete'=>0])->orderby('created_at','DESC')->get();
       
        $s_obj1=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
                ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
                ->orderby('oth.updated_at','DESC')
                ->get();

        if($role == 0 || $role == 2){
            //only super admin and accountant can access all OA Records

    	    // $s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();
            $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        }else{
            //only project admin wise OA Records access

    	    // $s_obj=SOModel::where(['delete'=>0,'a_id'=>$a_id])->orderby('created_at','DESC')->get();

            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.a_id','so.project_name','so.client_name','so.address','u.name','u.delete as u_delete','u.is_active')
            ->where(['so.a_id'=>$a_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();
            $us_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
        }


        $tdate=date("Y-m-d");

        $t_count=PunchInOutModel::where(['delete'=>0,'pin_date'=>$tdate])->orderby('updated_at','DESC')->count();
        $p_obj=PunchInOutModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();

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
        
    	// return view('labour.test',compact('u_obj','s_obj'));
    	return view('report.technicianAttendanceReport',compact('u_obj','s_obj','s_obj1','us_obj','t_count','p_obj','p_id'));

    }

    //get punch in out records
    public function techAttRecord(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $so_id = $req->get('so_id'); 
        $labours = $req->get('labours');
        

        // $so_id1 = implode(",",$so_id);
        $tdate=date("Y-m-d");

        if($so_id[0] == "all"){


            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            if(count($p_obj) > 0){
                //punch out records
                    // $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                // $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                foreach($p_obj as $p){
                    $startTime=$p->created_at;
                    $finishTime=$p->updated_at;
        
                    // $totalDuration = $finishTime->diffInMinutes($startTime);
                    $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                    $p->totalDuration=$totalDuration;
                    $p->pin_time=$p->created_at->format('H:i:s');          
                    $p->pout_time=$p->updated_at->format('H:i:s');  
        
                    $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $p->technician_name = $u->name;
                    }
                    
                    $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
        
                    foreach($tl_u_obj as $tl){
                        $p->tl_name = $tl->name;
                    }
        
                    $so_id = explode(",",$p->pin_so_id);
        
                    $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
                    $p->s_obj = $s_obj;

                }

            }else{

                //punch in records
                $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                foreach($p_obj as $p){
                    $startTime=$p->created_at;
                    $finishTime=$p->updated_at;
        
                    // $totalDuration = $finishTime->diffInMinutes($startTime);
                    $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                    $p->totalDuration=$totalDuration;
                    $p->pin_time=$p->created_at->format('H:i:s');          
                    $p->pout_time=$p->updated_at->format('H:i:s');  
        
                    $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $p->technician_name = $u->name;
                    }
                    
                    $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
        
                    foreach($tl_u_obj as $tl){
                        $p->tl_name = $tl->name;
                    }
        
                    $so_id = explode(",",$p->pout_so_id);
        
                    $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
                    $p->s_obj = $s_obj;
                }
            }


        }else{

            $startDate = Carbon::parse($from_date);
            $endDate = Carbon::parse($to_date);

            // Create a date range using the start date, end date, and interval
            $dateRange = Carbon::parse($startDate)->daysUntil($endDate);
            $data= array(); //create empty array
            // Loop through the date range and display each date
            foreach ($dateRange as $date) 
            {
                $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours,'pin_date'=>$date,'pin_oth_id'=>$so_id])->orderby('updated_at','DESC')->get();

                if(count($p_obj) > 0)
                {

                    //for punch in regularies

                    // $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
                    foreach($p_obj as $p){
                        $startTime=$p->created_at;
                        $finishTime=$p->updated_at;
            
                        // $totalDuration = $finishTime->diffInMinutes($startTime);
                        $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                        $p->totalDuration=$totalDuration;
                        $p->pin_time=$p->created_at->format('H:i:s');          
                        $p->pout_time=$p->updated_at->format('H:i:s');  
            
                        $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $p->technician_name = $u->name;
                        }
                        $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
            
                        foreach($tl_u_obj as $tl){
                            $p->tl_name = $tl->name;
                        }
            
                        // $so_id = explode(",",$p->pin_so_id);
            
                        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0,])->orderby('created_at','DESC')->get();
                        // $p->s_obj = $s_obj;

                        $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                        
                        $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                        $p->s_obj = $s_obj;
                        
                    }

                    if(count($p_obj) > 0)
                    {
                        foreach($p_obj as $p)
                        {
                            array_push($data,$p);
                        }
                    }
                }
                else
                {
                    
                    // for punch out regularies
                    $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours,'pout_date'=>$date,'pout_oth_id'=>$so_id])->orderby('updated_at','DESC')->get();

                    foreach($p_obj as $p){
                        $startTime=$p->created_at;
                        $finishTime=$p->updated_at;
            
                        // $totalDuration = $finishTime->diffInMinutes($startTime);
                        $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                        $p->totalDuration=$totalDuration;
                        $p->pin_time=$p->created_at->format('H:i:s');          
                        $p->pout_time=$p->updated_at->format('H:i:s');  
            
                        $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $p->technician_name = $u->name;
                        }
                        $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
            
                        foreach($tl_u_obj as $tl){
                            $p->tl_name = $tl->name;
                        }
            
            
                        // $so_id = explode(",",$p->pin_so_id);
                        $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                        
                        $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                        $p->s_obj = $s_obj;
                    }

                    if(count($p_obj) > 0)
                    {
                        foreach($p_obj as $p)
                        {
                            array_push($data,$p);
                        }
                    }
                }
            }
        }

        
        if(count($data)>0){
            return json_encode(array('status' => true ,'data' => $data,'fdate' =>$from_date ,'labours' =>$labours,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }

    }

    public function getLabour(Request $req)
    {
        $so_id = $req->get('so_id');
        $u_id="";
        // if($so_id[0] == 'all'){
            
        //     $data=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        //     if(!empty($data)){
        //         return json_encode(array('status' => true ,'data' => $data));
        //     }else{
        //         return ['status' => false, 'message' => 'No Data Found'];
        //     }

        // }else{
            
            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.a_id','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.id'=>$so_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

            $u_id1 = explode(",",$s_obj[0]->labour);
            array_push($u_id1,$s_obj[0]->lead_technician);
            // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();

            // $u_id1 = [];
            // foreach($s_obj as $s){
            //     $u_id = array_map('intval', explode(',', $s->labour));
                
            //     foreach($u_id as $u){

            //         array_push($u_id1, $u); 
            //     }
            // }

            $data=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->whereIn('id',$u_id1)->orderby('created_at','DESC')->get();

            // $data=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
            
            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'so_id'=>$so_id,'s_obj'=>$s_obj,'u_id'=>$u_id));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        // }
        
    }

    public function getPoutHLabour(Request $req)
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
            ->where(['oth.id'=>$pout_oth_id,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
        if(!empty($data)){
           return json_encode(array('status' => true ,'data' => $data,'l_obj'=>$l_obj,'s_obj'=>$s_obj,'pout_oth_id'=>$pout_oth_id));
        }else{
           return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    public function getPinHLabour(Request $req)
    {
        $pin_date = $req->get('pin_date');
        $pin_oth_id = $req->get('pin_oth_id');

        $a_id =Session::get('USER_ID');
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
        ->where(['oth.id'=>$pin_oth_id,'u.delete'=>0,'u.is_active'=>0])
        ->orderby('oth.updated_at','DESC')
        ->get();

     
        // $s_obj=SOModel::where(['delete'=>0,'id'=>$so_id])->orderby('created_at','DESC')->get();
        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
        if(!empty($data)){
           return json_encode(array('status' => true ,'data' => $data,'l_obj'=>$l_obj,'s_obj'=>$s_obj,'pin_oth_id'=>$pin_oth_id));
        }else{
           return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    //regularice attendance
    public function regulariseAttendance(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $so_id = $req->get('so_id'); 
        $labours = $req->get('labours');
        $reg_remark = $req->get('reg_remark'); 
        $reg_status = $req->get('reg_status');
        $reg_id = $req->get('reg_id');
        $reg_tl_id = $req->get('reg_tl_id');
        $ptype = $req->get('ptype');
        $reg_tech_date = $req->get('reg_tech_date');


        if($ptype == "pout_record"){
            if($reg_id!=null && $reg_remark!="" && $reg_status!="")
            {
                $reg_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$reg_tl_id,'pin_oth_id'=>$so_id,'pin_date'=>$reg_tech_date])->orderby('updated_at','DESC')->get();
                
                foreach($reg_obj as $r){
                    $u_obj1=PunchInOutModel::where('id',$reg_id)->get();
                    $u_obj=PunchInOutModel::where('id',$reg_id)
                    ->update([
                        'updated_at' => $r->updated_at,
                        'regular_remark' => $reg_remark,
                        'reg_status' => $reg_status,
                        'reg_admin_id' => $a_id,
                        'pout_u_id' => $u_obj1[0]->pin_u_id,
                        'pout_oth_id' => $r->pout_oth_id, 
                        'pout_remark' => $r->pout_remark,
                        'pout_work_desc' => $r->pout_work_desc,
                        'pout_date' => $r->pout_date, 
                        'pout_latitude' => $r->pout_latitude,
                        'pout_longitude' => $r->pout_longitude,
                        'delete' => 0, 
                        'pout_img' => $r->pout_img,
                    ]);
                }
                // return json_encode(array('status' => true ,'reg_obj' => $reg_obj,'reg_id' =>$reg_id ,'so_id' =>$so_id,'reg_tech_date'=>$reg_tech_date,'message' => 'testing'));
                
                // $u_obj=PunchInOutModel::find($reg_id);
                // $u_obj->reg_remark=$reg_remark;
                // $u_obj->reg_status=$reg_status;
                // $u_obj->delete=0;
                // $u_obj->reg_admin_id=$a_id;
                // $res=$u_obj->update();
    
            }
    
        }else{
            // for punchin record regularised

            $reg_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$reg_tl_id,'pout_oth_id'=>$so_id,'pout_date'=>$reg_tech_date])->orderby('updated_at','DESC')->get();
                
                foreach($reg_obj as $r){
                    $u_obj1=PunchInOutModel::where('id',$reg_id)->get();
                    $u_obj=PunchInOutModel::where('id',$reg_id)
                    ->update([
                        'regular_remark' => $reg_remark,
                        'reg_status' => $reg_status,
                        'reg_admin_id' => $a_id,
                        'pin_u_id' => $u_obj1[0]->pout_u_id,
                        'pin_oth_id' => $r->pin_oth_id, 
                        'pin_remark' => $r->pin_remark,
                        'pin_date' => $r->pin_date, 
                        'pin_latitude' => $r->pin_latitude,
                        'pin_longitude' => $r->pin_longitude,
                        'delete' => 0, 
                        'pin_img' => $r->pin_img,
                        'created_at' => $r->created_at,
                        'updated_at' => $r->updated_at,
                    ]);
                }
        }
      
        $tdate=date("Y-m-d");


        if($so_id[0] == "all"){


            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            if(count($p_obj) > 0){
                //punch out records
                    // $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                // $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                foreach($p_obj as $p){
                    $startTime=$p->created_at;
                    $finishTime=$p->updated_at;
        
                    // $totalDuration = $finishTime->diffInMinutes($startTime);
                    $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                    $p->totalDuration=$totalDuration;
                    $p->pin_time=$p->created_at->format('H:i:s');          
                    $p->pout_time=$p->updated_at->format('H:i:s');  
        
                    $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $p->technician_name = $u->name;
                    }
                    
                    $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
        
                    foreach($tl_u_obj as $tl){
                        $p->tl_name = $tl->name;
                    }
        
                    $so_id = explode(",",$p->pin_so_id);
        
                    $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
                    $p->s_obj = $s_obj;
                }
            }else{

                //punch in records
                $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                foreach($p_obj as $p){
                    $startTime=$p->created_at;
                    $finishTime=$p->updated_at;
        
                    // $totalDuration = $finishTime->diffInMinutes($startTime);
                    $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                    $p->totalDuration=$totalDuration;
                    $p->pin_time=$p->created_at->format('H:i:s');          
                    $p->pout_time=$p->updated_at->format('H:i:s');  
        
                    $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $p->technician_name = $u->name;
                    }
                    
                    $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
        
                    foreach($tl_u_obj as $tl){
                        $p->tl_name = $tl->name;
                    }
        
                    $so_id = explode(",",$p->pout_so_id);
        
                    $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
                    $p->s_obj = $s_obj;
                }
            }


        }else{
            $startDate = Carbon::parse($from_date);
            $endDate = Carbon::parse($to_date);

            // Create a date range using the start date, end date, and interval
            $dateRange = Carbon::parse($startDate)->daysUntil($endDate);
            $data= array(); //create empty array
            // Loop through the date range and display each date
            foreach ($dateRange as $date) 
            {
                $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours,'pin_date'=>$date,'pin_oth_id'=>$so_id])->orderby('updated_at','DESC')->get();

                if(count($p_obj) > 0)
                {

                    //for punch in regularies

                    // $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
                    foreach($p_obj as $p){
                        $startTime=$p->created_at;
                        $finishTime=$p->updated_at;
            
                        // $totalDuration = $finishTime->diffInMinutes($startTime);
                        $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                        $p->totalDuration=$totalDuration;
                        $p->pin_time=$p->created_at->format('H:i:s');          
                        $p->pout_time=$p->updated_at->format('H:i:s');  
            
                        $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $p->technician_name = $u->name;
                        }
                        $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
            
                        foreach($tl_u_obj as $tl){
                            $p->tl_name = $tl->name;
                        }
            
                        // $so_id = explode(",",$p->pin_so_id);
            
                        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0,])->orderby('created_at','DESC')->get();
                        // $p->s_obj = $s_obj;

                        $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                        
                        $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                        $p->s_obj = $s_obj;
                        
                    }

                    if(count($p_obj) > 0)
                    {
                        foreach($p_obj as $p)
                        {
                            array_push($data,$p);
                        }
                    }
                }
                else
                {
                    
                    // for punch out regularies
                    $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours,'pout_date'=>$date,'pout_oth_id'=>$so_id])->orderby('updated_at','DESC')->get();

                    foreach($p_obj as $p){
                        $startTime=$p->created_at;
                        $finishTime=$p->updated_at;
            
                        // $totalDuration = $finishTime->diffInMinutes($startTime);
                        $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                        $p->totalDuration=$totalDuration;
                        $p->pin_time=$p->created_at->format('H:i:s');          
                        $p->pout_time=$p->updated_at->format('H:i:s');  
            
                        $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $p->technician_name = $u->name;
                        }
                        $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
            
                        foreach($tl_u_obj as $tl){
                            $p->tl_name = $tl->name;
                        }
            
            
                        // $so_id = explode(",",$p->pin_so_id);
                        $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                        
                        $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                        $p->s_obj = $s_obj;
                    }

                    if(count($p_obj) > 0)
                    {
                        foreach($p_obj as $p)
                        {
                            array_push($data,$p);
                        }
                    }
                }
            }
        }

        if(!empty($data)){
            return json_encode(array('status' => true ,'data' => $data,'fdate' =>$from_date ,'labours' =>$labours,'reg_obj'=>$reg_obj,'message' => 'Technician Record Regularised Successfully'));
        }else{
            return ['status' => false, 'message' => 'Technician Record Regularised UnSuccessfully'];
        }
          

    }

    //get punch in out records
    public function updateTechTime(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $so_id = $req->get('so_id'); 
        $labours = $req->get('labours');

        $reg_date = $req->get('reg_date');
        $reg_labours = $req->get('reg_labours'); 
        $reg_remark = $req->get('reg_remark');
        $punchInTime = $req->get('punchInTime'); 
        $punchOutTime = $req->get('punchOutTime');


        if($reg_labours[0] == "all"){

            $s_obj=DB::table('oa_tl_history as oth')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','oth.updated_at','so.delete','so.labour','so.so_number','so.a_id','u.name','u.delete as u_delete','u.is_active')
            ->where(['oth.id'=>$so_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();
    
            $reg_labours = explode(",",$s_obj[0]->labour);
            array_push($reg_labours,$s_obj[0]->lead_technician);
        }

        if($punchInTime != null)
        {
            // Convert the string to a Carbon instance
            $carbonDate = Carbon::parse($punchInTime);
            // Format the date and time as desired (e.g., 'Y-m-d H:i:s')
            // $punchInTimeDate = $carbonDate->format('Y-m-d H:i:s');

            $punchInDate = $carbonDate->format('Y-m-d');

            if($punchInDate != $reg_date){
                return ['status' => false, 'message' => 'Punch In Date is invalid'];
            }

        }

        if($punchOutTime != null)
        {
            // Convert the string to a Carbon instance
            $carbonDate = Carbon::parse($punchOutTime);

            // Format the date and time as desired (e.g., 'Y-m-d H:i:s')
            // $punchOutTimeDate = $carbonDate->format('Y-m-d H:i:s');
            $punchOutDate = $carbonDate->format('Y-m-d');

            if($punchOutDate != $reg_date){
                return ['status' => false, 'message' => 'Punch Out Date is invalid'];
            }

        }


        $j=0;
        for ($i=1; $i <= count($reg_labours); $i++)
        {  
            $check=PunchInOutModel::where(['pin_u_id'=>$reg_labours[$j],'pin_date'=>$reg_date,'pin_oth_id'=>$so_id])->get();

            if(count($check) > 0){
                               
                $tl_data=PunchInOutModel::where(['pin_u_id'=>$labours,'pin_date'=>$reg_date,'pin_oth_id'=>$so_id])->get();

                // return json_encode(array('status' => true ,'reg_labours' => $reg_labours,'reg_remark' =>$reg_remark ,'punchInTime' => $punchInTime,'tl_data' =>$tl_data,'message' => 'Data Found'));

                $u_obj=PunchInOutModel::where(['pin_u_id'=>$reg_labours[$j],'pin_date'=>$reg_date]);

                $u_obj->update([
                    'pin_u_id' => $reg_labours[$j],
                    'pin_oth_id' => $so_id, 
                    'pin_remark' => $tl_data[0]->pin_remark,
                    'pin_date' => $tl_data[0]->pin_date, 
                    'pin_latitude' => $tl_data[0]->pin_latitude,
                    'pin_longitude' => $tl_data[0]->pin_longitude,
                    'pin_img' => $tl_data[0]->pin_img,
                    'pout_u_id' => $reg_labours[$j],
                    'pout_oth_id' => $tl_data[0]->pout_oth_id, 
                    'pout_remark' => $tl_data[0]->pout_remark,
                    'pout_work_desc' => $tl_data[0]->pout_work_desc,
                    'pout_date' => $tl_data[0]->pout_date, 
                    'pout_latitude' => $tl_data[0]->pout_latitude,
                    'pout_longitude' => $tl_data[0]->pout_longitude,
                    'pout_img' => $tl_data[0]->pout_img,
                    'regular_remark' => $reg_remark,
                    'reg_admin_id' => $a_id, 
                    'reg_status' => 'Confirm',
                    'delete' => 0,
                ]);

                if($punchInTime != null){
                    $u_obj->update([
                        'created_at' => $punchInTime, 
                    ]);
                }

                if($punchOutTime != null){
                    $u_obj->update([
                        'updated_at' => $punchOutTime,
                    ]);
                }

                
                
                // $u_obj->regular_remark=$reg_remark;
                // $u_obj->reg_admin_id=$a_id;
                // $u_obj->reg_status= 'Confirm';
                // $res=$u_obj->save();

                

            }else{
                             
                // if($punchInTime != null)
                // {
                //     // Convert the string to a Carbon instance
                //     $carbonDate = Carbon::parse($punchInTime);
                //     // Format the date and time as desired (e.g., 'Y-m-d H:i:s')
                //     $punchInTimeDate = $carbonDate->format('Y-m-d H:i:s');
                // }else{
                //     $punchInTimeDate=PunchInOutModel::where(['pout_u_id'=>$reg_labours[$j],'pout_date'=>$reg_date])->get('created_at');
                // }

                // if($punchInTime != null)
                // {
                //     // Convert the string to a Carbon instance
                //     $carbonDate = Carbon::parse($punchOutTime);

                //     // Format the date and time as desired (e.g., 'Y-m-d H:i:s')
                //     $punchOutTimeDate = $carbonDate->format('Y-m-d H:i:s');
                // }else{
                //     $punchOutTimeDate=PunchInOutModel::where(['pout_u_id'=>$reg_labours[$j],'pout_date'=>$reg_date])->get('updated_at');
                // }
                $tl_data=PunchInOutModel::where(['pin_u_id'=>$labours,'pin_date'=>$reg_date,'pin_oth_id'=>$so_id])->get();
                
                $u_obj=PunchInOutModel::where(['pout_u_id'=>$reg_labours[$j],'pout_date'=>$reg_date,'pout_oth_id'=>$so_id]); 

                $u_obj->update([
                    'pin_u_id' => $reg_labours[$j],
                    'pin_oth_id' => $so_id, 
                    'pin_remark' => $tl_data[0]->pin_remark,
                    'pin_date' => $tl_data[0]->pin_date, 
                    'pin_latitude' => $tl_data[0]->pin_latitude,
                    'pin_longitude' => $tl_data[0]->pin_longitude,
                    'pin_img' => $tl_data[0]->pin_img,
                    'pout_u_id' => $reg_labours[$j],
                    'pout_oth_id' => $tl_data[0]->pout_oth_id, 
                    'pout_remark' => $tl_data[0]->pout_remark,
                    'pout_work_desc' => $tl_data[0]->pout_work_desc,
                    'pout_date' => $tl_data[0]->pout_date, 
                    'pout_latitude' => $tl_data[0]->pout_latitude,
                    'pout_longitude' => $tl_data[0]->pout_longitude,
                    'pout_img' => $tl_data[0]->pout_img,
                    'regular_remark' => $reg_remark,
                    'reg_admin_id' => $a_id, 
                    'reg_status' => 'Confirm',
                    'delete' => 0,
                ]);

                if($punchInTime != null){
                    $u_obj->update([
                        'created_at' => $punchInTime, 
                    ]);
                }

                if($punchOutTime != null){
                    $u_obj->update([
                        'updated_at' => $punchOutTime,
                    ]);
                }

               


                // $u_obj->update([
                //     'created_at' => $punchInTimeDate, 
                //     'updated_at' => $punchOutTimeDate,
                //     'regular_remark' => $reg_remark,
                //     'reg_admin_id' => $a_id, 
                //     'reg_status' => 'Confirm',
                // ]);
            }
            $j++;
        }    

        if($so_id[0] == "all"){


            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

            if(count($p_obj) > 0){
                //punch out records
                    // $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                // $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                foreach($p_obj as $p){
                    $startTime=$p->created_at;
                    $finishTime=$p->updated_at;
        
                    // $totalDuration = $finishTime->diffInMinutes($startTime);
                    $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                    $p->totalDuration=$totalDuration;
                    $p->pin_time=$p->created_at->format('H:i:s');          
                    $p->pout_time=$p->updated_at->format('H:i:s');  
        
                    $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $p->technician_name = $u->name;
                    }
                    
                    $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
        
                    foreach($tl_u_obj as $tl){
                        $p->tl_name = $tl->name;
                    }
        
                    // $so_id = explode(",",$p->pin_so_id);
        
                    // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();


                    $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                    
                    $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                    $p->s_obj = $s_obj;

                }

            }else{

                //punch in records
                $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

                foreach($p_obj as $p){
                    $startTime=$p->created_at;
                    $finishTime=$p->updated_at;
        
                    // $totalDuration = $finishTime->diffInMinutes($startTime);
                    $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                    $p->totalDuration=$totalDuration;
                    $p->pin_time=$p->created_at->format('H:i:s');          
                    $p->pout_time=$p->updated_at->format('H:i:s');  
        
                    $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                    foreach($u_obj as $u){
                        $p->technician_name = $u->name;
                    }
                    
                    $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
        
                    foreach($tl_u_obj as $tl){
                        $p->tl_name = $tl->name;
                    }
        
                    // $so_id = explode(",",$p->pout_so_id);
        
                    // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();

                    $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                    
                    $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                    $p->s_obj = $s_obj;
                }
            }


        }else{


            $startDate = Carbon::parse($from_date);
            $endDate = Carbon::parse($to_date);

            // Create a date range using the start date, end date, and interval
            $dateRange = Carbon::parse($startDate)->daysUntil($endDate);
            $data= array(); //create empty array

            // Loop through the date range and display each date
            foreach ($dateRange as $date) 
            {
                $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours,'pin_date'=>$date,'pin_oth_id'=>$so_id])->orderby('updated_at','DESC')->get();

                if(count($p_obj) > 0)
                {

                    //for punch in regularies

                    // $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
                    foreach($p_obj as $p){
                        $startTime=$p->created_at;
                        $finishTime=$p->updated_at;
            
                        // $totalDuration = $finishTime->diffInMinutes($startTime);
                        $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                        $p->totalDuration=$totalDuration;
                        $p->pin_time=$p->created_at->format('H:i:s');          
                        $p->pout_time=$p->updated_at->format('H:i:s');  
            
                        $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $p->technician_name = $u->name;
                        }
                        $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
            
                        foreach($tl_u_obj as $tl){
                            $p->tl_name = $tl->name;
                        }
            
                        // $so_id = explode(",",$p->pin_so_id);
            
                        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0,])->orderby('created_at','DESC')->get();
                        // $p->s_obj = $s_obj;

                        $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                        
                        $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                        $p->s_obj = $s_obj;
                        
                    }

                    if(count($p_obj) > 0)
                    {
                        foreach($p_obj as $p)
                        {
                            array_push($data,$p);
                        }
                    }
                }
                else
                {
                    
                    // for punch out regularies
                    $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours,'pout_date'=>$date,'pout_oth_id'=>$so_id])->orderby('updated_at','DESC')->get();

                    foreach($p_obj as $p){
                        $startTime=$p->created_at;
                        $finishTime=$p->updated_at;
            
                        // $totalDuration = $finishTime->diffInMinutes($startTime);
                        $totalDuration = $startTime->diff($finishTime)->format('%H:%I:%S');
                        $p->totalDuration=$totalDuration;
                        $p->pin_time=$p->created_at->format('H:i:s');          
                        $p->pout_time=$p->updated_at->format('H:i:s');  
            
                        $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$labours])->orderby('created_at','DESC')->get();
                        foreach($u_obj as $u){
                            $p->technician_name = $u->name;
                        }
                        $tl_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->a_id])->orderby('created_at','DESC')->get();
            
                        foreach($tl_u_obj as $tl){
                            $p->tl_name = $tl->name;
                        }
            
            
                        // $so_id = explode(",",$p->pin_so_id);
                        $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();
                        
                        $s_obj=SOModel::where(['delete'=>0,'id'=>$oth_obj[0]->so_id])->orderby('created_at','DESC')->get();
                        $p->s_obj = $s_obj;
                    }

                    if(count($p_obj) > 0)
                    {
                        foreach($p_obj as $p)
                        {
                            array_push($data,$p);
                        }
                    }
                }
            }
        }

        
        if(count($data)>0){
            return json_encode(array('status' => true ,'data' => $data,'fdate' =>$from_date ,'labours' =>$labours,'message' => 'Data Found'));

            // return json_encode(array('status' => true ,'reg_labours' => $reg_labours,'reg_remark' =>$reg_remark ,'punchInTime' => $punchInTime,'punchOutTime' =>$punchOutTime,'punchInTimeDate'=>$punchInTimeDate ,'daat'=>$daat ,'punchOutTimeDate'=>$punchOutTimeDate ,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }

    }

    //get punch in out records
    public function getRegulariseHistory(Request $req)
    {
        $a_id=Session::get('USER_ID');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');

        if ($from_date == null && $to_date == null) 
        {
            $p_obj=DB::table('punch_in_out as pio')
            ->leftjoin('oa_tl_history as oth','oth.id','pio.pin_oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->leftjoin('users as u','u.id','pio.a_id')
            ->select('pio.id','pio.pin_u_id','pio.pin_date','pio.pout_u_id','pio.regular_remark','pio.reg_status','pio.reg_admin_id','pio.created_at','pio.updated_at','so.delete','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','u.name as lead_tech_name','u.delete as u_delete','u.is_active')
            ->where('pio.reg_admin_id','!=',null)
            ->orderby('pio.updated_at','DESC')
            ->get();

            foreach($p_obj as $p){

                $startTime = Carbon::parse($p->created_at);
                $finishTime = Carbon::parse($p->updated_at);

                // Calculate the difference 
                $duration = $finishTime->diff($startTime);
                $totalDuration = $duration->format('%H:%I:%S');

                $p->totalDuration = $totalDuration;
                $p->pin_time = $startTime->format('H:i:s');
                $p->pout_time = $finishTime->format('H:i:s');
    
                $u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->pin_u_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $p->technician_name = $u->name;
                }

                $ad_u_obj=UserModel::where(['delete'=>0,'is_active'=>0,'id'=>$p->reg_admin_id])->orderby('created_at','DESC')->get();
                foreach($ad_u_obj as $ad){
                    $p->admin_name = $ad->name;
                }

            }

        }else{




        }


        
        if(count($p_obj)>0){
            return json_encode(array('status' => true ,'data' => $p_obj,'fdate' =>$from_date ,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }

    }
}
