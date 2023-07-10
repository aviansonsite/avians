<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AutoValuesModel;

class CommonController extends Controller
{
    static function custEmpNumber()
    {
        $emp_obj=AutoValuesModel::select('id','inv_no','cur_yr','nxt_yr')->orderby('id','desc')->first();
        $emp_number;
        if($emp_obj){


            // UPDATE ONLY Employee Number
            $cpoi_obj= AutoValuesModel::find($emp_obj->id);
            $cpoi_obj->inv_no= ($emp_obj->inv_no) + 1;
            $res=$cpoi_obj->update();

            $num_padded = sprintf("%03d", (($emp_obj->inv_no) + 1));
            $emp_number = "P-$num_padded";


        }else{

            // DATABSE START
            $year = date("y"); 
            //insert new value
            $nxt_yr = $year+1;
            $cpoi_obj= new AutoValuesModel();
            $cpoi_obj->inv_no=1;
            $cpoi_obj->cur_yr=$year;
            $cpoi_obj->nxt_yr=$nxt_yr;
            $res=$cpoi_obj->save();

            $num_padded = sprintf("%03d", 1);
            $emp_number = "P-$num_padded";

        }

        return $emp_number;
    }

    static function dateDMY($date)
    {
        return date("d-m-Y",strtotime($date));
    }

    static function decode_ids($id)
    {
        $sid1=base64_decode($id);
        $nid1=str_rot13($sid1);
        $bid1=explode('IsIpl',$nid1);
        $id1=base64_decode($bid1[0]);
        $olddata=explode('-',$id1);

        return $olddata[0];
    }

    static function encode_ids($id)
    {
        $id3=$id.'-'.time();
        $bid=base64_encode($id3);
        $nid=$bid.'IsIpl'.rand(10,1000);
        $sid=str_rot13($nid);
        $j_id=base64_encode($sid);

        return $j_id;
    }
}
