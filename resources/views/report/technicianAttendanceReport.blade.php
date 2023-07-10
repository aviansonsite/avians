@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); ?>
@section('title',"Attendance Report | $title")
@push('page_css')
{!! Html::style('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') !!}
{!! Html::style('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') !!}
{!! Html::style('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') !!}
<!-- Icons Css -->
{!! Html::style('assets/css/icons.min.css') !!}
{!! Html::style('assets/libs/toastr/build/toastr.min.css') !!}
<!-- App Css-->
{!! Html::style('assets/css/app.min.css') !!}
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
                    <h4 class="mb-0 font-size-18"></h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Attendance Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Form Start --}}
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Report</h5>
                        {!! Form::open(['class'=>"form-horizontal",'id'=>"inv_report_form"]) !!}
                        <div class="row">
                            <?php $tdate=date("Y-m-d"); ?>
                            <div class="col-md-2 col-sm-12 col-lg-2">
                                <div class="form-floating mb-3">
                                    <input type="date" max="{{$tdate}}" class="form-control" id="from_date"  name="from_date" required placeholder="dd-mm-yyyy" value="{{$tdate}}">
                                    <label for="from_date">From Date <sup class="text-danger">*</sup></label>
                                    <small><span class="text-danger" id="fderror" style="font-size: 11px !important;"></span></small>
                                </div>
                            </div>
    
                            <div class="col-md-2 col-sm-12 col-lg-2">
                                <div class="form-floating mb-3">
                                    <input type="date" max="{{$tdate}}" class="form-control" id="to_date"  name="to_date" required placeholder="dd-mm-yyyy" 
                                    value="{{$tdate}}">
                                    <label for="to_date">To Date <sup class="text-danger">*</sup></label>
                                    <small><span class="text-danger" id="tderror" style="font-size: 11px !important;"></span></small>
                                </div>
                            </div>

                            <div class="col-md-5 col-sm-12 col-lg-5">
                                <div class="form-group mb-3">
                                    <label for="so" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Select OA<sup class="text-danger">*</sup></label>
                                    <select class="form-control select2" id="so" required name="so">
                                        <option value="" disabled selected>Select OA Name - ( Client Name , Project Name)</option>
                                        @foreach($s_obj as $s)
                                            <option value="{{$s->oth_id}}">{{$s->so_number}} - ( {{ $s->client_name}} , {{$s->project_name}} )</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error" id="soerror"></span>
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
                            <div class="d-sm-flex flex-wrap">
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light w-sm" id="att_records">Search</button>
                                </div>
                            </div>   
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>    
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap">     
                           <h4 class="card-title mb-4">Technician Attendance Report List  - &nbsp;</h4>
                           <div id="edr_title">
                                <h5 class="card-title mb-4">
                                    <strong>From Date : <span id="f_date"> - </span></strong>&nbsp;
                                    <strong>To Date : <span id="t_date"> - </span></strong>
                                </h5>
                           </div>
                             
                        </div>
                    
                        @include('common.alert')
                        <div id="alerts">
                        </div>
                        <div class="table-responsive">
                            <!-- <table class="table project-list-table table-nowrap align-middle table-borderless" id="datatable"> -->
                                <table id="f_rec" class="table table-bordered dt-responsive nowrap w-100 table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 20px;">Sr.No</th>
                                        <th scope="col" style="width: 100px">Date <br>(DD/MM/YY)</th>
                                        <th scope="col" style="width: 100px">OA Number</th>
                                        <th scope="col" style="width: 100px">Technician Name</th>
                                        <th scope="col" style="width: 100px">Punch In <br>(HH:MM:SS)</th>
                                        <th scope="col" style="width: 100px">Punch Out <br>(HH:MM:SS)</th>
                                        <th scope="col" style="width: 100px">Total Time <br>(HH:MM:SS)</th>
                                        <th scope="col" style="width: 100px">Technician Leader(TL)</th>
                                    </tr>
                                </thead>
                                <tbody id="att_table">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- sample modal content -->
 <div id="regulariseModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Regularise</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!! Form::open(['class'=>"form-horizontal"]) !!}
            <div class="modal-body">
                <div class="row">    
                    <input type="hidden" name="reg_id" id="reg_id" value="">
                    <input type="hidden" name="reg_tl_id" id="reg_tl_id" value="">
                    <input type="hidden" name="reg_from_date" id="reg_from_date" value="">
                    <input type="hidden" name="reg_to_date" id="reg_to_date" value="">
                    <input type="hidden" name="reg_so_id" id="reg_so_id" value="">
                    <input type="hidden" name="reg_labours" id="reg_labours" value="">
                    <input type="hidden" name="ptype" id="ptype" value="">

                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="form-group mb-3">
                            <label for="reg_remark">Enter Remark<sup class="text-danger">*</sup></label>
                            <textarea class="form-control" id="reg_remark" placeholder="Enter Remark" required name="reg_remark" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="100" rows="3"></textarea>
                            <span class="text-danger error" id="rmerror"></span>

                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 col-lg-6">
                        <div class="form-group mb-3">
                            <label for="reg_status" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Regularise Status<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="reg_status" required name="reg_status">
                            <option value="" disabled selected>Select</option>
                                <option value="Confirm">Confirm</option>
                            </select>
                            <span class="text-danger error" id="rserror"></span>
                        </div>
                    </div>

                </div>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="reg_record">Save</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
                            <label for="hist_labours" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Lead Technician Support<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="hist_labours" required name="hist_labours" disabled>
                            @foreach($u_obj as $u)
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
                            @foreach($s_obj as $s)
                                <option value="{{$s->oth_id}}">{{$s->so_number}}</option>
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
                            <label for="hist_labour" class="form-label" style="font-size: 11px;margin-bottom: 2px;">Support Technician<sup class="text-danger">*</sup></label>
                            <select class="select2 form-control" multiple="multiple" data-placeholder="Choose ..." id="hist_labour" name="hist_labour[]" placeholder="Support Technician" disabled>
                            @foreach($us_obj as $us)
                                <option value="{{$us->id}}">{{$us->name}}</option>
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
@push('page_js')
{!! Html::script('assets/libs/datatables.net/js/jquery.dataTables.min.js') !!}
{!! Html::script('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') !!}

