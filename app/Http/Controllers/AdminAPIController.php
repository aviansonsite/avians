<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\UserModel;
use App\Models\SOModel;
use App\Models\AutoValuesModel;
use App\Models\OATLHistoryModel;
use App\Models\LabourPaymentModel;
use App\Models\TransferPaymentModel;
use App\Models\TechnicianExpenseModel;
use App\Models\TravelExpenseModel;
use App\Models\PunchInOutModel;

use Carbon\Carbon;
use Session;
use DB;
use Hash;
use File;
use PDF;
use App\Http\Controllers\CommonController as Common;
class AdminAPIController extends Controller
{
    //******************************** User Management ***************************************************/

    public function users(Request $req)
    { 
		// $role=Session::get('ROLES');
		// $a_id=Session::get('USER_ID');
		$role = $req->get('role');
        $a_id = $req->get('u_id');
		if($role == 0){
			
			$u_obj=UserModel::where(['delete'=>0])->where('a_id','!=','0')->orderby('created_at','DESC')->get();

			foreach($u_obj as $u){

				//GET PROJECT NAME
				$u_obj1=UserModel::where(['delete'=>0,'id'=>$u->a_id])->where('role','!=','0')->orderby('created_at','DESC')->get();
				foreach($u_obj1 as $u1){
					$u->project_admin = $u1->name;
				}

				//get labour/sub technician so number
				$s_obj=SOModel::where('labour', 'LIKE', '%'.$u->id.'%')->where('lead_technician','!=',0)->where(['delete'=>0])->orderby('created_at','DESC')->get();
				$so_number = [];
				foreach($s_obj as $s){
					array_push($so_number, $s->so_number);  
				}

				//get lead technician so number
				$s_obj1=SOModel::where('lead_technician', 'LIKE', '%'.$u->id.'%')->where(['delete'=>0])->orderby('created_at','DESC')->get();
				
				foreach($s_obj1 as $s1){
					array_push($so_number, $s1->so_number);  
				}

				$u->so_number = $so_number;
			}
			
		}else{

			$u_obj=UserModel::where(['delete'=>0])->where('role','!=','0')->where('id','!=',$a_id)->orderby('created_at','DESC')->get();
			foreach($u_obj as $u){
				$so_number = [];
				//get labour/sub technician so number
				// $s_obj=SOModel::where('lead_technician','!=',0)->where(['delete'=>0,'labour'=>$u->id])->orderby('created_at','DESC')->get();
				

				$s_obj=DB::table('sales_orders as so')
					->leftjoin('users as u','u.id','so.a_id')
					->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','so.oa_type','u.name','u.delete as u_delete','u.is_active')
					->where('so.lead_technician','!=',0)
					->where(['so.delete'=>0,'so.labour'=>$u->id])
					->orderby('so.updated_at','DESC')
					->get();

				array_push($so_number, $s_obj); 

				// foreach($s_obj as $s){
				// 	array_push($so_number, $s->so_number);  
				// }

				//get lead technician so number
				$s_obj1=SOModel::where(['delete'=>0,'lead_technician'=>$u->id])->orderby('created_at','DESC')->get();

				$s_obj1=DB::table('sales_orders as so')
					->leftjoin('users as u','u.id','so.a_id')
					->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','so.oa_type','u.name','u.delete as u_delete','u.is_active')
					->where(['so.delete'=>0,'so.lead_technician'=>$u->id])
					->orderby('so.updated_at','DESC')
					->get();

				array_push($so_number, $s_obj1);

				// foreach($s_obj1 as $s1){
					  
				// }

				$u->so_number = $so_number;
			}
		}

		if(!empty($u_obj)){
            return json_encode(array('status' => true ,'data' => $u_obj ,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    public function postUser(Request $req)
    {
        
    	$user_id=isset($_POST['user_id']) ? $_POST['user_id'] : "NA";

    	$name=isset($_POST['name']) ? $_POST['name'] : "NA";
    	$email=isset($_POST['email']) ? $_POST['email'] : "NA";
    	$mobile=isset($_POST['mobile']) ? $_POST['mobile'] : "NA";
    	$pan_number=isset($_POST['pan_number']) ? $_POST['pan_number'] : "NA";
    	$aadhar_number=isset($_POST['aadhar_number']) ? $_POST['aadhar_number'] : "NA";

    	if(!isset($_POST['role'])){
            return ['status' => false, 'message' => 'Please select a ROLE!'];
    	}
    	$role=isset($_POST['role']) ? $_POST['role'] : "NA";
		
        $pan_file_ext=isset($_POST['pan_file_ext']) ? $_POST['pan_file_ext'] : null;
        $pan_file = $req->input('pan_file') ?$req->input('pan_file'): '';

        $aadhar_file_ext=isset($_POST['aadhar_file_ext']) ? $_POST['aadhar_file_ext'] : null;
        $aadhar_file = $req->input('aadhar_file') ?$req->input('aadhar_file'): '';

        $photo_file_ext=isset($_POST['photo_file_ext']) ? $_POST['photo_file_ext'] : null;
        $photo_file = $req->input('photo_file') ?$req->input('photo_file'): '';

        // For File Decoder 
        $destinationPath = 'files/user/';
        if($pan_file!="" && str_contains($pan_file, '+'))
        {         
            $img = str_replace('data:image/jpg;base64,', '', $pan_file);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            // $image_id= uniqid();
            $pan_filename= $pan_file_ext."_".md5($pan_file. microtime()).'.'.$pan_file_ext;

            file_put_contents($destinationPath.$pan_filename, $data);
            
        }

        if($aadhar_file!="" && str_contains($aadhar_file, '+'))
        {         
            $img = str_replace('data:image/jpg;base64,', '', $aadhar_file);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            // $image_id= uniqid();
            $aadhar_filename= $aadhar_file_ext."_".md5($aadhar_file. microtime()).'.'.$aadhar_file_ext;

            file_put_contents($destinationPath.$aadhar_filename, $data);
            
        }

        if($photo_file!="" && str_contains($photo_file, '+'))
        {         
            $img = str_replace('data:image/jpg;base64,', '', $photo_file);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            // $image_id= uniqid();
            $photo_filename= $photo_file_ext."_".md5($photo_file. microtime()).'.'.$photo_file_ext;

            file_put_contents($destinationPath.$photo_filename, $data);
            
        }

    	$check=UserModel::where(['mobile'=>$mobile,'delete'=>0])->exists();
		$check1=UserModel::where(['aadhar_number'=>$aadhar_number,'delete'=>0])->exists();
		if($user_id!=null)
    	{
            $a_id = $req->get('u_id');
            $permitted='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $pass=substr(str_shuffle($permitted),0,6);
            $password=Hash::make($mobile);

                $u_obj=UserModel::find($user_id);
                $u_obj->name=$name;
                $u_obj->email=$email;
                $u_obj->mobile=$mobile;
                $u_obj->password=$password;
                $u_obj->role=$role;
                $u_obj->pan_number=$pan_number;
                if($pan_file!="" && str_contains($pan_file, '+'))
                {
                    $u_obj->pan_file=$pan_filename;
                }

                $u_obj->aadhar_number=$aadhar_number;
                if($aadhar_file!="" && str_contains($aadhar_file, '+'))
                {
                    $u_obj->aadhar_file=$aadhar_filename;
                }
                if($photo_file!="" && str_contains($photo_file, '+'))
                {
                    $u_obj->photo_file=$photo_filename;
                }
                $u_obj->delete=0;
                $u_obj->is_active=0;
                $u_obj->a_id=$a_id;

                $res=$u_obj->update();

            if($res){
                return ['status' => true, 'message' => 'User Updated Successfully...!'];
            }else{
                return ['status' => false, 'message' => 'User Not Updated...!'];
            }
				
		}else{

			if($check1==false)
			{
                $a_id = $req->get('u_id');
                $permitted='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $pass=substr(str_shuffle($permitted),0,6);
                $password=Hash::make($mobile);

                $u_obj=new UserModel();
                $u_obj->emp_number = CommonController::custEmpNumber();
                $u_obj->name=$name;
                $u_obj->email=$email;
                $u_obj->mobile=$mobile;
                $u_obj->password=$password;
                $u_obj->role=$role;

                $u_obj->pan_number=$pan_number;
                if($pan_file!="" && str_contains($pan_file, '+'))
                {
                    $u_obj->pan_file=$pan_filename;
                }

                $u_obj->aadhar_number=$aadhar_number;
                if($aadhar_file!="" && str_contains($aadhar_file, '+'))
                {
                    $u_obj->aadhar_file=$aadhar_filename;
                }

                if($photo_file!="" && str_contains($photo_file, '+'))
                {
                    $u_obj->photo_file=$photo_filename;
                }

                $u_obj->delete=0;
                $u_obj->is_active=0;
                $u_obj->a_id=$a_id;
                
                $res=$u_obj->save();

                if($res){
                    return ['status' => true, 'message' => 'User Created Successfully...!'];
                }else{
                    return ['status' => false, 'message' => 'User Not Created...!'];
                }
			}else{
                return ['status' => false, 'message' => 'User With This AADHAR Number Already Exist...!'];
			}
			
		}
    	

    }

    public function change_status(Request $req)
    {
        $id=isset($_POST['id']) ? $_POST['id'] : "NA";

        $type='';
        $c_obj=UserModel::find($id);
        $s=$c_obj->is_active;
        $name=$c_obj->name;
        if($s==1)
        {
            $c_obj->is_active=0;
            $type='Actived';
        }
        else
        {
            $c_obj->is_active=1;
            $type='Deactivated';
        }
        $res=$c_obj->update();

        if($res)
        {
            return json_encode(array('data'=>true,'msg'=>"User $name is $type Successfully...!"));
            /*Session::put('SUCCESS_MESSAGE', "User $name is $type Successfully...!");*/
        }
        else
        {
            return json_encode(array('data'=>false,'msg'=>"User Status Updated Unsuccessfull..!"));
           /* Session::put('ERROR_MESSAGE', 'User Status Updated Unsuccessfull..!');*/
        }

    }

    public function user_delete(Request $req)
    {
        $id=isset($_POST['id']) ? $_POST['id'] : "NA";
        $u_obj=UserModel::find($id);
        $u_obj->delete=1;
        $u_obj->is_active=1;
        $res=$u_obj->update();

		if(!empty($res)){
            return json_encode(array('status' => true ,'message' => 'User Deleted Successfully...!'));
         }else{
            return ['status' => false, 'message' => 'User Deletion Unsuccessfull...!'];
         }
    }

	public function resPass(Request $req)
    {
		$id=isset($_POST['id']) ? $_POST['id'] : "NA";
        
        $u_obj=UserModel::find($id);
		$pass=Hash::make($u_obj->mobile);
        $u_obj->password=$pass;
        $res=$u_obj->update();
        // $res=$id;

		if(!empty($res)){
            return json_encode(array('status' => true ,'message' => 'Password Reset Successfully...!'));
         }else{
            return ['status' => false, 'message' => 'Password Reset Unsuccessfull...!'];
         }
    }


    //******************************** SO Management ***************************************************/

    public function checkTlStatus(Request $req)
    {
        // $roles=Session::get('ROLES');

        $u_id=isset($_POST['tech_id']) ? $_POST['tech_id'] : "NA";        // lead Technision ID
        $so_id=isset($_POST['so_id']) ? $_POST['so_id'] : "NA";         // SO ID
        $roles = $req->get('role');                                  
        $a_id = $req->get('u_id');

        // $a_id=Session::get('USER_ID');
 
        $data=OATLHistoryModel::where(['lead_technician'=>$u_id,'status'=>1])->orderby('created_at','DESC')->get();
        $count = count($data);
        
        $oa_number = "";
        $d_so_id = 0 ;
        $oa_status ="";
        // $oa_type =0;
            foreach($data as $d){
                $s_obj=SOModel::where(['delete'=>0,'id'=>$d->so_id])->orderby('created_at','DESC')->get();
                foreach($s_obj as $s){
                    $oa_number = $s->so_number;
                    // $oa_type = $d->status;
                    if($s->oa_type == 1){
                        if($d->status == 1)
                        {
                            $oa_status = "Active OA";
                        }else{
                            $oa_status = "In-Active OA";
                        }
                    }else{
                        if($d->status == 1){
                            $oa_status = "Visit Active OA";
                        }else{
                            $oa_status = "Visit In-Active OA";
                        }
                    }
                }
                $d_so_id = $d->so_id;
            }
 
        // User Data
        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'a_id'=>$a_id])->where('id', '!=',$u_id)->orderby('created_at','DESC')->get();

        if(!empty($data)){
            return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'count' => $count,'so_id' => $so_id,'d_so_id' => $d_so_id,'roles' => $roles,'oa_number' =>$oa_number ,'oa_status' =>$oa_status ,'message' => 'Data Found'));
         }else{
            return ['status' => false, 'message' => 'No Data Found'];
         }

    }

