@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
@section('title',"OA Management | $title")
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
                           <h4 class="card-title mb-4">OA Management</h4>
                           <div class="ms-auto">
                                <!-- <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" data-bs-toggle="modal" data-bs-target="#addModal" style="margin-left: 10px;">
                                <i class="mdi mdi-plus font-size-11"></i> Add OA
                                </button>  -->
                            </div>
                        </div>
             
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                       
                      
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#so_list" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">OA Active List</span> 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#so_inactive_list" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">OA In-Active List</span> 
                                </a>
                            </li>
                            @if($roles==1)
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#update_so" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                        <span class="d-none d-sm-block">ADD / Update OA</span> 
                                    </a>
                                </li>
                            @endif
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="so_list" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="datatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">OA Number</th>
                                                @if($roles==1)
                                                    <th scope="col">Action</th>
                                                @endif
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Lead Technician</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Project Address</th>
                                                <th scope="col" style="width: 100px">Client Person</th>
                                                <th scope="col" style="width: 100px">Client Phone No</th>
                                            </tr>
                                        </thead>
                                        <tbody id="so_records">
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="so_inactive_list" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="inactive_datatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">OA Number</th>
                                                @if($roles==1)
                                                    <th scope="col">Action</th>
                                                @endif
                                                <th scope="col" style="width: 100px">Status</th>
                                                <th scope="col" style="width: 100px">Project Admin</th>
                                                <th scope="col" style="width: 100px">Client Name</th>
                                                <th scope="col" style="width: 100px">Project Name</th>
                                                <th scope="col" style="width: 100px">Project Address</th>
                                                <th scope="col" style="width: 100px">Client Person</th>
                                                <th scope="col" style="width: 100px">Client Phone No</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody id="so_inactive_records">
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="update_so" role="tabpanel">
                                {!! Form::open(['class'=>"form-horizontal oa_form",'enctype'=>'multipart/form-data','files' => 'true','id'=>'postOAForm']) !!}

                                    <input type="hidden" name="edit_id" id="edit_id" value="">
                                    <input type="hidden" name="role" id="role" value="{{$roles}}">
                                    <input type="hidden" name="oa_type" id="oa_type" value="normal">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-12 col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="labours" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Lead Technician Support<sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="labours" required name="labours">
                                                
                                                </select>
                                                <span class="text-danger error" id="lerror"></span>
                                               
                                            </div>
                                        </div>

                                        <div class="col-md-8 col-sm-12 col-lg-8 lerror_msg">
                                            <span class="text-danger error" id="lerror_msg"></span>
                                        </div>

                                        <div class="col-md-2 col-sm-12 col-lg-2 labour_change">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="so_number" placeholder="Enter SO Number" name="so_number" required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="10">
                                                <label for="so_number">OA Number<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="soerror"></span>

                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2 labour_change">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="client_name" placeholder="Enter Client Name" name="client_name" required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50">
                                                <label for="client_name">Client Name<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="cnerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2 labour_change">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="project_name" placeholder="Enter Project Name" name="project_name" required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50">
                                                <label for="project_name">Project Name<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="pnerror"></span>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2 col-sm-12 col-lg-2 labour_change">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="cp_name" placeholder="Enter CP Name" name="cp_name"required onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50">
                                                <label for="cp_name">CP Name<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="cpnerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12 col-lg-2 labour_change">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="cp_ph_no" placeholder="Enter CP Phone" name="cp_ph_no" required maxlength="10">
                                                <label for="cp_ph_no">CP Phone<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="cpperror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-sm-12 col-lg-5 col-sm-12 labour_change">
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" id="address" placeholder="Enter Address" required name="address" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                                                <label for="address">Project Address<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="aerror"></span>

                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-12 col-lg-7">
                                            <div class="form-group mb-3 labour_change">
                                                <label for="labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Support Technician<sup class="text-danger">*</sup></label>
                                                <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="labour" name="labour[]" placeholder="Support Technician">
                    
                                                </select>
                                                <span class="text-danger error" id="slerror"></span>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="d-sm-flex flex-wrap">
                                        <h4 class="card-title mb-4"></h4>
                                        <div class="ms-auto labour_change">
                                            <button type="button" class="btn btn-primary waves-effect waves-light submit_btn" id="add_so"><i class="bx font-size-16 align-middle me-2 add_so"></i>Add</button>
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
<!-- Remove TL Modal -->
<div id="remove_tl_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Remove Technician Leader</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            {!! Form::open(['class'=>"form-horizontal",'method'=>"post",'url'=>'remove_tl','id'=>'add_dn_form']) !!}

                <div class="modal-body">
                    <strong>Do you really wants to Remove this OA Technician Leader..? </strong>
                    <!-- <strong>Do you really want to Reset Password For this Account..? </strong> -->

                    <div class="form-group">
                        <div class="col-md-4">
                        <input type="hidden" id="oth_id" name="oth_id" class="form-control"/>
                        <input type="hidden" id="oth_status" name="oth_status" class="form-control"/>
                        <input type="hidden" id="oth_so_id" name="oth_so_id" class="form-control"/>

                        
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary text-white btn-sm " id="update_tl_rec"><i class="fe fe-check mr-2"></i>Update</button>
                </div>
            {!! Form::close() !!}
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
            $('#datatable').dataTable();    
            $('#inactive_datatable').dataTable(); 
            
           
        });
    </script>
