@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); 
    $roles=Session::get('ROLES');
    $role=explode(',',$roles);
    $count=count($role);
?>
@section('title',"Technician Payment Management | $title")
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
                           <h4 class="card-title mb-4">Technician Payment Management</h4>
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
                                <a class="nav-link active" data-bs-toggle="tab" href="#lpayment_list" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Technician Payment List</span> 
                                </a>
                            </li>
                            @if($roles==2)
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#update_lpayment" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                        <span class="d-none d-sm-block">ADD / Update Technician Payment</span> 
                                    </a>
                                </li>
                            @endif
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="lpayment_list" role="tabpanel">
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
                                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="tech_pay_ftd_records">Search</button>
                                        </div>
                                    </div>
                                {!! Form::close() !!} 
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="datatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">Payment Date</th>
                                                <th scope="col" style="width: 100px">Technician Name</th>
                                                <th scope="col" style="width: 100px">Description</th>
                                                <th scope="col" style="width: 100px">Amount<br>(In Rs.)</th>
                                                
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="so_records">
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
         
                            <div class="tab-pane" id="update_lpayment" role="tabpanel">
                                {!! Form::open(['class'=>"form-horizontal payment_form",'enctype'=>'multipart/form-data','files' => 'true' ,'id'=>'postPaymentform']) !!}
                                    <input type="hidden" name="edit_id" id="edit_id" value="">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-12 col-lg-3">
                                            <div class="form-group mb-3">
                                                <label for="labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select Technician <sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="labour" required name="labour">
                                                    <option value="" disabled selected>Select</option>
                                                    @foreach($u_obj as $u)
                                                        <option value="{{$u->id}}">{{$u->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error" id="lerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12 col-lg-3">
                                            <div class="form-group mb-3">
                                                <label for="so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                                                <select class="form-control select2" id="so" required name="so">
                                                    <option value="" disabled selected>Select</option>
                                                    @foreach($s_obj as $s)
                                                        <option value="{{$s->id}}">{{$s->so_number}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error" id="soerror"></span>
                                            </div>
                                        </div>
                                        
                                        <?php $tdate=date("Y-m-d");?>
                                        <div class="col-md-3 col-sm-12 col-lg-3">
                                            <div class="form-floating mb-3">
                                                <input type="date" class="form-control" id="payment_date" placeholder="Payment Date" name="payment_date" required max="{{$tdate}}" value="{{$tdate}}">
                                                <label for="payment_date">Payment Date<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="pdaerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12 col-lg-3">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="payment_amnt" placeholder="Payment Amount" name="payment_amnt" maxlength="10" required>
                                                <label for="payment_amnt">Amount (In Rs.)<sup class="text-danger">*</sup></label>
                                                <span class="text-danger error" id="paerror"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" id="pay_desc" placeholder="Enter Payment Description" required name="pay_desc" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100"></textarea>
                                                <label for="pay_desc">Payment Description</label>
                                                <span class="text-danger error" id="pderror"></span>

                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-primary waves-effect waves-light" id="add_labour_payment">Submit</button>
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
        $('#labour,#so,#labours').select2();
       
    });
    var $body = $("body");

     // For from date ,to date records
     $(document).on("click",'#tech_pay_ftd_records',function()
    {           
               
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var labours = $('#labours').val();

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_labour_payment')}}",
            type :'get',
            data : {from_date:from_date,to_date:to_date,labours:labours},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#datatable").DataTable().destroy();
                content ="";
                var i = 0;       
                $("#labour").empty();            
                $("#so").empty();        
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
                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+payment_date+"</td>";
                        content +="<td>"+row.labour_name+"</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.payment_amnt+"</td>";

                        content +="<td>";
                        if((payment_date == $.datepicker.formatDate('dd-mm-yy', new Date())) && (data.role ==2) ){
                                content +="<a class='btn btn-outline-secondary btn-sm editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Technician' data-id='"+row.id+"' data-so='"+row.so_id+"' data-payment_date='"+row.payment_date+"' data-p_desc='"+row.p_desc+"' data-payment_amnt='"+row.payment_amnt+"' data-labour='"+row.u_id+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm delI' rel='tooltip' data-bs-placement='top' title='Delete Technician' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                            }
                            
                        content +="</td>";
                        content += "</tr>";
                });
                

                $("#so_records").html(content); //For append html data
                $('#datatable').dataTable();

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
            }
        });
       
    });

    getLabourPaymnet();
    function getLabourPaymnet(){

        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('get_labour_payment')}}",
            type :'get',
            data : {},
            cache: false,
            dataType: 'json',                 
            success:function(data){
                console.log(data.data);
                $("#datatable").DataTable().destroy();
                content ="";
                var i = 0;       
                $("#labour").empty();            
                $("#so").empty();        
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
                    var d = new Date();
                    var current_date = d.getDate();
                        content +="<tr>";
                        content +="<td>"+ ++i +"</td>";
                        content +="<td>"+payment_date+"</td>";
                        content +="<td>"+row.labour_name+"</td>";
                        content +="<td>"+row.p_desc+"</td>";
                        content +="<td>"+row.payment_amnt+"</td>";
                        content +="<td>";

                        if((payment_date == $.datepicker.formatDate('dd-mm-yy', new Date())) && (data.role ==2) ){
                            content +="<a class='btn btn-outline-secondary btn-sm editU' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Technician' data-id='"+row.id+"' data-so='"+row.so_id+"' data-payment_date='"+row.payment_date+"' data-p_desc='"+row.p_desc+"' data-payment_amnt='"+row.payment_amnt+"' data-labour='"+row.u_id+"' data-bs-toggle='modal'><i class='far fa-edit'></i></a> <button class='btn btn-outline-secondary btn-sm delI' rel='tooltip' data-bs-placement='top' title='Delete Technician' data-bs-toggle='modal' data-id='"+row.id+"'><i class='fas fa-trash-alt'></i></button>"
                        }
                        
                        content +="</td>";
                        content += "</tr>";
                });
                

                $("#so_records").html(content); //For append html data
                $('#datatable').dataTable();

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
            }
        });
    }

    $('.nav-tabs a[href="#update_lpayment"]').click(function(){

        $('.payment_form')[0].reset()
        $('#edit_id').val('');
        $("#labour").empty();            
        $("#so").empty();
        getLabourPaymnet();

    });

    //For set/unset select field
    $('.nav-tabs a[href="#lpayment_list"]').click(function()
    {
        $("#labour").empty();            
        $("#so").empty();
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

    // For Add Labour Payment
    $(document).on("click",'#add_labour_payment',function()
    {           
        if(n==5)
        {        
            var edit_id= $('#edit_id').val();
            var labour = $('#labour').val();
            var so= $('#so').val();
            var pay_desc = $('#pay_desc').val();
            var payment_date= $('#payment_date').val();
            var payment_amnt = $('#payment_amnt').val();

            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('post_labour_payment')}}",
                type :'get',
                data : {so:so,pay_desc:pay_desc,payment_date:payment_date,payment_amnt:payment_amnt,labour:labour,edit_id:edit_id},
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
                        $('.nav-tabs a[href="#lpayment_list"]').tab('show');
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

    // For edit So
    $(document).on("click",'#edit_labour_payment',function()
    {           
              
        var edit_id= $('#edit_id').val();
        var labour = $('#edit_labour').val();
        var so= $('#edit_so').val();
        var pay_desc = $('#edit_pay_desc').val();
        var payment_date= $('#edit_payment_date').val();
        var payment_amnt = $('#edit_payment_amnt').val();
        $.ajax({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{url('edit-labour-payment')}}",
            type :'get',
            data : {edit_id:edit_id,labour:labour,so:so,pay_desc:pay_desc,payment_date:payment_date,payment_amnt:payment_amnt},
            async: false,
            cache: true,
            dataType: 'json',
            success:function(response){
                console.log(response);

                if (response.status==true) {  

                    $("#edit_pay_desc").val('');
                    $("#edit_payment_date").val('');
                    $("#edit_payment_amnt").val();    
                    $("#edit_labour").empty();            
                    $("#edit_so").empty();            
                    
                    getLabourPaymnet();


                    $('.nav-tabs a[href="#lpayment_list"]').tab('show');

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

    // delete Product
    $(document).on("click",'.delI',function()
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
            url:"{{url('delete_labour_payment')}}",
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
@endpush