@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
<?php use App\Http\Controllers\CommonController as Common; ?>  

@section('title',"Attendance Management | $title")
@push('datatable_css')
{!! Html::style('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
@endpush
@push('page_css')
{!! Html::style('assets/libs/select2/css/select2.min.css') !!}
<style>
    .form-floating>.form-control, .form-floating>.form-select {
        height: calc(2.8rem + 1px) !important;
        padding: 1rem .75rem;
    }
    .form-floating>.textuti {
        height: calc(2.8rem + 30px) !important;
    }
    .select2
     {
            width: 100% !important;
     }
     .amsify-suggestags-list{
        z-index: 1 !important;
        width: 100% !important;
     }
     textarea,
    .textarea {
      min-height: inherit;
      height: auto;
    }
    .form-check
    {
        display: inline-block;
    }

    /** SPINNER CREATION **/

    .loader {
    position: relative;
    text-align: center;
    margin: 15px auto 35px auto;
    z-index: 9999;
    display: block;
    width: 80px;
    height: 80px;
    border: 10px solid rgba(0, 0, 0, .3);
    border-radius: 50%;
    border-top-color: #000;
    animation: spin 1s ease-in-out infinite;
    -webkit-animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
    to {
        -webkit-transform: rotate(360deg);
    }
    }

    @-webkit-keyframes spin {
    to {
        -webkit-transform: rotate(360deg);
    }
    }

    #results { padding:20px; border:1px solid; background:#ccc; }
</style>
@endpush
@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">Attendance Management</h4>
                           <div class="ms-auto">
                           @foreach($p_id as $p)
                           <?php 
                                $pout_date = $p->pout_date;
                           ?>
                               
                           @endforeach
                           @if($t_count == 0)
                                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" data-bs-toggle="modal" data-bs-target="#punchInModal" style="margin-left: 10px;">
                                    <i class="mdi mdi-plus font-size-11"></i> Punch In
                                    </button> 
                                @endif
                                @if($t_count == 1 &&  $pout_date == null)
                                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" style="margin-left: 10px;" data-pin_id="{{$p->id}}" id="pout_btn" data-pin_oth_id="{{$p->pin_oth_id}}" data-pin_u_id="{{$p->pin_u_ids}}" >
                                    <i class="mdi mdi-plus font-size-11"></i> Punch Out
                                    </button>
                                @endif

                            </div>
                        </div>
                        
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                        {!! Form::open(['class'=>"form-horizontal",'id'=>"exp_search_form"]) !!}
                        <div class="row">
                            <?php $tdate=date("Y-m-d"); ?>
                            <div class="col-md-3 col-sm-12 col-lg-3">
                                <div class="form-floating mb-3">
                                    <input type="date" max="{{$tdate}}" class="form-control" id="from_date"  name="from_date" required placeholder="dd-mm-yyyy" value="{{$tdate}}">
                                    <label for="from_date">From Date <sup class="text-danger">*</sup></label>
                                    <small><span class="text-danger" id="fderror" style="font-size: 11px !important;"></span></small>
                                </div>
                            </div>
    
                            <div class="col-md-3 col-sm-12 col-lg-3">
                                <div class="form-floating mb-3">
                                    <input type="date" max="{{$tdate}}" class="form-control" id="to_date"  name="to_date" required placeholder="dd-mm-yyyy" 
                                    value="{{$tdate}}">
                                    <label for="to_date">To Date <sup class="text-danger">*</sup></label>
                                    <small><span class="text-danger" id="tderror" style="font-size: 11px !important;"></span></small>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-12 col-lg-3">
                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="pio_ftd_records">Search</button>
                            </div>
                        </div>
                        {!! Form::close() !!} 
                        <div class="table-responsive">
                            <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="datatable"> 
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 20px;">Sr.No</th>
                                        <th scope="col" style="width: 100px">Date <br>(DD/MM/YY)</th>
                                        <th scope="col" style="width: 100px">Punch In <br>(HH:MM:SS)</th>
                                        <th scope="col" style="width: 100px">Punch Out <br>(HH:MM:SS)</th>
                                        <th scope="col" style="width: 100px">Total Time <br>(HH:MM:SS)</th>
                                    </tr>
                                </thead>
                                <tbody id="pio_records">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- punch in modal content -->
 <div id="punchInModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Punch In</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!! Form::open(['class'=>"form-horizontal punch_in_form",'method'=>"post",'url'=>'punch_in','enctype'=>'multipart/form-data','files' => 'true' ,'id'=>'postPunchInform']) !!}
            <div class="modal-body">
                <div class="row">
                    <!-- <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA <sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="p_in_so" name="p_in_so[]" placeholder="Select SO">
                                <option value="all">All</option>
                               
                            </select>
                            <span class="text-danger error" id="pin_soerror"></span>
                        </div>
                    </div> -->
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="p_in_so" required name="p_in_so">
                                
                            </select>
                            <span class="text-danger error" id="esoerror"></span>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="p_in_labour" name="p_in_labour[]" placeholder="Select Technician">
                                
                               
                            </select>
                            <span class="text-danger error" id="pin_lerror"></span>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_remark">Enter Remark</label>
                            <textarea class="form-control" id="p_in_remark" placeholder="Enter Remark" name="p_in_remark" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" rows="3"></textarea>
                            <span class="text-danger error" id="pderror"></span>

                        </div>
                    </div>
                    <!-- <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="p_in_work_desc" placeholder="Enter Work Description" required name="p_in_work_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                            <label for="p_in_work_desc">Work Description</label>
                            <span class="text-danger error" id="pderror"></span>

                        </div>
                    </div> -->
                    <?php $tdate=date("Y-m-d");?>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="p_in_date" placeholder="Punch In Date" name="p_in_date" required max="{{$tdate}}" value="{{$tdate}}" readonly>
                            <label for="p_in_date">Date<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pin_derror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
                        <!-- <a href="" id="editU"><i class="fa fa-eye"></i> Take a Photo</a> -->
                        <input type="button" class="btn btn-primary btn-sm waves-effect waves-light" value="Take a Selfie" id="photo">
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12 photo text-center">
                        <div class="form-floating mb-3">
                            <div id="my_camera" class="text-center"></div>
                            <br/>
                            <input type="button" class="btn btn-primary btn-sm waves-effect waves-light" value="Take Snapshot" onClick="take_snapshot()">
                            <input type="hidden" name="pin_img" class="image-tag ">
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12 photo1 text-center">
                        <p id="results">Your captured image will appear here...</p>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="p_in_latitude" placeholder="Latitude" name="p_in_latitude" maxlength="10" required readonly>
                            <label for="p_in_latitude">Latitude<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pin_laerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="p_in_longitude" placeholder="Longitude" name="p_in_longitude" maxlength="10" required readonly>
                            <label for="p_in_longitude">Longitude<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pin_loerror"></span>
                        </div>
                    </div>
                    <!-- <p id="demo"></p> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" id="punch_in">Save</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- punch in history modal content -->
<div id="pinhistryModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Punch In History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_soh" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="p_in_soh" required name="p_in_soh" disabled>
                            @foreach($s_obj1 as $so)
                                <option value="{{$so->oth_id}}">{{$so->so_number}}</option>
                            @endforeach
                            </select>
                            <span class="text-danger error" id="esoerror"></span>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_labourh" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="p_in_labourh" name="p_in_labourh[]" placeholder="Select Technician" disabled>
                                <option value="all">All</option>
                                @foreach($us_obj as $u)
                                    <option value="{{$u->id}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error" id="lerror"></span>
                        </div>
                    </div>
                      
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="p_in_remarkh">Enter Remark</label>
                            <textarea class="form-control" id="p_in_remarkh" placeholder="Enter Remark" name="p_in_remarkh" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" readonly rows="3"></textarea>
                            <span class="text-danger error" id="pderror"></span>

                        </div>
                    </div>
                    <!-- <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <textarea class="form-control" id="p_in_work_desc" placeholder="Enter Work Description" required name="p_in_work_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                            <label for="p_in_work_desc">Work Description</label>
                            <span class="text-danger error" id="pderror"></span>

                        </div>
                    </div> -->
                    <?php $tdate=date("Y-m-d");?>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="p_in_dateh" placeholder="Punch In Date" name="p_in_dateh" required max="{{$tdate}}" value="{{$tdate}}" readonly>
                            <label for="p_in_dateh">Date<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pdaerror"></span>
                        </div>
                    </div>
                  
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <a href="" id="attachment1" target="_blank"><i class="fa fa-eye"></i> View Photo</a>
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="p_in_latitudeh" placeholder="Latitude" name="p_in_latitudeh" maxlength="10" required readonly>
                            <label for="p_in_latitudeh">Latitude<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="uaerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="p_in_longitudeh" placeholder="Longitude" name="p_in_longitudeh" maxlength="10" required readonly>
                            <label for="p_in_longitudeh">Longitude<sup class="text-danger">*</sup></label>
                            <!-- <span class="text-danger error" id="uaerror"></span> -->
                        </div>
                    </div>
                    <!-- <p id="demo"></p> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- punch out modal content -->
<div id="punchOutModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Punch Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!! Form::open(['class'=>"form-horizontal punchout_form",'method'=>"post",'url'=>'punch_out','enctype'=>'multipart/form-data','files' => 'true' ,'id'=>'postPunchOutform']) !!}
            <div class="modal-body">
                <div class="row">
                <input type="hidden" name="pin_id" id="pin_id" value="">
                   
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="pout_so" required name="pout_so">
                                
                            </select>
                            <span class="text-danger error" id="esoerror"></span>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="pout_labour" name="pout_labour[]" placeholder="Select Technician">
                                
                            </select>
                            <span class="text-danger error" id="pout_lerror"></span>
                        </div>
                    </div>
                      
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_remark">Enter Remark</label>
                            <textarea class="form-control" id="pout_remark" placeholder="Enter Remark" name="pout_remark" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" rows="3"></textarea>
                            <span class="text-danger error" id="pout_rerror"></span>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_work_desc">Work Description<sup class="text-danger">*</sup></label>
                            <textarea class="form-control" id="pout_work_desc" placeholder="Enter Work Description" name="pout_work_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" rows="3"></textarea>
                            <span class="text-danger error" id="pout_wderror"></span>

                        </div>
                    </div>
                    <?php $tdate=date("Y-m-d");?>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="pout_date" placeholder="Punch In Date" name="pout_date" required max="{{$tdate}}" value="{{$tdate}}" readonly>
                            <label for="pout_date">Date<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pout_derror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6 mb-3">
                        <!-- <a href="" id="editU"><i class="fa fa-eye"></i> Take a Photo</a> -->
                        <input type="button" class="btn btn-primary btn-sm waves-effect waves-light" value="Take a Selfie" id="ophoto">
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12 ophoto text-center">
                        <div class="form-floating mb-3">
                            <div id="my_camera1" class="text-center"></div>
                            <br/>
                            <input type="button" class="btn btn-primary btn-sm waves-effect waves-light" value="Take Snapshot" onClick="take_snapshot1()">
                            <input type="hidden" name="pout_img" class="image-tag1">
                            <span class="text-danger error" id="pout_ierror"></span>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12 ophoto1 text-center">
                        <p id="results1">Your captured image will appear here...</p>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="pout_latitude" placeholder="Latitude" name="pout_latitude" maxlength="10" required readonly>
                            <label for="pout_latitude">Latitude<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pout_laerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="pout_longitude" placeholder="Longitude" name="pout_longitude" maxlength="10" required readonly>
                            <label for="pout_longitude">Longitude<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pout_loerror"></span>
                        </div>
                    </div>
                    <!-- <p id="demo"></p> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" id="punch_out">Save</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- punch out history modal content -->
<div id="pouthistryModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Punch Out History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                

                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_soh" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="pout_soh" required name="pout_soh" disabled>
                            @foreach($s_obj1 as $so)
                                <option value="{{$so->oth_id}}">{{$so->so_number}}</option>
                            @endforeach
                            </select>
                            <span class="text-danger error" id="esoerror"></span>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_labourh" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="pout_labourh" name="pout_labourh[]" placeholder="Select Technician" disabled>
                                <option value="all">All</option>
                                @foreach($us_obj as $u)
                                    <option value="{{$u->id}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error" id="lerror"></span>
                        </div>
                    </div>
                      
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_remarkh">Enter Remark</label>
                            <textarea class="form-control" id="pout_remarkh" placeholder="Enter Remark" required name="pout_remarkh" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" readonly rows="3"></textarea>
                            <span class="text-danger error" id="pderror"></span>

                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="pout_work_desch">Work Description</label>
                            <textarea class="form-control" id="pout_work_desch" placeholder="Enter Work Description" required name="pout_work_desch" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" readonly rows="3"></textarea>
                            <span class="text-danger error" id="pderror"></span>

                        </div>
                    </div>
                    <?php $tdate=date("Y-m-d");?>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="pout_dateh" placeholder="Punch In Date" name="pout_dateh" required max="{{$tdate}}" value="{{$tdate}}" readonly>
                            <label for="pout_dateh">Date<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pdaerror"></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <a href="" id="attachment2" target="_blank"><i class="fa fa-eye"></i> View Photo</a>
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="pout_latitudeh" placeholder="Latitude" name="pout_latitudeh" maxlength="10" required readonly>
                            <label for="pout_latitudeh">Latitude<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="uaerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="pout_longitudeh" placeholder="Longitude" name="pout_longitudeh" maxlength="10" required readonly>
                            <label for="pout_longitudeh">Longitude<sup class="text-danger">*</sup></label>
                            <!-- <span class="text-danger error" id="uaerror"></span> -->
                        </div>
                    </div>
                    <!-- <p id="demo"></p> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@include('common.delete_modal')    
@stop
@push('datatable_js')
    {!! Html::script('assets/libs/datatables.net/js/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') !!}
    <script>
        $(document).ready(function(){
            $('#datatable').dataTable();    
        });
    </script>
@endpush

@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    $(document).ready(function(){
        var $body = $("body");

        $('#p_in_so,#p_in_labour').select2({ 
            dropdownParent: $('#punchInModal') 
        });
        $('#pout_so,#pout_labour').select2({ 
            dropdownParent: $('#punchOutModal') 
        });
        $('#p_in_soh,#p_in_labourh').select2({ 
            dropdownParent: $('#pinhistryModal') 
        });
        $('#pout_soh,#pout_labourh').select2({ 
            dropdownParent: $('#pouthistryModal') 
        });
    });
    var $body = $("body");

    // For punch out Validation
    var n =0;
    $("#postPunchOutform").submit(function(event) 
    {
        // alert('hi');
        var pout_labour = $('#pout_labour').val();
        var pout_so= $('#pout_so').val();
        var pout_date = $('#pout_date').val();
        var pout_latitude= $('#pout_latitude').val();
        var pout_longitude = $('#pout_longitude').val();
        var pout_work_desc = $('#pout_work_desc').val();

        var pout_img = $('#pout_img').val();

        // alert(pout_img)

        n=0;   
        // if( $.trim(pout_img).length == 0 )
        // {
        //     $('#pout_ierror').text('Please Take a Selfie.');
        //     event.preventDefault();
        // }else{
        //     $('#pout_ierror').text('');
        //     ++n;
        // }

        if( $.trim(pout_work_desc).length == 0 )
        {
            $('#pout_wderror').text('Please Enter Work Description.');
            event.preventDefault();
        }else{
            $('#pout_wderror').text('');
            ++n;
        }

        if( $.trim(pout_latitude).length == 0 )
        {
            $('#pout_laerror').text('Please Enter Latitude');
            event.preventDefault();
        }else{
            $('#pout_laerror').text('');
            ++n;
        }

        if( $.trim(pout_date).length == 0 )
        {
            $('#pdaerror').text('Please Enter date.');
            event.preventDefault();
        }else{
            $('#pdaerror').text('');
            ++n;
        }
       
        if( $.trim(pout_longitude).length == 0 )
        {
            $('#pout_loerror').text('Please Enter Longitude.');
            event.preventDefault();
        }else{
            $('#pout_loerror').text('');
            ++n;
        }

        if( $.trim(pout_so).length == 0 )
        {
            $('#pout_soerror').text('Please Select OA.');
            event.preventDefault();
        }else{
            $('#pout_soerror').text('');
            ++n;
        }

        if( $.trim(pout_labour).length == 0 )
        {
            $('#pout_lerror').text('Please Select Technician.');
            event.preventDefault();
        }else{
            $('#pout_lerror').text('');
            ++n;
        }
    });

    // For punch out Validation
    var n =0;
    $("#postPunchInform").submit(function(event) 
    {
        // alert('hi');
        var pin_labour = $('#p_in_labour').val();
        var pin_so= $('#p_in_so').val();
        var pin_date = $('#p_in_date').val();
        var pin_latitude= $('#p_in_latitude').val();
        var pin_longitude = $('#p_in_longitude').val();
        // var pin_img = $('#p_in_img').val();

        // alert(pout_img)

        n=0;   
        // if( $.trim(pout_img).length == 0 )
        // {
        //     $('#pout_ierror').text('Please Take a Selfie.');
        //     event.preventDefault();
        // }else{
        //     $('#pout_ierror').text('');
        //     ++n;
        // }

        if( $.trim(pin_latitude).length == 0 )
        {
            $('#pin_laerror').text('Please Enter Latitude.');
            event.preventDefault();
        }else{
            $('#pin_laerror').text('');
            ++n;
        }

        if( $.trim(pin_date).length == 0 )
        {
            $('#pdaerror').text('Please Enter date.');
            event.preventDefault();
        }else{
            $('#pdaerror').text('');
            ++n;
        }
       
        if( $.trim(pin_longitude).length == 0 )
        {
            $('#pin_loerror').text('Please Enter Longitude.');
            event.preventDefault();
        }else{
            $('#pin_loerror').text('');
            ++n;
        }

        if( $.trim(pin_so).length == 0 )
        {
            $('#pin_soerror').text('Please Select OA.');
            event.preventDefault();
        }else{
            $('#pin_soerror').text('');
            ++n;
        }

        if( $.trim(pin_labour).length == 0 )
        {
            $('#pin_lerror').text('Please Select Technician.');
            event.preventDefault();
        }else{
            $('#pin_lerror').text('');
            ++n;
        }
    });

    // For from date ,to date records
    $(document).on("click",'#pio_ftd_records',function()
    {           
               
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_pio_records')}}",
            type :'get',
            data : {from_date:from_date,to_date:to_date},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data);
                $("#datatable").DataTable().destroy();

                content ="";  
                var i = 0;       
      
                $.each(data.data,function(index,row){
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }
                  
                    if(row.pin_date != null){
                        var pin_date = formatDate (row.pin_date); // "18/01/10"
                    }else{
                        var pin_date = " - "
                    }

                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+pin_date+"</td>";
                        content +="<td>"+row.pin_time+"<br><span class='badge badge-soft-primary pinhistry' data-pin_oth_id='"+row.pin_oth_id+"' data-pin_u_id='"+row.pin_u_id+"' data-pin_remark='"+row.pin_remark+"' data-pin_latitude='"+row.pin_latitude+"' data-pin_date='"+row.pin_date+"' data-pin_longitude='"+row.pin_longitude+"' data-pin_img='"+row.pin_img+"'>View History</span></td>";

                        if(row.created_at == row.updated_at){ 
                            content +="<td> - </td>";

                        }else{
                            content +="<td>"+row.pout_time+"<br><span class='badge badge-soft-primary pouthistry' data-pout_oth_id='"+row.pout_oth_id+"' data-pout_u_id='"+row.pout_u_id+"' data-pout_remark='"+row.pout_remark+"' data-pout_work_desc='"+row.pout_work_desc+"' data-pout_latitude='"+row.pout_latitude+"' data-pout_date='"+row.pout_date+"' data-pout_longitude='"+row.pout_longitude+"' data-pout_img='"+row.pout_img+"'>View History</span> </td>";
                        }
                                
                        content +="<td>"+row.totalDuration+"</td>";
                       
                        content += "</tr>";

                });
                

                $("#pio_records").html(content); //For append html data
                $('#datatable').dataTable();

                $.each(data.s_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                    $('#p_in_so').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    // $('#p_in_soh').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    
                    $('#pout_so').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    // $('#pout_soh').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    
                    
                });

            }   
        });
        
        
    });

    getPInOut();
    function getPInOut(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_pio_records')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data);
                $("#datatable").DataTable().destroy();

                content ="";  
                var i = 0;       
      
                $.each(data.data,function(index,row){
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }
                  
                    if(row.pin_date != null){
                        var pin_date = formatDate (row.pin_date); // "18/01/10"
                    }else{
                        var pin_date = " - "
                    }

                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+pin_date+"</td>";
                        content +="<td>"+row.pin_time+"<br><span class='badge badge-soft-primary pinhistry' data-pin_oth_id='"+row.pin_oth_id+"' data-pin_u_id='"+row.pin_u_id+"' data-pin_remark='"+row.pin_remark+"' data-pin_latitude='"+row.pin_latitude+"' data-pin_date='"+row.pin_date+"' data-pin_longitude='"+row.pin_longitude+"' data-pin_img='"+row.pin_img+"'>View History</span></td>";

                        if(row.created_at == row.updated_at){ 
                            content +="<td> - </td>";

                        }else{
                            content +="<td>"+row.pout_time+"<br><span class='badge badge-soft-primary pouthistry' data-pout_oth_id='"+row.pout_oth_id+"' data-pout_u_id='"+row.pout_u_id+"' data-pout_remark='"+row.pout_remark+"' data-pout_work_desc='"+row.pout_work_desc+"' data-pout_latitude='"+row.pout_latitude+"' data-pout_date='"+row.pout_date+"' data-pout_longitude='"+row.pout_longitude+"' data-pout_img='"+row.pout_img+"'>View History</span> </td>";
                        }
                                
                        content +="<td>"+row.totalDuration+"</td>";
                       
                        content += "</tr>";

                });
                

                $("#pio_records").html(content); //For append html data
                $('#datatable').dataTable();

                $.each(data.s_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                    $('#p_in_so').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    // $('#p_in_soh').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    
                    $('#pout_so').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                    // $('#pout_soh').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                });

                $.each(data.u_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                    $('#p_in_labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                    // $('#p_in_soh').append("<option value='"+row.oth_id+"' selected>"+row.name+"</option>");
                    
                    $('#pout_labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                    // $('#pout_soh').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                });

            }
        });
    }

    //For Edit Operation
    $(document).on("click",'.editU',function()
    {
        var id = $(this).data('id');
        if(id !=""){

            var labour = $(this).data('labour');
            var pay_desc = $(this).data('p_desc');
            var payment_date = $(this).data('payment_date');
            var payment_amnt = $(this).data('payment_amnt');
            var so= $(this).data('so');
            var r=new Array();
            if (so.toString().indexOf(',')>-1)
            { 
                var r=so.split(',');
            }else{
                r[0]=so.toString();
            }
            // ACTIVE PANE AND LINK
            $('.nav-tabs a[href="#update_lpayment"]').tab('show');

            $('#edit_id').val(id);   
            $('#pay_desc').val(pay_desc); 
            $('#payment_date').val(payment_date); 
            $('#payment_amnt').val(payment_amnt); 

            $.each(r,function(index,value)
            {
                // $("#so").find("option[value="+value+"]").prop("selected", "selected");
            $('#so option[value='+value+']').attr('selected','selected').change();

            });

            //  $("#labour").find("option[value="+labour+"]").prop("selected", "selected");
            $('#labour option[value='+labour+']').attr('selected','selected').change();
        }
        

    });

    //For punch out history modal
    $(document).on("click",'#pout_btn',function()
    {   
        var pin_u_id = $(this).data('pin_u_id');
        var pin_oth_id = $(this).data('pin_oth_id');

        // alert(pin_so_id);
        //for multiple oa
        // var r=new Array();
        // if (pin_so_id.toString().indexOf(',')>-1)
        // { 
        //     var r=pin_so_id.split(',');
        // }else{
        //     r[0]=pin_so_id.toString();
        // }


        // for multiple labour
        var r1=new Array();
        if (pin_u_id.toString().indexOf(',')>-1)
        { 
            var r1=pin_u_id.split(',');
        }else{
            r1[0]=pin_u_id.toString();
        }

        $('#pout_so option[value='+pin_oth_id+']').attr('selected','selected').change();

        // $.each(r,function(index,value)
        // {
        //     $('#pout_so option[value='+value+']').attr('selected','selected').change();
        // });

        // alert(r1);
        $.each(r1,function(index,value)
        {
            $('#pout_labour option[value='+value+']').attr('selected','selected').change();
        });
        
        $('#punchOutModal').modal('show');

    });


    //For punch in history modal
    $(document).on("click",'.pinhistry',function()
    {   
        var pin_oth_id = $(this).data('pin_oth_id');
        var pin_u_id = $(this).data('pin_u_id');
        var pin_remark = $(this).data('pin_remark');
        var pin_latitude = $(this).data('pin_latitude');
        var pin_longitude = $(this).data('pin_longitude');
        var pin_date = $(this).data('pin_date');
        var pin_img = $(this).data('pin_img');
        // $('#p_in_soh option[value='+pin_oth_id+']').attr('selected','selected').change();    
        // $("#p_in_soh option[value='"+pin_oth_id+"']").attr('selected','selected').change();              
        $("#p_in_soh").val(pin_oth_id).trigger("change"); 

        $.ajax({    
            url:"{{url('get-pinh-labour')}}",
            type :'get',
            data : {pin_date:pin_date,pin_oth_id:pin_oth_id},
            async: true,
            cache: true,
            dataType: 'json',
            success: function(response) 
            {
                console.log(response);

                $.each(response.l_obj,function(index,row){

                    $('#p_in_labourh option[value='+row.id+']').attr('selected','selected').change();                    
                });
            }
        });

  
        var  attachment="files/attendance/punchIn/"+pin_img;
        $('#p_in_remarkh').val(pin_remark);   
        $('#p_in_latitudeh').val(pin_latitude); 
        $('#p_in_longitudeh').val(pin_longitude); 
        $('#p_in_dateh').val(pin_date); 
        $('#attachment1').attr("href",attachment);
        
        $('#pinhistryModal').modal('show');

    });

    //For punch out history modal
    $(document).on("click",'.pouthistry',function()
    {   
        var pout_u_id = $(this).data('pout_u_id');
        var pout_oth_id = $(this).data('pout_oth_id');
        var pout_date = $(this).data('pout_date');
        var pout_remark = $(this).data('pout_remark');
        var pout_work_desc = $(this).data('pout_work_desc');
        var pout_latitude = $(this).data('pout_latitude');
        var pout_longitude = $(this).data('pout_longitude');
        var pout_img = $(this).data('pout_img');
        // $('#pout_soh option[value='+pout_oth_id+']').attr('selected','selected').change();      
        // $("#pout_soh option[value='"+pout_oth_id+"']").attr('selected','selected').change();              
        $("#pout_soh").val(pout_oth_id).trigger("change"); 
        $.ajax({    
            url:"{{url('get-pouth-labour')}}",
            type :'get',
            data : {pout_date:pout_date},
            async: true,
            cache: true,
            dataType: 'json',
            success: function(response) 
            {
                console.log(response);

                //For Full Finish Invoice
                // $('#labours').append("<option value='all' class='text-muted' selected>"+'ALL'+"</option>");
                $.each(response.s_obj,function(index,row){

                });

                $.each(response.l_obj,function(index,row){

                    $('#pout_labourh option[value='+row.id+']').attr('selected','selected').change();                    
                });
            }
        });

  

        // alert(pout_so_id);
        //for multiple oa
        // var r=new Array();
        // if (pout_so_id.toString().indexOf(',')>-1)
        // { 
        //     var r=pout_so_id.split(',');
        // }else{
        //     r[0]=pout_so_id.toString();
        // }


        // // for multiple labour
        // var r1=new Array();
        // if (pout_u_id.toString().indexOf(',')>-1)
        // { 
        //     var r1=pout_u_id.split(',');
        // }else{
        //     r1[0]=pout_u_id.toString();
        // }

        var  attachment="files/attendance/punchOut/"+pout_img;
        $('#pout_remarkh').val(pout_remark);   
        $('#pout_work_desch').val(pout_work_desc);   
        $('#pout_latitudeh').val(pout_latitude); 
        $('#pout_longitudeh').val(pout_longitude); 
        $('#pout_dateh').val(pout_date); 
        $('#attachment2').attr("href",attachment);


        // $.each(r,function(index,value)
        // {
        //     $('#pout_soh option[value='+value+']').attr('selected','selected').change();
        // });

        // $.each(r1,function(index,value)
        // {
        //     $('#pout_labourh option[value='+value+']').attr('selected','selected').change();
        // });
        
        $('#pouthistryModal').modal('show');

    });
  

    // delete Product
    $(document).on("click",'.delI',function()
    {
        var id = $(this).data('id');
        $('#id').val(id);
        // $('#delete_record_modal form').attr("action","delete_labour_payment/"+id);
        $('#delete_record_modal').modal('show');
    });

   // delete Product
   $(document).on("click",'#pout_btn',function()
    {
        var pin_id = $(this).data('pin_id');
        $('#pin_id').val(pin_id);


    });