@endpush

@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    $(document).ready(function(){
        var $body = $("body");
        $('#labour,#labours').select2();
        $(".labour_change").hide();     // on labour change 
        $(".lerror_msg").hide();     // on labour change check st
    });
    var $body = $("body");
   $( function(){
        $( "#addModal" ).draggable();
    });
    
    $('#labour').select2({
        dropdownParent: $('#addModal')
    });

    $('.nav-tabs a[href="#update_so"]').click(function(){
       
        $('#so_number').prop('disabled', false);
        $('#client_name').prop('disabled', false);
        $('#project_name').prop('disabled', false);

        $(".labour_change").hide();     // on labour change 
        $(".lerror_msg").hide();     // on labour change check status
        $('.oa_form')[0].reset()
        $('#edit_id').val('');
        $("#labour").empty();        
        $("#labours").empty();            

        getSO();
        

    });

    //For set/unset select field
    $('.nav-tabs a[href="#so_list"]').click(function()
    {
        $("#labour").empty();    
        $("#labours").empty();            
        getSO();
    });

    getSO();
    function getSO(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_so')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#datatable").DataTable().destroy();
                content ="";
                content1 ="";
                var i=j= 0;        
                $("#labour").empty();         
                $.each(data.data,function(index,row){
                    if(row.oa_type == 1 )
                    {
                        if(row.oth_status == 1 )
                        {
                            content +="<tr>";
                            content +="<td>"+ ++i  +"</td>";
                            content +="<td>"+row.so_number+"</td>";
                            if(data.roles == 1){
                                content +="<td>";
                                content +="<a class='btn btn-outline-secondary btn-sm editU' rel='tooltip' data-bs-placement='top' title='Edit OA' data-id='"+row.id+"' data-so_number='"+row.so_number+"' data-client_name='"+row.client_name+"' data-project_name='"+row.project_name+"' data-address='"+row.address+"' data-cp_name='"+row.cp_name+"' data-cp_ph_no='"+row.cp_ph_no+"' data-lead_technician='"+row.lead_technician+"' data-labour='"+row.labour+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm delI' rel='tooltip' data-bs-placement='top' title='Delete OA' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>";
                                
                                if(row.oth_status == 1){
                                    content += " <a class='btn btn-outline-secondary btn-sm removeTL'  rel='tooltip' data-bs-placement='top' title='Remove TL' data-oth_so_id='"+row.id+"' data-oth_id='"+row.oth_id+"' data-oth_status='"+row.oth_status+"' data-bs-toggle='modal'><i class='fas fa-ban'></i></a>";
                                }
                                content += "</td>";
                            }
                            if(row.oth_status == 1){
                                content +="<td><span class='badge badge-soft-success'>ACTIVE</span></td>";

                            }else{
                                content +="<td><span class='badge badge-soft-danger'>IN-ACTIVE</span></td>";
                            }
                            content +="<td>"+row.name+"</td>";
                            content +="<td>"+row.lead_technician_name+"</td>";
                            content +="<td>"+row.client_name+"</td>";
                            content +="<td>"+row.project_name+"</td>";
                            content +="<td>"+row.address+"</td>";
                            content +="<td>"+row.cp_name+"</td>";
                            content +="<td>"+row.cp_ph_no+"</td>";
                            content += "</tr>";

                        }
                        else{

                            content1 +="<tr>";
                            content1 +="<td>"+ ++j +"</td>";
                            content1 +="<td>"+row.so_number+"</td>";
                            
                            if(data.roles == 1){
                                content1 +="<td>";
                                content1 +="<a class='btn btn-outline-secondary btn-sm editU' rel='tooltip' data-bs-placement='top' title='Edit OA' data-id='"+row.id+"' data-so_number='"+row.so_number+"' data-client_name='"+row.client_name+"' data-project_name='"+row.project_name+"' data-address='"+row.address+"' data-cp_name='"+row.cp_name+"' data-cp_ph_no='"+row.cp_ph_no+"' data-lead_technician='"+row.lead_technician+"' data-labour='"+row.labour+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm delI' rel='tooltip' data-bs-placement='top' title='Delete OA' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>";
                                
                                if(row.oth_status == 1){
                                    content1 += " <a class='btn btn-outline-secondary btn-sm removeTL'  rel='tooltip' data-bs-placement='top' title='Remove TL' data-oth_so_id='"+row.id+"' data-oth_id='"+row.oth_id+"' data-oth_status='"+row.oth_status+"' data-bs-toggle='modal'><i class='fas fa-ban'></i></a>";
                                }
                                content1 += "</td>";
                            }
                            if(row.oth_status == 1){
                                content1 +="<td><span class='badge badge-soft-success'>ACTIVE</span></td>";

                            }else{
                                content1 +="<td><span class='badge badge-soft-danger'>IN-ACTIVE</span></td>";
                            }
                            content1 +="<td>"+row.name+"</td>";
                            content1 +="<td>"+row.client_name+"</td>";
                            content1 +="<td>"+row.project_name+"</td>";
                            content1 +="<td>"+row.address+"</td>";
                            content1 +="<td>"+row.cp_name+"</td>";
                            content1 +="<td>"+row.cp_ph_no+"</td>";
                            content1 += "</tr>";
                        }
                    }
                });
                
                $("#so_records").html(content); //For append html data
                $('#datatable').dataTable();

                $("#so_inactive_records").html(content1); //For append html data
                $('#inactive_datatable').dataTable();
                
                //For labour
                $('#labours').append("<option value='' class='text-muted' selected disabled>"+'Select'+"</option>");
                $('#labour').append("<option value='' class='text-muted' >"+'All'+"</option>");

                $.each(data.u_obj,function(index,row){
                    //For Add Material Modal
                    // $('#edit_labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                    $('#labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                    $('#labours').append("<option value='"+row.id+"'>"+row.name+"</option>");
                });
            }
        });
    }

    //Lead technician support on change fields
    $('#labours').change(function(e)
    {
        var id = $(this).val();
        var so_id= $('#edit_id').val();         //so_id
        
        // alert(id);
      
        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('check_tl_status')}}",
            type :'get',
            data : {id:id,so_id:so_id},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data);

                if(data.so_id == null){
                    if (data.count == 0) 
                    { 
                        $(".labour_change").show();     // on labour change 
                        $(".lerror_msg").hide();     // on labour change check status
                
                    }else{
                        $(".lerror_msg").show();     // on labour change check status
                        $(".labour_change").hide();     // on labour change 
                        $("#lerror_msg").html("This Technician Already Exist in <strong>"+data.oa_status+" - "+data.oa_number+"</strong> ,Please Select Another Technician For Further Process.");
                    }
                }else{
                    if (((data.count == 1) && (data.so_id == data.d_so_id)) || ((data.count == 0) && (data.d_so_id == 0)) ) 
                    { 
                        $(".labour_change").show();     // on labour change 
                        $(".lerror_msg").hide();     // on labour change check status
                
                    }else{
                        $(".lerror_msg").show();     // on labour change check status
                        $(".labour_change").hide();     // on labour change 
                        $("#lerror_msg").html("This Technician Already Exist in <strong>"+data.oa_status+" - "+data.oa_number+"</strong> ,Please Select Another Technician For Further Process.");
                    }
                }
                 
              
                //     $("#labour").empty();
                
                // //For labour
                // // $('#labours').append("<option value='' class='text-muted' selected disabled>"+'Select'+"</option>");
                // $('#labour').append("<option value='' class='text-muted' >"+'All'+"</option>");

                // $.each(data.u_obj,function(index,row){
                //     //For Add Material Modal
                //     // $('#edit_labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                //     $('#labour').append("<option value='"+row.id+"'>"+row.name+"</option>");
                //     // $('#labours').append("<option value='"+row.id+"'>"+row.name+"</option>");
                // });
            }
        });
  
    });

    $(document).on("click",'.editU',function()
    {
        var id = $(this).data('id');
       
        if(id !=""){
            // $("#labour").empty();        
            // $("#labours").empty();
            // getSO();
            var role= $('#role').val();
            if(role == 1){
                $('#so_number').prop('disabled', true);
                $('#client_name').prop('disabled', true);
                $('#project_name').prop('disabled', true);
            }
            
            var so_number = $(this).data('so_number');
            var client_name = $(this).data('client_name');
            var project_name = $(this).data('project_name');
            var address = $(this).data('address');
            var cp_name = $(this).data('cp_name');
            var cp_ph_no = $(this).data('cp_ph_no');
            var labour= $(this).data('labour');
            var lead_technician= $(this).data('lead_technician');
            
            // alert(lead_technician);
            var r=new Array();
            if (labour.toString().indexOf(',')>-1)
            { 
                var r=labour.split(',');
            }
            else
            {
                r[0]=labour.toString();
            }
            // ACTIVE PANE AND LINK
            $('.nav-tabs a[href="#update_so"]').tab('show');

            
            $('#edit_id').val(id);   
            $('#so_number').val(so_number); 
            $('#client_name').val(client_name); 
            $('#project_name').val(project_name); 
            $('#address').val(address); 
            $('#cp_name').val(cp_name); 
            $('#cp_ph_no').val(cp_ph_no);
            
            $.each(r,function(index,value)
            {
                $("#labour option[value='"+value+"']").attr('selected','selected').change();
            });

            if(lead_technician == 0){
                $(".lerror_msg").show();     // on labour change check status
                $(".labour_change").hide();     // on labour change 
                $("#lerror_msg").html("Please Select Technician Leader For this <strong>"+so_number+"</strong> For Further Operation.");
            }else{
                
                $("#labours option[value='"+lead_technician+"']").attr('selected','selected').change();

            }

        }

    });

    // From SO Validation
    var n =0;
    $("#add_so").click(function(event) 
    {
        // alert('hi');
        var so_number= $('#so_number').val();
        var client_name = $('#client_name').val();
        var project_name= $('#project_name').val();
        var address = $('#address').val();
        var cp_name = $('#cp_name').val();
        var cp_ph_no= $('#cp_ph_no').val();
        var labour = $('#labour').val();
        var labours = $('#labours').val();
        n=0;    

        if( $.trim(so_number).length == 0 )
        {
            $('#soerror').text('Please Enter SO Number');
            event.preventDefault();
        }else{
            $('#soerror').text('');
            ++n;
        }

        if( $.trim(client_name).length == 0 )
        {
            $('#cnerror').text('Please Enter Client Name.');
            event.preventDefault();
        }else{
            $('#cnerror').text('');
            ++n;
        }

        if( $.trim(project_name).length == 0 )
        {
            $('#pnerror').text('Please Enter Project Name.');
            event.preventDefault();
        }else{
            $('#pnerror').text('');
            ++n;
        }

        if( $.trim(address).length == 0 )
        {
            $('#aerror').text('Please Enter Address.');
            event.preventDefault();
        }else{
            $('#aerror').text('');
            ++n;
        }
       
        if( $.trim(cp_name).length == 0 )
        {
            $('#cpnerror').text('Please Enter CP Name.');
            event.preventDefault();
        }else{
            $('#cpnerror').text('');
            ++n;
        }

        if( $.trim(cp_ph_no).length == 0 )
        {
            $('#cpperror').text('Please Enter CP Ph number.');
            event.preventDefault();
        }else{
            $('#cpperror').text('');
            ++n;
        }

        if( $.trim(labour).length == 0 )
        {
            $('#slerror').text('Please Select Support Technicians.');
            event.preventDefault();
        }else{
            $('#slerror').text('');
            ++n;
        }

        if( $.trim(labours).length == 0 )
        {
            $('#lerror').text('Please Select Lead Technician.');
            event.preventDefault();
        }else{
            $('#lerror').text('');
            ++n;
        }
    });

    // For Add So
    $(document).on("click",'#add_so',function()
    {        
        // alert(n)   ;
        if(n==8)
        {        
           
            var edit_id= $('#edit_id').val();
            var so_number= $('#so_number').val();
            var oa_type= $('#oa_type').val();
            var client_name = $('#client_name').val();
            var project_name= $('#project_name').val();
            var address = $('#address').val();
            var cp_name = $('#cp_name').val();
            var cp_ph_no= $('#cp_ph_no').val();
            var labour = $('#labour').val();
            var labours = $('#labours').val();

            $(".add_so").addClass("bx-loader bx-spin");
            $("#add_so").prop('disabled', true);
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('post_oa')}}",
                type :'post',
                data : {so_number:so_number,client_name:client_name,project_name:project_name,address:address,cp_name:cp_name,cp_ph_no:cp_ph_no,labour:labour,labours:labours,edit_id:edit_id,oa_type:oa_type},
                async: false,
                cache: true,
                dataType: 'json',
                success:function(response){
                    console.log(response);

                    if (response.status==true) {  

                        $("#so_number").val('');
                        $("#client_name").val('');
                        $("#project_name").val();    
                        $("#address").val('');
                        $("#cp_name").val('');
                        $("#cp_ph_no").val();    
                        $("#labour").empty();    
                        $("#labours").empty();             

                        // $("#addModal").modal("hide");
                        getSO();

                        // ACTIVE PANE AND LINK
                        $('.nav-tabs a[href="#so_list"]').tab('show');
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
                },
                complete:function(data){
                    // Hide image container
                    $(".add_so").removeClass("bx-loader bx-spin");
                    $("#add_so").prop('disabled', false);
                   
                }
            });
        }
        
    });  

    // delete Product
    $(document).on("click",'.delI',function()
    {
        var id = $(this).data('id');
        $('#id').val(id);
        // $('#delete_record_modal form').attr("action","delete_so/"+id);
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
            url:"{{url('delete_so')}}",
            type :'get',
            data : {id:id},
            async: false,
            cache: true,
            dataType: 'json',
            success:function(response){
                console.log(response);

                if (response.status==true) {  

                    $("#id").val('');
                    getSO();
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


    //  Remove TL
    $(document).on("click",'.removeTL',function()
    {
        var oth_id = $(this).data('oth_id');
        var oth_status = $(this).data('oth_status');
        var oth_so_id = $(this).data('oth_so_id');

        
        $('#oth_id').val(oth_id);
        $('#oth_status').val(oth_status);
        $('#oth_so_id').val(oth_so_id);


        // $('#remove_tl_modal form').attr("action","remove_tl/"+id);
        $('#remove_tl_modal').modal('show');
    });
</script>
@endpush