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
use App\Models\PunchInOutModel;
use App\Models\OATLHistoryModel;
use Session;
use Hash;
use File;
use DB;

class LabourAPIController extends Controller
{
    public function postExpenseLPaymentAPI(Request $req)
    {
        $edit_id=$req->get('exp_edit_id');
        $exp_desc = $req->get('exp_desc');
   		$exp_date = $req->get('exp_date');
   		$expense_amnt = $req->get('expense_amnt');
        $exp_type = $req->get('exp_type');
        $user_id = $req->get('user_id');
        // $attachment = $req->get('attachment');
        $oth_id = $req->get('exp_so');
        // $so=implode(',',$sos);

    	// $a_id=Session::get('USER_ID');
        $user_id = CommonController::decode_ids($user_id);
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

    public function punchInAPI(Request $req)
    {
    	// $a_id=Session::get('USER_ID');
        $user_id = $req->get('user_id');
        $p_in_so=isset($_POST['p_in_so']) ? $_POST['p_in_so'] : "NA";
    	$p_in_labour=isset($_POST['p_in_labour']) ? $_POST['p_in_labour'] : "NA";
    	$p_in_remark=isset($_POST['p_in_remark']) ? $_POST['p_in_remark'] : "NA";
    	$p_in_date=isset($_POST['p_in_date']) ? $_POST['p_in_date'] : "NA";
        $p_in_latitude=isset($_POST['p_in_latitude']) ? $_POST['p_in_latitude'] : "NA";
    	$p_in_longitude=isset($_POST['p_in_longitude']) ? $_POST['p_in_longitude'] : "NA";
        $user_id = CommonController::decode_ids($user_id);
        // $u_id = strval($user_id); 
        // array_push($p_in_labour, $user_id);    //Push user id for attendance
        // $tech_count = count($p_in_labour);
        return ['status' => true,'p_in_so' => $p_in_so,'p_in_labour' => $p_in_labour,'p_in_remark' => $p_in_remark,'p_in_date' => $p_in_date,'p_in_latitude' => $p_in_latitude,'p_in_longitude' => $p_in_longitude]; 
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
                $u_obj->a_id=$user_id;

                if($req->hasfile('attachment'))  
                {  
                    $file=$req->file('attachment');  
                    $extension=$file->getClientOriginalExtension();  
                    $filename= $extension."_".md5($file. microtime()).'.'.$extension;
                    // $fileName= '.png'."_".md5($file. microtime()).'.png';
                    $file->move(public_path('files/attendance/punchIn/'), $filename);
                
                    // $image->image=$filename;  
                    $u_obj->pin_img=$filename;
                }  

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
        $tech_count = count($pout_labour);
        // dd($tech_count);

        // $pout_so=implode(',',$pout_so);
        // $pout_labour=implode(',',$pout_labour);

        
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
                        $fileName= '.png'."_".md5($img. microtime()).'.png';
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

                            $fileName= '.png'."_".md5($img. microtime()).'.png';

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
}
