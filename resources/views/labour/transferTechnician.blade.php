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

        <!-- Transfer Table -->
        <div class="row">
            <div class="col-lg-12">
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
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">Transfer to Other Technician &nbsp;<small class="text-danger note">(Note - Payment will Transfer to only Active TL.)</small></h4>
                           <div class="ms-auto">
                           
                            </div>
                        </div>
                    
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tpayment_list" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Transfer Payment List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#update_tpayment" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">ADD / Update Transfer Payment</span> 
                                </a>
                            </li>
                            
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="tpayment_list" role="tabpanel">
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
                                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="tot_ftd_records">Search</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="toDatatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Payment Date</th>
                                                <th scope="col" style="width: 100px">OA Number</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Amount <br>(In Rs.)</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="trpay_records">
                                           
                                        </tbody>
                                        <tfoot id="ttrandata">
                                            <tr>
                                                <th colspan="5" class="text-center"><strong>Total</strong></th>
                                                <th id="t_tranamount"></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
         
                            <div class="tab-pane" id="update_tpayment" role="tabpanel">
                                {!! Form::open(['class'=>"form-horizontal tpayment_form",'enctype'=>'multipart/form-data','files' => 'true' ,'id'=>'postPaymentform']) !!}
                                    <input type="hidden" name="edit_id" id="edit_id" value="">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA <sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="so" required name="so">
                                                   
                                                </select>
                                                <span class="text-danger error" id="soerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12 col-lg-3">
                                            <div class="form-group mb-3">
                                                <label for="labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician - (OA Number)<sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="labour" required name="labour">
                                                    
                                                </select>
                                                <span class="text-danger error" id="lerror"></span>
                                            </div>
                                        </div>
                                        

                                        <?php $tdate=date("Y-m-d");?>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="date" class="form-control" id="payment_date" placeholder="Payment Date" name="payment_date" required max="{{$tdate}}" value="{{$tdate}}">
                                                <label for="payment_date">Payment Date<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="pdaerror"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-5 col-sm-12 col-lg-5">
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" id="pay_desc" placeholder="Enter Payment Description" required name="pay_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="500"></textarea>
                                                <label for="pay_desc">Payment Description<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="pderror"></span>

                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="payment_amnt" placeholder="Payment Amount" name="payment_amnt" maxlength="10" required>
                                                <label for="payment_amnt">Amount (In Rs.)<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="paerror"></span>
                                            </div>
                                        </div>
                                        <div class="d-sm-flex flex-wrap">
                                            <div class="ms-auto">
                                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="add_labour_payment">Submit</button>
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
                            <label for="oa_hit" class="form-label" style="font-size: 11px;margin-bottom: 2px;">OA Number<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="oa_hit" required name="oa_hit" disabled>
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
                            <label for="labour1" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Support Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="labour1" name="labour1[]" placeholder="Support Technician" disabled>
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
            $('#expDatatable').dataTable();
            $('#clearedDatatable').dataTable();    
            $('#cancelDatatable').dataTable();    
        });
    </script>
