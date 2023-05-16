@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
@section('title',"Manage Technician Payment | $title")
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
                           <h4 class="card-title mb-4">Manage Expense Requests</h4>
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
                                        <div class="form-group mb-3">
                                            <label for="labours" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="labours" required name="labours">
                                                <option value="" disabled selected>Select</option>
                                                @foreach($u_obj as $u)
                                                    <option value="{{$u->id}}">{{$u->name}}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error" id="lerror"></span>
                                        </div>
                                    </div>
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
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="ucpayment_list" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="expDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Expense Date</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                @if($roles == 1)
                                                    <th scope="col">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="exp_pay_records">
                                           
                                        </tbody>
                                        <tfoot id="tucledata">
                                            <tr>
                                                <th colspan="7" class="text-center"><strong>Total</strong></th>
                                                <th id="t_ucleamount"></th>
                                                @if($roles == 1)
                                                    <th></th>
                                                @endif
                                            </tr>
                                        </tfoot>
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
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>

                                                @if($roles == 0)
                                                    <th scope="col">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="cll_records">
                                        
                                        </tbody>
                                        <tfoot id="tclldata">
                                            <tr>
                                                <th colspan="7" class="text-center"><strong>Total</strong></th>
                                                <th id="t_cllamount"></th>
                                                @if($roles == 0)
                                                    <th></th>
                                                    @endif
                                            </tr>
                                        </tfoot>
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
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">SA Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="apprvd_records">
                                        
                                        </tbody>
                                        <tfoot id="taprdata">
                                            <tr>
                                                <th colspan="8" class="text-center"><strong>Total</strong></th>
                                                <th id="t_apprvdamount"></th>
                                            </tr>
                                        </tfoot>
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
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Expense Type</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Admin Remark</th>
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                @if($roles == 0)
                                                    <th scope="col">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="cal_records">
                                        
                                        </tbody>
                                        <tfoot id="tcaldata">
                                            <tr>
                                                <th colspan="7" class="text-center"><strong>Total</strong></th>
                                                <th id="t_calamount"></th>
                                                @if($roles == 0)
                                                    <th></th>
                                                @endif
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

 <!-- sample modal content -->
 <div id="editPaymentModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Manage Expense</h5>
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
                            <label for="exp_type" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Expense Type<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="exp_type" required name="exp_type" disabled>
                                <option value="" disabled selected>Select</option>
                                    <option value="Crane/Hydra">Crane/Hydra</option>
                                    <option value="Daily Allowance">Daily Allowance</option>
                                    <option value="Hotel">Hotel</option>
                                    <option value="Labour_Hired">Labour Hired</option>
                                    <option value="Material_Purchase">Material Purchase</option>
                                    <option value="Scaffolding">Scaffolding</option>
                                    <option value="Other">Other</option>
                            </select>
                            <span class="text-danger error" id="eterror"></span>
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
                    <?php $tdate=date("Y-m-d");?>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="exp_date" placeholder="Expense Date" name="exp_date" required max="{{$tdate}}" value="{{$tdate}}" disabled>
                            <label for="exp_date">Expense Date<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="edaerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="expense_amnt" placeholder="Expense Amount" name="expense_amnt" maxlength="10" required disabled>
                            <label for="expense_amnt">Requested Amount (In Rs.)<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="eaerror"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="exp_desc" placeholder="Enter Expense Description" required name="exp_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" disabled></textarea>
                            <label for="exp_desc">Expense Description</label>
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
                            <input type="text" class="form-control" id="updated_amnt" placeholder="Expense Amount" name="updated_amnt" maxlength="10" required>
                            <label for="updated_amnt">PA Aprvd Amount (In Rs.)<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="uaerror"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6 sa_div">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="sa_updated_amnt" placeholder="Expense Amount" name="sa_updated_amnt" maxlength="10" required>
                            <label for="sa_updated_amnt">SA Aprvd Amount (In Rs.)<sup class="text-danger">*</sup></label>
                            <span class="text-danger error" id="sauaerror"></span>
                        </div>
                    </div>

                    <div class="col-md-12 sa_div">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="sa_remark" placeholder="Enter Accountant Remark" required name="sa_remark" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                            <label for="sa_remark">SA Remark</label>
                            <span class="text-danger error" id="sarerror"></span>

                        </div>
                    </div>
                    
                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <a href="" id="attachment1" target="_blank"><i class="fa fa-eye"></i> View Attachment File</a>
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
            $('#cancelDatatable').dataTable(); 
            $('#apprvdDatatable').dataTable();    


        });
    </script>
