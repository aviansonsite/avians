@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
<?php use App\Http\Controllers\CommonController as Common; ?>
@section('title',"Technician Dashboard | $title")
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
                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Dashboard</li>
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
                            <strong>OA Number : </strong>{{$s->so_number}} , <strong>Project Name : </strong>{{$s->project_name}} , <strong>Client Name : </strong>{{$s->client_name}} , <strong>Address : </strong>{{$s->address}}
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
                                <p class="text-muted fw-medium text-center">Total Amount in Wallet</p>
                                <h4 class="mb-2 text-center">{{$total_wallet}}.00</h4>
                                <div class="row ">
                                    <div class="col-md-5">
                                        <h6 class="mb-0 text-center waves-effect style1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="From Avians Accountant" >{{$accountant_payment}}</h6> 
                                        <!-- <p class="text-muted fw-medium text-center">Accountant</p> -->
                                    </div>  
                                    <div class="col-md-1">
                                        |
                                    </div>  

                                    <div class="col-md-5">
                                        <h6 class="mb-0 text-center waves-effect style1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="From Other Technician" >{{$fot}}</h6>
                                        <!-- <p class="text-muted fw-medium text-center">FOT</p> -->
                                    </div> 
                                </div>
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
                                <div class="row ">
                                    <div class="col-md-5">
                                        <h6 class="mb-0 text-center waves-effect style1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Technician Expense">{{$total_tech_expense}}</h6> 
                                        <!-- <p class="text-muted fw-medium text-center">T Expense</p> -->
                                    </div>  
                                    <div class="col-md-1">
                                        |
                                    </div>  
                                    <div class="col-md-5">
                                        <h6 class="mb-0 text-center waves-effect style1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Transfer to Other Technician">{{$ttot}}</h6>
                                        <!-- <p class="text-muted fw-medium text-center">TTOT</p> -->
                                    </div> 
                                </div>
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
                                <h4 class="mb-2 text-center">{{$cleared_pay}}</h4>
                                <div class="row ">
                                    <div class="col-md-12">
                                        <h6 class="mb-0 text-center waves-effect style1" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Uncleared Expense">{{$uncleared_pay}}</h6>
                                        <!-- <p class="text-muted fw-medium text-center"> Uncleared </p> -->
                                    </div> 
                                </div>
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
                                <h4 class="mb-2 text-center">{{$balance}}</h4>
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
                                    <span class="d-none d-sm-block">Payment From Avians Account - <strong class="t_accamount"></strong></span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#technicianPayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Payment From Other Technician - <strong class="t_othamount"></strong></span> 
                                </a>
                            </li>
                            
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="aviansPaymentList" role="tabpanel">

                                {!! Form::open(['class'=>"form-horizontal",'id'=>"tot_search_form"]) !!}
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
                                {!! Form::close() !!}

                                <div class="table-responsive">
                                <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 20px;">#</th>
                                            <th scope="col" style="width: 100px">Date</th>
                                            <th scope="col" style="width: 100px">SO Number</th>
                                            <th scope="col" style="white-space: normal;">Description</th>
                                            <th scope="col">Amount<br>(In Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="acc_pay_records">
                                       
                                    </tbody>
                                    <tfoot id="taccdata">
                                        <tr>
                                            <th colspan="4" class="text-center"><strong>Total</strong></th>
                                            <th class="t_accamount"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            </div>
         
                            <div class="tab-pane" id="technicianPayment" role="tabpanel">
                            {!! Form::open(['class'=>"form-horizontal",'id'=>"tot_search_form"]) !!}
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
                                {!! Form::close() !!}

                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="fromDatatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col" style="width: 100px">Date</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="white-space: normal;">Description</th>
                                                <th scope="col">Amount<br>(In Rs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="oth_tech_records">
                                           
                                        </tbody>
                                        <tfoot id="tothdata">
                                            <tr>
                                                <th colspan="4"><strong>Total</strong></th>
                                                <th class="t_othamount"></th>
                                            </tr>
                                        </tfoot>
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
<!-- OA history modal content -->
<div id="oaHistoryModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">OA History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-group mb-3">
                            <label for="labours" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Lead Technician Support<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="labours" required name="labours" disabled>
                            @foreach($us_obj as $u)
                                <option value="{{$u->id}}">{{$u->name}}</option>
                            @endforeach
                            </select>
                            <span class="text-danger error" id="lerror"></span>
                            
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-group mb-3">
                            <label for="p_in_soh" class="form-label" style="font-size: 11px;margin-bottom: 2px;">OA Number<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="p_in_soh" required name="p_in_soh" disabled>
                            @foreach($s_obj1 as $so)
                                <option value="{{$so->oth_id}}">{{$so->so_number}}</option>
                            @endforeach
                            </select>
                            <span class="text-danger error" id="esoerror"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="client_name" placeholder="Enter Client Name" name="client_name" required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50" disabled>
                            <label for="client_name">Client Name<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="cnerror"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="project_name" placeholder="Enter Project Name" name="project_name" required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50" disabled>
                            <label for="project_name">Project Name<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="pnerror"></span>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="cp_name" placeholder="Enter CP Name" name="cp_name"required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50" disabled>
                            <label for="cp_name">CP Name<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="cpnerror"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="cp_ph_no" placeholder="Enter CP Phone" name="cp_ph_no" required maxlength="10" disabled>
                            <label for="cp_ph_no">CP Phone<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="cpperror"></span>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12 col-lg-8">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="address" placeholder="Enter Address" required name="address" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" disabled></textarea>
                            <label for="address">Project Address<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="aerror"></span>

                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Support Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="labour" name="labour[]" placeholder="Support Technician" disabled>
                             @foreach($us_obj as $u)
                                    <option value="{{$u->id}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error" id="slerror"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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

        });
    </script>