@endpush
@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    
    $(document).ready(function(){
        var $body = $("body");
        $('#exp_type,#exp_so').select2();
        $("#tucledata").hide();
        $("#tclldata").hide();
        $("#tcaldata").hide();
        
    });

    getLabourExpenses();
    function getLabourExpenses(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_labour_expenses')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#expDatatable").DataTable().destroy();
                $("#clearedDatatable").DataTable().destroy();
                $("#cancelDatatable").DataTable().destroy();

                $("#tucledata").show();
                $("#tclldata").show();
                $("#tcaldata").show();
                var t_ucleamount=t_cllamount=t_calamount=0; 
                content ="";        //For Uncleared datatable
                content1 ="";        //For Cleared datatable
                content2 ="";        //For Cancelled datatable

                var i = 0;       
                $("#labour").empty();            
                $("#so").empty();        
                
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
                            content +="<td>"+row.exp_type+"</td>";
                            content +="<td>"+row.exp_desc+"</td>";
                            content +="<td>"+row.amount+"</td>";
                            content +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content +="<td>";
                                if(exp_date == $.datepicker.formatDate('dd-mm-yy', new Date())){
                                    content +="<a class='btn btn-outline-secondary btn-sm exp_editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Expense' data-id='"+row.id+"' data-so='"+row.so_id+"' data-exp_date='"+row.exp_date+"' data-exp_desc='"+row.exp_desc+"' data-amount='"+row.amount+"' data-exp_type='"+row.exp_type+"' data-attachment='"+row.attachment+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm exp_delI' rel='tooltip' data-bs-placement='top' title='Delete Expense' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                                }
                            content +="</td>";
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
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }

                        t_cllamount+=Number(row.amount);           //total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content1 +="<tr>";
                            content1 +="<td>"+ ++i +"</td>";
                            content1 +="<td>"+exp_date+"</td>";
                            content1 +="<td>"+row.exp_type+"</td>";
                            content1 +="<td>"+row.exp_desc+"</td>";
                            content1 +="<td>"+row.amount+"</td>";
                            content1 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content1 += "</tr>";
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
                        if(row.exp_date != null){
                            var exp_date = formatDate (row.exp_date); // "18/01/10"
                        }else{
                            var exp_date = " - "
                        }

                        t_calamount+=Number(row.amount);       // total of amount

                        var d = new Date();
                        var current_date = d.getDate();
                            content2 +="<tr>";
                            content2 +="<td>"+ ++i +"</td>";
                            content2 +="<td>"+exp_date+"</td>";
                            content2 +="<td>"+row.exp_type+"</td>";
                            content2 +="<td>"+row.exp_desc+"</td>";
                            content2 +="<td>"+row.amount+"</td>";
                            content2 +="<td><span class='badge badge-soft-primary'>"+row.status+"</span></td>";
                            content2 += "</tr>";
                    }

                });
                

                $("#exp_pay_records").html(content); //For append html data
                $('#expDatatable').dataTable();

                $("#cll_records").html(content1); //For append Cleared datatable html data 
                $('#clearedDatatable').dataTable();

                $("#cal_records").html(content2); //For append Cancelled datatable html data
                $('#cancelDatatable').dataTable();

                //table footer
                $("#t_ucleamount").html(t_ucleamount+".00");
                $("#t_cllamount").html(t_cllamount+".00");
                $("#t_calamount").html(t_calamount+".00");


                //For so
                // $('#exp_so').append("<option value='' class='text-muted' selected disabled>"+'ALL'+"</option>");
                $.each(data.s_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                    $('#exp_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");

                });
            }
        });
    }

    //
    $('.nav-tabs a[href="#update_epayment"]').click(function(){
        $('.epayment_form')[0].reset()
        $('#exp_edit_id').val('');         
        $("#exp_so").empty();
        // $("#exp_type option:selected").removeAttr("selected").change();
        // ("#exp_type option:selected").prop("selected", false);
        // $('#exp_type option:selected').removeAttr('selected','selected').change();

        $('#attachment1').hide();
        getLabourExpenses();

    });

    //For set/unset select field 
    $('.nav-tabs a[href="#epayment_list"]').click(function()
    {          
        $("#exp_so").empty();
        getLabourExpenses();
    });

    //For Edit Operation
    $(document).on("click",'.exp_editU',function()
    {
        var id = $(this).data('id');
        // $('#exp_type option:selected').remove();
        $("#exp_type option:selected").removeAttr("selected");
        if(id !=""){
            $('#attachment1').show();
            var exp_type = $(this).data('exp_type');
            var exp_desc = $(this).data('exp_desc');
            var exp_date = $(this).data('exp_date');
            var amount = $(this).data('amount');
            var attachment = $(this).data('attachment');
            var so= $(this).data('so');
            var r=new Array();
            if (so.toString().indexOf(',')>-1)
            { 
                var r=so.split(',');
            }else{
                r[0]=so.toString();
            }
            // ACTIVE PANE AND LINK
            $('.nav-tabs a[href="#update_epayment"]').tab('show');

            var  attachment="files/user/expense/"+attachment;
            $('#exp_edit_id').val(id);   
            $('#exp_desc').val(exp_desc); 
            $('#exp_date').val(exp_date); 
            $('#expense_amnt').val(amount); 
            $('#attachment1').attr("href",attachment);
            $.each(r,function(index,value)
            {
                // $("#so").find("option[value="+value+"]").prop("selected", "selected");
            $('#exp_so option[value='+value+']').attr('selected','selected').change();

            });

            //  $("#labour").find("option[value="+labour+"]").prop("selected", "selected");
            $('#exp_type option[value='+exp_type+']').attr('selected','selected').change();
        }
        

    });

    // From SO Validation
    var n =0;
    $("#add_expense").click(function(event) 
    {
        // alert('hi');
        var edit_id= $('#exp_edit_id').val();
        var exp_type = $('#exp_type').val();
        var exp_so= $('#exp_so').val();
        var exp_desc = $('#exp_desc').val();
        var exp_date= $('#exp_date').val();
        var attachment= $('#attachment').val();
        var expense_amnt = $('#expense_amnt').val();

        n=0;    
        if( $.trim(expense_amnt).length == 0 )
        {
            $('#eaerror').text('Please Enter Amount.');
            event.preventDefault();
        }else{
            $('#eaerror').text('');
            ++n;
        }

        if( $.trim(exp_date).length == 0 )
        {
            $('#edaerror').text('Please Enter date.');
            event.preventDefault();
        }else{
            $('#edaerror').text('');
            ++n;
        }
       
        if( $.trim(exp_desc).length == 0 )
        {
            $('#ederror').text('Please Enter Expense Description.');
            event.preventDefault();
        }else{
            $('#ederror').text('');
            ++n;
        }

        if( $.trim(exp_so).length == 0 )
        {
            $('#esoerror').text('Please Select SO.');
            event.preventDefault();
        }else{
            $('#esoerror').text('');
            ++n;
        }

        if( $.trim(exp_type).length == 0 )
        {
            $('#eterror').text('Please Select Expense Type.');
            event.preventDefault();
        }else{
            $('#eterror').text('');
            ++n;
        }

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
        if(n==5)
        {        
            var form_data = new FormData();
            form_data.append("exp_edit_id", $("#exp_edit_id").val());
            form_data.append("exp_type", $("#exp_type").val());
            form_data.append("exp_so", $("#exp_so").val());
            form_data.append("exp_desc", $("#exp_desc").val());
            form_data.append("exp_date", $("#exp_date").val());
            form_data.append("expense_amnt", $("#expense_amnt").val());

       
            // For File Encode
            var attachment1=$('#payment_encodedfile').val();
            var extension1=$('#payment_extension').val();
            form_data.append("attachment",attachment1);
            form_data.append("payment_extension",extension1);

            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('post_elabour_payment')}}",
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
                        $("#exp_desc").val();    
                        $("#exp_date").val('');
                        $("#expense_amnt").val('');          
                        $("#exp_so").empty();            

                        getLabourExpenses();

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
    $(document).on("click",'.exp_delI',function()
    {
        var id = $(this).data('id');
        // alert(id);
        $('#id').val(id);
        $('#type').val('deleteExpenses');
        // $('#delete_record_modal form').attr("action","delete_labour_payment/"+id);
        $('#delete_modal').modal('show');
    });

</script>
<script>
    $(document).ready(function(){
        var $body = $("body");
        $('#labour,#so').select2();
        $("#ttrandata").hide();
        $(".note").hide();
        $('#labour1,#labours,#oa_hit').select2({ 
            dropdownParent: $('#oaHistoryModal') 
        });
    });

    // For from date ,to date records
    $(document).on("click",'#tot_ftd_records',function()
    {           
               
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('transfer_labour_payment')}}",
            type :'get',
            data : {from_date:from_date,to_date:to_date},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                if (data.status==true) 
                {
                    $("#toDatatable").DataTable().destroy();
                    $("#ttrandata").show();

                    content ="";  

                    var i = 0;       
                    $("#labour").empty();            
                    $("#so").empty();    
                    var t_tranamount=0;    
                    $.each(data.data,function(index,row){
                        //date convert into dd/mm/yyyy
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0].substring(0), // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }

                        t_tranamount+=Number(row.amount);

                        if(row.p_date != null){
                            var payment_date = formatDate (row.p_date); // "18/01/10"
                        }else{
                            var payment_date = " - "
                        }
                        var d = new Date();
                        var current_date = d.getDate();
                            content +="<tr>";
                            content +="<td>"+ ++i +"</td>";
                            content +="<td>"+payment_date+"</td>";
                            content +="<td>"+row.labour_name+"</td>";
                            content +="<td>"+row.p_desc+"</td>";
                            content +="<td>"+row.amount+"</td>";
                            content +="<td>";
                                if(payment_date == $.datepicker.formatDate('dd-mm-yy', new Date())){
                                    content +="<a class='btn btn-outline-secondary btn-sm editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Technician' data-id='"+row.id+"' data-so='"+row.so_id+"' data-payment_date='"+row.p_date+"' data-p_desc='"+row.p_desc+"' data-amount='"+row.amount+"' data-labour='"+row.u_id+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm delI' rel='tooltip' data-bs-placement='top' title='Delete Technician' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                                }
                            content +="</td>";
                            content += "</tr>";

                    });
                    

                    $("#trpay_records").html(content); //For append html data
                    $('#toDatatable').dataTable();

                    //table footer
                    $("#t_tranamount").html(t_tranamount+".00");

                    //For labour
                    // $('#edit_labour').append("<option value='' class='text-muted' selected disabled>"+'Select '+"</option>");
                    $('#labour').append("<option value='' class='text-muted' selected disabled>"+'Select '+"</option>");

                    $.each(data.u_obj,function(index,row){
                        //For Add Material Modal
                        // $('#edit_labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                        $('#labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                    });

                    //For so
                    // $('#edit_so').append("<option value='' class='text-muted' selected disabled>"+'ALL'+"</option>");
                    $.each(data.s_obj,function(index,row){
                        //For Add Material Modal
                        // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                        $('#so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");

                    });
                    
                    // ACTIVE PANE AND LINK
                    $('.nav-tabs a[href="#tpayment_list"]').tab('show');
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

    getLabourPaymnet();
    function getLabourPaymnet(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('transfer_labour_payment')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data);
                $("#toDatatable").DataTable().destroy();
                $("#ttrandata").show();

                content ="";  

                var i = 0;       
                $("#labour").empty();            
                $("#so").empty();    
                var t_tranamount=0;    
                $.each(data.data,function(index,row){
                    //date convert into dd/mm/yyyy
                    function formatDate (input) {
                        var datePart = input.match(/\d+/g),
                        year = datePart[0].substring(0), // get only two digits
                        month = datePart[1], day = datePart[2];
                        return day+'-'+month+'-'+year;
                    }

                    t_tranamount+=Number(row.amount);

                    if(row.p_date != null){
                        var payment_date = formatDate (row.p_date); // "18/01/10"
                    }else{
                        var payment_date = " - "
                    }
                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+payment_date+"</td>";
                        content +="<td>"+row.so_number+"</td>";
                        content +="<td>"+row.labour_name+"</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.amount+"</td>";
                        content +="<td>";
                            if(payment_date == $.datepicker.formatDate('dd-mm-yy', new Date())){
                                content +="<a class='btn btn-outline-secondary btn-sm editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Technician' data-id='"+row.id+"' data-oth_id='"+row.oth_id+"' data-payment_date='"+row.p_date+"' data-p_desc='"+row.p_desc+"' data-amount='"+row.amount+"' data-labour='"+row.u_id+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm delI' rel='tooltip' data-bs-placement='top' title='Delete Technician' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                            }
                        content +="</td>";
                        content += "</tr>";

                });
                

                $("#trpay_records").html(content); //For append html data
                $('#toDatatable').dataTable();

                //table footer
                $("#t_tranamount").html(t_tranamount+".00");

                //For labour
                // $('#edit_labour').append("<option value='' class='text-muted' selected disabled>"+'Select '+"</option>");
                $('#labour').append("<option value='' class='text-muted' selected disabled>"+'Select '+"</option>");

                $.each(data.u_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                    if(row.lead_technician != data.a_id){
                        $('#labour').append("<option value='"+row.lead_technician+"' data-oth_id='"+row.oth_id+"'>"+row.name+" -  ("+row.so_number+")</option>");

                    }
                });



                $.each(data.s_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_so').append("<option value='"+row.id+"'>"+row.so_number+"</option>");
                    $('#so').append("<option value='"+row.oth_id+"'>"+row.so_number+"</option>");

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
                        $("#labour1 option[value='"+value+"']").attr('selected','selected').change();
                    });

                    $("#labours option[value='"+row.lead_technician+"']").attr('selected','selected').change(); 

                });
                
            }
        });
    }
    
    $('.nav-tabs a[href="#update_tpayment"]').click(function(){

        $('.tpayment_form')[0].reset()
        $('#edit_id').val('');
        $("#labour").empty();            
        $("#so").empty();
        $(".note").show();
        getLabourPaymnet();

    });

    //For set/unset select field 
    $('.nav-tabs a[href="#tpayment_list"]').click(function()
    {
        $("#labour").empty();            
        $("#so").empty();
        $(".note").hide();
        getLabourPaymnet();
    });


    //For Edit Operation
    $(document).on("click",'.editU',function()
    {
        var id = $(this).data('id');
        if(id !=""){

            var labour = $(this).data('labour');
            var pay_desc = $(this).data('p_desc');
            var payment_date = $(this).data('payment_date');
            var amount = $(this).data('amount');
            var so= $(this).data('oth_id');
            // var r=new Array();
            // if (so.toString().indexOf(',')>-1)
            // { 
            //     var r=so.split(',');
            // }else{
            //     r[0]=so.toString();
            // }
            // ACTIVE PANE AND LINK
            $('.nav-tabs a[href="#update_tpayment"]').tab('show');

            $('#edit_id').val(id);   
            $('#pay_desc').val(pay_desc); 
            $('#payment_date').val(payment_date); 
            $('#payment_amnt').val(amount); 

            // $.each(r,function(index,value)
            // {
            //     // $("#so").find("option[value="+value+"]").prop("selected", "selected");
            // $('#so option[value='+value+']').attr('selected','selected').change();

            // });

            $('#so option[value='+so+']').attr('selected','selected').change();

            //  $("#labour").find("option[value="+labour+"]").prop("selected", "selected");
            $('#labour option[value='+labour+']').attr('selected','selected').change();
        }
        

    });

    // From SO Validation
    var n =0;
    $("#add_labour_payment").click(function(event) 
    {
        // alert('hi');
        var labour = $('#labour').val();
        var so= $('#so').val();
        var pay_desc = $('#pay_desc').val();
        var payment_date= $('#payment_date').val();
        var payment_amnt = $('#payment_amnt').val();

        n=0;    
        if( $.trim(payment_amnt).length == 0 )
        {
            $('#paerror').text('Please Enter Amount.');
            event.preventDefault();
        }else{
            $('#paerror').text('');
            ++n;
        }

        if( $.trim(payment_date).length == 0 )
        {
            $('#pdaerror').text('Please Enter date.');
            event.preventDefault();
        }else{
            $('#pdaerror').text('');
            ++n;
        }
       
        if( $.trim(pay_desc).length == 0 )
        {
            $('#pderror').text('Please Enter Payment Description.');
            event.preventDefault();
        }else{
            $('#pderror').text('');
            ++n;
        }

        if( $.trim(so).length == 0 )
        {
            $('#soerror').text('Please Select SO.');
            event.preventDefault();
        }else{
            $('#soerror').text('');
            ++n;
        }

        if( $.trim(labour).length == 0 )
        {
            $('#lerror').text('Please Select Labour.');
            event.preventDefault();
        }else{
            $('#lerror').text('');
            ++n;
        }
    });

    // For Add transfer Labour Payment
    $(document).on("click",'#add_labour_payment',function()
    {           
        if(n==5)
        {        
            var edit_id= $('#edit_id').val();
            var labour = $('#labour').val();
            var oth_id = $('#labour').find('option:selected').data('oth_id'); 
            var so= $('#so').val();
            var pay_desc = $('#pay_desc').val();
            var payment_date= $('#payment_date').val();
            var payment_amnt = $('#payment_amnt').val();
            // alert(labour);
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('post_tlabour_payment')}}",
                type :'get',
                data : {so:so,pay_desc:pay_desc,payment_date:payment_date,payment_amnt:payment_amnt,labour:labour,edit_id:edit_id,oth_id:oth_id},
                async: false,
                cache: true,
                dataType: 'json',
                success:function(response){
                    console.log(response);

                    if (response.status==true) {  
                        $("#pay_desc").val();    
                        $("#payment_date").val('');
                        $("#payment_amnt").val('');
                        // $("#cp_ph_no").val();    
                        $("#labour").empty();            
                        $("#so").empty();            

                        getLabourPaymnet();

                        // ACTIVE PANE AND LINK
                        $('.nav-tabs a[href="#tpayment_list"]').tab('show');
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

    // delete transfer Labour Payment
    $(document).on("click",'.delI',function()
    {
        var id = $(this).data('id');
       
        // alert(id);
        $('#id').val(id);
        $('#type').val('transferPaymnet');

        // $('#delete_record_modal form').attr("action","delete_labour_payment/"+id);
        $('#delete_modal').modal('show');
    });

    // For delete user
    $(document).on("click",'#del_rec',function()
    {   

        var id= $('#id').val();
        var type= $('#type').val();
        // delete transfer payment
        if(type == 'transferPaymnet')
        {

            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('delete_tlabour_payment')}}",
                type :'get',
                data : {id:id},
                async: false,
                cache: true,
                dataType: 'json',
                success:function(response){
                    console.log(response);

                    if (response.status==true) {  

                        $("#id").val('');
                    
                        getLabourPaymnet();         //set data records

                        $("#delete_modal").modal("hide");

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

        //delete labour Expenses
        if(type == 'deleteExpenses')
        {

            $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('delete_expense')}}",
            type :'get',
            data : {id:id},
            async: false,
            cache: true,
            dataType: 'json',
            success:function(response){
                console.log(response);

                if (response.status==true) {  

                    $("#id").val('');
                   
                    getLabourExpenses();         //set data records

                    $("#delete_modal").modal("hide");

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