{!! Html::script('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') !!}
{!! Html::script('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') !!}
{!! Html::script('assets/libs/jszip/jszip.min.js') !!}
{!! Html::script('assets/libs/pdfmake/build/pdfmake.js') !!}
{!! Html::script('assets/libs/pdfmake/build/vfs_fonts.js') !!}
{!! Html::script('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') !!}
{!! Html::script('assets/libs/datatables.net-buttons/js/buttons.print.min.js') !!}
{!! Html::script('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') !!}
{!! Html::script('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') !!}
{!! Html::script('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') !!}

{!! Html::script('assets/js/pages/datatables.init.js') !!}
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}
<script>


    $(document).ready(function ()
    {   
        var $body = $("body");
        $("#att_records").prop('disabled', true);
        $('#labours,#so').select2();
        $('#reg_status').select2({ dropdownParent: $('#regulariseModal') });

        $("#f_rec").DataTable({
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            buttons: [
                {extend: 'copy', className: 'btn-sm'},
                {extend: 'csv', 
                    title: 'Attendance Records', 
                    className: 'btn-sm',
                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                },
                {   header: true,
                    footer:true,
                    extend: 'excel',
                    title: 'Attendance Records', 
                    messageTop: function () {
                            var thead_name=$('#edr_title').text();
                            return thead_name;
                    
                    },
                    messageBottom:'The information in this table is copyright to Avians',
                    className: 'btn-sm',
                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                    
                },
                {header: true,
                    footer:true,
                    extend: 'pdf', 
                    title: 'Attendance Records', 
                    className: 'btn-sm',
                    messageTop: function () {
                            var thead_name=$('#edr_title').text();
                            return thead_name;
                    
                    },
                    messageBottom:'The information in this table is copyright to Avians',
                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                },
                {extend: 'print', className: 'btn-sm'}
            ]
        });

        $("#labours").prop('disabled', true);     //buyer div on load disabled
        $('#hist_labour,#hist_labours,#oa_hit').select2({ 
            dropdownParent: $('#oaHistoryModal') 
        });
    });      

    $('#so').change(function(e)
    {
        var so_id = $(this).val(); 
        // alert(so_id);
        $("#labours").empty();
        $("#att_records").prop('disabled', true);
        $.ajax({    
            url:"{{url('get-labour')}}",
            type :'get',
            data : {so_id:so_id},
            async: true,
            cache: true,
            dataType: 'json',
            success: function(response) 
            {
                console.log(response);

                //For Full Finish Invoice
                $('#labours').append("<option value='all' class='text-muted' selected disabled>"+'Please Select'+"</option>");

                $.each(response.data,function(index,row){

                    //For Full Finish Invoice
                    $('#labours').append("<option value='"+row.id+"'>"+row.name+"</option>");
                                    
                });
            },
            complete:function(data){
                // For Button Loader
                $("#att_records").prop('disabled', false); 
                $("#labours").prop('disabled', false);
                
            }
        });


    });

    // From Validation
    var n =0;
    $("#att_records").click(function(event) 
    {
        // alert('hi');
        var from_date= $('#from_date').val();
        var to_date = $('#to_date').val();
        var labours = $('#labours').val();

        n=0;               
        if( $.trim(from_date).length == 0 )
        {
            $('#fderror').text('Please Enter date.');
            event.preventDefault();
        }else{
            $('#fderror').text('');
            ++n;
        }

        if( $.trim(to_date).length == 0 )
        {
            $('#tderror').text('Please Enter date.');
            event.preventDefault();
        }else{
            $('#tderror').text('');
            ++n;
        }

        if( $.trim(labours).length == 0 )
        {
            $('#lerror').text('Please Select Technician.');
            event.preventDefault();
        }else{
            $('#lerror').text('');
            ++n;
        }
    });

    //For technician attendance Records
    $(document).on("click", "#att_records", function ()
    {   
        if (n==3) {

            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var so_id = $('#so').val();
            var labours = $('#labours').val();

            $('#reg_from_date').val(from_date);   
            $('#reg_to_date').val(to_date); 
            $('#reg_so_id').val(so_id); 
            $('#reg_labours').val(labours); 

            // alert(so_id);
            //date convert into dd/mm/yyyy
            function formatDate (input) {
            var datePart = input.match(/\d+/g),
            year = datePart[0].substring(0), // get only two digits
            month = datePart[1], day = datePart[2];
            return day+'-'+month+'-'+year;
            }

            var from_date1 = formatDate (from_date); // "18/01/10"
            var to_date1 = formatDate (to_date); // "18/01/10"

            $("#f_date").html(from_date1);
            $("#t_date").html(to_date1);

            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('tech-att-record')}}",
                type :'get',
                data : {from_date:from_date,to_date:to_date,labours:labours,so_id:so_id},
                async: false,
                cache: true,
                dataType: 'json',
                success:function(data){
                    console.log(data);

                    if (data.status==true) {
                        $("#f_rec").DataTable().destroy();  //For using 
                        
                        content ="";
                        var i = 0;

                        $.each(data.data,function(index,row)
                        {
                            //date convert into dd/mm/yyyy
                            function formatDate (input) {
                                var datePart = input.match(/\d+/g),
                                year = datePart[0].substring(0), // get only two digits
                                month = datePart[1], day = datePart[2];
                                return day+'-'+month+'-'+year;
                            }
                        
                           

                            var d = new Date();

                            var current_date = d.getDate();

                            if(row.pin_date != null){
                                var pin_date = formatDate (row.pin_date); // "18/01/10"
                                content +="<tr>";
                                content +="<td>"+ ++i +"</td>";
                                content +="<td>"+pin_date+"</td>";
                                content +="<td>";
                                $.each(row.s_obj,function(index,row)
                                {   
                                    content += row.so_number+"<br> <span class='badge badge-soft-primary oa_hist'data-id='"+row.id+"' data-so_number='"+row.so_number+"' data-client_name='"+row.client_name+"' data-project_name='"+row.project_name+"' data-address='"+row.address+"' data-cp_name='"+row.cp_name+"' data-cp_ph_no='"+row.cp_ph_no+"' data-lead_technician='"+row.lead_technician+"' data-labour='"+row.labour+"' data-bs-toggle='modal'>OA History</span>";
                                });
                                content +="</td>";
                                content +="<td>"+row.technician_name+"</td>";
                                content +="<td class='pinhistry' data-pin_oth_id='"+row.pin_oth_id+"' data-pin_u_id='"+row.pin_u_id+"' data-pin_remark='"+row.pin_remark+"' data-pin_latitude='"+row.pin_latitude+"' data-pin_date='"+row.pin_date+"' data-pin_longitude='"+row.pin_longitude+"' data-pin_img='"+row.pin_img+"'>"+row.pin_time+"</td>";

                                if(row.created_at == row.updated_at){ 
                                    content +="<td><span class='badge badge-soft-danger regularise_modal' data-id='"+row.id+"' data-tl_id='"+row.a_id+"' data-ptype='pout_record'>Regularise</span></td>";

                                }else{
                                    content +="<td class='pouthistry' data-pout_oth_id='"+row.pout_oth_id+"' data-pout_u_id='"+row.pout_u_id+"' data-pout_remark='"+row.pout_remark+"' data-pout_work_desc='"+row.pout_work_desc+"' data-pout_latitude='"+row.pout_latitude+"' data-pout_date='"+row.pout_date+"' data-pout_longitude='"+row.pout_longitude+"' data-pout_img='"+row.pout_img+"'>"+row.pout_time+"</td>";
                                }
                                        
                                content +="<td>"+row.totalDuration+"</td>";
                                content +="<td>"+row.tl_name+"</td>";
                                content += "</tr>";

                            }else{

                                if(row.pin_date != null){
                                    var pin_date = formatDate (row.pin_date); // "18/01/10"
                                }else{
                                    var pout_date = formatDate (row.pout_date); // "18/01/10"
                                }
                                content +="<tr>";
                                content +="<td>"+ ++i +"</td>";
                                content +="<td>"+pout_date+"</td>";
                                content +="<td>";
                                $.each(row.s_obj,function(index,row)
                                {   
                                    content += row.so_number+"<br> <span class='badge badge-soft-primary oa_hist'data-id='"+row.id+"' data-so_number='"+row.so_number+"' data-client_name='"+row.client_name+"' data-project_name='"+row.project_name+"' data-address='"+row.address+"' data-cp_name='"+row.cp_name+"' data-cp_ph_no='"+row.cp_ph_no+"' data-lead_technician='"+row.lead_technician+"' data-labour='"+row.labour+"' data-bs-toggle='modal'>OA History</span>";
                                });
                                content +="</td>";
                                content +="<td>"+row.technician_name+"</td>";

                                if(row.created_at == row.updated_at){ 
                                    content +="<td><span class='badge badge-soft-danger regularise_modal' data-id='"+row.id+"' data-tl_id='"+row.a_id+"' data-ptype='pin_record'>Regularise</span></td>";

                                }else{

                                    content +="<td class='pinhistry' data-pin_oth_id='"+row.pin_oth_id+"' data-pin_u_id='"+row.pin_u_id+"' data-pin_remark='"+row.pin_remark+"' data-pin_latitude='"+row.pin_latitude+"' data-pin_date='"+row.pin_date+"' data-pin_longitude='"+row.pin_longitude+"' data-pin_img='"+row.pin_img+"'>"+row.pin_time+"</td>";

                                }

                                content +="<td class='pouthistry' data-pout_oth_id='"+row.pout_oth_id+"' data-pout_u_id='"+row.pout_u_id+"' data-pout_remark='"+row.pout_remark+"' data-pout_work_desc='"+row.pout_work_desc+"' data-pout_latitude='"+row.pout_latitude+"' data-pout_date='"+row.pout_date+"' data-pout_longitude='"+row.pout_longitude+"' data-pout_img='"+row.pout_img+"'>"+row.pout_time
                                        
                                content +="<td>"+row.totalDuration+"</td>";
                                content +="<td>"+row.tl_name+"</td>";
                                content += "</tr>";
                            }
                                
                        });

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
                                $("#hist_labour option[value='"+value+"']").attr('selected','selected').change();
                            });

                            $("#hist_labours option[value='"+row.lead_technician+"']").attr('selected','selected').change(); 
                        });     

                        $("#att_table").html(content); //For append html data

                        $("#f_rec").DataTable({
                            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
                            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            buttons: [
                                {extend: 'copy', className: 'btn-sm'},
                                {extend: 'csv', 
                                    title: 'Attendance Records', 
                                    className: 'btn-sm',
                                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                                },
                                {   header: true,
                                    footer:true,
                                    extend: 'excel',
                                    title: 'Attendance Records', 
                                    messageTop: function () {
                                            var thead_name=$('#edr_title').text();
                                            return thead_name;
                                    
                                    },
                                    messageBottom:'The information in this table is copyright to Avians',
                                    className: 'btn-sm',
                                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                                    
                                },
                                {header: true,
                                    footer:true,
                                    extend: 'pdf', 
                                    title: 'Attendance Records', 
                                    className: 'btn-sm',
                                    messageTop: function () {
                                            var thead_name=$('#edr_title').text();
                                            return thead_name;
                                    
                                    },
                                    messageBottom:'The information in this table is copyright to Avians',
                                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                                },
                                {extend: 'print', className: 'btn-sm'}
                            ]
                        });

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
        }
    });  

    //For OA history modal
    $(document).on("click",'.oa_hist',function()
    {   
        var id = $(this).data('id');
        // var so_number = $(this).data('so_number');
        var client_name = $(this).data('client_name');
        var project_name = $(this).data('project_name');
        var address = $(this).data('address');
        var cp_name = $(this).data('cp_name');
        var cp_ph_no = $(this).data('cp_ph_no');
        var labour= $(this).data('labour');
        var lead_technician= $(this).data('lead_technician');
        
        // alert(labour);
        var r=new Array();
        if (labour.toString().indexOf(',')>-1)
        { 
            var r=labour.split(',');
        }
        else
        {
            r[0]=labour.toString();
        }


        // $('#oa_hit').val(id);   
        // $('#oa_hit').val(so_number); 
        $('#client_name').val(client_name); 
        $('#project_name').val(project_name); 
        $('#address').val(address); 
        $('#cp_name').val(cp_name); 
        $('#cp_ph_no').val(cp_ph_no);
        
        $("#oa_hit option[value='"+id+"']").attr('selected','selected').change();
        $.each(r,function(index,value)
        {
            $("#hist_labour option[value='"+value+"']").attr('selected','selected').change();
        });

        $("#hist_labours option[value='"+lead_technician+"']").attr('selected','selected').change();

        $('#oaHistoryModal').modal('show');

    });

    //For regularise modal
    $(document).on("click",'.regularise_modal',function()
    {   
        var id = $(this).data('id');
        var tl_id = $(this).data('tl_id');
        var ptype = $(this).data('ptype');

        // alert (id);
        $('#reg_id').val(id);   
        $('#reg_tl_id').val(tl_id);   
        $('#ptype').val(ptype);   
        
        $('#regulariseModal').modal('show');

    });


    // Regularise form Validation
    var m =0;
    $("#reg_record").click(function(event) 
    {
        // alert('hi');
        var reg_remark= $('#reg_remark').val();
        var reg_status = $('#reg_status').val();
        n=0;               
        if( $.trim(reg_remark).length == 0 )
        {
            $('#rmerror').text('Please Enter Remark.');
            event.preventDefault();
        }else{
            $('#rmerror').text('');
            ++m;
        }

        if( $.trim(reg_status).length == 0 )
        {
            $('#rserror').text('Please Select Status.');
            event.preventDefault();
        }else{
            $('#rserror').text('');
            ++m;
        }

    });


    //For reg-Records
    $(document).on("click", "#reg_record", function ()
    {   
        if (m==2) {
            var reg_tl_id = $('#reg_tl_id').val();
            var reg_id = $('#reg_id').val();
            var from_date = $('#reg_from_date').val();
            var to_date = $('#reg_to_date').val();
            var so_id = $('#reg_so_id').val();
            var labours = $('#reg_labours').val();
            var reg_remark= $('#reg_remark').val();
            var reg_status = $('#reg_status').val();
            var ptype = $('#ptype').val();
            // alert(reg_tl_id);
            $.ajax({
                headers:{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{url('regularise-attendance')}}",
                type :'get',
                data : {from_date:from_date,to_date:to_date,labours:labours,so_id:so_id,reg_remark:reg_remark,reg_status:reg_status,reg_id:reg_id,reg_tl_id:reg_tl_id,ptype:ptype},
                async: false,
                cache: true,
                dataType: 'json',
                success:function(data){
                    console.log(data);

                    if (data.status==true) {
                        $("#f_rec").DataTable().destroy();  //For using 
                        $("#reg_remark").val('');
                        $("#reg_status").val("").trigger("change");      // for when use select options are not dynamically print 
                        content ="";
                        var i = 0;

                        $.each(data.data,function(index,row)
                        {
                            //date convert into dd/mm/yyyy
                            function formatDate (input) {
                                var datePart = input.match(/\d+/g),
                                year = datePart[0].substring(0), // get only two digits
                                month = datePart[1], day = datePart[2];
                                return day+'-'+month+'-'+year;
                            }
                        
                            if(row.pin_date != null){
                                var pin_date = formatDate (row.pin_date); // "18/01/10"
                                content +="<tr>";
                                content +="<td>"+ ++i +"</td>";
                                content +="<td>"+pin_date+"</td>";
                                content +="<td>";
                                $.each(row.s_obj,function(index,row)
                                {   
                                    content += row.so_number;
                                });
                                content +="<br> <span class='badge badge-soft-primary' data-id='"+row.id+"' data-tl_id='"+row.a_id+"'>OA History</span></td>";
                                content +="<td>"+row.technician_name+"</td>";
                                content +="<td class='pinhistry' data-pin_oth_id='"+row.pin_oth_id+"' data-pin_u_id='"+row.pin_u_id+"' data-pin_remark='"+row.pin_remark+"' data-pin_latitude='"+row.pin_latitude+"' data-pin_date='"+row.pin_date+"' data-pin_longitude='"+row.pin_longitude+"' data-pin_img='"+row.pin_img+"'>"+row.pin_time+"</td>";

                                if(row.created_at == row.updated_at){ 
                                    content +="<td><span class='badge badge-soft-danger regularise_modal' data-id='"+row.id+"' data-tl_id='"+row.a_id+"' data-ptype='pout_record'>Regularise</span></td>";

                                }else{
                                    content +="<td class='pouthistry' data-pout_oth_id='"+row.pout_oth_id+"' data-pout_u_id='"+row.pout_u_id+"' data-pout_remark='"+row.pout_remark+"' data-pout_work_desc='"+row.pout_work_desc+"' data-pout_latitude='"+row.pout_latitude+"' data-pout_date='"+row.pout_date+"' data-pout_longitude='"+row.pout_longitude+"' data-pout_img='"+row.pout_img+"'>"+row.pout_time+"</td>";
                                }
                                        
                                content +="<td>"+row.totalDuration+"</td>";
                                content +="<td>"+row.tl_name+"</td>";
                                content += "</tr>";

                            }else{

                                if(row.pin_date != null){
                                    var pin_date = formatDate (row.pin_date); // "18/01/10"
                                }else{
                                    var pout_date = formatDate (row.pout_date); // "18/01/10"
                                }
                                content +="<tr>";
                                content +="<td>"+ ++i +"</td>";
                                content +="<td>"+pout_date+"</td>";
                                content +="<td>";
                                $.each(row.s_obj,function(index,row)
                                {   
                                    content += row.so_number+",";
                                });
                                content +="</td>";
                                content +="<td>"+row.technician_name+"</td>";

                                if(row.created_at == row.updated_at){ 
                                    content +="<td><span class='badge badge-soft-danger regularise_modal' data-id='"+row.id+"' data-tl_id='"+row.a_id+"' data-ptype='pin_record'>Regularise</span></td>";

                                }else{

                                    content +="<td class='pinhistry' data-pin_oth_id='"+row.pin_oth_id+"' data-pin_u_id='"+row.pin_u_id+"' data-pin_remark='"+row.pin_remark+"' data-pin_latitude='"+row.pin_latitude+"' data-pin_date='"+row.pin_date+"' data-pin_longitude='"+row.pin_longitude+"' data-pin_img='"+row.pin_img+"'>"+row.pin_time+"</td>";

                                }

                                content +="<td class='pouthistry' data-pout_oth_id='"+row.pout_oth_id+"' data-pout_u_id='"+row.pout_u_id+"' data-pout_remark='"+row.pout_remark+"' data-pout_work_desc='"+row.pout_work_desc+"' data-pout_latitude='"+row.pout_latitude+"' data-pout_date='"+row.pout_date+"' data-pout_longitude='"+row.pout_longitude+"' data-pout_img='"+row.pout_img+"'>"+row.pout_time
                                        
                                content +="<td>"+row.totalDuration+"</td>";
                                content +="<td>"+row.tl_name+"</td>";
                                content += "</tr>";
                            }

                        });

                            

                        $("#att_table").html(content); //For append html data

                        $("#f_rec").DataTable({
                            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
                            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            buttons: [
                                {extend: 'copy', className: 'btn-sm'},
                                {extend: 'csv', 
                                    title: 'Attendance Records', 
                                    className: 'btn-sm',
                                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                                },
                                {   header: true,
                                    footer:true,
                                    extend: 'excel',
                                    title: 'Attendance Records', 
                                    messageTop: function () {
                                            var thead_name=$('#edr_title').text();
                                            return thead_name;
                                    
                                    },
                                    messageBottom:'The information in this table is copyright to Avians',
                                    className: 'btn-sm',
                                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                                    
                                },
                                {header: true,
                                    footer:true,
                                    extend: 'pdf', 
                                    title: 'Attendance Records', 
                                    className: 'btn-sm',
                                    messageTop: function () {
                                            var thead_name=$('#edr_title').text();
                                            return thead_name;
                                    
                                    },
                                    messageBottom:'The information in this table is copyright to Avians',
                                    exportOptions: {columns: [0,1,2,3,4,5,6,7]}
                                },
                                {extend: 'print', className: 'btn-sm'}
                            ]
                        });
                        $("#regulariseModal").modal("hide");
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
        }
    });  

</script>
@endpush