@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
<?php use App\Http\Controllers\CommonController as Common; ?>
@section('title',"OA Payment History | $title")
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

    
    .style1{
        display: block;
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
                    <h4 class="mb-sm-0 font-size-18">OA Payment History</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">OA Payment History</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="text-muted">
                    @foreach($s_obj as $s)
                        <div>
                            <strong>OA Number : </strong>{{$s->so_number}} , <strong>Client Name : </strong>{{$s->client_name}} , <strong>Project Name : </strong>{{$s->project_name}}
                        </div>                        
                    @endforeach
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">From Avians Accountant</p>
                                <h4 class="mb-2 text-center">{{$accountant_payment}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">From Other Technician</p>
                                <h4 class="mb-2 text-center">{{$fot}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">Total Amount in Wallet</p>
                                <h4 class="mb-2 text-center">{{$total_wallet}}.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">Technician Expense</p>
                                <h4 class="mb-2 text-center">{{$total_tech_expense}}.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">Transfer to Other Technician</p>
                                <h4 class="mb-2 text-center">{{$ttot}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">Total Expense</p>
                                <h4 class="mb-2 text-center">{{$total_expense}}.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">Total Approved Amount</p>
                                <h4 class="mb-2 text-center">{{$cleared_pay}}.00</h4>
                                <!-- <div class="row ">
                                    <div class="col-md-12">
                                        <h6 class="mb-0 text-center waves-effect style1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Uncleared Expense">{{$uncleared_pay}}</h6>
                                        <p class="text-muted fw-medium text-center"> Uncleared </p>
                                    </div> 
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium text-center">Balance in Wallet</p>
                                <h4 class="mb-2 text-center">{{$balance}}.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">Total Payment in Wallet - Transactions</h4>
                           <div class="ms-auto">
                           
                            </div>
                        </div>
                    
                        @include('common.alert')
                        <div id="alerts"></div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#aviansPaymentList" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Payment From Avians Account<strong class="t_accamount"></strong></span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#technicianPayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Payment From Other Technician<strong class="t_othamount"></strong></span> 
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="aviansPaymentList" role="tabpanel">

                                <!-- {!! Form::open(['class'=>"form-horizontal",'id'=>"tot_search_form"]) !!}
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
                                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="acc_ftd_records">Search</button>
                                    </div>
                                </div>
                                {!! Form::close() !!} -->

                                <div class="table-responsive">
                                <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 20px;">#</th>
                                            <th scope="col" style="width: 100px">Date</th>
                                            <th scope="col" style="width: 100px">OA Number</th>
                                            <th scope="col" style="width: 100px">Lead Technician</th>
                                            <th scope="col" style="white-space: normal;">Description</th>
                                            <th scope="col">Amount<br>(In Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="acc_pay_records">
                                        <?php $h=0; $roless=Session::get('ROLES'); ?>
                                        @foreach($avians_payment as $ap)
                                            <?php $p_date = Common::dateDMY($ap->payment_date); ?>
                                            <tr>
                                                <td>{{++$h}}</td>
                                                <td>{{$p_date}}</td>
                                                <td>{{$ap->so_number}}</td>
                                                <td>{{$ap->name}}</td>
                                                <td>{{$ap->p_desc}}</td>
                                                <td>{{$ap->payment_amnt}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <!-- <tfoot id="taccdata">
                                        <tr>
                                            <th colspan="5" class="text-center"><strong>Total</strong></th>
                                            <th class="t_accamount"></th>
                                        </tr>
                                    </tfoot> -->
                                </table>
                            </div>
                            </div>
         
                            <div class="tab-pane" id="technicianPayment" role="tabpanel">
                            <!-- {!! Form::open(['class'=>"form-horizontal",'id'=>"tot_search_form"]) !!}
                                <div class="row">
                                    <?php $tdate=date("Y-m-d"); ?>
                                    <div class="col-md-3 col-sm-12 col-lg-3">
                                        <div class="form-floating mb-3">
                                            <input type="date" max="{{$tdate}}" class="form-control" id="from_date1"  name="from_date1" required placeholder="dd-mm-yyyy" value="{{$tdate}}">
                                            <label for="from_date1">From Date <sup class="text-danger">*</sup></label>
                                            <small><span class="text-danger" id="fderror" style="font-size: 11px !important;"></span></small>
                                        </div>
                                    </div>
            
                                    <div class="col-md-3 col-sm-12 col-lg-3">
                                        <div class="form-floating mb-3">
                                            <input type="date" max="{{$tdate}}" class="form-control" id="to_date1"  name="to_date1" required placeholder="dd-mm-yyyy" 
                                            value="{{$tdate}}">
                                            <label for="to_date1">To Date <sup class="text-danger">*</sup></label>
                                            <small><span class="text-danger" id="tderror" style="font-size: 11px !important;"></span></small>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12 col-lg-3">
                                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="otech_ftd_records">Search</button>
                                    </div>
                                </div>
                                {!! Form::close() !!} -->

                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="fromDatatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Payment Date</th>
                                                <th scope="col" style="width: 100px">Sender OA Number</th>
                                                <th scope="col" style="width: 100px">Sender Technician Name</th>
                                                <th scope="col" style="width: 100px">Receiver Technician Name</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="oth_tech_records">
                                        <?php $v=0; $roless=Session::get('ROLES'); ?>
                                        @foreach($receiver_payment as $rp)
                                            <?php $p_date = Common::dateDMY($ap->payment_date); ?>
                                            <tr>
                                            <td>{{++$v}}</td>
                                            <td>{{$p_date}}</td>
                                            <td>{{$rp->sender_so_number}}</td>
                                            <td>{{$rp->sender_labour_name}}</td>
                                            <td>{{$rp->labour_name}}</td>
                                            <td>{{$rp->p_desc}}</td>
                                            <td>{{$rp->amount}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <!-- <tfoot id="tothdata">
                                            <tr>
                                                <th colspan="4"><strong>Total</strong></th>
                                                <th class="t_othamount"></th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

          <!-- Transfer Table -->
          <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">Transfer to Other Technician</h4>
                           <div class="ms-auto">
                           
                            </div>
                        </div>
                    
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="toDatatable"> 
                                <thead>
                                    <tr>
                                        <th scope="col">Sr.No</th>
                                        <th scope="col" style="white-space: normal;">Payment Date</th>
                                        <th scope="col" style="width: 100px">OA Number</th>
                                        <th scope="col" style="width: 100px">Sender Technician Name</th>
                                        <th scope="col" style="width: 100px">Receiver Technician Name</th>
                                        <th scope="col" style="width: 100px">Description</th>
                                        <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                    </tr>
                                </thead>
                                <tbody id="trpay_records">
                                <?php $i=0;?>
                                    @foreach($transfer_payment as $tp)
                                        <?php $p_date = Common::dateDMY($tp->p_date); ?>
                                        <tr>
                                            <td>{{++$i}}</td>
                                            <td>{{$p_date}}</td>
                                            <td>{{$tp->recvr_so_number}}</td>
                                            <td>{{$tp->labour_name}}</td>
                                            <td>{{$tp->recvr_labour_name}}</td>
                                            <td>{{$tp->p_desc}}</td>
                                            <td>{{$tp->amount}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!-- <tfoot id="ttrandata">
                                    <tr>
                                        <th colspan="5" class="text-center"><strong>Total</strong></th>
                                        <th id="t_tranamount"></th>
                                        
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- general  expense -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">General Expense</h4>
                           <div class="ms-auto">
                                <!-- <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" data-bs-toggle="modal" data-bs-target="#addModal" style="margin-left: 10px;">
                                <i class="mdi mdi-plus font-size-11"></i> Add Labour Payment
                                </button>  -->
                                
                            </div>
                        </div>
             
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                      
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
                                    <span class="d-none d-sm-block">Disapproved Payment List</span> 
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
                                                <th scope="col" style="white-space: normal;">Expense Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="exp_pay_records">
                                            <?php $k=$l=$m=$n=0; $roless=Session::get('ROLES'); ?>
                                            @foreach($general_expense as $ge)
                                                @if($ge->status == 'Uncleared')
                                                    <?php $exp_date = Common::dateDMY($ge->exp_date); ?>
                                                    <tr>
                                                        <td>{{++$k}}</td>
                                                        <td>{{$exp_date}}</td>
                                                        <td>{{$ge->amount}}</td>
                                                        <td>{{$ge->labour_name}}</td>
                                                        <td>{{$ge->so_number}}</td>
                                                        <td>{{$ge->client_name}}</td>
                                                        <td>{{$ge->project_name}}</td>
                                                        <td>{{$ge->project_admin}}</td>
                                                        <td>
                                                            @if($ge->exp_type == "Material_Purchase")
                                                                Material Purchase
                                                            @elseif($ge->exp_type == "Labour_Hired")
                                                                Labour Hired
                                                            @elseif($ge->exp_type != "Material_Purchase" && $ge->exp_type != "Labour_Hired")
                                                                {{$ge->exp_type}}
                                                            @endif
                                                        </td>
                                                        <td>{{$ge->exp_desc}}</td>
                                                        @if($ge->acc_remark != null)
                                                            <td class='text-center'>{{$ge->acc_remark}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif
                                                        <td><span class='badge badge-soft-primary'>{{$ge->status}}</span></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <!-- <tfoot id="tucledata">
                                            <tr>
                                                <th colspan="2" class="text-center"><strong>Total</strong></th>
                                                <th id="t_ucleamount"></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>   
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
                                                <th scope="col" style="white-space: normal;">Expense Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody id="cll_records">
                                            @foreach($general_expense as $ge)
                                                @if($ge->status == 'Cleared')
                                                <?php $exp_date = Common::dateDMY($ge->exp_date); ?>
                                                <tr>
                                                    <td>{{++$l}}</td>
                                                    <td>{{$exp_date}}</td>
                                                    <td>{{$ge->aprvd_amount}}</td>
                                                    <td>{{$ge->labour_name}}</td>
                                                    <td>{{$ge->so_number}}</td>
                                                    <td>{{$ge->client_name}}</td>
                                                    <td>{{$ge->project_name}}</td>
                                                    <td>{{$ge->project_admin}}</td>
                                                    <td>
                                                        @if($ge->exp_type == "Material_Purchase")
                                                            Material Purchase
                                                        @elseif($ge->exp_type == "Labour_Hired")
                                                            Labour Hired
                                                        @elseif($ge->exp_type != "Material_Purchase" && $ge->exp_type != "Labour_Hired")
                                                            {{$ge->exp_type}}
                                                        @endif
                                                    </td>
                                                    <td>{{$ge->exp_desc}}</td>
                                                    @if($ge->acc_remark != null)
                                                        <td class='text-center'>{{$ge->acc_remark}}</td>
                                                    @else
                                                        <td class='text-center'> - </td>
                                                    @endif
                                                    <td><span class='badge badge-soft-primary'>{{$ge->status}}</span></td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <!-- <tfoot id="tclldata">
                                            <tr>
                                                <th colspan="2" class="text-center"><strong>Total</strong></th>
                                                <th id="t_cllamount"></th>  
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>                                     
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
                                                <th scope="col" style="white-space: normal;">Expense Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">SA Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody id="apprvd_records">
                                            @foreach($general_expense as $ge)
                                                @if($ge->status == 'Approved')
                                                <?php $exp_date = Common::dateDMY($ge->exp_date); ?>
                                                <tr>
                                                    <td>{{++$m}}</td>
                                                    <td>{{$exp_date}}</td>
                                                    <td>{{$ge->aprvd_amount}}</td>
                                                    <td>{{$ge->labour_name}}</td>
                                                    <td>{{$ge->so_number}}</td>
                                                    <td>{{$ge->client_name}}</td>
                                                    <td>{{$ge->project_name}}</td>
                                                    <td>{{$ge->project_admin}}</td>
                                                    <td>
                                                        @if($ge->exp_type == "Material_Purchase")
                                                            Material Purchase
                                                        @elseif($ge->exp_type == "Labour_Hired")
                                                            Labour Hired
                                                        @elseif($ge->exp_type != "Material_Purchase" && $ge->exp_type != "Labour_Hired")
                                                            {{$ge->exp_type}}
                                                        @endif
                                                    </td>
                                                    <td>{{$ge->exp_desc}}</td>

                                                    @if($ge->acc_remark != null)
                                                        <td class='text-center'>{{$ge->acc_remark}}</td>
                                                    @else
                                                        <td class='text-center'> - </td>
                                                    @endif

                                                    @if($ge->sa_remark != null)
                                                        <td class='text-center'>{{$ge->sa_remark}}</td>
                                                    @else
                                                        <td class='text-center'> - </td>
                                                    @endif

                                                    <td><span class='badge badge-soft-primary'>{{$ge->status}}</span></td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <!-- <tfoot id="taprdata">
                                            <tr>
                                                <th colspan="2" class="text-center"><strong>Total</strong></th>
                                                <th id="t_apprvdamount"></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
                                                <th scope="col" style="white-space: normal;">Expense Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cal_records">
                                            @foreach($general_expense as $ge)
                                                @if($ge->status == 'Disapproved')
                                                <?php $exp_date = Common::dateDMY($ge->exp_date); ?>
                                                <tr>
                                                    <td>{{++$n}}</td>
                                                    <td>{{$exp_date}}</td>
                                                    <td>{{$ge->amount}}</td>
                                                    <td>{{$ge->labour_name}}</td>
                                                    <td>{{$ge->so_number}}</td>
                                                    <td>{{$ge->client_name}}</td>
                                                    <td>{{$ge->project_name}}</td>
                                                    <td>{{$ge->project_admin}}</td>
                                                    <td>
                                                        @if($ge->exp_type == "Material_Purchase")
                                                            Material Purchase
                                                        @elseif($ge->exp_type == "Labour_Hired")
                                                            Labour Hired
                                                        @elseif($ge->exp_type != "Material_Purchase" && $ge->exp_type != "Labour_Hired")
                                                            {{$ge->exp_type}}
                                                        @endif
                                                    </td>
                                                    <td>{{$ge->exp_desc}}</td>

                                                    @if($ge->acc_remark != null)
                                                        <td class='text-center'>{{$ge->acc_remark}}</td>
                                                    @else
                                                        <td class='text-center'> - </td>
                                                    @endif
                                                    <td><span class='badge badge-soft-primary'>{{$ge->status}}</span></td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <!-- <tfoot id="tcaldata">
                                            <tr>
                                                <th colspan="2" class="text-center"><strong>Total</strong></th>
                                                <th id="t_calamount"></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Travel  expense -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">Travel Expense</h4>
                           <div class="ms-auto">
                                <!-- <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" data-bs-toggle="modal" data-bs-target="#addModal" style="margin-left: 10px;">
                                <i class="mdi mdi-plus font-size-11"></i> Add Labour Payment
                                </button>  -->
                                
                            </div>
                        </div>
             
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#trucpayment_list" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Uncleared Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#trcllpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Cleared Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#trapprvdpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Approved Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#trcalpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Disapproved Payment List</span> 
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="trucpayment_list" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="trexpDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Travel Mode</th>
                                                <th scope="col" style="width: 100px">From Location</th>
                                                <th scope="col" style="width: 100px">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="exp_pay_records">
                                            <?php $p=$q=$r=$s=0; $roless=Session::get('ROLES'); ?>
                                            @foreach($travel_expense as $tr)
                                                @if($tr->status == 'Uncleared')
                                                    <?php $travel_date = Common::dateDMY($tr->travel_date); ?>
                                                    <tr>
                                                        <td>{{++$p}}</td>
                                                        <td>{{$travel_date}}</td>
                                                        @if($tr->attachment != null)
                                                            <td>{{$tr->travel_amount}}<br><span class='badge badge-soft-primary view_attachment' data-attachment="{{$tr->attachment}}">View Attachment</span></td>
                                                        @else
                                                            <td>{{$tr->travel_amount}}</td>
                                                        @endif
                                                 
                                                        <td><span class='badge badge-soft-primary'>{{$tr->status}}</span></td>
                                                        <td>{{$tr->so_number}}</td>
                                                        <td>{{$tr->client_name}}</td>
                                                        <td>{{$tr->project_name}}</td>
                                                        <td>{{$tr->labour_name}}</td>
                                                        <td>{{$tr->project_admin}}</td>

                                                        <td>
                                                            @if($tr->mode_travel == "Shared_Auto")
                                                                Shared Auto
                                                            @elseif($tr->mode_travel == "Private_Auto")
                                                                Private Auto
                                                            @elseif($tr->mode_travel != "Private_Auto" && $tr->mode_travel != "Shared_Auto")
                                                                {{$tr->mode_travel}}
                                                            @endif
                                                        </td>
                                                        <td>{{$tr->from_location}}</td>
                                                        <td>{{$tr->to_location}}</td>

                                                        @if($tr->total_km != null)
                                                            <td class='text-center'>{{$tr->total_km}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->travel_desc != null)
                                                            <td class='text-center'>{{$tr->travel_desc}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif
                                                        
                                                    </tr>
                                                @endif
                                            @endforeach
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
         
                            <div class="tab-pane" id="trcllpayment" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="trclearedDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="white-space: normal;">Travel Mode</th>
                                                <th scope="col" style="white-space: normal;">From Location</th>
                                                <th scope="col" style="white-space: normal;">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cll_records">
                                            @foreach($travel_expense as $tr)
                                                @if($tr->status == 'Cleared')
                                                    <?php $travel_date = Common::dateDMY($tr->travel_date); ?>
                                                    <tr>
                                                        <td>{{++$q}}</td>
                                                        <td>{{$travel_date}}</td>

                                                        @if($tr->attachment != null)
                                                            <td>{{$tr->aprvd_amount}}<br><span class='badge badge-soft-primary view_attachment' data-attachment="{{$tr->attachment}}">View Attachment</span></td>
                                                        @else
                                                            <td>{{$tr->aprvd_amount}}</td>
                                                        @endif
                                                 
                                                        <td><span class='badge badge-soft-primary'>{{$tr->status}}</span></td>
                                                        <td>{{$tr->so_number}}</td>
                                                        <td>{{$tr->client_name}}</td>
                                                        <td>{{$tr->project_name}}</td>
                                                        <td>{{$tr->labour_name}}</td>
                                                        <td>{{$tr->project_admin}}</td>

                                                        <td>
                                                            @if($tr->mode_travel == "Shared_Auto")
                                                                Shared Auto
                                                            @elseif($tr->mode_travel == "Private_Auto")
                                                                Private Auto
                                                            @elseif($tr->mode_travel != "Private_Auto" && $tr->mode_travel != "Shared_Auto")
                                                                {{$tr->mode_travel}}
                                                            @endif
                                                        </td>
                                                        <td>{{$tr->from_location}}</td>
                                                        <td>{{$tr->to_location}}</td>

                                                        @if($tr->total_km != null)
                                                            <td class='text-center'>{{$tr->total_km}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->ad_remark != null)
                                                            <td class='text-center'>{{$tr->ad_remark}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->travel_desc != null)
                                                            <td class='text-center'>{{$tr->travel_desc}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif
                                                        
                                                    </tr>
                                                @endif
                                            @endforeach
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

                            <div class="tab-pane" id="trapprvdpayment" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="trapprvdDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="white-space: normal;">Travel Mode</th>
                                                <th scope="col" style="white-space: normal;">From Location</th>
                                                <th scope="col" style="white-space: normal;">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">SA Remark</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="apprvd_records">
                                            @foreach($travel_expense as $tr)
                                                @if($tr->status == 'Approved')
                                                    <?php $travel_date = Common::dateDMY($tr->travel_date); ?>
                                                    <tr>
                                                        <td>{{++$r}}</td>
                                                        <td>{{$travel_date}}</td>

                                                        @if($tr->attachment != null)
                                                            <td>{{$tr->aprvd_amount}}<br><span class='badge badge-soft-primary view_attachment' data-attachment="{{$tr->attachment}}">View Attachment</span></td>
                                                        @else
                                                            <td>{{$tr->aprvd_amount}}</td>
                                                        @endif
                                                 
                                                        <td><span class='badge badge-soft-primary'>{{$tr->status}}</span></td>
                                                        <td>{{$tr->so_number}}</td>
                                                        <td>{{$tr->client_name}}</td>
                                                        <td>{{$tr->project_name}}</td>
                                                        <td>{{$tr->labour_name}}</td>
                                                        <td>{{$tr->project_admin}}</td>

                                                        <td>
                                                            @if($tr->mode_travel == "Shared_Auto")
                                                                Shared Auto
                                                            @elseif($tr->mode_travel == "Private_Auto")
                                                                Private Auto
                                                            @elseif($tr->mode_travel != "Private_Auto" && $tr->mode_travel != "Shared_Auto")
                                                                {{$tr->mode_travel}}
                                                            @endif
                                                        </td>
                                                        <td>{{$tr->from_location}}</td>
                                                        <td>{{$tr->to_location}}</td>

                                                        @if($tr->total_km != null)
                                                            <td class='text-center'>{{$tr->total_km}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->ad_remark != null)
                                                            <td class='text-center'>{{$tr->ad_remark}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->sa_remark != null)
                                                            <td class='text-center'>{{$tr->sa_remark}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->travel_desc != null)
                                                            <td class='text-center'>{{$tr->travel_desc}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif
                                                        
                                                    </tr>
                                                @endif
                                            @endforeach
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

                            <div class="tab-pane" id="trcalpayment" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="trcancelDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Travel Date</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Travel Mode</th>
                                                <th scope="col" style="width: 100px">From Location</th>
                                                <th scope="col" style="width: 100px">To Location</th>
                                                <th scope="col" style="width: 100px">Total KM</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cal_records">
                                            @foreach($travel_expense as $tr)
                                                @if($tr->status == 'Disapproved')
                                                    <?php $travel_date = Common::dateDMY($tr->travel_date); ?>
                                                    <tr>
                                                        <td>{{++$s}}</td>
                                                        <td>{{$travel_date}}</td>
                                                        @if($tr->attachment != null)
                                                            <td>{{$tr->travel_amount}}<br><span class='badge badge-soft-primary view_attachment' data-attachment="{{$tr->attachment}}">View Attachment</span></td>
                                                        @else
                                                            <td>{{$tr->travel_amount}}</td>
                                                        @endif
                                                 
                                                        <td><span class='badge badge-soft-primary'>{{$tr->status}}</span></td>
                                                        <td>{{$tr->so_number}}</td>
                                                        <td>{{$tr->client_name}}</td>
                                                        <td>{{$tr->project_name}}</td>
                                                        <td>{{$tr->labour_name}}</td>
                                                        <td>{{$tr->project_admin}}</td>

                                                        <td>
                                                            @if($tr->mode_travel == "Shared_Auto")
                                                                Shared Auto
                                                            @elseif($tr->mode_travel == "Private_Auto")
                                                                Private Auto
                                                            @elseif($tr->mode_travel != "Private_Auto" && $tr->mode_travel != "Shared_Auto")
                                                                {{$tr->mode_travel}}
                                                            @endif
                                                        </td>
                                                        <td>{{$tr->from_location}}</td>
                                                        <td>{{$tr->to_location}}</td>

                                                        @if($tr->total_km != null)
                                                            <td class='text-center'>{{$tr->total_km}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif

                                                        @if($tr->travel_desc != null)
                                                            <td class='text-center'>{{$tr->travel_desc}}</td>
                                                        @else
                                                            <td class='text-center'> - </td>
                                                        @endif
                                                        
                                                    </tr>
                                                @endif
                                            @endforeach
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  
@stop
@push('datatable_js')
    {!! Html::script('assets/libs/datatables.net/js/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') !!}
    <script>
        $(document).ready(function(){
            $('#datatable').dataTable();    
            $('#fromDatatable').dataTable();
            $('#toDatatable').dataTable();
           
            $('#expDatatable').dataTable();
            $('#clearedDatatable').dataTable();    
            $('#cancelDatatable').dataTable();    
            $('#apprvdDatatable').dataTable();  
            $('#trexpDatatable').dataTable();
            $('#trclearedDatatable').dataTable();    
            $('#trcancelDatatable').dataTable();    
            $('#trapprvdDatatable').dataTable();
        });
    </script>
@endpush
@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    
    $(document).ready(function(){
        var $body = $("body");
        $('#exp_type,#exp_so,#p_in_soh').select2();
        $("#taccdata").hide();
        $("#tothdata").hide();

        $('#labour,#labours,#oa_hit').select2({ 
            dropdownParent: $('#oaHistoryModal') 
        });
    });

    // For avians acc from date ,to date records
    // $(document).on("click",'#acc_ftd_records',function()
    // {            
    //     var from_date = $('#from_date').val();
    //     var to_date = $('#to_date').val();

    //     $.ajax({
    //         headers:{
    //             'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url:"{{url('get_acc_payment')}}",
    //         type :'get',
    //         data : {from_date:from_date,to_date:to_date},
    //         cache: false,
    //         dataType: 'json',                 
    //         success:function(data){
    //             console.log(data.data);
    //             $("#datatable").DataTable().destroy();

    //             $("#taccdata").show();  // footer
    //             var t_accamount=0; 
    //             content ="";        //For datatable

    //             var i = 0;       
    //             $.each(data.data,function(index,row){
                  
    //                 //date convert into dd/mm/yyyy
    //                 function formatDate (input) {
    //                     var datePart = input.match(/\d+/g),
    //                     year = datePart[0].substring(0), // get only two digits
    //                     month = datePart[1], day = datePart[2];
    //                     return day+'-'+month+'-'+year;
    //                 }
    //                 if(row.payment_date != null){
    //                     var payment_date = formatDate (row.payment_date); // "18/01/10"
    //                 }else{
    //                     var payment_date = " - "
    //                 }

    //                 t_accamount+=Number(row.payment_amnt);           //total of amount

    //                 var d = new Date();
    //                 var current_date = d.getDate();
    //                     content +="<tr>";
    //                     content +="<td>"+ ++i +"</td>";
    //                     content +="<td>"+payment_date+"</td>";
    //                     content +="<td>";
    //                     $.each(row.s_obj,function(index,row){
    //                         content +=row.so_number+",";
    //                     });
    //                     content +="</td>";
    //                     content +="<td>"+row.p_desc+"</td>";
    //                     content +="<td>"+row.payment_amnt+"</td>";
    //                     content += "</tr>";
    //             });
                
    //             $("#acc_pay_records").html(content); //For append html data
    //             $('#datatable').dataTable();

    //             //table footer
    //             $(".t_accamount").html(t_accamount+".00");


    //         }
    //     });
        
        
    // });

    // getAccPayment();
    // function getAccPayment(){

    //     $.ajax({
    //         headers:{
    //             'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url:"{{url('get_acc_payment')}}",
    //         type :'get',
    //         data : {},
    //         cache: false,
    //         dataType: 'json',                 
    //         success:function(data){
    //             console.log(data.data);
    //             $("#datatable").DataTable().destroy();

    //             $("#taccdata").show();  // footer
    //             var t_accamount=0; 
    //             content ="";        //For datatable

    //             var i = 0;       
    //             $.each(data.data,function(index,row){
                  
    //                 //date convert into dd/mm/yyyy
    //                 function formatDate (input) {
    //                     var datePart = input.match(/\d+/g),
    //                     year = datePart[0].substring(0), // get only two digits
    //                     month = datePart[1], day = datePart[2];
    //                     return day+'-'+month+'-'+year;
    //                 }
    //                 if(row.payment_date != null){
    //                     var payment_date = formatDate (row.payment_date); // "18/01/10"
    //                 }else{
    //                     var payment_date = " - "
    //                 }

    //                 t_accamount+=Number(row.payment_amnt);           //total of amount

    //                 var d = new Date();
    //                 var current_date = d.getDate();
    //                     content +="<tr>";
    //                     content +="<td>"+ ++i +"</td>";
    //                     content +="<td>"+payment_date+"</td>";
    //                     content +="<td>";
    //                     $.each(row.s_obj,function(index,row){
    //                         content +=row.so_number;
    //                     });
    //                     content +="</td>";

    //                     if(row.p_desc != null){
    //                         content +="<td>"+row.p_desc+"</td>";
    //                     }else{
    //                         content +="<td class='text-center'> - </td>";
    //                     }

    //                     content +="<td>"+row.payment_amnt+"</td>";
    //                     content += "</tr>";
    //             });
                
    //             $("#acc_pay_records").html(content); //For append html data
    //             $('#datatable').dataTable();

    //             //table footer
    //             $(".t_accamount").html(t_accamount+".00");

    //             $.each(data.s_obj,function(index,row){
    
  
    //                 $('#client_name').val(row.client_name); 
    //                 $('#project_name').val(row.project_name); 
    //                 $('#address').val(row.address); 
    //                 $('#cp_name').val(row.cp_name); 
    //                 $('#cp_ph_no').val(row.cp_ph_no);


    //                 var r=new Array();
    //                 if (row.labour.toString().indexOf(',')>-1)
    //                 { 
    //                     var r=row.labour.split(',');
    //                 }
    //                 else
    //                 {
    //                     r[0]=row.labour.toString();
    //                 }

    //                 $.each(r,function(index,value)
    //                 {
    //                     $("#labour option[value='"+value+"']").attr('selected','selected').change();
    //                 });

    //                 $("#labours option[value='"+row.lead_technician+"']").attr('selected','selected').change();   


    //             });
    //         }
    //     });
    // }

    // For other technician  from date ,to date records
    // $(document).on("click",'#otech_ftd_records',function()
    // {           
    //     var from_date = $('#from_date1').val();
    //     var to_date = $('#to_date1').val();

    //     $.ajax({
    //         headers:{
    //             'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url:"{{url('get_ot_tech_payment')}}",
    //         type :'get',
    //         data : {from_date:from_date,to_date:to_date},
    //         cache: false,
    //         dataType: 'json',                 
    //         success:function(data){
    //             console.log(data.data);
    //             $("#fromDatatable").DataTable().destroy();

    //             $("#tothdata").show();  // footer
    //             var t_othamount=0; 
    //             content ="";        //For datatable

    //             var i = 0;       
    //             $.each(data.data,function(index,row){
                  
    //                 //date convert into dd/mm/yyyy
    //                 function formatDate (input) {
    //                     var datePart = input.match(/\d+/g),
    //                     year = datePart[0].substring(0), // get only two digits
    //                     month = datePart[1], day = datePart[2];
    //                     return day+'-'+month+'-'+year;
    //                 }
    //                 if(row.p_date != null){
    //                     var p_date = formatDate (row.p_date); // "18/01/10"
    //                 }else{
    //                     var p_date = " - "
    //                 }

    //                 t_othamount+=Number(row.amount);           //total of amount

    //                 var d = new Date();
    //                 var current_date = d.getDate();
    //                     content +="<tr>";
    //                     content +="<td>"+ ++i +"</td>";
    //                     content +="<td>"+p_date+"</td>";
    //                     content +="<td>"+row.name+"</td>";
    //                     content +="<td>"+row.p_desc+"</td>";
    //                     content +="<td>"+row.amount+"</td>";
    //                     content += "</tr>";
    //             });
                
    //             $("#oth_tech_records").html(content); //For append html data
    //             $('#fromDatatable').dataTable();

    //             //table footer
    //             $(".t_othamount").html(t_othamount+".00");


    //         }
    //     });
        
        
    // });

    //For Get other technician payment
    // getOtherTechPayment();
    // function getOtherTechPayment(){

    //     $.ajax({
    //         headers:{
    //             'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url:"{{url('get_ot_tech_payment')}}",
    //         type :'get',
    //         data : {},
    //         cache: false,
    //         dataType: 'json',                 
    //         success:function(data){
    //             console.log(data.data);
    //             $("#fromDatatable").DataTable().destroy();

    //             $("#tothdata").show();  // footer
    //             var t_othamount=0; 
    //             content ="";        //For datatable

    //             var i = 0;       
    //             $.each(data.data,function(index,row){
                  
    //                 //date convert into dd/mm/yyyy
    //                 function formatDate (input) {
    //                     var datePart = input.match(/\d+/g),
    //                     year = datePart[0].substring(0), // get only two digits
    //                     month = datePart[1], day = datePart[2];
    //                     return day+'-'+month+'-'+year;
    //                 }
    //                 if(row.p_date != null){
    //                     var p_date = formatDate (row.p_date); // "18/01/10"
    //                 }else{
    //                     var p_date = " - "
    //                 }

    //                 t_othamount+=Number(row.amount);           //total of amount

    //                 var d = new Date();
    //                 var current_date = d.getDate();
    //                     content +="<tr>";
    //                     content +="<td>"+ ++i +"</td>";
    //                     content +="<td>"+p_date+"</td>";
    //                     content +="<td>"+row.name+"</td>";
    //                     if(row.p_desc != null){
    //                         content +="<td>"+row.p_desc+"</td>";
    //                     }else{
    //                         content +="<td class='text-center'> - </td>";
    //                     }
    //                     content +="<td>"+row.amount+"</td>";
    //                     content += "</tr>";
    //             });
                
    //             $("#oth_tech_records").html(content); //For append html data
    //             $('#fromDatatable').dataTable();

    //             //table footer
    //             $(".t_othamount").html(t_othamount+".00");


    //         }
    //     });
    // }
</script>

<script>
// getLabourExpenses();
//     function getLabourExpenses(){

//         $.ajax({
//             headers:{
//                 'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
//             },
//             url:"{{url('get_all_expenses')}}",
//             type :'get',
//             data : {},
//             cache: false,
//             dataType: 'json',                 
//             success:function(data){
//                 console.log(data.data);


//                     $("#expDatatable").DataTable().destroy();
//                     $("#clearedDatatable").DataTable().destroy();
//                     $("#cancelDatatable").DataTable().destroy();
//                     $("#apprvdDatatable").DataTable().destroy();

                    
//                     $("#tucledata").show();
//                     $("#tclldata").show();
//                     $("#tcaldata").show();
//                     $("#taprdata").show();

//                     var t_ucleamount=t_cllamount=t_calamount=t_apprvdamount=0; 
//                     content ="";
//                     content1 ="";
//                     content2 ="";
//                     content3 ="";
//                     var i=j=k=l= 0;       
//                     // $("#labour").empty();            
//                     // $("#so").empty();        
//                     $.each(data.data,function(index,row){

//                         if(row.status == 'Uncleared'){
//                             //date convert into dd/mm/yyyy
//                             function formatDate (input) {
//                                 var datePart = input.match(/\d+/g),
//                                 year = datePart[0].substring(0), // get only two digits
//                                 month = datePart[1], day = datePart[2];
//                                 return day+'-'+month+'-'+year;
//                             }
//                             if(row.exp_date != null){
//                                 var exp_date = formatDate (row.exp_date); // "18/01/10"
//                             }else{
//                                 var exp_date = " - "
//                             }
//                             t_ucleamount+=Number(row.amount);           //total of amount
//                             var d = new Date();
//                             var current_date = d.getDate();
//                                 content +="<tr>";
//                                 content +="<td>"+ ++i +"</td>";
//                                 content +="<td>"+exp_date+"</td>";
//                                 content +="<td>"+row.amount+"</td>";
//                                 content +="<td>"+row.labour_name+"</td>";
//                                 content +="<td>"+row.so_number+"</td>";
//                                 content +="<td>"+row.client_name+"</td>";
//                                 content +="<td>"+row.project_name+"</td>";
//                                 content +="<td>"+row.project_admin+"</td>";

//                                 if(row.exp_type == "Material_Purchase"){
//                                     content +="<td> Material Purchase </td>";
//                                 }else if(row.exp_type == "Labour_Hired"){
//                                     content +="<td> Labour Hired </td>";
//                                 }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
//                                     content +="<td>"+row.exp_type+"</td>";
//                                 }
//                                 content +="<td>"+row.exp_desc+"</td>";
//                                 if(row.acc_remark != null){
//                                     content +="<td>"+row.acc_remark+"</td>";
//                                 }else{
//                                     content +="<td class='text-center'> - </td>";
//                                 }
                                
//                                 content +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                                
                                
//                                 content += "</tr>";

//                         }   

//                         if(row.status == 'Cleared'){

//                             //date convert into dd/mm/yyyy
//                             function formatDate (input) {
//                                 var datePart = input.match(/\d+/g),
//                                 year = datePart[0].substring(0), // get only two digits
//                                 month = datePart[1], day = datePart[2];
//                                 return day+'-'+month+'-'+year;
//                             }
//                             if(row.exp_date != null){
//                                 var exp_date = formatDate (row.exp_date); // "18/01/10"
//                             }else{
//                                 var exp_date = " - "
//                             }

//                             t_cllamount+=Number(row.aprvd_amount);           //total of amount

//                             var d = new Date();
//                             var current_date = d.getDate();
//                                 content1 +="<tr>";
//                                 content1 +="<td>"+ ++j +"</td>";
//                                 content1 +="<td>"+exp_date+"</td>";
//                                 content1 +="<td>"+row.aprvd_amount+"</td>";
//                                 content1 +="<td>"+row.labour_name+"</td>";
//                                 content1 +="<td>"+row.so_number+"</td>";
//                                 content1 +="<td>"+row.client_name+"</td>";
//                                 content1 +="<td>"+row.project_name+"</td>";
//                                 content1 +="<td>"+row.project_admin+"</td>";
//                                 if(row.exp_type == "Material_Purchase"){
//                                     content1 +="<td> Material Purchase </td>";
//                                 }else if(row.exp_type == "Labour_Hired"){
//                                     content1 +="<td> Labour Hired </td>";
//                                 }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
//                                     content1 +="<td>"+row.exp_type+"</td>";
//                                 }
//                                 content1 +="<td>"+row.exp_desc+"</td>";
//                                 if(row.acc_remark != null){
//                                     content1 +="<td>"+row.acc_remark+"</td>";
//                                 }else{
//                                     content1 +="<td class='text-center'> - </td>";
//                                 }
//                                 content1 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                                
                                
//                                 content1 += "</tr>";
//                         }

//                         if(row.status == 'Approved'){

//                             //date convert into dd/mm/yyyy
//                             function formatDate (input) {
//                                 var datePart = input.match(/\d+/g),
//                                 year = datePart[0].substring(0), // get only two digits
//                                 month = datePart[1], day = datePart[2];
//                                 return day+'-'+month+'-'+year;
//                             }
//                             if(row.exp_date != null){
//                                 var exp_date = formatDate (row.exp_date); // "18/01/10"
//                             }else{
//                                 var exp_date = " - "
//                             }
//                             t_apprvdamount+=Number(row.aprvd_amount);           //total of amount
//                             var d = new Date();
//                             var current_date = d.getDate();
//                                 content3 +="<tr>";
//                                 content3 +="<td>"+ ++l +"</td>";
//                                 content3 +="<td>"+exp_date+"</td>";
//                                 content3 +="<td>"+row.aprvd_amount+"</td>";
//                                 content3 +="<td>"+row.labour_name+"</td>";
//                                 content3 +="<td>"+row.so_number+"</td>";
//                                 content3 +="<td>"+row.client_name+"</td>";
//                                 content3 +="<td>"+row.project_name+"</td>";
//                                 content3 +="<td>"+row.project_admin+"</td>";

//                                 if(row.exp_type == "Material_Purchase"){
//                                     content3 +="<td> Material Purchase </td>";
//                                 }else if(row.exp_type == "Labour_Hired"){
//                                     content3 +="<td> Labour Hired </td>";
//                                 }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
//                                     content3 +="<td>"+row.exp_type+"</td>";
//                                 }
//                                 content3 +="<td>"+row.exp_desc+"</td>";
//                                 if(row.acc_remark != null){
//                                     content3 +="<td>"+row.acc_remark+"</td>";
//                                 }else{
//                                     content3 +="<td class='text-center'> - </td>";
//                                 }
//                                 if(row.sa_remark != null){
//                                     content3 +="<td>"+row.sa_remark+"</td>";
//                                 }else{
//                                     content3 +="<td class='text-center'> - </td>";
//                                 }
//                                 content3 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                               
//                                 content3 += "</tr>";


//                         }

//                         if(row.status == 'Disapproved'){

//                             //date convert into dd/mm/yyyy
//                             function formatDate (input) {
//                                 var datePart = input.match(/\d+/g),
//                                 year = datePart[0].substring(0), // get only two digits
//                                 month = datePart[1], day = datePart[2];
//                                 return day+'-'+month+'-'+year;
//                             }
//                             if(row.exp_date != null){
//                                 var exp_date = formatDate (row.exp_date); // "18/01/10"
//                             }else{
//                                 var exp_date = " - "
//                             }
//                             t_calamount+=Number(row.amount);       // total of amount
//                             var d = new Date();
//                             var current_date = d.getDate();
//                                 content2 +="<tr>";
//                                 content2 +="<td>"+ ++k +"</td>";
//                                 content2 +="<td>"+exp_date+"</td>";
//                                 content2 +="<td>"+row.amount+"</td>";
//                                 content2 +="<td>"+row.labour_name+"</td>";
//                                 content2 +="<td>"+row.so_number+"</td>";
//                                 content2 +="<td>"+row.client_name+"</td>";
//                                 content2 +="<td>"+row.project_name+"</td>";
//                                 content2 +="<td>"+row.project_admin+"</td>";

//                                 if(row.exp_type == "Material_Purchase"){
//                                     content2 +="<td> Material Purchase </td>";
//                                 }else if(row.exp_type == "Labour_Hired"){
//                                     content2 +="<td> Labour Hired </td>";
//                                 }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
//                                     content2 +="<td>"+row.exp_type+"</td>";
//                                 }
//                                 content2 +="<td>"+row.exp_desc+"</td>";
//                                 if(row.acc_remark != null){
//                                     content2 +="<td>"+row.acc_remark+"</td>";
//                                 }else{
//                                     content2 +="<td class='text-center'> - </td>";
//                                 }
//                                 content2 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                                
                                
//                                 content2 += "</tr>";

//                         }
                        
//                     });
                    

//                     $("#exp_pay_records").html(content); //For append html data
//                     $('#expDatatable').dataTable();

//                     $("#cll_records").html(content1); //For append html data
//                     $('#clearedDatatable').dataTable();

//                     $("#cal_records").html(content2); //For append html data
//                     $('#cancelDatatable').dataTable();   
                    
//                     $("#apprvd_records").html(content3); //For append html data
//                     $('#apprvdDatatable').dataTable();    
                    
//                     //table footer
//                     $("#t_ucleamount").html(t_ucleamount+".00");
//                     $("#t_cllamount").html(t_cllamount+".00");
//                     $("#t_calamount").html(t_calamount+".00");
//                     $("#t_apprvdamount").html(t_apprvdamount+".00");

//             }
//         });
//     }

</script>
@endpush