<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\UserModel;
use App\Models\SOModel;
use App\Models\OATLHistoryModel;
use Session;
use Hash;
use DB;

class SOController extends Controller
{
    public function so_list()
    {
    	$u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
        // dd($u_obj);
    	// return view('users.users_list',compact('u_obj'));
    	return view('so.soList',compact('u_obj'));

    }

    public function postOA(Request $req)
    {
    	$edit_id=empty($req->get('edit_id')) ? null : $req->get('edit_id');
        // dd($req);
        $so_number = $req->get('so_number');
   		$client_name = $req->get('client_name');
   		$project_name = $req->get('project_name');
   		$cp_ph_no = $req->get('cp_ph_no');
        $address = $req->get('address');
   		$cp_name = $req->get('cp_name');
        $labour1 = $req->get('labours');    // Lead Technician Support
        $labours = $req->get('labour');     // Support Technicians
        $labour=implode(',',$labours);
    	$a_id=Session::get('USER_ID');

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
                    return ['status' => true, 'check_exist_tl'=>count($check_exist_tl),'message' => 'SO Update Successfully'];
                }else{
                   return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                }
            }else{
                return ['status' => false, 'message' => 'Please Try Again..']; 
            }   

        }else{    
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
		return redirect()->back();

    }

    public function getSO(Request $req)
    {
        $roles=Session::get('ROLES');
        $a_id=Session::get('USER_ID');
        // $data = SOModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();

        if($roles == 0){
            $data=DB::table('sales_orders as so')
            ->leftjoin('users as u','u.id','so.a_id')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','u.name','u.delete as u_delete','u.is_active')
            ->where(['so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('so.updated_at','DESC')
            ->get();

            foreach($data as $d){
                $oth_obj=OATLHistoryModel::where(['so_id'=>$d->id,'status'=>1])->orderby('created_at','DESC')->get();

                foreach($oth_obj as $o){
                    $d->oth_status = $o->status;
                    $d->oth_id = $o->id;
                }
            }
            // $oth_obj=OATLHistoryModel::where(['id'=>$so_id])->orderby('created_at','DESC')->get();

        }else{
            $data=DB::table('sales_orders as so')
            ->leftjoin('users as u','u.id','so.a_id')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','u.name','u.delete as u_delete','u.is_active')
            ->where(['so.a_id'=>$a_id,'so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('so.updated_at','DESC')
            ->get();
            
            foreach($data as $d){
                $oth_obj=OATLHistoryModel::where(['so_id'=>$d->id,'status'=>1])->orderby('created_at','DESC')->get();

                foreach($oth_obj as $o){
                    $d->oth_status = $o->status;
                    $d->oth_id = $o->id;
                }
            }

        }
        
        // User Data
        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'a_id'=>$a_id])->orderby('created_at','DESC')->get();
        if(!empty($data)){
            return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'roles' => $roles ,'message' => 'Data Found'));
         }else{
            return ['status' => false, 'message' => 'No Data Found'];
         }

    }

    public function editSO(Request $req)
    {
        $edit_id = $req->get('edit_id');
        $so_number = $req->get('so_number');
   		$client_name = $req->get('client_name');
   		$project_name = $req->get('project_name');
   		$cp_ph_no = $req->get('cp_ph_no');
        $address = $req->get('address');
   		$cp_name = $req->get('cp_name');

        $labours = $req->get('labour');
        $labour=implode(',',$labours);

    	$a_id=Session::get('USER_ID');
        // return ['status' => true, 'message' => "$so_number,$client_name,$project_name,$address,$cp_name,$cp_ph_no,$labour"];
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
    		$u_obj->a_id=$a_id;
    		$res=$u_obj->update();
       		
            if($res){
                return ['status' => true, 'message' => 'SO Update Successfully'];
            }else{
               return ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            }
        }else{
            return ['status' => false, 'message' => 'Please Try Again..']; 
        }   

    }

    public function soDelete(Request $req)
    {
        $id=$req->get('id');
        $u_obj=SOModel::find($id);
        $u_obj->delete=1;
        $res=$u_obj->update();

        if($res){
            return ['status' => true, 'message' => 'SO Deleted Successfully'];
        }else{
           return ['status' => false, 'message' => 'SO Deletion Unsuccessfull...!'];
        }
    }

    public function checkTlStatus(Request $req)
    {
        $roles=Session::get('ROLES');
        $u_id = $req->get('id');
        $so_id = $req->get('so_id');
        $a_id=Session::get('USER_ID');
 
        $data=OATLHistoryModel::where(['lead_technician'=>$u_id,'status'=>1])->orderby('created_at','DESC')->get();
        $count = count($data);
        
        $oa_number = "";
        $d_so_id = 0 ;
        
            foreach($data as $d){
                $s_obj=SOModel::where(['delete'=>0,'id'=>$d->so_id,'a_id'=>$a_id,])->orderby('created_at','DESC')->get();
                foreach($s_obj as $s){
                    $oa_number = $s->so_number;
                }
                $d_so_id = $d->so_id;
            }
 
        // User Data
        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0,'a_id'=>$a_id])->where('id', '!=',$u_id)->orderby('created_at','DESC')->get();
        if(!empty($data)){
            return json_encode(array('status' => true ,'data' => $data,'u_obj' => $u_obj,'count' => $count,'so_id' => $so_id,'d_so_id' => $d_so_id,'roles' => $roles,'oa_number' =>$oa_number ,'message' => 'Data Found'));
         }else{
            return ['status' => false, 'message' => 'No Data Found'];
         }

    }


    public function removeTL(Request $req)
    {
        $oth_id=isset($_POST['oth_id']) ? $_POST['oth_id'] : "NA";
    	$oth_status=isset($_POST['oth_status']) ? $_POST['oth_status'] : "NA";
    	$oth_so_id=isset($_POST['oth_so_id']) ? $_POST['oth_so_id'] : "NA";

        //    dd($oth_id,$oth_status);

         //    $oth_obj=OATLHistoryModel::where(['so_id'=>$d->id,'status'=>1])->orderby('created_at','DESC')->get();
       if($oth_status == 1){
            $oth_obj=OATLHistoryModel::find($oth_id);
            $oth_obj->status=0;
            $res=$oth_obj->update();

            $s_obj=SOModel::find($oth_id);
            $s_obj->lead_technician=0;
            $res=$s_obj->update();
        }
        
        if($res){
            Session::put('SUCCESS_MESSAGE', "Remove TL Successfully...!");
        }else{
            Session::put('ERROR_MESSAGE',"Remove TL Unsuccessfull...!");
        }

        return redirect()->back();
    }

    public function forgotPass()
    {
        $mobile=$_POST['mobile'];
        $u_obj=UsersModel::where('whatsapp', 'LIKE', '%'.$mobile.'%')->where(['delete'=>0,'is_active'=>0])->select('id','name','delete','is_active','whatsapp','pers_email')->get();
        
        if(count($u_obj)>0)
        {
            if($u_obj[0]->delete!='1')
            {

                if($u_obj[0]->is_active!='1')
                {
            
                    $permitted='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    $pass=substr(str_shuffle($permitted),0,6);
                    $password=Hash::make($pass);

                    $c_obj=UsersModel::find($u_obj[0]->id);
                    $c_obj->password=$password;
                    $res=$c_obj->update();

                    if($res)
                    {
                        $name=$u_obj[0]->name;
                        $email=$u_obj[0]->pers_email;
                        $mobile_number=substr($u_obj[0]->whatsapp, -10);
                        $image=public_path('assets/images/mv-logo.png');

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
                            $mail->addAddress($email,$name);     
                            $mail->addReplyTo(config('constants.MAIL_REPLY'), 'Money Vision');

                            //Content
                            $mail->CharSet = "utf-8";       
                            // set charset to utf8
                            $mail->isHTML(true); 
                            $mail->AddEmbeddedImage($image, 'logo_2u');              
                            // Set email format to HTML
                            $mail->Subject = config('constants.AUTHOR_NAME')." - New Password.";
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
                                                                                   ." <strong>Mobile:  ".$mobile_number."</strong>"
                                                                                   ." <br>"
                                                                                   ." <strong>Password:  ".$pass."</strong>"
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

                            Session::put('SUCCESS_MESSAGE', "New Password & Login Details Sent On E-mail.");
                            return redirect()->route('login.page');
                        }
                        catch (Exception $e)
                        {

                            Session::put('ERROR_MESSAGE', "E-mail could not be sent.$mail->ErrorInfo");
                            return redirect()->route('login.page');
                        }
                        /*
                        $SMS_URL= config('constants.SMS_API_LINK');
                        $sender = config('constants.SMS_SENDER_ID');
                      
                        
                        $message="Hello User, \r\nYour Autogenerated Password: ".$pass." Please Update your password in profile section. \r\n- ".config('constants.SENDER_NAME');


                        $username=config('constants.SMS_USERNAME');
                        $password=config('constants.SMS_PASSWORD');
                        $entityid=config('constants.ENTITY_ID');
                        $templateid=config('constants.TEMPLATE_ID_6'); 

                        ============== 2. SMS PASSWORD UPDATE ==================

                        $ch = curl_init($SMS_URL);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$username&authkey=$password&sender=$sender&mobile=$mobile_number&text=$message&entityid=$entityid&templateid=$templateid&output=json");
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $response = curl_exec($ch);
                        //print($response);
                        curl_close($ch);

                        /*===========================================*/

                        
                        
                    
                    }
                }
                else
                {
                    Session::put('ERROR_MESSAGE','Account Is Deactivated..!');
                    return redirect()->route('login.page');
                }
            }
            else
            {
                Session::put('ERROR_MESSAGE','Account is Deleted.');
                return redirect()->route('login.page');
            }
        }
        else
        {
            Session::put('ERROR_MESSAGE','Account Does Not Exists..!');
            return redirect()->route('login.page');
        }
    }
}
