<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfileModel;
use Session;
use File;

class CompanyProfileController extends Controller
{
    public function company_profile()
    {
    	$u_obj=CompanyProfileModel::all();
    	return view('pages.company',compact('u_obj'));
    }

    public function edit_company(Request $req)
    {
    	$c_id=$req->get('c_id');
    	$company_name=isset($_POST['company_name']) ? $_POST['company_name'] : "NA";
    	$website=isset($_POST['company_website']) ? $_POST['company_website'] : "NA";
    	$company_email=isset($_POST['company_email']) ? $_POST['company_email'] : "NA";
    	$accounts_email=isset($_POST['accounts_email']) ? $_POST['accounts_email'] : "NA";
    	$address=isset($_POST['address']) ? $_POST['address'] : "NA";
    	$city=isset($_POST['city']) ? $_POST['city'] : "NA";
    	$state=isset($_POST['state']) ? $_POST['state'] : "NA";
    	$pincode=isset($_POST['pincode']) ? $_POST['pincode'] : 0;
    	$pan_number=isset($_POST['pan_number']) ? $_POST['pan_number'] : "NA";
    	$gst_number=isset($_POST['gst_number']) ? $_POST['gst_number'] : "NA";
    	$iso_certificate_number=isset($_POST['iso_certificate_number']) ? $_POST['iso_certificate_number'] : "NA";
    	$primary_mobile=isset($_POST['primary_mobile']) ? $_POST['primary_mobile'] : "NA";
    	$alternate_mobile=isset($_POST['alternate_mobile']) ? $_POST['alternate_mobile'] : "NA";
    	$iec_code=isset($_POST['iec_code']) ? $_POST['iec_code'] : "NA";
    	$account_name=isset($_POST['account_name']) ? $_POST['account_name'] : "NA";
    	$account_number=isset($_POST['account_number']) ? $_POST['account_number'] : "NA";
    	$branch=isset($_POST['branch']) ? $_POST['branch'] : "NA";
    	$ifsc_code=isset($_POST['ifsc_code']) ? $_POST['ifsc_code'] : "NA";
    	$bank_name=isset($_POST['bank_name']) ? $_POST['bank_name'] : "NA";
        $bike_pkm_rate=isset($_POST['bike_pkm_rate']) ? $_POST['bike_pkm_rate'] : "NA";
    	$car_pkm_rate=isset($_POST['car_pkm_rate']) ? $_POST['car_pkm_rate'] : "NA";

    	$c_obj=CompanyProfileModel::find($c_id);
    	$c_obj->company_name=$company_name;
        $c_obj->website=$website;
        $c_obj->company_email=$company_email;
        $c_obj->account_email=$accounts_email;
        $c_obj->address=$address;
        $c_obj->city=$city;
        $c_obj->state=$state;
        $c_obj->pincode=$pincode;
        $c_obj->pan_number=$pan_number;
        $c_obj->gst_number=$gst_number; 
        $c_obj->iec_code=$iec_code;
        $c_obj->iso_certificate_number=$iso_certificate_number;
        $c_obj->primary_mobile=$primary_mobile;
        $c_obj->alternate_mobile=$alternate_mobile;
        $c_obj->account_name=$account_name;
        $c_obj->account_number=$account_number;
        $c_obj->branch=$branch;
        $c_obj->bank_name=$bank_name;
        $c_obj->ifsc_code=$ifsc_code;
        $c_obj->bike_pkm_rate=$bike_pkm_rate;
        $c_obj->car_pkm_rate=$car_pkm_rate;

        $of1=null;
        $of2=null;
        $of3=null;
        $of4=null;
        $destinationPath = 'public/files/company/';

        if($req->hasfile('logo'))
        {
            $logo = $req->file('logo');
            $of1 = rand(1,999).'.'.$logo->getClientOriginalExtension(); 
            $logo->move(public_path('files/company/'), $of1);
            $c_obj->logo=$of1;
        }

        if($req->hasfile('pan_file'))
        {
            // dd($req->hasfile('pan_file'));

            $pan_file = $req->file('pan_file');
            $of2 = rand(1,999).'.'.$pan_file->getClientOriginalExtension(); 
            $pan_file->move(public_path('files/company/'), $of2);
            $c_obj->pan_file=$of2;
        }

        if($req->hasfile('gst_file'))
        {
            $gst_file = $req->file('gst_file');
            $of3 = rand(1,999).'.'.$gst_file->getClientOriginalExtension(); 
            $gst_file->move(public_path('files/company/'), $of3);
            $c_obj->gst_file=$of3;
        }

        if($req->hasfile('iso_file'))
        {
            $iso_file = $req->file('iso_file');
            $of4 = rand(1,999).'.'.$iso_file->getClientOriginalExtension(); 
            $iso_file->move(public_path('files/company/'), $of4);
            $c_obj->iso_file=$of4;
        }

        $res1=$c_obj->update();

        if($res1){
            Session::put('SUCCESS_MESSAGE', 'Company Profile Updated Successfully...!');
        }else{
            Session::put('ERROR_MESSAGE', 'Company Profile Updated Unsuccessfull...!');
        }

        return redirect()->back();
    }
}