    public function postOA(Request $req)
    {
        $edit_id=isset($_POST['edit_id']) ? $_POST['edit_id'] : "NA";
        // dd($req);
        $so_number=isset($_POST['so_number']) ? $_POST['so_number'] : "NA";
        $oa_type=isset($_POST['oa_type']) ? $_POST['oa_type'] : "NA";
        $client_name=isset($_POST['client_name']) ? $_POST['client_name'] : "NA";
        $project_name=isset($_POST['project_name']) ? $_POST['project_name'] : "NA";
        $cp_ph_no=isset($_POST['cp_ph_no']) ? $_POST['cp_ph_no'] : "NA";
        $address=isset($_POST['address']) ? $_POST['address'] : "NA";
        $cp_name=isset($_POST['cp_name']) ? $_POST['cp_name'] : "NA";   
        $labour1=isset($_POST['labours']) ? $_POST['labours'] : "NA";       // Lead Technician Support     
        $labours=isset($_POST['labour']) ? $_POST['labour'] : "NA";         // Support Technicians
        $labour=implode(',',$labours);
        $a_id = $req->get('u_id');

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$labour1])->orderby('created_at','DESC')->get();

        if($edit_id!=null)
    	{
            if ($so_number !='' && $client_name !='' && $project_name !='' && $address !='' && $cp_name !='' && $cp_ph_no !='') 
            {
                $u_obj=SOModel::find($edit_id);
                $u_obj->so_number=$so_number;
                $u_obj->client_name=$client_name;
                $u_obj->project_name=$project_name;
                $u_obj->address=$address;
                $u_obj->cp_name=$cp_name;
                $u_obj->cp_ph_no=$cp_ph_no;
                $u_obj->labour=$labour;
                $u_obj->lead_technician=$labour1;
                if($oa_type == "normal"){
                    $u_obj->oa_type=1;
                }else{
                    $u_obj->oa_type=0;
                }
                $u_obj->a_id=$a_id;
                $res=$u_obj->update();
                

                $data=OATLHistoryModel::where(['so_id'=>$edit_id,'status'=>1])->orderby('created_at','DESC')->get();

                if(count($data) == 0){
                    $check_exist_tl=OATLHistoryModel::where(['so_id'=>$edit_id,'lead_technician'=>$labour1,'status'=>0])->orderby('created_at','DESC')->get();

                    if(count($check_exist_tl) == 1){
                        $new_pr_tl=OATLHistoryModel::where(['so_id'=>$edit_id,'lead_technician'=>$labour1,'status'=> 0])->update(['status' => 1]);

                    }else{
                        $tl_obj=new OATLHistoryModel();
                        $tl_obj->so_id=$edit_id;
                        $tl_obj->lead_technician=$labour1;
                        $tl_obj->a_id=$a_id;
                        $res1=$tl_obj->save();
                    }
                    

                }else{
                    $check_exist_tl=OATLHistoryModel::where(['so_id'=>$edit_id,'lead_technician'=>$labour1,'status'=>0])->orderby('created_at','DESC')->get();
                    
                    if($data[0]->lead_technician != $labour1 ){
                        if(count($check_exist_tl) == 1){
                            //status update previous tl
                            $update_pr_tl=OATLHistoryModel::where(['so_id'=>$edit_id,'status'=> 1])->where('lead_technician', '!=', $labour1)->update(['status' => 0]);


                            $new_pr_tl=OATLHistoryModel::where(['so_id'=>$edit_id,'lead_technician'=>$labour1,'status'=> 0])->update(['status' => 1]);

                        }else{

                            //status update previous tl
                            $update_pr_tl=OATLHistoryModel::where(['so_id'=>$edit_id,'status'=> 1])->where('lead_technician', '!=', $labour1)->update(['status' => 0]);

                            $tl_obj=new OATLHistoryModel();
                            $tl_obj->so_id=$edit_id;
                            $tl_obj->lead_technician=$labour1;
                            $tl_obj->a_id=$a_id;
                            $res2=$tl_obj->save();
                        }
                        
                    }
                }

                if($res){
                    $tl_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$labour1])->orderby('created_at','DESC')->get();

                    $s_name = [];
                    foreach($labours as $l){
                        $s_tl_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$l])->orderby('created_at','DESC')->get();
                        foreach($s_tl_obj as $p1){
                            array_push($s_name, $p1->name);    //Push user id for attendance
                        }
                    }

                    // $s_obj = SOModel::where(['delete'=>0,])->orderby('updated_at','DESC')->get();

                    $tl_name = $tl_obj[0]->name;
                    $email = $tl_obj[0]->email;
                    $image = public_path('files/company/logo.png');
                    $all_sup_tech=implode(', ',$s_name);
                    $mail = new PHPMailer(true);
                    try 
                    {
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
                        $mail->addAddress($email,$tl_name);     
                        $mail->addReplyTo(config('constants.MAIL_REPLY'), 'Avians Innovations technology Pvt.Ltd');

                        //Content
                        $mail->CharSet = "utf-8";       
                        // set charset to utf8
                        $mail->isHTML(true); 
                        $mail->AddEmbeddedImage($image, 'logo_2u');              
                        // Set email format to HTML
                        $mail->Subject = config('constants.AUTHOR_NAME')." - OA Details.";
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
                                                                               ." <b> Hello ".$tl_name.", </b>"
                                                                           ." </h3> "
                                                                           ." <p style='margin:5px 0 0 0;font:12px/16px Arial,sans-serif'> Greetings from Avians Innovations technology Pvt.Ltd"
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
                                                                               ." <span style='color:rgb(102,102,102)'>Please go through This information of OA which is assigned for you For tomorrow's tasks.</span>"
                                                                               ." <br/><br/>"
                                                                               ." <strong>Login Link:  </strong> <a href='".config('constants.LOGIN_LINK')."'>Click Here.</a>"
                                                                               ." <br> "
                                                                               ." <strong>OA Number:  ".$so_number."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Client Name:  ".$client_name."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Project Name:  ".$project_name."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>CP Name:  ".$cp_name."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>CP Phone Number:  ".$cp_ph_no."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Project Address:  ".$address."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Support Technicians:  ".$all_sup_tech."</strong>"
                                                                               ." <br/><br/>"
                                                                               ." <span style='color:rgb(102,102,102)'>If there is anything that feels unclear or needs to change, please share your feedback with Admin.</span>"
                                                                               ." <br/><br/><br/>"
                                                                               ." <span style='color:rgb(102,102,102)'>Thanks, </span>"
                                                                               ." <br>"
                                                                               ." <span style='color:rgb(102,102,102)'>Avians Innovations technology Pvt.Ltd</span>"
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

                        // Session::put('SUCCESS_MESSAGE', "New Password & Login Details Sent On E-mail.");
                        // return redirect()->route('login.page');
                    }
                    catch (Exception $e)
                    {
                        return ['status' => false, 'message' => $mail->ErrorInfo];
                        // Session::put('ERROR_MESSAGE', "E-mail could not be sent.$mail->ErrorInfo");
                        // return redirect()->route('login.page');
                    }

                    return ['status' => true, 'check_exist_tl'=>count($check_exist_tl),'message' => 'SO Update Successfully'];
                }else{
                   return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   

        }else{    

            // $check = SOModel::where(['so_number'=>$so_number,'delete'=>0])->get();
            // if(count($check)>0)
            // {
            //     return ['status' => false, 'message' => "These $so_number is Already Exists..."]; 

            // }
            // return ['status' => true, 'message' => "$so_number,$client_name,$project_name,$address,$cp_name,$cp_ph_no,$labour"];
            if ($so_number !='' && $client_name !='' && $project_name !='' && $address !='' && $cp_name !='' && $cp_ph_no !='') 
            {
                $u_obj=new SOModel();
                $u_obj->so_number=$so_number;
                $u_obj->client_name=$client_name;
                $u_obj->project_name=$project_name;
                $u_obj->address=$address;
                $u_obj->cp_name=$cp_name;
                $u_obj->cp_ph_no=$cp_ph_no;
                $u_obj->labour=$labour;
                $u_obj->lead_technician=$labour1;

                if($oa_type == "normal")
                {
                    $u_obj->oa_type=1;          // normal oa
                }else{
                    $u_obj->oa_type=0;          // visit oa
                }

                $u_obj->delete=0;
                $u_obj->a_id=$a_id;
                $res=$u_obj->save();

                $so_id = $u_obj->id;
                $data=OATLHistoryModel::where(['lead_technician'=>$labour1,'status'=>1])->orderby('created_at','DESC')->get();

                if(count($data) == 0){
                    $tl_obj=new OATLHistoryModel();
                    $tl_obj->so_id=$so_id;
                    $tl_obj->lead_technician=$labour1;
                    $tl_obj->a_id=$a_id;
                    $res=$tl_obj->save();
                }
                
                if($res){
                    
    	            $tl_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$labour1])->orderby('created_at','DESC')->get();

                    $s_name = [];
                    foreach($labours as $l){
                        $s_tl_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$l])->orderby('created_at','DESC')->get();
                        foreach($s_tl_obj as $p1){
                            array_push($s_name, $p1->name);    //Push user id for attendance
                        }
                    }

                    // $s_obj = SOModel::where(['delete'=>0,])->orderby('updated_at','DESC')->get();

                    $pass = "test";
                    $tl_name = $tl_obj[0]->name;
                    $email = $tl_obj[0]->email;
                    $mobile_number ="omkar test";
                    $image = public_path('files/company/logo.png');
                    $all_sup_tech=implode(', ',$s_name);
                    $mail = new PHPMailer(true);
                    try 
                    {
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
                        $mail->addAddress($email,$tl_name);     
                        $mail->addReplyTo(config('constants.MAIL_REPLY'), 'Avians Innovations technology Pvt.Ltd');

                        //Content
                        $mail->CharSet = "utf-8";       
                        // set charset to utf8
                        $mail->isHTML(true); 
                        $mail->AddEmbeddedImage($image, 'logo_2u');              
                        // Set email format to HTML
                        $mail->Subject = config('constants.AUTHOR_NAME')." - OA Details.";
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
                                                                               ." <b> Hello ".$tl_name.", </b>"
                                                                           ." </h3> "
                                                                           ." <p style='margin:5px 0 0 0;font:12px/16px Arial,sans-serif'> Greetings from Avians Innovations technology Pvt.Ltd"
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
                                                                               ." <span style='color:rgb(102,102,102)'>Please go through This information of OA which is assigned for you For tomorrow's tasks.</span>"
                                                                               ." <br/><br/>"
                                                                               ." <strong>Login Link:  </strong> <a href='".config('constants.LOGIN_LINK')."'>Click Here.</a>"
                                                                               ." <br> "
                                                                               ." <strong>OA Number:  ".$so_number."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Client Name:  ".$client_name."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Project Name:  ".$project_name."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>CP Name:  ".$cp_name."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>CP Phone Number:  ".$cp_ph_no."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Project Address:  ".$address."</strong>"
                                                                               ." <br>"
                                                                               ." <strong>Support Technicians:  ".$all_sup_tech."</strong>"
                                                                               ." <br/><br/>"
                                                                               ." <span style='color:rgb(102,102,102)'>If there is anything that feels unclear or needs to change, please share your feedback with Admin.</span>"
                                                                               ." <br/><br/><br/>"
                                                                               ." <span style='color:rgb(102,102,102)'>Thanks, </span>"
                                                                               ." <br>"
                                                                               ." <span style='color:rgb(102,102,102)'>Avians Innovations technology Pvt.Ltd</span>"
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

                        // Session::put('SUCCESS_MESSAGE', "New Password & Login Details Sent On E-mail.");
                        // return redirect()->route('login.page');
                    }
                    catch (Exception $e)
                    {
                        return ['status' => false, 'message' => $mail->ErrorInfo];
                        // Session::put('ERROR_MESSAGE', "E-mail could not be sent.$mail->ErrorInfo");
                        // return redirect()->route('login.page');
                    }

                    return ['status' => true, 'so_id'=>$so_id,'message' => 'SO Add Successfully'];
                }else{
                   return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   
        }

        return ['status' => false, 'message' => 'Please Try Again..'];
    }

    public function getSO(Request $req)
    {
        // $roles=Session::get('ROLES');
        // $a_id=Session::get('USER_ID');
        $roles = $req->get('role');                                  
        $a_id = $req->get('u_id');
        // $data = SOModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();
       

        if($roles == 0){
            $data=DB::table('sales_orders as so')
            ->leftjoin('users as u','u.id','so.a_id')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','so.oa_type','u.name','u.delete as u_delete','u.is_active')
            ->where(['so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('so.updated_at','DESC')
            ->get();

            foreach($data as $d){
                $oth_obj=OATLHistoryModel::where(['so_id'=>$d->id,'status'=>1])->orderby('created_at','DESC')->get();

                foreach($oth_obj as $o){
                    $d->oth_status = $o->status;
                    $d->oth_id = $o->id;
                }

                // User Data
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->lead_technician])->orderby('created_at','DESC')->get();

                foreach($u_obj as $u){
                    $d->lead_technician_name = $u->name;
                }

                $d->enc_id = CommonController::encode_ids($d->id);
            }
            // $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();

        }else{
            $data=DB::table('sales_orders as so')
            ->leftjoin('users as u','u.id','so.a_id')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','so.oa_type','u.name','u.delete as u_delete','u.is_active')
            ->where(['so.a_id'=>$a_id,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('so.updated_at','DESC')
            ->get();
            
            foreach($data as $d){
                $oth_obj=OATLHistoryModel::where(['so_id'=>$d->id,'status'=>1])->orderby('created_at','DESC')->get();

                foreach($oth_obj as $o){
                    $d->oth_status = $o->status;
                    $d->oth_id = $o->id;
                }

                // User Data
                $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'id'=>$d->lead_technician])->orderby('created_at','DESC')->get();

                foreach($u_obj as $u){
                    $d->lead_technician_name = $u->name;
                }

                $d->enc_id = CommonController::encode_ids($d->id);
            }

        }
        
        // User Data
        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        if(!empty($data)){
            return json_encode(array('status' => true ,'data' => $data,'roles' => $roles ,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }

    }

    public function getSOTechnician(Request $req)
    {

        $roles = $req->get('role');                                  
        $a_id = $req->get('u_id');

        // User Data
        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

        if(!empty($u_obj)){
            return json_encode(array('status' => true ,'u_obj' => $u_obj,'roles' => $roles ,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }

    }

    public function removeTL(Request $req)
    {
        $oth_id=isset($_POST['oth_id']) ? $_POST['oth_id'] : "NA";
    	$oth_status=isset($_POST['oth_status']) ? $_POST['oth_status'] : "NA";
    	$oth_so_id=isset($_POST['oth_so_id']) ? $_POST['oth_so_id'] : "NA";

        $roles = $req->get('role');                                  
        $a_id = $req->get('u_id');
       
       if($oth_status == 1){
            $oth_obj=OATLHistoryModel::find($oth_id);
            $oth_obj->status=0;
            $res=$oth_obj->update();

            $s_obj=SOModel::find($oth_so_id);
            $s_obj->lead_technician=0;
            $res=$s_obj->update();
        }
        
        if($res){
            return ['status' => true, 'message' => 'Remove TL Successfully...!'];
        }else{
            return ['status' => false, 'message' => 'Remove TL Unsuccessfull...!'];
        }
    }

    public function soDelete(Request $req)
    {
        $id=$req->get('id');
        $s_obj=SOModel::find($id);
        $s_obj->delete=1;
        $s_obj->lead_technician=0;
        $res=$s_obj->update();

        $data=OATLHistoryModel::where(['so_id'=>$id,'status'=>1])->update(['status'=>0]);

        if($res){
            return ['status' => true, 'message' => 'OA Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'OA Deletion Unsuccessfull...!'];
        }
    }

    public function manageExpTechnicians(Request $req)
    {
        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');
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

        return json_encode(array('status' => true ,'u_obj' => $u_obj,'roles' => $role,'message' => 'Data Found'));

    }

    public function getAllExpense(Request $req)
    {

        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');
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
        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');

        $edit_id=isset($_POST['exp_edit_id']) ? $_POST['exp_edit_id'] : "NA";
        $updated_amnt=isset($_POST['updated_amnt']) ? $_POST['updated_amnt'] : "NA";
        $status=isset($_POST['status']) ? $_POST['status'] : "NA";
        $acc_remark=isset($_POST['acc_remark']) ? $_POST['acc_remark'] : "NA";
        $sa_remark=isset($_POST['sa_remark']) ? $_POST['sa_remark'] : "NA";
        $sa_updated_amnt=isset($_POST['sa_updated_amnt']) ? $_POST['sa_updated_amnt'] : "NA";
    	

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

    public function travelExpTechnicians(Request $req)
    {
        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');

        if($role == 0){
    	    $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
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
    	   
        }

        return json_encode(array('status' => true ,'u_obj' => $u_obj,'roles' => $role,'message' => 'Data Found'));


    }

    public function getTravelExpense(Request $req)
    {
        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');
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

    public function updateTravelExpenses(Request $req)
    {
        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');
        $edit_id=$req->get('exp_edit_id');
        $updated_amnt = $req->get('updated_amnt');
   		$status = $req->get('status');
   		$acc_remark = $req->get('acc_remark');
        $sa_remark = $req->get('sa_remark');
        $sa_updated_amnt = $req->get('sa_updated_amnt');
        
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

    public function techniciansPayments()
    {
    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();

    	$s_obj=SOModel::where(['delete'=>0])->orderby('created_at','DESC')->get();
        
        return json_encode(array('status' => true ,'u_obj' => $u_obj,'s_obj' => $s_obj,'message' => 'Data Found'));

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
    	$role = $req->get('role');                                  
        $a_id = $req->get('u_id');

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
    	$role = $req->get('role');                                  
        $a_id = $req->get('u_id');
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');

        if ($from_date == null && $to_date == null && $labours == null) 
        {

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

           

            if(!empty($data)){
                return json_encode(array('status' => true ,'data' => $data,'role'=>$role ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

        }else{

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

            if(count($data)>0){
                return json_encode(array('status' => true ,'data' => $data,'role'=>$role ,'message' => 'Data Found'));
            }else{
                return ['status' => false, 'message' => 'No Data Found'];
            }

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

    public function viewOAPaymentHistory(Request $req)
    {
        $so_id=$req->get('so_id');
        $so_id = CommonController::decode_ids($so_id);

        $data=DB::table('sales_orders as so')
            ->leftjoin('oa_tl_history as oth','oth.so_id','so.id')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','so.oa_type','oth.id as oth_id','oth.status')
            ->where(['so.id'=>$so_id,'so.delete'=>0])
            ->orderby('so.updated_at','DESC')
            ->get();

        // dd($data);

        $oth_id= array(); //create empty array

        foreach($data as $dl)
        {   
            array_push($oth_id,$dl->oth_id);        // push lead technician in all technicians
        }
        // Avians account Payment
        $accountant_payment = LabourPaymentModel::where(['delete'=>0])->whereIn('oth_id',$oth_id)->sum('payment_amnt');

        // fot - from other technician
        $fot = TransferPaymentModel::where(['delete'=>0])->whereIn('recvr_oth_id',$oth_id)->sum('amount');
        $total_wallet = $accountant_payment + $fot;
      
        //Technician Expense
        $technician_expenses = TechnicianExpenseModel::where(['delete'=>0])->whereIn('oth_id',$oth_id)->whereIn('oth_id',$oth_id)->sum('amount');

        //Travel Expense
        $travel_expense = TravelExpenseModel::where(['delete'=>0])->whereIn('oth_id',$oth_id)->sum('travel_amount');

        $total_tech_expense = $technician_expenses + $travel_expense;

        //transfer to other technician
        $ttot = TransferPaymentModel::where(['delete'=>0])->whereIn('oth_id',$oth_id)->sum('amount');
        // dd($ttot);
        $total_expense = $technician_expenses + $travel_expense + $ttot;

        //Cleared Payment
        $aprvd_technician_expenses = TechnicianExpenseModel::where(['delete'=>0,'status'=>'Approved'])->whereIn('oth_id',$oth_id)->sum('aprvd_amount');
    
        //Cleared Payment
        $apprvd_travel_expense = TravelExpenseModel::where(['delete'=>0,'status'=>'Approved'])->whereIn('oth_id',$oth_id)->sum('aprvd_amount');
           
        $cleared_pay = $aprvd_technician_expenses +  $apprvd_travel_expense;

        //uncleared Payment
        $uncleared_pay = TechnicianExpenseModel::where(['delete'=>0])->whereIn('oth_id',$oth_id)->where('status', '!=','Approved')->sum('amount');

        $balance = $total_wallet - $total_expense;

        $s_obj=SOModel::where(['delete'=>0,'id'=>$so_id])->orderby('created_at','DESC')->get();
        // dd($s_obj);
        //from avians account payment
        $avians_payment=DB::table('sales_orders as so')
            ->leftjoin('oa_tl_history as oth','oth.so_id','so.id')
            ->leftjoin('labour_payments as lp','lp.oth_id','oth.id')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','so.oa_type','oth.id as oth_id','oth.status','lp.p_desc','lp.payment_date','lp.payment_amnt','lp.delete','u.name','u.delete as u_delete',)
            ->where(['so.delete'=>0,'lp.delete'=>0,'u.delete'=>0])
            ->whereIn('oth_id',$oth_id)
            ->orderby('so.updated_at','DESC')
            ->get();   

        // dd($avians_payment);
        $general_expense=DB::table('technician_expenses as te')
            ->leftjoin('users as u','u.id','te.a_id')
            ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','te.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
            ->where(['te.delete'=>0,'u.delete'=>0,'so.delete'=>0,'so.id'=>$so_id])
            ->orderby('te.created_at','DESC')
            ->get();

            foreach($general_expense as $ge){
                $u_obj=UserModel::where(['delete'=>0,'id'=>$ge->u_a_id])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $ge->project_admin = $u->name;
                }
            }
            
        $travel_expense=DB::table('travel_expenses as te')
            ->leftjoin('users as u','u.id','te.a_id')
            ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','te.id','te.oth_id','te.ad_id','te.sa_id','te.mode_travel','te.from_location','te.to_location','te.total_km','te.travel_date','te.travel_desc','te.ad_remark','te.sa_remark','te.attachment','te.no_of_person','te.travel_amount','te.aprvd_amount','te.status','te.a_id','te.delete','te.created_at','te.updated_at','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
            ->where(['te.delete'=>0,'u.delete'=>0,'so.id'=>$so_id])
            // ->where('te.created_at', '>=', $date)
            ->orderby('te.created_at','DESC')
            ->get();

            foreach($travel_expense as $tr)
            {
                $u_obj=UserModel::where(['delete'=>0,'id'=>$tr->u_a_id])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $tr->project_admin = $u->name;
                }
            }
        

        $transfer_payment=DB::table('transfer_payments as tp')
            ->leftjoin('oa_tl_history as oth','oth.id','tp.oth_id')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','tp.id','tp.oth_id','tp.u_id','tp.recvr_oth_id','tp.p_date','tp.p_desc','tp.amount','tp.a_id','tp.delete','tp.created_at','tp.updated_at','so.id as so_id','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
            ->where(['tp.delete'=>0,'u.delete'=>0,'so.id'=>$so_id])
            ->orderby('tp.created_at','DESC')
            ->get();

            foreach($transfer_payment as $tp)
            {
                $s_obj1=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.updated_at','so.delete','so.labour','so.so_number')
                ->where(['oth.id'=>$tp->recvr_oth_id,'so.delete'=>0])
                ->orderby('so.updated_at','DESC')
                ->get();

                foreach($s_obj1 as $s){
                    $tp->recvr_so_number = $s->so_number;
                    $tp->recvr_labour_name = $s->labour_name;
                }
            }
        
        $receiver_payment=DB::table('transfer_payments as tp')
            ->leftjoin('oa_tl_history as oth','oth.id','tp.recvr_oth_id')
            ->leftjoin('users as u','u.id','oth.lead_technician')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','tp.id','tp.oth_id','tp.u_id','tp.recvr_oth_id','tp.p_date','tp.p_desc','tp.amount','tp.a_id','tp.delete','tp.created_at','tp.updated_at','so.id as so_id','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.a_id as so_aid')
            ->where(['tp.delete'=>0,'u.delete'=>0,'so.id'=>$so_id])
            ->orderby('tp.created_at','DESC')
            ->get();

            foreach($receiver_payment as $tp)
            {
                $s_obj1=DB::table('oa_tl_history as oth')
                ->leftjoin('users as u','u.id','oth.lead_technician')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.a_id as u_a_id','u.emp_number','oth.id as oth_id','oth.so_id','oth.lead_technician','oth.status','so.updated_at','so.delete','so.labour','so.so_number')
                ->where(['oth.id'=>$tp->oth_id,'so.delete'=>0])
                ->orderby('so.updated_at','DESC')
                ->get();

                foreach($s_obj1 as $s){
                    $tp->sender_so_number = $s->so_number;
                    $tp->sender_labour_name = $s->labour_name;
                }
            }    
        // dd($receiver_payment);
        return json_encode(array('status' => true ,'s_obj' => $s_obj,'data'=>$data ,'accountant_payment' => $accountant_payment,'total_wallet'=>$total_wallet,'technician_expenses' => $technician_expenses,'fot'=>$fot,'ttot' => $ttot,'total_expense'=>$total_expense,'cleared_pay' => $cleared_pay,'uncleared_pay'=>$uncleared_pay,'balance' => $balance,'total_tech_expense'=>$total_tech_expense,'avians_payment' => $avians_payment,'general_expense'=>$general_expense,'travel_expense' => $travel_expense,'transfer_payment'=>$transfer_payment,'receiver_payment,'=>$receiver_payment,'message' => 'Data Found'));
    }

    public function siteExpTechnicians()
    {
    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
        
        return json_encode(array('status' => true ,'u_obj' => $u_obj, 'message' => 'Data Found'));

    }

    public function getExpRecord(Request $req)
    {
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $labours = $req->get('labours');
        $so = $req->get('so');
        $oth_id = $req->get('oth_id');

        $u_obj1=UserModel::where(['delete'=>0,'id'=>$labours])->where('role','!=','0')->orderby('created_at','DESC')->get();

        foreach($u_obj1 as $u)
        {
            $u->from_date = date('d-m-Y', strtotime($from_date));
            $u->to_date = date('d-m-Y', strtotime($to_date));
            $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u->id])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->sum('payment_amnt');
            $u->adv_amnt = $accountant_payment;
        }

        $total_date =  array();
        //Technician Expense
        $exp_date = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$labours])->whereDate('exp_date', '>=' ,$from_date)->whereDate('exp_date', '<=' ,$to_date)->groupBy('exp_date')->get('exp_date');

        $trav_date = TravelExpenseModel::where(['delete'=>0,'a_id'=>$labours])->whereDate('travel_date', '>=' ,$from_date)->whereDate('travel_date', '<=' ,$to_date)->groupBy('travel_date')->get('travel_date');

        // push unique date in one object
        foreach($trav_date as $t)
        {
            array_push($total_date,$t);
        }

        foreach($exp_date as $ed)
        {
            array_push($total_date,$ed);
        }

        foreach ($total_date as $td) {
            if (isset($td->travel_date)) {
                $td->exp_date = $td->travel_date;
                unset($td->travel_date);
            }
        }

        // Convert the array to a Laravel Collection
        $collection = collect($total_date);

        // Use the unique() method with a custom key ('exp_date') to remove duplicates
        $uniqueDates = $collection->unique('exp_date')->values();

        // Sort the collection to ensure the dates are in sequential order
        $sortedDates = $uniqueDates->sortBy('exp_date')->values();

        // $uniqueDates = array_values(array_unique($total_date));
        // $uniqueDates = $uniqueDates->sortBy('exp_date');
        $tech_exp= array(); //create empty array
        foreach($sortedDates as $ed)
        {

            $data=DB::table('technician_expenses as te')
                ->leftjoin('users as u','u.id','te.a_id')
                ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
                ->where(['te.delete'=>0,'u.delete'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.oth_id'=>$oth_id,'te.status'=>"Approved",'te.exp_date'=>$ed->exp_date])
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

            foreach($data as $d){
                array_push($tech_exp,$d);
            }

            $t_data=DB::table('travel_expenses as te')
            ->leftjoin('users as u','u.id','te.a_id')
            ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.oth_id','te.travel_date as exp_date','te.travel_desc as exp_desc','te.mode_travel as exp_type','te.a_id','te.delete','te.attachment','te.ad_id','te.ad_remark','te.status','te.sa_remark','te.sa_id','te.travel_amount as amount','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
            ->where(['te.delete'=>0,'u.delete'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.oth_id'=>$oth_id,'te.status'=>"Approved",'te.travel_date'=>$ed->exp_date])
            ->orderby('u.created_at','DESC')
            ->get();

            foreach($t_data as $td){
                $u_obj=UserModel::where(['delete'=>0,'id'=>$td->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $td->project_admin = $u->name;
                }

                $u_obj=UserModel::where(['delete'=>0,'id'=>$td->sa_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $td->super_admin = $u->name;
                }
            }

            foreach($t_data as $td){
                array_push($tech_exp,$td);
            }
  
        }

    
        if(count($tech_exp)>0){
            return json_encode(array('status' => true ,'data' => $tech_exp,'u_obj1'=>$u_obj1,'message' => 'Data Found'));
        }else{
            return ['status' => false, 'message' => 'No Data Found'];
        }
    }

    public function generatePdf(Request $req)
    {
        $delOldPDF = "files/temp/";
        File::cleanDirectory($delOldPDF);

        $a_id=Session::get('USER_ID');
        $from_date = $req->get('pdf_from_date');
        $to_date = $req->get('pdf_to_date');
        $labours = $req->get('pdf_labours');
        $oth_id = $req->get('pdf_oth_id'); 


        $u_obj1=UserModel::where(['delete'=>0,'id'=>$labours])->where('role','!=','0')->orderby('created_at','DESC')->get();
        // dd($u_obj1);
        foreach($u_obj1 as $u)
        {
            $u->from_date = date('d-m-Y', strtotime($from_date));
            $u->to_date = date('d-m-Y', strtotime($to_date));
            $accountant_payment = LabourPaymentModel::where(['delete'=>0,'u_id'=>$u->id])->whereDate('payment_date', '>=' ,$from_date)->whereDate('payment_date', '<=' ,$to_date)->sum('payment_amnt');
            $u->adv_amnt = $accountant_payment;
            
        }

        $total_date =  array();
        //Technician Expense
        $exp_date = TechnicianExpenseModel::where(['delete'=>0,'a_id'=>$labours])->whereDate('exp_date', '>=' ,$from_date)->whereDate('exp_date', '<=' ,$to_date)->groupBy('exp_date')->get('exp_date');

        $trav_date = TravelExpenseModel::where(['delete'=>0,'a_id'=>$labours])->whereDate('travel_date', '>=' ,$from_date)->whereDate('travel_date', '<=' ,$to_date)->groupBy('travel_date')->get('travel_date');

        // push unique date in one object
        foreach($trav_date as $t)
        {
            array_push($total_date,$t);
        }

        foreach($exp_date as $ed)
        {
            array_push($total_date,$ed);
        }

        foreach ($total_date as $td) {
            if (isset($td->travel_date)) {
                $td->exp_date = $td->travel_date;
                unset($td->travel_date);
            }
        }
        
        // Convert the array to a Laravel Collection
        $collection = collect($total_date);

        // Use the unique() method with a custom key ('exp_date') to remove duplicates
        $uniqueDates = $collection->unique('exp_date')->values();

        // Sort the collection to ensure the dates are in sequential order
        $sortedDates = $uniqueDates->sortBy('exp_date')->values();

        // $uniqueDates = array_values(array_unique($total_date));
        // $uniqueDates = $uniqueDates->sortBy('exp_date');
        $tech_exp= array(); //create empty array
        foreach($sortedDates as $ed)
        {

            $data=DB::table('technician_expenses as te')
                ->leftjoin('users as u','u.id','te.a_id')
                ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
                ->leftjoin('sales_orders as so','so.id','oth.so_id')
                ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.exp_type','te.exp_date','te.exp_desc','te.amount','te.a_id','te.delete','te.attachment','te.acc_id','te.oth_id','te.acc_remark','te.status','te.sa_remark','te.sa_id','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
                ->where(['te.delete'=>0,'u.delete'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.oth_id'=>$oth_id,'te.status'=>"Approved",'te.exp_date'=>$ed->exp_date])
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

            foreach($data as $d){
                array_push($tech_exp,$d);
            }

            $t_data=DB::table('travel_expenses as te')
            ->leftjoin('users as u','u.id','te.a_id')
            ->leftjoin('oa_tl_history as oth','oth.id','te.oth_id')
            ->leftjoin('sales_orders as so','so.id','oth.so_id')
            ->select('u.id as u_id','u.name as labour_name','u.delete as u_delete','u.is_active','u.emp_number','u.a_id as u_a_id','te.id','te.oth_id','te.travel_date as exp_date','te.travel_desc as exp_desc','te.mode_travel as exp_type','te.a_id','te.delete','te.attachment','te.ad_id','te.ad_remark','te.status','te.sa_remark','te.sa_id','te.travel_amount as amount','te.aprvd_amount','so.delete','so.labour','so.so_number','so.project_name','so.client_name','so.address','so.cp_name','so.cp_ph_no','so.a_id as so_aid')
            ->where(['te.delete'=>0,'u.delete'=>0,'so.delete'=>0,'te.a_id'=>$labours,'te.oth_id'=>$oth_id,'te.status'=>"Approved",'te.travel_date'=>$ed->exp_date])
            ->orderby('u.created_at','DESC')
            ->get();

            foreach($t_data as $td){
                $u_obj=UserModel::where(['delete'=>0,'id'=>$td->so_aid])->where('role','!=','0')->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $td->project_admin = $u->name;
                }

                $u_obj=UserModel::where(['delete'=>0,'id'=>$td->sa_id])->orderby('created_at','DESC')->get();
                foreach($u_obj as $u){
                    $td->super_admin = $u->name;
                }
            }

            foreach($t_data as $td){
                array_push($tech_exp,$td);
            }
  
        }

        // dd($tech_exp);
        // return view('report.siteExpensePdf',compact('tech_exp','u_obj1'));
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

        $file_name="SITE_EXPENSE_REPORT_".$u_obj1[0]->id.".pdf";
        $delOldPDF = "files/temp/$file_name";
        file_put_contents("files/temp/$file_name", $pdf1->download());
        if(count($tech_exp)>0){
            return json_encode(array('status' => true ,'data' => $file_name,'message' => 'Generate PDF Successfully'));
        }else{
            return ['status' => false, 'message' => 'Generate PDF UnSuccessfull'];
        }
       
    }

    public function technicianAttendance(Request $req)
    {
        // $a_idd [] =Session::get('USER_ID');
        // $a_id =Session::get('USER_ID');
        // $role =Session::get('ROLES');

        $role = $req->get('role');                                  
        $a_id = $req->get('u_id');
        $a_idd []= $req->get('u_id');

    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->where('id', '!=', $a_id)->orderby('created_at','DESC')->get();
    	// $s_obj=SOModel::whereIn('labour',$a_idd)->where(['delete'=>0])->orderby('created_at','DESC')->get();
       
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
    	// return view('report.technicianAttendanceReport',compact('u_obj','s_obj','t_count','p_obj','p_id'));
        return json_encode(array('status' => true ,'u_obj' => $u_obj,'s_obj'=>$s_obj,'message' => 'Data Found'));
    }

    public function getLabour(Request $req)
    {
        $so_id = $req->get('oth_id');
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

    //get punch in out records
    public function techAttRecord(Request $req)
    {
        $from_date = $req->get('from_date');
        $to_date = $req->get('to_date');
        $so_id = $req->get('so_id'); 
        $labours = $req->get('labours');                                 
        $a_id = $req->get('u_id');

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


            $p_obj=PunchInOutModel::where(['delete'=>0,'pin_u_id'=>$labours,'pin_oth_id'=>$so_id])->whereDate('pin_date', '>=' ,$from_date)->whereDate('pin_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();

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
            }
            else
            {
                
                // for punch out regularies
                $p_obj=PunchInOutModel::where(['delete'=>0,'pout_u_id'=>$labours,'pout_oth_id'=>$so_id])->whereDate('pout_date', '>=' ,$from_date)->whereDate('pout_date', '<=' ,$to_date)->orderby('updated_at','DESC')->get();
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

            }
        }

        
        if(count($p_obj)>0){
            return json_encode(array('status' => true ,'data' => $p_obj,'fdate' =>$from_date ,'labours' =>$labours,'message' => 'Data Found'));
        }else{
        return ['status' => false, 'message' => 'No Data Found'];
        }
        

    }
}   
