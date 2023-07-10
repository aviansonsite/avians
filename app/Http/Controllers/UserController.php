<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\SOModel;
use App\Models\AutoValuesModel;
use Session;
use DB;
use Hash;
use File;
use App\Http\Controllers\CommonController as Common;
class UserController extends Controller
{
    public function user_list()
    { 
		$role=Session::get('ROLES');
		$a_id=Session::get('USER_ID');
		
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

		}

		// dd($u_obj);
    	return view('users.users_list',compact('u_obj'));
    }

    public function postUser(Request $req)
    {
    	$user_id=empty($req->get('user_id')) ? null : $req->get('user_id');
    	$name=isset($_POST['name']) ? $_POST['name'] : "NA";
    	$email=isset($_POST['email']) ? $_POST['email'] : "NA";
    	$mobile=isset($_POST['mobile']) ? $_POST['mobile'] : "NA";
    	$pan_number=isset($_POST['pan_number']) ? $_POST['pan_number'] : "NA";
    	$aadhar_number=isset($_POST['aadhar_number']) ? $_POST['aadhar_number'] : "NA";

    	if(!isset($_POST['role'])){
    		Session::put('ERROR_MESSAGE',"Please select a ROLE!");
    		return redirect()->back();
    	}
    	$role=isset($_POST['role']) ? $_POST['role'] : "NA";
		
    	$check=UserModel::where(['mobile'=>$mobile,'delete'=>0])->exists();
		$check1=UserModel::where(['aadhar_number'=>$aadhar_number,'delete'=>0])->exists();
		if($user_id!=null)
    	{

			if($check1==false)
			{
				if($check==false)
				{
					$a_id=Session::get('USER_ID');
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
						$u_obj->aadhar_number=$aadhar_number;
						$u_obj->delete=0;
						$u_obj->is_active=0;
						$u_obj->a_id=$a_id;

						$of1=null;
						$of2=null;
						$of3=null;
		
					if($req->hasfile('aadhar_file'))
					{
						// dd($req->hasfile('aadhar_file'));
						$aadhar_file = $req->file('aadhar_file');
						$of1 = rand(1,999).'.'.$aadhar_file->getClientOriginalExtension(); 
						$aadhar_file->move(public_path('files/user/'), $of1);
						$u_obj->aadhar_file=$of1;
					}
			
					if($req->hasfile('pan_file'))
					{
						$pan_file = $req->file('pan_file');
						$of2 = rand(1,999).'.'.$pan_file->getClientOriginalExtension(); 
						$pan_file->move(public_path('files/user/'), $of2);
						$u_obj->pan_file=$of2;
					}

					if($req->hasfile('photo_file'))
					{
						$photo_file = $req->file('photo_file');
						$of3 = rand(1,999).'.'.$photo_file->getClientOriginalExtension(); 
						$photo_file->move(public_path('files/user/'), $of3);
						$u_obj->photo_file=$of3;
					}
					$res=$u_obj->update();

					if($res){
						Session::put('SUCCESS_MESSAGE', 'User Updated Successfully...!');
					}else{
						Session::put('ERROR_MESSAGE',"User Not Updated...!");
					}
				}else{
					Session::put('ERROR_MESSAGE',"User With This Number Already Exist...!");
				}

			}else{
				Session::put('ERROR_MESSAGE',"User With This AADHAR Number Already Exist...!");
			}

			

		}else{

			if($check1==false)
			{

				if($check==false)
				{

					$a_id=Session::get('USER_ID');
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
					$u_obj->aadhar_number=$aadhar_number;
					$u_obj->delete=0;
					$u_obj->is_active=0;
					$u_obj->a_id=$a_id;
					
					$of1=null;
					$of2=null;
					$of3=null;

		
					if($req->hasfile('aadhar_file'))
					{
						$aadhar_file = $req->file('aadhar_file');
						$of1 = rand(1,999).'.'.$aadhar_file->getClientOriginalExtension(); 
						$aadhar_file->move(public_path('files/user/'), $of1);
						$u_obj->aadhar_file=$of1;
					}
			
					if($req->hasfile('pan_file'))
					{
						$pan_file = $req->file('pan_file');
						$of2 = rand(1,999).'.'.$pan_file->getClientOriginalExtension(); 
						$pan_file->move(public_path('files/user/'), $of2);
						$u_obj->pan_file=$of2;
					}

					if($req->hasfile('photo_file'))
					{
						$photo_file = $req->file('photo_file');
						// dd($photo_file);

						$of3 = rand(1,999).'.'.$photo_file->getClientOriginalExtension(); 
						$photo_file->move(public_path('files/user/'), $of3);
						$u_obj->photo_file=$of3;
					}
					$res=$u_obj->save();

					if($res){
						// $mobile_number=substr($mobile, -10);

						// $SMS_URL= config('constants.SMS_API_LINK');
						// $sender = config('constants.SMS_SENDER_ID');
					
						// $message="Hello User, \r\nYour Autogenerated Password: ".$pass." Please Update your password in profile section. \r\n- ".config('constants.SENDER_NAME');


						// $username=config('constants.SMS_USERNAME');
						// $password=config('constants.SMS_PASSWORD');
						// $entityid=config('constants.ENTITY_ID');
						// $templateid=config('constants.TEMPLATE_ID_6'); 

						// /*============== 2. SMS PASSWORD UPDATE ==================*/

						// $ch = curl_init($SMS_URL);
						// curl_setopt($ch, CURLOPT_POST, true);
						// curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$username&authkey=$password&sender=$sender&mobile=$mobile_number&text=$message&entityid=$entityid&templateid=$templateid&output=json");
						// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						// $response = curl_exec($ch);
						// //print($response);
						// curl_close($ch);

						/*===========================================*/


						Session::put('SUCCESS_MESSAGE', "User Created Successfully...!.$pass");
					}else{
						Session::put('ERROR_MESSAGE',"User Not Created...!");
					}

				}else{
					Session::put('ERROR_MESSAGE',"User With This Number Already Exist...!");
				}

			}else{
				Session::put('ERROR_MESSAGE',"User With This AADHAR Number Already Exist...!");
			}
			
		}
    	
		return redirect()->back();

    }

    public function edit_users(Request $req)
    {
    	$edit_id=$req->get('edit_id');
    	$name=isset($_POST['name']) ? $_POST['name'] : "NA";
    	$email=isset($_POST['email']) ? $_POST['email'] : "NA";
    	$mobile=isset($_POST['mobile']) ? $_POST['mobile'] : "NA";
    	if(!isset($_POST['role'])){
    		Session::put('ERROR_MESSAGE',"Please select a ROLE!");
    		return redirect()->back();
    	}
    	$roles=$_POST['role'];
        $role=implode(',',$roles);

    	$a_id=Session::get('USER_ID');
		$permitted='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$pass=substr(str_shuffle($permitted),0,6);
		$password=Hash::make($pass);


		$u_obj=UserModel::find($edit_id);
		$u_obj->name=$name;
		$u_obj->email=$email;
		$u_obj->mobile=$mobile;
		$u_obj->password=$password;
		$u_obj->role=$role;
		$res=$u_obj->update();

		if($res)
		{
			$mobile_number=substr($mobile, -10);

			$SMS_URL= config('constants.SMS_API_LINK');
			$sender = config('constants.SMS_SENDER_ID');
		  
			$message="Hello User, \r\nYour Autogenerated Password: ".$pass." Please Update your password in profile section. \r\n- ".config('constants.SENDER_NAME');


			$username=config('constants.SMS_USERNAME');
			$password=config('constants.SMS_PASSWORD');
			$entityid=config('constants.ENTITY_ID');
			$templateid=config('constants.TEMPLATE_ID_6'); 

			/*============== 2. SMS PASSWORD UPDATE ==================*/

			$ch = curl_init($SMS_URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$username&authkey=$password&sender=$sender&mobile=$mobile_number&text=$message&entityid=$entityid&templateid=$templateid&output=json");
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			//print($response);
			curl_close($ch);

			/*===========================================*/


			Session::put('SUCCESS_MESSAGE', 'User Updated Successfully...!');
		}
		else
		{
			Session::put('ERROR_MESSAGE',"User Not Updated...!");
		}
    	
    	return redirect()->back();

    }


    public function change_status($id)
    {
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
		$id=$req->get('id');
        $u_obj=UserModel::find($id);
        $u_obj->delete=1;
        $u_obj->is_active=1;
        $res=$u_obj->update();
        // $res=$id;


		$role=Session::get('ROLES');
    	$data=UserModel::where(['delete'=>0])->where('role','!=','0')->orderby('created_at','DESC')->get();

		if(!empty($res)){
            return json_encode(array('status' => true ,'data' => $data,'role'=>$role,'message' => 'User Deleted Successfully...!'));
         }else{
            return ['status' => false, 'message' => 'User Deletion Unsuccessfull...!'];
         }
    }

	public function resPass(Request $req)
    {
		$id=$req->get('rp_id');
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
}