</script>

<script language="JavaScript">

    $(".photo").hide();
    $(".photo1").hide();

    Webcam.set({
        width: 250,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    $(document).on("click",'#photo',function()
    {   
        $(".photo").show();
        $("#photo").hide();
         Webcam.attach( '#my_camera' );
        
    });
    
    function take_snapshot() {
        $(".photo1").show();


        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );

        // var x = document.getElementById("demo");
        getLocation();
        function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            // x.innerHTML = "Geolocation is not supported by this browser.";
        }
        }

        function showPosition(position) {
            // x.innerHTML = "Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude;
            
            document.getElementById("p_in_latitude").value = position.coords.latitude;
            document.getElementById("p_in_longitude").value = position.coords.longitude;
          
        }

        


    }
</script>

<script language="JavaScript">

    $(".ophoto").hide();
    $(".ophoto1").hide();

    Webcam.set({
        width: 250,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    $(document).on("click",'#ophoto',function()
    {   
        $(".ophoto").show();
        $("#ophoto").hide();
        Webcam.attach( '#my_camera1' );
        
    });
    
    function take_snapshot1() {
        $(".ophoto1").show();


        Webcam.snap( function(data_uri) {
            $(".image-tag1").val(data_uri);
            document.getElementById('results1').innerHTML = '<img src="'+data_uri+'"/>';
        } );

        // var x = document.getElementById("demo");
        getLocation();
        function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            // x.innerHTML = "Geolocation is not supported by this browser.";
        }
        }

        function showPosition(position) {
            // x.innerHTML = "Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude;
            
            document.getElementById("pout_latitude").value = position.coords.latitude;
            document.getElementById("pout_longitude").value = position.coords.longitude;
          
        }

        


    }
</script>
@endpush