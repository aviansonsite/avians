<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\SOModel;
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
                
                if($res){
                    return ['status' => true, 'message' => 'SO Update Successfully'];
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



                
                if($res){
                    return ['status' => true, 'message' => 'SO Add Successfully'];
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

        // $data = SOModel::where(['delete'=>0])->orderby('updated_at','DESC')->get();
        $data=DB::table('sales_orders as so')
            ->leftjoin('users as u','u.id','so.a_id')
            ->select('so.id','so.address','so.a_id','so.client_name','so.cp_name','so.cp_ph_no','so.delete','so.labour','so.project_name','so.so_number','so.lead_technician','so.updated_at','u.name','u.delete as u_delete','u.is_active')
            ->where(['so.delete'=>0,'u.delete'=>0,'u.is_active'=>0])
            ->orderby('updated_at','DESC')
            ->get();
        // User Data
        $u_obj=UserModel::where(['delete'=>0,'role'=>3,'is_active'=>0])->orderby('created_at','DESC')->get();
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
}
