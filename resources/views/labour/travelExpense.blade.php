@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
@section('title',"Manage Travel Expense | $title")
@push('datatable_css')
{!! Html::style('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') !!}
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
                           <h4 class="card-title mb-4">Manage Travel Expense</h4>
                           <div class="ms-auto">
                                <!-- <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" data-bs-toggle="modal" data-bs-target="#addModal" style="margin-left: 10px;">
                                <i class="mdi mdi-plus font-size-11"></i> Add Labour Payment
                                </button>  -->
                                
                            </div>
                        </div>
             
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                        {!! Form::open(['class'=>"form-horizontal",'id'=>"tech_pay_search_form"]) !!}
                                <div class="row">
                                    <?php $tdate=date("Y-m-d"); ?>
                                    <input type="hidden" name="roles" id="roles" value="{{$roles}}">
                                    
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
                                    @if($roles == 1 || $roles == 0)
                                        <div class="col-md-3 col-sm-12 col-lg-3">
                                            <div class="form-group mb-3">
                                                <label for="labours" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician <sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="labours" required name="labours">
                                                    <option value="" disabled selected>Select</option>
                                                    @foreach($u_obj as $u)
                                                        <option value="{{$u->id}}">{{$u->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error" id="slerror"></span>
                                            </div>
                                        </div>
                                    @endif    
                                    <div class="col-md-3 col-sm-12 col-lg-3">
                                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="exp_req_ftd_records">Search</button>
                                        </div>
                                    </div>
                                {!! Form::close() !!} 
                      
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#ucpayment_list" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Uncleared Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cllpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Cleared Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#apprvdpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Approved Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#calpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Cancelled Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#update_epayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">ADD / Update Travel Expense</span> 
                                </a>
                            </li>
                      
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="ucpayment_list" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="expDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>

                                                @if($roles != 0)
                                                    <th scope="col" style="width: 100px">Action</th>
                                                @endif
                                                <th scope="col" style="width: 100px">Travel Mode</th>
                                                <th scope="col" style="width: 100px">From Location</th>
                                                <th scope="col" style="width: 100px">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="exp_pay_records">
                                           
                                        </tbody>
                                        <!-- <tfoot id="tucledata">
                                            <tr>
                                                <th colspan="2" class="text-center"><strong>Total</strong></th>
                                                <th id="t_ucleamount"></th>
                                                @if($roles == 1)
                                                    <th></th>
                                                @endif   
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>
         
                            <div class="tab-pane" id="cllpayment" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="clearedDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                @if($roles == 0)
                                                    <th scope="col" style="width: 100px">Action</th>
                                                @endif
                                                <th scope="col" style="white-space: normal;">Travel Mode</th>
                                                <th scope="col" style="white-space: normal;">From Location</th>
                                                <th scope="col" style="white-space: normal;">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cll_records">
                                        
                                        </tbody>
                                        <!-- <tfoot id="tclldata">
                                            <tr>
                                                <th colspan="7" class="text-center"><strong>Total</strong></th>
                                                <th id="t_cllamount"></th>
                                                @if($roles == 0)
                                                    <th></th>
                                                @endif
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="apprvdpayment" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="apprvdDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                <th scope="col" style="white-space: normal;">Travel Mode</th>
                                                <th scope="col" style="white-space: normal;">From Location</th>
                                                <th scope="col" style="white-space: normal;">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">SA Remark</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="apprvd_records">
                                        
                                        </tbody>
                                        <!-- <tfoot id="taprdata">
                                            <tr>
                                                <th colspan="8" class="text-center"><strong>Total</strong></th>
                                                <th id="t_apprvdamount"></th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="calpayment" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="cancelDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                @if($roles == 0)
                                                    <th scope="col">Action</th>
                                                @endif
                                                <th scope="col" style="width: 100px">Travel Mode</th>
                                                <th scope="col" style="width: 100px">From Location</th>
                                                <th scope="col" style="width: 100px">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cal_records">
                                        
                                        </tbody>
                                        <!-- <tfoot id="tcaldata">
                                            <tr>
                                                <th colspan="7" class="text-center"><strong>Total</strong></th>
                                                <th id="t_calamount"></th>
                                            
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                   
                                </div>
                            </div>

                            <div class="tab-pane" id="update_epayment" role="tabpanel">
                                {!! Form::open(['class'=>"form-horizontal epayment_form",'enctype'=>'multipart/form-data','files' => 'true' ,'id'=>'postexpPaymentform']) !!}
                                    <input type="hidden" name="exp_edit_id" id="exp_edit_id" value="">
                                    <input type="hidden" name="bike_rates" id="bike_rates" value="{{Session::get('BIKE_RATE')}}">
                                    <input type="hidden" name="car_rates" id="car_rates" value="{{Session::get('CAR_RATE')}}">

                                    <div class="row">
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="exp_so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="exp_so" required name="exp_so">
                                                    
                                                </select>
                                                <span class="text-danger error" id="esoerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="mode_travel" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Mode of Travel<sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="mode_travel" required name="mode_travel">
                                                    <option value="" disabled selected>Select</option>
                                                    <option value="Bus">Bus</option>
                                                    <option value="Train">Train</option>
                                                    <option value="Bike">Bike</option>
                                                    <option value="Shared_Auto">Shared Auto</option>
                                                    <option value="Private_Auto">Private Auto</option>
                                                    <option value="Own Car">Own Car</option>
                                                </select>
                                                <span class="text-danger error" id="mterror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="from_location" placeholder="From Location" name="from_location" maxlength="10" required>
                                                <label for="from_location">From Location<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="flerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="to_location" placeholder="To Location" name="to_location" maxlength="10" required>
                                                <label for="to_location">To Location<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="tlerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="no_of_person" placeholder="To Location" name="no_of_person" maxlength="10" required>
                                                <label for="no_of_person">No of Person<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="nperror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="travel_amnt" placeholder="Travel Amount" name="travel_amnt" maxlength="10" required>
                                                <label for="travel_amnt">Amount (In Rs.)<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="taerror"></span>
                                            </div>
                                        </div>
                                        <?php $tdate=date("Y-m-d");?>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="date" class="form-control" id="travel_date" placeholder="Travel Date" name="travel_date" required max="{{$tdate}}" value="{{$tdate}}" readonly>
                                                <label for="travel_date">Travel Date<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="tderror"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-12 col-lg-2" id="km_div">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="total_km" placeholder="Total KM" name="total_km" maxlength="10" required>
                                                <label for="total_km">Total KM</label>
                                                <small id="bike_rate" style="color:green">Per KM Rate : {{Session::get('BIKE_RATE')}}</small>
                                                <small id="car_rate" style="color:green">Per KM Rate : {{Session::get('CAR_RATE')}}</small>
                                                <span class="text-danger error" id="tkerror"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="file" class="form-control" id="attachment" placeholder="Enter photo File" name="attachment">
                                                <label for="attachment">Attachment</label>
                                                <!-- For Extension an Encoding File  -->
                                                <textarea class="form-control" style="display: none" id="payment_encodedfile"></textarea> 
                                                <input type="hidden" name="payment_extension" id="payment_extension">
                                                <!-- END For Extension an Encoding File  -->
                                                
                                                <a href="" id="attachment1" target="_blank"><i class="fa fa-eye"></i> View Previous File</a>
                                                <span id="aerror"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" id="travel_desc" placeholder="Enter Travel Description" required name="travel_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                                                <label for="travel_desc">Travel Description</label>
                                                <span class="text-danger error" id="tdeerror"></span>

                                            </div>
                                        </div>
                                       
                                        <div class="d-sm-flex flex-wrap">
                                            <div class="ms-auto">
                                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light mb-2" id="add_expense">Submit</button>
                                            </div>
                                        </div>
                                    </div> 
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- sample modal content -->
 <div id="editPaymentModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Manage Travel Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!! Form::open(['class'=>"form-horizontal"]) !!}
            <div class="modal-body">
                <div class="row">    
                    <input type="hidden" name="exp_edit_id" id="exp_edit_id" value="">
                    <input type="hidden" name="role" id="role" value="{{$roles}}">
                    <div class="col-md-6 col-sm-12 col-lg-6  mb-3">
                        <h6>
                            <strong>Name : <span id="t_name"> - </span></strong>&nbsp;
                        </h6> 
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6  mb-3">
                        <h6>
                            <strong>Emp Number : <span id="e_num"> - </span></strong>&nbsp;
                        </h6> 
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-group mb-3">
                            <label for="a_mode_travel" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Mode of Travel<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="a_mode_travel" required name="a_mode_travel" disabled>
                                <option value="" disabled selected>Select</option>
                                <option value="Bus">Bus</option>
                                <option value="Train">Train</option>
                                <option value="Bike">Bike</option>
                                <option value="Shared_Auto">Shared Auto</option>
                                <option value="Private_Auto">Private Auto</option>
                                <option value="Own Car">Own Car</option>
                            </select>
                            <span class="text-danger error" id="mterror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-group mb-3">
                            <label for="status_change" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Expense Status<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="status_change" required name="status_change">
                            @if($roles == 1)
                                <option value="" disabled selected>Select</option>
                                <option value="Cleared">Cleared</option>
                                <option value="Cancelled">Cancelled</option>
                            @else
                                <option value="Approved" selected>Approved</option>
                            @endif
                            </select>
                            <span class="text-danger error" id="eserror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="a_from_location" placeholder="From Location" name="a_from_location" maxlength="10" required disabled>
                            <label for="a_from_location">From Location<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="flerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="a_to_location" placeholder="To Location" name="a_to_location" maxlength="10" required disabled>
                            <label for="a_to_location">To Location<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="tlerror"></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="a_total_km" placeholder="Total KM" name="a_total_km" maxlength="10" required disabled>
                            <label for="a_total_km">Total KM<sup class="text-danger">*</sup></label>
                            <small id="abike_rate" style="color:green">Per KM Rate - {{Session::get('BIKE_RATE')}}</small>
                            <small id="acar_rate" style="color:green">Per KM Rate - {{Session::get('CAR_RATE')}}</small>
                            <span class="text-danger error" id="tkerror"></span>
                        </div>
                    </div>

                    <?php $tdate=date("Y-m-d");?>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="a_travel_date" placeholder="Travel Date" name="a_travel_date" required max="{{$tdate}}" value="{{$tdate}}" disabled>
                            <label for="a_travel_date">Travel Date<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="tderror"></span>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="a_travel_desc" placeholder="Enter Travel Description" required name="a_travel_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" disabled></textarea>
                            <label for="a_travel_desc">Travel Description</label>
                            <span class="text-danger error" id="ederror"></span>

                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="acc_remark" placeholder="Enter Accountant Remark" required name="acc_remark" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                            <label for="acc_remark">Admin Remark</label>
                            <span class="text-danger error" id="arerror"></span>

                        </div>
                    </div>
                    
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="expense_amnt" placeholder="Expense Amount" name="expense_amnt" maxlength="10" required disabled>
                            <label for="expense_amnt">Requested Amount (In Rs.)<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="eaerror"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="updated_amnt" placeholder="Expense Amount" name="updated_amnt" maxlength="10" required>
                            <label for="updated_amnt">PA Aprvd Amount (In Rs.)<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="uaerror"></span>
                        </div>
                    </div>

                   

                    <div class="col-md-12 sa_div">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="sa_remark" placeholder="Enter Accountant Remark" required name="sa_remark" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                            <label for="sa_remark">SA Remark</label>
                            <span class="text-danger error" id="sarerror"></span>

                        </div>
                    </div>
                    
                    <div class="col-md-6 col-sm-12 col-lg-6 sa_div">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="sa_updated_amnt" placeholder="Expense Amount" name="sa_updated_amnt" maxlength="10" required>
                            <label for="sa_updated_amnt">SA Aprvd Amount (In Rs.)<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="sauaerror"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <a href="" id="attachment2" target="_blank"><i class="fa fa-eye"></i> View Attachment File</a>
                    </div>

                </div>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="update_expense">Save</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
   
<!-- delete Modal -->
<div id="delete_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form  class="form-horizontal">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Record Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <strong>Do you really wants to delete the record..? </strong>
                <!-- <strong>Do you really want to Reset Password For this Account..? </strong> -->

                <div class="form-group">
                    <div class="col-md-4">
                    <input type="hidden" id="id" name="id" class="form-control"/>
                    <input type="hidden" id="type" name="type" class="form-control"/>

                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary text-white btn-sm " id="del_rec"><i class="fe fe-check mr-2"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form  class="form-horizontal">
                <div class="modal-header">
                    <h5 class="modal-title">Travel Expense Attachment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="image_div">
                    </div>
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal" id="image_close">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('common.delete_modal')    
@stop
@push('datatable_js')
    {!! Html::script('assets/libs/datatables.net/js/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') !!}
    <script>
        $(document).ready(function(){
            $('#expDatatable').dataTable();    
            $('#clearedDatatable').dataTable();    
            $('#apprvdDatatable').dataTable();    
            $('#cancelDatatable').dataTable(); 


        });
    </script>
@endpush

@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}



<script>
    
    $(document).ready(function(){
        var $body = $("body");
        $('#mode_travel,#exp_so').select2();
        $("#tucledata").hide();
        $("#tclldata").hide();
        $("#tcaldata").hide();
        $("#taprdata").hide();

        $("#bike_rate").hide();     // on change bike rate 
        $("#car_rate").hide();      // on change car rate
        $("#km_div").hide();     // on change car 
    });

    // mode of travel on change fields
    $('#mode_travel').change(function(e)
    {
        $("#bike_rate").hide();     // on change bike rate 
        $("#car_rate").hide();      // on change car rate
        $("#km_div").hide();     // on change car 
        $("#travel_amnt").prop('readonly', false);
        var edit_id= $('#exp_edit_id').val();
        // alert(edit_id);
        if(edit_id == ""){
            $("#travel_amnt").val("");
            $("#total_km").val("");
        }
       

        var mode_travel = $(this).val(); 

        if(mode_travel == "Bike"){
          
            
            $("#km_div").show();     // on change bike 
            $("#bike_rate").show();     // on change bike rate 
            $("#travel_amnt").prop('readonly', true);
        }

        if(mode_travel == "Own Car"){
            // $("#travel_amnt").val("");
            // $("#km_div").val("");
            $("#km_div").show();     // on change car 
            $("#car_rate").show();      // on change car rate
            $("#travel_amnt").prop('readonly', true);
        }
  
    });

    // // mode of travel on change fields
    $('#total_km').blur(function(e)
    {

        var total_km = $('#total_km').val();
        var bike_rate = $('#bike_rates').val();
        var car_rate = $('#car_rates').val();

        var mode_travel = $('#mode_travel').val();
        if(mode_travel == "Bike"){
            var travel_amnt = parseFloat(total_km * bike_rate).toFixed(2); // discount formule
            $('#travel_amnt').val(travel_amnt);
        }

        if(mode_travel == "Own Car"){
            var travel_amnt = parseFloat(total_km * car_rate).toFixed(2); // discount formule
            $('#travel_amnt').val(travel_amnt);
        }
        
    });

    // For serch record Validation
    var n =0;
    $("#exp_req_ftd_records").click(function(event) 
    {

        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var labours= $('#labours').val();

        n=0;    
        if( $.trim(from_date).length == 0 )
        {
            $('#fderror').text('Please Select From Date.');
            event.preventDefault();
        }else{
            $('#fderror').text('');
            ++n;
        }

        if( $.trim(to_date).length == 0 )
        {
            $('#tderror').text('Please Select To Date.');
            event.preventDefault();
        }else{
            $('#tderror').text('');
            ++n;
        }
       
        if( $.trim(labours).length == 0 )
        {
            $('#slerror').text('Please Select Technician');
            event.preventDefault();
        }else{
            $('#slerror').text('');
            ++n;
        }

    });

    // For from date ,to date records
    $(document).on("click",'#exp_req_ftd_records',function()
    {     
 
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var labours = $('#labours').val();
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('get_travel_expenses')}}",
                type :'get',
                data : {from_date:from_date,to_date:to_date,labours:labours},
                cache: false,
                dataType: 'json',                 
                success:function(data){
                    console.log(data.data);
                    if (data.status==true) 
                    { 
                        $("#expDatatable").DataTable().destroy();
                        $("#clearedDatatable").DataTable().destroy();
                        $("#cancelDatatable").DataTable().destroy();
                        $("#apprvdDatatable").DataTable().destroy();

                        $("#tucledata").show();
                        $("#tclldata").show();
                        $("#tcaldata").show();
                        $("#taprdata").show();

                        var t_ucleamount=t_cllamount=t_calamount=0; 
                        content ="";        //For Uncleared datatable
                        content1 ="";        //For Cleared datatable
                        content2 ="";        //For Cancelled datatable
                        content3 ="";        //For Cancelled datatable
                        var i = 0;       
        
                        
                        $.each(data.data,function(index,row){
                            if(row.status == 'Uncleared')
                            {
                                //date convert into dd/mm/yyyy
                                function formatDate (input) {
                                    var datePart = input.match(/\d+/g),
                                    year = datePart[0].substring(0), // get only two digits
                                    month = datePart[1], day = datePart[2];
                                    return day+'-'+month+'-'+year;
                                }
                                if(row.travel_date != null){
                                    var travel_date = formatDate (row.travel_date); // "18/01/10"
                                }else{
                                    var travel_date = " - "
                                }

                                t_ucleamount+=Number(row.travel_amount);           //total of amount
                                var d = new Date();
                                var current_date = d.getDate();
                                    content +="<tr>";
                                    content +="<td>"+ ++i +"</td>";
                                    content +="<td>"+travel_date+"</td>";
                                    if(data.role != 0){
                                        content +="<td>";
                                            if((travel_date == $.datepicker.formatDate('dd-mm-yy', new Date())) && (data.role == 3)){
                                                content +="<a class='btn btn-outline-secondary btn-sm exp_editT' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm exp_delT' rel='tooltip' data-bs-placement='top' title='Delete Travel Expense' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                                            }
                                            if((data.role == 1)){
                                                content +="<a class='btn btn-outline-secondary btn-sm exp_editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"' data-ad_remark='"+row.ad_remark+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a>"
                                                
                                            }
                                        content +="</td>";
                                    }
                                    if(row.mode_travel == "Shared_Auto"){
                                        content +="<td> Shared Auto </td>";
                                    }else if(row.mode_travel == "Private_Auto"){
                                        content +="<td> Private Auto </td>";
                                    }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                        content +="<td>"+row.mode_travel+"</td>";
                                    }
                                
                                    content +="<td>"+row.from_location+"</td>";
                                    content +="<td>"+row.to_location+"</td>";
                                    if(row.total_km != null){
                                        content +="<td>"+row.total_km+"</td>";
                                    }else{
                                        content +="<td class='text-center'> - </td>";
                                    }
                                    
                                    if(row.attachment != null){
                                        content +="<td>"+row.travel_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                                    }else{
                                        content +="<td>"+row.travel_amount+"</td>";
                                    }   

                                    content +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";

                                    if(row.travel_desc != null){
                                        content +="<td>"+row.travel_desc+"</td>";
                                    }else{
                                        content +="<td class='text-center'> - </td>";
                                    }
                                    content += "</tr>";
                            }      

                            if(row.status == 'Cleared')
                            {
                                //date convert into dd/mm/yyyy
                                function formatDate (input) {
                                    var datePart = input.match(/\d+/g),
                                    year = datePart[0].substring(0), // get only two digits
                                    month = datePart[1], day = datePart[2];
                                    return day+'-'+month+'-'+year;
                                }
                                if(row.travel_date != null){
                                    var travel_date = formatDate (row.travel_date); // "18/01/10"
                                }else{
                                    var travel_date = " - "
                                }

                                t_cllamount+=Number(row.travel_amount);           //total of amount

                                var d = new Date();
                                var current_date = d.getDate();
                                    content1 +="<tr>";
                                    content1 +="<td>"+ ++i +"</td>";
                                    content1 +="<td>"+travel_date+"</td>";
                                
                                    if(data.role == 0){
                                        content1 +="<td>";
                                        content1 +="<a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a>"
                                        content1 +="</td>";

                                    }
                                    if(row.mode_travel == "Shared_Auto"){
                                        content1 +="<td> Shared Auto </td>";
                                    }else if(row.mode_travel == "Private_Auto"){
                                        content1 +="<td> Private Auto </td>";
                                    }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                        content1 +="<td>"+row.mode_travel+"</td>";
                                    }
                                    content1 +="<td>"+row.from_location+"</td>";
                                    content1 +="<td>"+row.to_location+"</td>";
                                    if(row.total_km != null){
                                        content1 +="<td>"+row.total_km+"</td>";
                                    }else{
                                        content1 +="<td class='text-center'> - </td>";
                                    }

                                    if(row.attachment != null){
                                        content1 +="<td>"+row.travel_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                                    }else{
                                        content1 +="<td>"+row.travel_amount+"</td>";
                                    }   
                                    content1 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                                    if(row.ad_remark != null){
                                        content1 +="<td>"+row.ad_remark+"</td>";
                                    }else{
                                        content1 +="<td class='text-center'> - </td>";
                                    }
                                    if(row.travel_desc != null){
                                        content1 +="<td>"+row.travel_desc+"</td>";
                                    }else{
                                        content1 +="<td class='text-center'> - </td>";
                                    }
                                    content1 += "</tr>";
                            }

                            if(row.status == 'Approved')
                            {

                                //date convert into dd/mm/yyyy
                                function formatDate (input) {
                                    var datePart = input.match(/\d+/g),
                                    year = datePart[0].substring(0), // get only two digits
                                    month = datePart[1], day = datePart[2];
                                    return day+'-'+month+'-'+year;
                                }
                                if(row.travel_date != null){
                                    var travel_date = formatDate (row.travel_date); // "18/01/10"
                                }else{
                                    var travel_date = " - "
                                }

                                t_apprvdamount+=Number(row.travel_amount);           //total of amount

                                var d = new Date();
                                var current_date = d.getDate();
                                    content3 +="<tr>";
                                    content3 +="<td>"+ ++i +"</td>";
                                    content3 +="<td>"+travel_date+"</td>";
                                    if(row.mode_travel == "Shared_Auto"){
                                        content3 +="<td> Shared Auto </td>";
                                    }else if(row.mode_travel == "Private_Auto"){
                                        content3 +="<td> Private Auto </td>";
                                    }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                        content3 +="<td>"+row.mode_travel+"</td>";
                                    }
                                    content3 +="<td>"+row.from_location+"</td>";
                                    content3 +="<td>"+row.to_location+"</td>";
                                    if(row.total_km != null){
                                        content3 +="<td>"+row.total_km+"</td>";
                                    }else{
                                        content3 +="<td class='text-center'> - </td>";
                                    }

                                    if(row.attachment != null){
                                        content3 +="<td>"+row.travel_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                                    }else{
                                        content3 +="<td>"+row.travel_amount+"</td>";
                                    }   

                                    content3 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";

                                    if(row.ad_remark != null){
                                        content3 +="<td>"+row.ad_remark+"</td>";
                                    }else{
                                        content3 +="<td class='text-center'> - </td>";
                                    }

                                    if(row.sa_remark != null){
                                        content3 +="<td>"+row.sa_remark+"</td>";
                                    }else{
                                        content3 +="<td class='text-center'> - </td>";
                                    }

                                    if(row.travel_desc != null){
                                        content3 +="<td>"+row.travel_desc+"</td>";
                                    }else{
                                        content3 +="<td class='text-center'> - </td>";
                                    }
                                    content3 += "</tr>";


                            }

                            if(row.status == 'Cancelled')
                            {
                                //date convert into dd/mm/yyyy
                                function formatDate (input) {
                                    var datePart = input.match(/\d+/g),
                                    year = datePart[0].substring(0), // get only two digits
                                    month = datePart[1], day = datePart[2];
                                    return day+'-'+month+'-'+year;
                                }
                                if(row.travel_date != null){
                                    var travel_date = formatDate (row.travel_date); // "18/01/10"
                                }else{
                                    var travel_date = " - "
                                }

                                t_calamount+=Number(row.travel_amount);           //total of amount

                                var d = new Date();
                                var current_date = d.getDate();
                                    content2 +="<tr>";
                                    content2 +="<td>"+ ++i +"</td>";
                                    content2 +="<td>"+travel_date+"</td>";

                                    if(row.mode_travel == "Shared_Auto"){
                                        content2 +="<td> Shared Auto </td>";
                                    }else if(row.mode_travel == "Private_Auto"){
                                        content2 +="<td> Private Auto </td>";
                                    }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                        content2 +="<td>"+row.mode_travel+"</td>";
                                    }
                                    content2 +="<td>"+row.from_location+"</td>";
                                    content2 +="<td>"+row.to_location+"</td>";
                                    if(row.total_km != null){
                                        content2 +="<td>"+row.total_km+"</td>";
                                    }else{
                                        content2 +="<td class='text-center'> - </td>";
                                    }

                                    if(row.attachment != null){
                                        content2 +="<td>"+row.travel_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                                    }else{
                                        content2 +="<td>"+row.travel_amount+"</td>";
                                    } 

                                    content2 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                                    if(row.travel_desc != null){
                                        content2 +="<td>"+row.travel_desc+"</td>";
                                    }else{
                                        content2 +="<td class='text-center'> - </td>";
                                    }
                                    content2 += "</tr>";
                            }

                        });

                        $("#exp_pay_records").html(content); //For append html data
                        $('#expDatatable').dataTable();

                        $("#cll_records").html(content1); //For append Cleared datatable html data 
                        $('#clearedDatatable').dataTable();

                        $("#cal_records").html(content2); //For append Cancelled datatable html data
                        $('#cancelDatatable').dataTable();

                        $("#apprvd_records").html(content3); //For append html data
                        $('#apprvdDatatable').dataTable();    

                        $.each(data.s_obj,function(index,row){
                            //For Add Material Modal
                            // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                            $('#exp_so').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                        });

                        // ACTIVE PANE AND LINK
                        $('.nav-tabs a[href="#epayment_list"]').tab('show');
                            //For Notification
                            toastr.options.timeOut = 5000;
                            toastr.options.positionClass = 'toast-top-right';
                            toastr.options.showEasing= 'swing';
                            toastr.options.hideEasing= 'linear';
                            toastr.options.showMethod= 'fadeIn';
                            toastr.options.hideMethod= 'fadeOut';
                            toastr.options.closeButton= true;
                            toastr.success(data.message);
                
                    }else{

                        //For Notification
                        toastr.options.timeOut = 5000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.options.showEasing= 'swing';
                        toastr.options.hideEasing= 'linear';
                        toastr.options.showMethod= 'fadeIn';
                        toastr.options.hideMethod= 'fadeOut';
                        toastr.options.closeButton= true;
                        toastr.error(data.message);
                    }

                }
            });
    
       
        
        
    });

    getTravelExpenses();
    function getTravelExpenses(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_travel_expenses')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data);
                $("#expDatatable").DataTable().destroy();
                $("#clearedDatatable").DataTable().destroy();
                $("#cancelDatatable").DataTable().destroy();
                $("#apprvdDatatable").DataTable().destroy();


                $("#tucledata").show();
                $("#tclldata").show();
                $("#tcaldata").show();
                $("#taprdata").show();

                var t_ucleamount=t_cllamount=t_calamount=t_apprvdamount=0; 
                content ="";        //For Uncleared datatable
                content1 ="";        //For Cleared datatable
                content2 ="";        //For Cancelled datatable
                content3 ="";        //For Approved datatable
                var i=j=k=l= 0;   
                  

                $.each(data.data,function(index,row){
                    if(row.status == 'Uncleared')
                    {
                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.travel_date != null){
                            var travel_date = formatDate (row.travel_date); // "18/01/10"
                        }else{
                            var travel_date = " - "
                        }

                        t_ucleamount+=Number(row.travel_amount);           //total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content +="<tr>";
                            content +="<td>"+ ++i +"</td>";
                            content +="<td>"+travel_date+"</td>";

                            if(data.role != 0){
                                content +="<td>";
                                    if((travel_date == $.datepicker.formatDate('dd-mm-yy', new Date())) && (data.role == 3)){
                                        content +="<a class='btn btn-outline-secondary btn-sm exp_editT' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm exp_delT' rel='tooltip' data-bs-placement='top' title='Delete Travel Expense' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                                    }
                                    if((data.role == 1)){
                                        content +="<a class='btn btn-outline-secondary btn-sm exp_editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"' data-ad_remark='"+row.ad_remark+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a>"
                                        
                                    }
                                content +="</td>";
                            }
                            if(row.mode_travel == "Shared_Auto"){
                                content +="<td> Shared Auto </td>";
                            }else if(row.mode_travel == "Private_Auto"){
                                content +="<td> Private Auto </td>";
                            }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                content +="<td>"+row.mode_travel+"</td>";
                            }
                           
                            content +="<td>"+row.from_location+"</td>";
                            content +="<td>"+row.to_location+"</td>";

                            if(row.total_km != null){
                                content +="<td>"+row.total_km+"</td>";
                            }else{
                                content +="<td class='text-center'> - </td>";
                            }

                            
                            if(row.attachment != null){
                                content +="<td>"+row.travel_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                            }else{
                                content +="<td>"+row.travel_amount+"</td>";
                            } 

                                
                            content +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            if(row.travel_desc != null){
                                content +="<td>"+row.travel_desc+"</td>";
                            }else{
                                content +="<td class='text-center'> - </td>";
                            }
                            content += "</tr>";
                    }      

                    if(row.status == 'Cleared')
                    {
                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.travel_date != null){
                            var travel_date = formatDate (row.travel_date); // "18/01/10"
                        }else{
                            var travel_date = " - "
                        }

                        t_cllamount+=Number(row.aprvd_amount);           //total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content1 +="<tr>";
                            content1 +="<td>"+ ++j +"</td>";
                            content1 +="<td>"+travel_date+"</td>";
                           
                            if(data.role == 0){
                                content1 +="<td>";
                                content1 +="<a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a>"
                                content1 +="</td>";

                            }

                            if(row.mode_travel == "Shared_Auto"){
                                content1 +="<td> Shared Auto </td>";
                            }else if(row.mode_travel == "Private_Auto"){
                                content1 +="<td> Private Auto </td>";
                            }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                content1 +="<td>"+row.mode_travel+"</td>";
                            }

                            content1 +="<td>"+row.from_location+"</td>";
                            content1 +="<td>"+row.to_location+"</td>";

                            if(row.total_km != null){
                                content1 +="<td>"+row.total_km+"</td>";
                            }else{
                                content1 +="<td class='text-center'> - </td>";
                            }

                            if(row.attachment != null){
                                content1 +="<td>"+row.aprvd_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                            }else{
                                content1 +="<td>"+row.aprvd_amount+"</td>";
                            } 

                            content1 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";

                            if(row.ad_remark != null){
                                content1 +="<td>"+row.ad_remark+"</td>";
                            }else{
                                content1 +="<td class='text-center'> - </td>";
                            }

                            if(row.travel_desc != null){
                                content1 +="<td>"+row.travel_desc+"</td>";
                            }else{
                                content1 +="<td class='text-center'> - </td>";
                            }
                            content1 += "</tr>";
                    }

                    if(row.status == 'Approved'){

                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.travel_date != null){
                            var travel_date = formatDate (row.travel_date); // "18/01/10"
                        }else{
                            var travel_date = " - "
                        }

                        t_apprvdamount+=Number(row.aprvd_amount);           //total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content3 +="<tr>";
                            content3 +="<td>"+ ++k +"</td>";
                            content3 +="<td>"+travel_date+"</td>";
                            if(row.mode_travel == "Shared_Auto"){
                                content3 +="<td> Shared Auto </td>";
                            }else if(row.mode_travel == "Private_Auto"){
                                content3 +="<td> Private Auto </td>";
                            }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                content3 +="<td>"+row.mode_travel+"</td>";
                            }
                            content3 +="<td>"+row.from_location+"</td>";
                            content3 +="<td>"+row.to_location+"</td>";

                            if(row.total_km != null){
                                content3 +="<td>"+row.total_km+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }

                            if(row.attachment != null){
                                content3 +="<td>"+row.aprvd_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                            }else{
                                content3 +="<td>"+row.aprvd_amount+"</td>";
                            } 

                            content3 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";

                            if(row.ad_remark != null){
                                content3 +="<td>"+row.ad_remark+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }

                            if(row.sa_remark != null){
                                content3 +="<td>"+row.sa_remark+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }

                            if(row.travel_desc != null){
                                content3 +="<td>"+row.travel_desc+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }
                            content3 += "</tr>";


                    }

                    if(row.status == 'Cancelled')
                    {
                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.travel_date != null){
                            var travel_date = formatDate (row.travel_date); // "18/01/10"
                        }else{
                            var travel_date = " - "
                        }

                        t_calamount+=Number(row.travel_amount);           //total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content2 +="<tr>";
                            content2 +="<td>"+ ++l +"</td>";
                            content2 +="<td>"+travel_date+"</td>";

                            if(data.role == 0){
                                content2 +="<td>";
                                content2 +="<a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Travel Expense' data-id='"+row.id+"' data-travel_date='"+row.travel_date+"' data-mode_travel='"+row.mode_travel+"' data-from_location='"+row.from_location+"' data-to_location='"+row.to_location+"' data-total_km='"+row.total_km+"' data-travel_amount='"+row.travel_amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-attachment='"+row.attachment+"' data-travel_desc='"+row.travel_desc+"'  data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a>"
                                content2 +="</td>";

                            }

                            if(row.mode_travel == "Shared_Auto"){
                                content2 +="<td> Shared Auto </td>";
                            }else if(row.mode_travel == "Private_Auto"){
                                content2 +="<td> Private Auto </td>";
                            }else if(row.mode_travel != "Private_Auto" && row.mode_travel != "Shared_Auto"){
                                content2 +="<td>"+row.mode_travel+"</td>";
                            }
                            content2 +="<td>"+row.from_location+"</td>";
                            content2 +="<td>"+row.to_location+"</td>";

                            if(row.total_km != null){
                                content2 +="<td>"+row.total_km+"</td>";
                            }else{
                                content2 +="<td class='text-center'> - </td>";
                            }

                            if(row.attachment != null){
                                content2 +="<td>"+row.travel_amount+"<br><span class='badge badge-soft-primary view_attachment' data-attachment='"+row.attachment+"'>View Attachment</span></td>";
                            }else{
                                content2 +="<td>"+row.travel_amount+"</td>";
                            } 
                            content2 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            if(row.travel_desc != null){
                                content2 +="<td>"+row.travel_desc+"</td>";
                            }else{
                                content2 +="<td class='text-center'> - </td>";
                            }
                            content2 += "</tr>";
                    }

                });
                

                $("#exp_pay_records").html(content); //For append html data
                $('#expDatatable').dataTable();

                $("#cll_records").html(content1); //For append Cleared datatable html data 
                $('#clearedDatatable').dataTable();

                $("#cal_records").html(content2); //For append Cancelled datatable html data
                $('#cancelDatatable').dataTable();

                $("#apprvd_records").html(content3); //For append html data
                $('#apprvdDatatable').dataTable();    

                //table footer
                // $("#t_ucleamount").html(t_ucleamount+".00");
                // $("#t_cllamount").html(t_cllamount+".00");
                // $("#t_calamount").html(t_calamount+".00");
                // $("#t_apprvdamount").html(t_apprvdamount+".00");

                //For so
                // $('#exp_so').append("<option value='' class='text-muted' selected disabled>"+'ALL'+"</option>");
                $.each(data.s_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                    $('#exp_so').append("<option value='"+row.oth_id+"' selected>"+row.so_number+"</option>");
                });
            }
        });
    }

    //For Edit Operation
    $(document).on("click",'.exp_editT',function()
    {

        var attachment = $(this).data('attachment');

        var  attachment="files/user/travel_expense/"+attachment;

        $('#attachment1').attr("href",attachment);
    });

    //For image show Modal  
    $(document).on("click", ".view_attachment", function ()
    {   
        $src = $(this).data('attachment');
        $("#image_div").html('<img src="files/user/travel_expense/'+$src+'" id="imagepreview" style="width: 400px; height: 264px;">');
        // here asign the image to the modal when the user click the enlarge link
        $("#imageModal").modal("show");
    });

    //For image hide Modal  
    $(document).on("click", "#image_close", function ()
    {
        $('#image_div').empty(); 
    });

    //
    $('.nav-tabs a[href="#update_epayment"]').click(function(){
        $('.epayment_form')[0].reset()
        $('#exp_edit_id').val('');         
        // $("#exp_so").empty();
        // $("#exp_type option:selected").removeAttr("selected").change();
        // ("#exp_type option:selected").prop("selected", false);
        // $('#exp_type option:selected').removeAttr('selected','selected').change();

        $('#attachment1').hide();
        // getLabourExpenses();
        $("#mode_travel").val("").trigger("change");      // for when use select options are not dynamically print 

    });

    //For set/unset select field 
    $('.nav-tabs a[href="#epayment_list"]').click(function()
    {          
        // $("#exp_so").empty();
        // getLabourExpenses();
    });

    //For Edit Operation
    $(document).on("click",'.exp_editT',function()
    {
        var id = $(this).data('id');
        // $('#exp_type option:selected').remove();
        // $("#exp_type option:selected").removeAttr("selected");
        if(id !=""){
            $('#attachment1').show();
            var travel_date = $(this).data('travel_date');
            var mode_travel = $(this).data('mode_travel');
            var from_location = $(this).data('from_location');
            var to_location = $(this).data('to_location');
            var total_km = $(this).data('total_km');
            var travel_amount = $(this).data('travel_amount');
            var travel_desc = $(this).data('travel_desc');
            var attachment = $(this).data('attachment');
            // ACTIVE PANE AND LINK
            $('.nav-tabs a[href="#update_epayment"]').tab('show');

            var  attachment="files/user/travel_expense/"+attachment;
            $('#exp_edit_id').val(id);   
            $('#travel_date').val(travel_date); 
            $('#from_location').val(from_location); 
            $('#to_location').val(to_location);   
            $('#total_km').val(total_km); 
            $('#travel_amnt').val(travel_amount); 
            $('#travel_desc').val(travel_desc); 
            $('#attachment1').attr("href",attachment);

            //  $("#labour").find("option[value="+labour+"]").prop("selected", "selected");
            // $("#mode_travel option[value='"+mode_travel+"']").attr('selected','selected').change();
            $("#mode_travel").val(mode_travel).trigger("change");      // for when use select options are not dynamically print 
        }
        

    });

    // add/update travel expense Validation
    var n =0;
    $("#add_expense").click(function(event) 
    {
        // alert('hi');
        var edit_id= $('#exp_edit_id').val();
        var exp_so = $('#exp_so').val();
        var mode_travel = $('#mode_travel').val();
        var from_location= $('#from_location').val();
        var to_location = $('#to_location').val();
        var total_km= $('#total_km').val();
        var travel_date= $('#travel_date').val();
        var travel_desc = $('#travel_desc').val();
        var travel_amnt = $('#travel_amnt').val();
        var no_of_person = $('#no_of_person').val();


        n=0;    
        if( $.trim(mode_travel).length == 0 )
        {
            $('#mterror').text('Please Select Travel Mode.');
            event.preventDefault();
        }else{
            $('#mterror').text('');
            ++n;
        }

        if( $.trim(from_location).length == 0 )
        {
            $('#flerror').text('Please Enter From Location.');
            event.preventDefault();
        }else{
            $('#flerror').text('');
            ++n;
        }
       
        if( $.trim(to_location).length == 0 )
        {
            $('#tlerror').text('Please Enter To Location.');
            event.preventDefault();
        }else{
            $('#tlerror').text('');
            ++n;
        }

        if( $.trim(travel_date).length == 0 )
        {
            $('#tderror').text('Please Select Date.');
            event.preventDefault();
        }else{
            $('#tderror').text('');
            ++n;
        }

        if( $.trim(travel_amnt).length == 0 )
        {
            $('#taerror').text('Please Enter Amount.');
            event.preventDefault();
        }else{
            $('#taerror').text('');
            ++n;
        }
        
        if( $.trim(no_of_person).length == 0 )
        {
            $('#nperror').text('Please Enter No of Person.');
            event.preventDefault();
        }else{
            $('#nperror').text('');
            ++n;
        }

        // if( $.trim(travel_desc).length == 0 )
        // {
        //     $('#tdeerror').text('Please Enter Description.');
        //     event.preventDefault();
        // }else{
        //     $('#tdeerror').text('');
            
        // }

        // if( $.trim(total_km).length == 0 )
        // {
        //     $('#tkerror').text('Please Enter KM.');
        //     event.preventDefault();
        // }else{
        //     $('#tkerror').text('');
        // }

        var ext1 = $('#attachment').val().split('.').pop().toLowerCase();
        if($.inArray(ext1, ['png','jpg','jpeg']) == -1 && ext1 != '')
        {
            $('#aerror').html('Only .jpg, .jpeg, .png allowed').css('color','red');
            e.preventDefault();
             return false;
        }
    });

        //**************** Expense FILE ENCODER ********************

            $('#attachment').change(function(){
                var reader = new FileReader();  //step  3 -send to onloaded
                var f = document.getElementById("attachment").files;  // step 1 - file get 

                //step 4 - Encode File into base64 format 
                reader.onloadend = function () {    
                    ////console.log(reader.result);
                    var filevalue = reader.result.replace(/^data:.+;base64,/, '');  
                    document.getElementById("payment_encodedfile").value=filevalue; 
                }
                // End of File Encoding
                reader.readAsDataURL(f[0]); // step 2 - send to file reader
                var extension = document.getElementById("attachment").value.split('.').pop().toLowerCase(); // get extension file 
                document.getElementById("payment_extension").value=extension; // set extension on hidden input file 
            });

        //**************** END FILE ENCODER ********************

    // For Add Expenses Labour Payment
    $(document).on("click",'#add_expense',function()
    {           
        if(n==6)
        {        
            var form_data = new FormData();
            form_data.append("exp_edit_id", $("#exp_edit_id").val());
            form_data.append("exp_so", $("#exp_so").val());
            form_data.append("mode_travel", $("#mode_travel").val());
            form_data.append("from_location", $("#from_location").val());
            form_data.append("to_location", $("#to_location").val());
            form_data.append("total_km", $("#total_km").val());
            form_data.append("travel_date", $("#travel_date").val());
            form_data.append("travel_desc", $("#travel_desc").val());
            form_data.append("travel_amnt", $("#travel_amnt").val());
            form_data.append("no_of_person", $("#no_of_person").val());
       
            // For File Encode
            var attachment1=$('#payment_encodedfile').val();
            var extension1=$('#payment_extension').val();
            form_data.append("attachment",attachment1);
            form_data.append("payment_extension",extension1);

            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('post_travel_expense')}}",
                type :'POST',
                data : form_data,
                async: false,
                cache: true,
                dataType: 'json',
                contentType: false,
                processData: false,
                mimeType: "multipart/form-data",
                success:function(response){
                    console.log(response);

                    if (response.status==true) {  
                        // $("#exp_desc").val();    
                        // $("#exp_date").val('');
                        // $("#expense_amnt").val('');          
                        // $("#exp_so").empty();            

                        getTravelExpenses();

                        // ACTIVE PANE AND LINK
                        $('.nav-tabs a[href="#ucpayment_list"]').tab('show');
                        //For Notification
                        toastr.options.timeOut = 5000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.options.showEasing= 'swing';
                        toastr.options.hideEasing= 'linear';
                        toastr.options.showMethod= 'fadeIn';
                        toastr.options.hideMethod= 'fadeOut';
                        toastr.options.closeButton= true;
                        toastr.success(response.message);
            
                    }else{

                        //For Notification
                        toastr.options.timeOut = 5000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.options.showEasing= 'swing';
                        toastr.options.hideEasing= 'linear';
                        toastr.options.showMethod= 'fadeIn';
                        toastr.options.hideMethod= 'fadeOut';
                        toastr.options.closeButton= true;
                        toastr.error(response.message);
                    }
                }
            });
        }
        
    });

    // delete Product
    $(document).on("click",'.exp_delT',function()
    {
        var id = $(this).data('id');
        $('#id').val(id);
        // $('#delete_record_modal form').attr("action","delete_labour_payment/"+id);
        $('#delete_record_modal').modal('show');
    });

    // For delete user
    $(document).on("click",'#del_rec',function()
    {           
        var id= $('#id').val();
        // alert(id);

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('delete_travel_expense')}}",
            type :'get',
            data : {id:id},
            async: false,
            cache: true,
            dataType: 'json',
            success:function(response){
                console.log(response);

                if (response.status==true) {  

                    $("#id").val('');
                   
                    getTravelExpenses();         //set data records

                    $("#delete_record_modal").modal("hide");

                    //For Notification
                    toastr.options.timeOut = 5000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.options.showEasing= 'swing';
                    toastr.options.hideEasing= 'linear';
                    toastr.options.showMethod= 'fadeIn';
                    toastr.options.hideMethod= 'fadeOut';
                    toastr.options.closeButton= true;
                    toastr.success(response.message);
        
                }else{

                    //For Notification
                    toastr.options.timeOut = 5000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.options.showEasing= 'swing';
                    toastr.options.hideEasing= 'linear';
                    toastr.options.showMethod= 'fadeIn';
                    toastr.options.hideMethod= 'fadeOut';
                    toastr.options.closeButton= true;
                    toastr.error(response.message);
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function(){
        var $body = $("body");
        $('#labours').select2();
        $('#a_mode_travel,#status_change').select2({ dropdownParent: $('#editPaymentModal') });

        $("#abike_rate").hide();     // on change bike rate 
        $("#acar_rate").hide();      // on change car rate

    });
    var $body = $("body");

    // mode of travel on change fields
    $('#a_mode_travel').change(function(e)
    {
        $("#abike_rate").hide();     // on change bike rate 
        $("#acar_rate").hide();      // on change car rate

        var mode_travel = $(this).val(); 
        if(mode_travel == "Bike"){
            $("#abike_rate").show();     // on change bike rate 
        }

        if(mode_travel == "Own Car"){
            $("#acar_rate").show();      // on change car rate
        }

    });

    //For Admin Edit Expenses Operation
    $(document).on("click",'.exp_editU',function()
    {
        var id = $(this).data('id');
        var role = $('#role').val();
        
        if(role == 0)
        {
            $(".sa_div").show();
            $("#updated_amnt").prop('disabled', true);
            $("#acc_remark").prop('disabled', true);
        }else{
            $(".sa_div").hide();
            $("#updated_amnt").prop('disabled', false);
            $("#acc_remark").prop('disabled', false);
        }
        // $('#exp_type option:selected').remove();
        $("#mode_travel option:selected").removeAttr("selected");
        if(id !=""){
            $("#attachment2").show();
            var t_name = $(this).data('labour_name');
            var e_num = $(this).data('emp_number');

            var travel_date = $(this).data('travel_date');
            var mode_travel = $(this).data('mode_travel');
            var from_location = $(this).data('from_location');
            var to_location = $(this).data('to_location');
            var total_km = $(this).data('total_km');
            var travel_amount = $(this).data('travel_amount');
            var travel_desc = $(this).data('travel_desc');
            var attachment = $(this).data('attachment');
            var aprvd_amount = $(this).data('aprvd_amount');

            if(attachment == null){
                $("#attachment2").hide();
            }
            var  attachment="files/user/travel_expense/"+attachment;

            $('#exp_edit_id').val(id);   
            $('#a_travel_date').val(travel_date); 
            $('#a_from_location').val(from_location); 
            $('#a_to_location').val(to_location);   
            $('#a_total_km').val(total_km); 
            $('#expense_amnt').val(travel_amount); 
            $('#a_travel_desc').val(travel_desc); 
            // $('#updated_amnt').val(travel_amount); 
            $('#attachment2').attr("href",attachment);
            $('#t_name').html(t_name);   
            $('#e_num').html(e_num);

            if(aprvd_amount != null){
                $('#updated_amnt').val(aprvd_amount); 
                // $('#sa_updated_amnt').val(aprvd_amount);
            }else{
                $('#updated_amnt').val(travel_amount); 
                // $('#sa_updated_amnt').val(travel_amount);
            }

            $("#a_mode_travel").val(mode_travel).trigger("change");      // for when use select options are not dynamically print 

            // $("#a_mode_travel option[value='"+mode_travel+"']").attr('selected','selected').change();
            // $('#a_mode_travel option[value='+mode_travel+']').attr('selected','selected').change();
            $('#status_change option[value=Cleared]').attr('selected','selected').change();

            $('#editPaymentModal').modal('show');
        }
        

    });

     //For SA Edit Expenses Operation
     $(document).on("click",'.exp_editSA',function()
    {
        var id = $(this).data('id');
        var role = $('#role').val();
        
        if(role == 0)
        {
            $(".sa_div").show();
            $("#updated_amnt").prop('disabled', true);
            $("#acc_remark").prop('disabled', true);
        }else{
            $(".sa_div").hide();
            $("#updated_amnt").prop('disabled', false);
            $("#acc_remark").prop('disabled', false);
        }

        // $('#exp_type option:selected').remove();
        $("#exp_type option:selected").removeAttr("selected");
        if(id !=""){
            $("#attachment2").show();
            var t_name = $(this).data('labour_name');
            var e_num = $(this).data('emp_number');

             var travel_date = $(this).data('travel_date');
            var mode_travel = $(this).data('mode_travel');
            var from_location = $(this).data('from_location');
            var to_location = $(this).data('to_location');
            var total_km = $(this).data('total_km');
            var travel_amount = $(this).data('travel_amount');
            var aprvd_amount = $(this).data('aprvd_amount');
            var travel_desc = $(this).data('travel_desc');
            var ad_remark = $(this).data('ad_remark');
            var attachment = $(this).data('attachment');
            // alert(aprvd_amount);
            if(attachment == null){
                $("#attachment2").hide();
            }
            var  attachment="files/user/travel_expense/"+attachment;
  
            $('#exp_edit_id').val(id);   
            $('#a_travel_date').val(travel_date); 
            $('#a_from_location').val(from_location); 
            $('#a_to_location').val(to_location);   
            $('#a_total_km').val(total_km); 
            $('#a_travel_desc').val(travel_desc); 
            $('#expense_amnt').val(travel_amount); 

            if(aprvd_amount != null){
                $('#updated_amnt').val(aprvd_amount); 
                $('#sa_updated_amnt').val(aprvd_amount);
            }else{
                $('#updated_amnt').val(travel_amount); 
                $('#sa_updated_amnt').val(travel_amount);
            }
            

            $('#ad_remark').val(ad_remark);

            $('#attachment2').attr("href",attachment);
            $('#t_name').html(t_name);   
            $('#e_num').html(e_num);

            $("#a_mode_travel").val(mode_travel).trigger("change");      // for when use select options are not dynamically print 
            
            // $("#a_mode_travel option[value='"+mode_travel+"']").attr('selected','selected').change();
            $('#status_change option[value=Cleared]').attr('selected','selected').change();

            $('#editPaymentModal').modal('show');
        }
        

    });

    // From update_expense Validation
    var n =0;
    $("#update_expense").click(function(event) 
    {
        var status= $('#status_change').val();

        // alert('hi');
        var acc_remark = $('#acc_remark').val();
        var updated_amnt = $('#updated_amnt').val();

        n=0;    
        if( $.trim(updated_amnt).length == 0 )
        {
            $('#uaerror').text('Please Enter Amount.');
            event.preventDefault();
        }else{
            $('#uaerror').text('');
            ++n;
           
        }

        if( $.trim(status).length == 0 )
        {
            $('#eserror').text('Please Select Status.');
            event.preventDefault();
        }else{
            $('#eserror').text('');
            ++n;
        }
       
        if( $.trim(acc_remark).length == 0 )
        {
            $('#arerror').text('Please Enter Project Admin Remark');
            event.preventDefault();
        }else{
            $('#arerror').text('');
            
        }

    });

    // For Add Labour Payment
    $(document).on("click",'#update_expense',function()
    {       
        if(n>=2)
        {        
            var exp_edit_id= $('#exp_edit_id').val();
            var acc_remark = $('#acc_remark').val();
            var status= $('#status_change').val();
            var updated_amnt = $('#updated_amnt').val();
            var sa_updated_amnt= $('#sa_updated_amnt').val();
            var sa_remark = $('#sa_remark').val();
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('update_travel_expense')}}",
                type :'get',
                data : {updated_amnt:updated_amnt,status:status,acc_remark:acc_remark,exp_edit_id:exp_edit_id,sa_remark:sa_remark,sa_updated_amnt:sa_updated_amnt},
                async: false,
                cache: true,
                dataType: 'json',
                success:function(response){
                    console.log(response);

                    if (response.status==true) {  
                        $("#updated_amnt").val();    
                        $("#acc_remark").val('');
                        $("#sa_updated_amnt").val();    
                        $("#sa_remark").val('');
                        getTravelExpenses();

                        $("#editPaymentModal").modal("hide");
                        //For Notification
                        toastr.options.timeOut = 5000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.options.showEasing= 'swing';
                        toastr.options.hideEasing= 'linear';
                        toastr.options.showMethod= 'fadeIn';
                        toastr.options.hideMethod= 'fadeOut';
                        toastr.options.closeButton= true;
                        toastr.success(response.message);
            
                    }else{

                        //For Notification
                        toastr.options.timeOut = 5000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.options.showEasing= 'swing';
                        toastr.options.hideEasing= 'linear';
                        toastr.options.showMethod= 'fadeIn';
                        toastr.options.hideMethod= 'fadeOut';
                        toastr.options.closeButton= true;
                        toastr.error(response.message);
                    }
                }
            });
        }
        
    });  

</script>
@endpush