@endpush
@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    
    $(document).ready(function(){
        var $body = $("body");
        $('#exp_type,#exp_so').select2();
        $("#taccdata").hide();
        $("#tothdata").hide();

        $('#labour,#labours,#oa_hit').select2({ 
            dropdownParent: $('#oaHistoryModal') 
        });
    });

    // For avians acc from date ,to date records
    $(document).on("click",'#acc_ftd_records',function()
    {            
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_acc_payment')}}",
            type :'get',
            data : {from_date:from_date,to_date:to_date},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#datatable").DataTable().destroy();

                $("#taccdata").show();  // footer
                var t_accamount=0; 
                content ="";        //For datatable

                var i = 0;       
                $.each(data.data,function(index,row){
                  
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }
                    if(row.payment_date != null){
                        var payment_date = formatDate (row.payment_date); // "18/01/10"
                    }else{
                        var payment_date = " - "
                    }

                    t_accamount+=Number(row.payment_amnt);           //total of amount

                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+payment_date+"</td>";
                        content +="<td>";
                        $.each(row.s_obj,function(index,row){
                            content +=row.so_number+",";
                        });
                        content +="</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.payment_amnt+"</td>";
                        content += "</tr>";
                });
                
                $("#acc_pay_records").html(content); //For append html data
                $('#datatable').dataTable();

                //table footer
                $(".t_accamount").html(t_accamount+".00");


            }
        });
        
        
    });

    getAccPayment();
    function getAccPayment(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_acc_payment')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#datatable").DataTable().destroy();

                $("#taccdata").show();  // footer
                var t_accamount=0; 
                content ="";        //For datatable

                var i = 0;       
                $.each(data.data,function(index,row){
                  
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }
                    if(row.payment_date != null){
                        var payment_date = formatDate (row.payment_date); // "18/01/10"
                    }else{
                        var payment_date = " - "
                    }

                    t_accamount+=Number(row.payment_amnt);           //total of amount

                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+payment_date+"</td>";
                        content +="<td>";
                        $.each(row.s_obj,function(index,row){
                            content +=row.so_number+",";
                        });
                        content +="</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.payment_amnt+"</td>";
                        content += "</tr>";
                });
                
                $("#acc_pay_records").html(content); //For append html data
                $('#datatable').dataTable();

                //table footer
                $(".t_accamount").html(t_accamount+".00");

                $.each(data.s_obj,function(index,row){
    
  
                    $('#client_name').val(row.client_name); 
                    $('#project_name').val(row.project_name); 
                    $('#address').val(row.address); 
                    $('#cp_name').val(row.cp_name); 
                    $('#cp_ph_no').val(row.cp_ph_no);


                    var r=new Array();
                    if (row.labour.toString().indexOf(',')>-1)
                    { 
                        var r=row.labour.split(',');
                    }
                    else
                    {
                        r[0]=row.labour.toString();
                    }

                    $.each(r,function(index,value)
                    {
                        $("#labour option[value='"+value+"']").attr('selected','selected').change();
                    });

                    $("#labours option[value='"+row.lead_technician+"']").attr('selected','selected').change();   


                });
            }
        });
    }

    // For other technician  from date ,to date records
    $(document).on("click",'#otech_ftd_records',function()
    {           
        var from_date = $('#from_date1').val();
        var to_date = $('#to_date1').val();

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_ot_tech_payment')}}",
            type :'get',
            data : {from_date:from_date,to_date:to_date},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#fromDatatable").DataTable().destroy();

                $("#tothdata").show();  // footer
                var t_othamount=0; 
                content ="";        //For datatable

                var i = 0;       
                $.each(data.data,function(index,row){
                  
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }
                    if(row.p_date != null){
                        var p_date = formatDate (row.p_date); // "18/01/10"
                    }else{
                        var p_date = " - "
                    }

                    t_othamount+=Number(row.amount);           //total of amount

                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+p_date+"</td>";
                        content +="<td>"+row.name+"</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.amount+"</td>";
                        content += "</tr>";
                });
                
                $("#oth_tech_records").html(content); //For append html data
                $('#fromDatatable').dataTable();

                //table footer
                $(".t_othamount").html(t_othamount+".00");


            }
        });
        
        
    });

    //For Get other technician payment
    getOtherTechPayment();
    function getOtherTechPayment(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_ot_tech_payment')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#fromDatatable").DataTable().destroy();

                $("#tothdata").show();  // footer
                var t_othamount=0; 
                content ="";        //For datatable

                var i = 0;       
                $.each(data.data,function(index,row){
                  
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }
                    if(row.p_date != null){
                        var p_date = formatDate (row.p_date); // "18/01/10"
                    }else{
                        var p_date = " - "
                    }

                    t_othamount+=Number(row.amount);           //total of amount

                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+p_date+"</td>";
                        content +="<td>"+row.name+"</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.amount+"</td>";
                        content += "</tr>";
                });
                
                $("#oth_tech_records").html(content); //For append html data
                $('#fromDatatable').dataTable();

                //table footer
                $(".t_othamount").html(t_othamount+".00");


            }
        });
    }
</script>
@endpush