@endpush

@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    $(document).ready(function(){
        var $body = $("body");
        $('#labours').select2();
        $('#exp_type,#status_change').select2({ dropdownParent: $('#editPaymentModal') });

        $("#tucledata").hide();
        $("#tclldata").hide();
        $("#tcaldata").hide();
        $("#taprdata").hide();
    });
    var $body = $("body");

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
            url:"{{url('get_all_expenses')}}",
            type :'get',
            data : {from_date:from_date,to_date:to_date,labours:labours},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#expDatatable").DataTable().destroy();
                $("#clearedDatatable").DataTable().destroy();
                $("#cancelDatatable").DataTable().destroy();
                $("#apprvdDatatable").DataTable().destroy();


                $("#tucledata").show();
                $("#tclldata").show();
                $("#tcaldata").show();
                $("#taprdata").show();

                var t_ucleamount=t_cllamount=t_calamount=t_apprvdamount=0; 
                content ="";
                content1 ="";
                content2 ="";
                content3 ="";
                var i=j=k=l= 0;       
                // $("#labour").empty();            
                // $("#so").empty();        
                $.each(data.data,function(index,row){

                    if(row.status == 'Uncleared'){
                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }
                        t_ucleamount+=Number(row.amount);           //total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content +="<tr>";
                            content +="<td>"+ ++i +"</td>";
                            content +="<td>"+exp_date+"</td>";
                            content +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content +="<td>"+row.exp_type+"</td>";
                            }
                            content +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content +="<td class='text-center'> - </td>";
                            }
                            
                            content +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content +="<td>"+row.amount+"</td>";
                            if(data.role == 1){
                                content +="<td><a class='btn btn-outline-secondary btn-sm exp_editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-status='"+row.status+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"'  data-bs-toggle='modal'><i class='far fa-edit'></i></a></td>";
                            }
                            content += "</tr>";

                    }   

                    if(row.status == 'Cleared'){

                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }

                        t_cllamount+=Number(row.aprvd_amount);           //total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content1 +="<tr>";
                            content1 +="<td>"+ ++j +"</td>";
                            content1 +="<td>"+exp_date+"</td>";
                            content1 +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content1 +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content1 +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content1 +="<td>"+row.exp_type+"</td>";
                            }
                            content1 +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content1 +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content1 +="<td class='text-center'> - </td>";
                            }
                            content1 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content1 +="<td>"+row.aprvd_amount+"</td>";
                            if(data.role == 0){
                                content1 +="<td><a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-status='"+row.status+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-acc_remark='"+row.acc_remark+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a></td>";
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
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }
                        t_apprvdamount+=Number(row.aprvd_amount);           //total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content3 +="<tr>";
                            content3 +="<td>"+ ++l +"</td>";
                            content3 +="<td>"+exp_date+"</td>";
                            content3 +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content3 +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content3 +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content3 +="<td>"+row.exp_type+"</td>";
                            }
                            content3 +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content3 +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }
                            if(row.sa_remark != null){
                                content3 +="<td>"+row.sa_remark+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }
                            content3 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content3 +="<td>"+row.aprvd_amount+"</td>";
                            content3 += "</tr>";


                    }

                    if(row.status == 'Cancelled'){

                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }
                        t_calamount+=Number(row.amount);       // total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content2 +="<tr>";
                            content2 +="<td>"+ ++k +"</td>";
                            content2 +="<td>"+exp_date+"</td>";
                            content2 +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content2 +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content2 +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content2 +="<td>"+row.exp_type+"</td>";
                            }
                            content2 +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content2 +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content2 +="<td class='text-center'> - </td>";
                            }
                            content2 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content2 +="<td>"+row.amount+"</td>";
                            if(data.role == 0){
                                content2 +="<td><a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-status='"+row.status+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-acc_remark='"+row.acc_remark+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a></td>";
                            }
                            content2 += "</tr>";

                    }
                    
                });

                $("#exp_pay_records").html(content); //For append html data
                $('#expDatatable').dataTable();

                $("#cll_records").html(content1); //For append html data
                $('#clearedDatatable').dataTable();

                $("#cal_records").html(content2); //For append html data
                $('#cancelDatatable').dataTable();     
                
                $("#apprvd_records").html(content3); //For append html data
                $('#apprvdDatatable').dataTable();    
            }
        });
    });
    
    getLabourExpenses();
    function getLabourExpenses(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_all_expenses')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#expDatatable").DataTable().destroy();
                $("#clearedDatatable").DataTable().destroy();
                $("#cancelDatatable").DataTable().destroy();
                $("#apprvdDatatable").DataTable().destroy();

                
                $("#tucledata").show();
                $("#tclldata").show();
                $("#tcaldata").show();
                $("#taprdata").show();

                var t_ucleamount=t_cllamount=t_calamount=t_apprvdamount=0; 
                content ="";
                content1 ="";
                content2 ="";
                content3 ="";
                var i=j=k=l= 0;       
                // $("#labour").empty();            
                // $("#so").empty();        
                $.each(data.data,function(index,row){

                    if(row.status == 'Uncleared'){
                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }
                        t_ucleamount+=Number(row.amount);           //total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content +="<tr>";
                            content +="<td>"+ ++i +"</td>";
                            content +="<td>"+exp_date+"</td>";
                            content +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content +="<td>"+row.exp_type+"</td>";
                            }
                            content +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content +="<td class='text-center'> - </td>";
                            }
                            
                            content +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content +="<td>"+row.amount+"</td>";
                            if(data.role == 1){
                                content +="<td><a class='btn btn-outline-secondary btn-sm exp_editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-status='"+row.status+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"'  data-bs-toggle='modal'><i class='far fa-edit'></i></a></td>";
                            }
                            content += "</tr>";

                    }   

                    if(row.status == 'Cleared'){

                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }

                        t_cllamount+=Number(row.aprvd_amount);           //total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content1 +="<tr>";
                            content1 +="<td>"+ ++j +"</td>";
                            content1 +="<td>"+exp_date+"</td>";
                            content1 +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content1 +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content1 +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content1 +="<td>"+row.exp_type+"</td>";
                            }
                            content1 +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content1 +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content1 +="<td class='text-center'> - </td>";
                            }
                            content1 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content1 +="<td>"+row.aprvd_amount+"</td>";
                            if(data.role == 0){
                                content1 +="<td><a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-status='"+row.status+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-acc_remark='"+row.acc_remark+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a></td>";
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
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }
                        t_apprvdamount+=Number(row.aprvd_amount);           //total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content3 +="<tr>";
                            content3 +="<td>"+ ++l +"</td>";
                            content3 +="<td>"+exp_date+"</td>";
                            content3 +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content3 +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content3 +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content3 +="<td>"+row.exp_type+"</td>";
                            }
                            content3 +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content3 +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }
                            if(row.sa_remark != null){
                                content3 +="<td>"+row.sa_remark+"</td>";
                            }else{
                                content3 +="<td class='text-center'> - </td>";
                            }
                            content3 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content3 +="<td>"+row.aprvd_amount+"</td>";
                            content3 += "</tr>";


                    }

                    if(row.status == 'Cancelled'){

                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }
                        t_calamount+=Number(row.amount);       // total of amount
                        var d = new Date();
                        var current_date = d.getDate();
                            content2 +="<tr>";
                            content2 +="<td>"+ ++k +"</td>";
                            content2 +="<td>"+exp_date+"</td>";
                            content2 +="<td>"+row.labour_name+"</td>";
                            if(row.exp_type == "Material_Purchase"){
                                content2 +="<td> Material Purchase </td>";
                            }else if(row.exp_type == "Labour_Hired"){
                                content2 +="<td> Labour Hired </td>";
                            }else if(row.exp_type != "Material_Purchase" && row.exp_type != "Labour_Hired"){
                                content2 +="<td>"+row.exp_type+"</td>";
                            }
                            content2 +="<td>"+row.exp_desc+"</td>";
                            if(row.acc_remark != null){
                                content2 +="<td>"+row.acc_remark+"</td>";
                            }else{
                                content2 +="<td class='text-center'> - </td>";
                            }
                            content2 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content2 +="<td>"+row.amount+"</td>";
                            if(data.role == 0){
                                content2 +="<td><a class='btn btn-outline-secondary btn-sm exp_editSA' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-aprvd_amount='"+row.aprvd_amount+"' data-status='"+row.status+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-emp_number='"+row.emp_number+"' data-labour_name='"+row.labour_name+"' data-acc_remark='"+row.acc_remark+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a></td>";
                            }
                            content2 += "</tr>";

                    }
                    
                });
                

                $("#exp_pay_records").html(content); //For append html data
                $('#expDatatable').dataTable();

                $("#cll_records").html(content1); //For append html data
                $('#clearedDatatable').dataTable();

                $("#cal_records").html(content2); //For append html data
                $('#cancelDatatable').dataTable();   
                
                $("#apprvd_records").html(content3); //For append html data
                $('#apprvdDatatable').dataTable();    
                
                //table footer
                $("#t_ucleamount").html(t_ucleamount+".00");
                $("#t_cllamount").html(t_cllamount+".00");
                $("#t_calamount").html(t_calamount+".00");
                $("#t_apprvdamount").html(t_apprvdamount+".00");
            }
        });
    }



    //For admin Edit Expenses Operation
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
        // $("#exp_type option:selected").removeAttr("selected");
        if(id !=""){
            var t_name = $(this).data('labour_name');
            var e_num = $(this).data('emp_number');
            var exp_type = $(this).data('exp_type');
            var exp_desc = $(this).data('exp_desc');
            var exp_date = $(this).data('exp_date');
            var amount = $(this).data('amount');
            var aprvd_amount = $(this).data('aprvd_amount');
            var attachment = $(this).data('attachment');

            var  attachment="files/user/expense/"+attachment;
            $('#exp_edit_id').val(id);   
            $('#exp_desc').val(exp_desc); 
            $('#exp_date').val(exp_date); 
            $('#expense_amnt').val(amount); 
            $('#attachment1').attr("href",attachment); 
            $('#t_name').html(t_name);   
            $('#e_num').html(e_num);

            if(aprvd_amount != null){
                $('#updated_amnt').val(aprvd_amount); 
            }else{
                $('#updated_amnt').val(amount); 
            }
            
            $("#exp_type").val(exp_type).trigger("change"); 
            // $("#exp_type option[value='"+exp_type+"']").attr('selected','selected').change();
            // $('#exp_type option[value='+exp_type+']').attr('selected','selected').change();
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
            var t_name = $(this).data('labour_name');
            var e_num = $(this).data('emp_number');
            var exp_type = $(this).data('exp_type');
            var exp_desc = $(this).data('exp_desc');
            var exp_date = $(this).data('exp_date');
            var acc_remark = $(this).data('acc_remark');
            var amount = $(this).data('amount');
            var aprvd_amount = $(this).data('aprvd_amount');
            var attachment = $(this).data('attachment');

            var  attachment="files/user/expense/"+attachment;
            $('#exp_edit_id').val(id);   
            $('#exp_desc').val(exp_desc); 
            $('#exp_date').val(exp_date); 
            $('#expense_amnt').val(amount); 
            $('#acc_remark').val(acc_remark);
            $('#attachment1').attr("href",attachment);
           
            $('#t_name').html(t_name);   
            $('#e_num').html(e_num);

            if(aprvd_amount != null){
                $('#updated_amnt').val(aprvd_amount); 
                $('#sa_updated_amnt').val(aprvd_amount);
            }else{
                $('#updated_amnt').val(amount); 
                $('#sa_updated_amnt').val(amount);
            }

            $("#exp_type").val(exp_type).trigger("change"); 

            // $("#exp_type option[value='"+exp_type+"']").attr('selected','selected').change();
            // $('#exp_type option[value='+exp_type+']').attr('selected','selected').change();
            $('#status_change option[value=Cleared]').attr('selected','selected').change();

            $('#editPaymentModal').modal('show');
        }
        

    });

    // From SO Validation
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
            $('#arerror').text('Please Enter Accountant Remark');
            event.preventDefault();
        }else{
            $('#arerror').text('');
            ++n;
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
                url:"{{url('post_expense')}}",
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
                        getLabourExpenses();

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