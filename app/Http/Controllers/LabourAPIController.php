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

                // if($req->hasfile('attachment'))  
                // {  
                   
                //     try {
                //         // Your file upload and move code here
                //         $file=$req->file('attachment');  
                //         $extension=$file->getClientOriginalExtension();  
                //         $filename= $extension."_".md5($file. microtime()).'.'.$extension;
                //         // $fileName= '.png'."_".md5($file. microtime()).'.png';
                //         $file->move(public_path('files/attendance/punchIn/'), $filename);

                //     } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                //         // Log or display the exception message for further debugging
                //         // echo "File upload error: " . $e->getMessage();
                //         // return ['status' => false, 'message' =>  $e->getMessage()]; 
                //         $u_obj->pin_img=$filename;
                       
                //     }
                //     // $image->image=$filename;  
                    
                // }  

                    // $img = $req->pin_img;                        //get image
                    // $folderPath = public_path('files/attendance/punchIn/');     // folder path
                    
                    // $image_parts = explode(";base64,", $img);
                    // $image_type_aux = explode("image/", $image_parts[0]);
                    // $image_type = $image_type_aux[1];
                    
                    // $image_base64 = base64_decode($image_parts[1]);

                    // $fileName= '.png'."_".md5($img. microtime()).'.png';
                    // $fileName = uniqid() . '.png';
                    // $file = $folderPath . $fileName;
                    // file_put_contents($file, $image_base64);        //move to specific folder

             
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
        // $pin_id=!empty($_POST['pin_id']) ? $_POST['pin_id'] : "" ;                   //punch in today id
        $pout_so=isset($_POST['pout_so']) ? $_POST['pout_so'] : "NA";
    	$pout_labour=isset($_POST['pout_labour']) ? $_POST['pout_labour'] : "NA";
    	$pout_remark=isset($_POST['pout_remark']) ? $_POST['pout_remark'] : "NA";
    	$pout_work_desc=isset($_POST['pout_work_desc']) ? $_POST['pout_work_desc'] : "NA";
    	$pout_date=isset($_POST['pout_date']) ? $_POST['pout_date'] : "NA";
        $pout_latitude=isset($_POST['pout_latitude']) ? $_POST['pout_latitude'] : "NA";
    	$pout_longitude=isset($_POST['pout_longitude']) ? $_POST['pout_longitude'] : "NA";
        $photo_path_ext=isset($_POST['profile_photo_ext']) ? $_POST['profile_photo_ext'] : null;
        $photo_path = $req->input('attachment') ?$req->input('attachment'): '';
        // $photo_path = $req->hasfile('attachment');

        $u_id = strval($a_id); 
        array_push($pout_labour, $u_id);    //Push user id for attendance
        $tech_count = count($pout_labour);
        // dd($tech_count);
        // $pout_so=implode(',',$pout_so);
        // $pout_labour=implode(',',$pout_labour);

        // return ['status' => true,'pout_so' => $pout_so,'pout_labour' => $pout_labour,'pout_remark' => $pout_remark,'pout_work_desc' => $pout_work_desc,'pout_date'=>$pout_date,'pout_latitude' => $pout_latitude,'pout_longitude' => $pout_longitude,'a_id'=>$a_id,'photo_path_ext'=>$photo_path_ext,'photo_path'=>$photo_path]; 

        if ($pout_latitude !='' && $pout_longitude !='') 
        {
            $j=0;
            for ($i=1; $i <= count($pout_labour); $i++)
            {  
                $check=PunchInOutModel::where(['pin_u_id'=>$pout_labour[$j],'pin_date'=>$pout_date])->get();

                if(count($check) > 0){
                    $u_obj=PunchInOutModel::where(['pin_u_id'=>$pout_labour[$j],'pin_date'=>$pout_date]);
                    // $img = $req->pout_img;                        //get image
                    if($photo_path!="" && str_contains($photo_path, '+'))
                    {

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
                            'pout_img' => $fileName,
                        ]);

                    }else{
                        // Session::put('ERROR_MESSAGE', 'Something went wrong. Please try again.');
                        return ['status' => false, 'message' => 'Something went wrong. Please try again.']; 
                    }
                    
                }else{

                    // $img = $req->pout_img;                        //get image
                    if($photo_path!=""){
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

                            $destinationPath = 'files/attendance/punchIn/';
                            if($photo_path!="" && str_contains($photo_path, '+'))
                            {              
                                
                                $img = str_replace('data:image/jpg;base64,', '', $photo_path);
                                $img = str_replace(' ', '+', $img);
                                $data = base64_decode($img);
                                // $image_id= uniqid();
                                $filename= $photo_path_ext."_".md5($photo_path. microtime()).'.'.$photo_path_ext;

                                file_put_contents($destinationPath.$filename, $data);                                
                            }


                        $u_obj->pout_img=$fileName;
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
            ->where(['oth.id'=>$pin_oth_id,'oth.status'=>1,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('oth.updated_at','DESC')
            ->get();

        // $s_obj=SOModel::whereIn('id',$so_id)->where(['delete'=>0])->orderby('created_at','DESC')->get();
        if(!empty($data)){
           return json_encode(array('status' => true ,'data' => $data,'l_obj'=>$l_obj,'s_obj'=>$s_obj));
        }else{
           return ['status' => false, 'message' => 'No Data Found'];
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
        $user_id = $req->get('u_id');

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

    public function postExpenseLPaymentAPI(Request $req)
    {
        $edit_id=$req->get('exp_edit_id');
        $exp_desc = $req->get('exp_desc');
   		$exp_date = $req->get('exp_date');
   		$expense_amnt = $req->get('expense_amnt');
        $exp_type = $req->get('exp_type');
        $user_id = $req->get('u_id');
        // $attachment = $req->get('attachment');
        $oth_id = $req->get('exp_oth_id');
        // $so=implode(',',$sos);

    	// $a_id=Session::get('USER_ID');
        // $user_id = CommonController::decode_ids($user_id);
        $check = UserModel::where('id',$user_id)->exists();
        if($check == true ){


            if($req->hasfile('attachment'))  
            {  
                $file=$req->file('attachment');  
                $extension=$file->getClientOriginalExtension();  
                $filename= $extension."_".md5($file. microtime()).'.'.$extension;

                $file->move(public_path('files/user/expense/'), $filename);
               
                // $image->image=$filename;  
            }  

            
            // For File Decoder 
            // if($attachment!='') 
            // {

            //     // $check = PaymentModel::where('p_id',$project_id)->exists();
            //     // $destinationPath = 'public/files/project-payment/'.$project_id.'/';

            //     // check folder exits or not
            //     // if ($check == false) {
            //     //     $result = File::makeDirectory($destinationPath, 0775, true, true); 
            //     // }

            //     // $destinationPath=public_path('/files/loan-receipt/');   //Folder Path
            //     $image1 = $req->input('attachment');     // encoded File name
            //     $extension = $req->input('payment_extension');       //File Extension  
                
            //     $pattern='/^data:.+;base64,/';

            //     $img = preg_replace($pattern, '', $image1);  //removed $pattern
            //     $img = str_replace(' ', '+', $img);  //for + sign blank space convert
            //     $data = base64_decode($img);       //decode All File
                
            //     $filename= $extension."_".md5($image1. microtime()).'.'.$extension;

            //     // $image_id= uniqid();    // create random name,number
            //     // $file = $image_id . '.'.$extension; // create name for file
            //     // $fp  = $image_id.'.'.$extension;   // send the file to destination path

            //     file_put_contents(public_path('files/user/expense/').$filename,$data); 
            // }

            if($edit_id!=null)
            {
                
                if ($expense_amnt !='' && $exp_date !='' && $exp_type !='') 
                {
                        
                    $u_obj=TechnicianExpenseModel::find($edit_id);
                    $u_obj->exp_type=$exp_type;
                    $u_obj->oth_id=$oth_id;
                    $u_obj->exp_desc=$exp_desc;
                    $u_obj->exp_date=$exp_date;
                    $u_obj->amount=$expense_amnt;
                    if($req->hasfile('attachment')) 
                    {
                        $u_obj->attachment=$filename;
                    }
                    $u_obj->delete=0;
                    $u_obj->a_id=$user_id;
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

                if ($expense_amnt !='' && $exp_date !='' && $exp_type !='') 
                {
                    $u_obj=new TechnicianExpenseModel();
                    $u_obj->exp_type=$exp_type;
                    $u_obj->oth_id=$oth_id;
                    $u_obj->exp_desc=$exp_desc;
                    $u_obj->exp_date=$exp_date;
                    $u_obj->amount=$expense_amnt;
                    if($req->hasfile('attachment'))  
                    {
                        $u_obj->attachment=$filename;
                    }
                    $u_obj->delete=0;
                    $u_obj->a_id=$user_id;
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
        }else{
            return ['status' => false, 'message' => 'Please Try Again..']; 
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

    public function postTransferLPaymentAPI(Request $req)
    {
    	$edit_id=empty($req->get('edit_id')) ? null : $req->get('edit_id');
        $pay_desc = $req->get('pay_desc');
   		$payment_date = $req->get('payment_date');
   		$payment_amnt = $req->get('payment_amnt');
        $labour = $req->get('labour');
        $user_id = $req->get('user_id');
        $oth_id = $req->get('so');
        // $so=implode(',',$sos);

    	// $a_id=Session::get('USER_ID');
        $user_id = CommonController::decode_ids($user_id);
        $check = UserModel::where('id',$user_id)->exists();
        if($check == true ){
            if($edit_id!=null)
            {
                if ($payment_amnt !='' && $payment_date !='') 
                {
                    $u_obj=TransferPaymentModel::find($edit_id);
                    $u_obj->u_id=$labour;
                    $u_obj->oth_id=$oth_id;
                    $u_obj->p_desc=$pay_desc;
                    $u_obj->p_date=$payment_date;
                    $u_obj->amount=$payment_amnt;
                    $u_obj->delete=0;
                    $u_obj->a_id=$user_id;
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
                    $u_obj->oth_id=$oth_id;
                    $u_obj->p_desc=$pay_desc;
                    $u_obj->p_date=$payment_date;
                    $u_obj->amount=$payment_amnt;
                    $u_obj->delete=0;
                    $u_obj->a_id=$user_id;
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
        }else{
            return ['status' => false, 'message' => 'Please Try Again..']; 
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
        $user_id = $req->get('user_id');
    	// $a_id=Session::get('USER_ID');

        
        $user_id = CommonController::decode_ids($user_id);
        $check = UserModel::where('id',$user_id)->exists();

        if($check == true)
        {
            if($req->hasfile('attachment'))  
            {  
                $file=$req->file('attachment');  
                $extension=$file->getClientOriginalExtension();  
                $filename= $extension."_".md5($file. microtime()).'.'.$extension;

                $file->move(public_path('files/user/travel_expense/'), $filename);
                
                // $image->image=$filename;  
            }  


            // For File Decoder 
            // if($attachment!='') 
            // {

            //     // $check = PaymentModel::where('p_id',$project_id)->exists();
            //     // $destinationPath = 'public/files/project-payment/'.$project_id.'/';

            //     // check folder exits or not
            //     // if ($check == false) {
            //     //     $result = File::makeDirectory($destinationPath, 0775, true, true); 
            //     // }

            //     // $destinationPath=public_path('/files/loan-receipt/');   //Folder Path
            //     $image1 = $req->input('attachment');     // encoded File name
            //     $extension=$req->input('payment_extension');       //File Extension  
                
            //     $pattern='/^data:.+;base64,/';

            //     $img = preg_replace($pattern, '', $image1);  //removed $pattern
            //     $img = str_replace(' ', '+', $img);  //for + sign blank space convert
            //     $data = base64_decode($img);       //decode All File

            //     $filename= $extension."_".md5($image1. microtime()).'.'.$extension;

            //     // $image_id= uniqid();    // create random name,number
            //     // $file = $image_id . '.'.$extension; // create name for file
            //     // $fp  = $image_id.'.'.$extension;   // send the file to destination path

            //     file_put_contents(public_path('files/user/travel_expense/').$filename, $data); 
            // }

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

                    if($req->hasfile('attachment'))
                    {
                        $u_obj->attachment=$filename;
                    }
                    $u_obj->delete=0;
                    $u_obj->a_id=$user_id;
                    $res=$u_obj->update();
                    
                    if($res){
                        return ['status' => true, 'message' => 'Travel Expense Update Successfully'];
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
                    if($req->hasfile('attachment'))
                    {
                        $u_obj->attachment=$filename;
                    }
                    $u_obj->delete=0;
                    $u_obj->a_id=$user_id;
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
        }else{
            return ['status' => false, 'message' => 'Please Try Again..']; 
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




   
}
