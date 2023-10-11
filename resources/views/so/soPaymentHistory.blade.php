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
                           
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="so_list" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered dt-responsive nowrap w-100 table table-striped" id="datatable"> 
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 20px;">Sr.No</th>
                                                <th scope="col" style="white-space: normal;">OA Number</th>
                                                @if($roles!=3)
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
            $('#inactive_datatable').dataTable(); 
            
           
        });
    </script>
@endpush

@push('page_js')
{!! Html::script('assets/libs/select2/js/select2.min.js') !!}

<script>
    $(document).ready(function(){
        var $body = $("body");
    });

    //For set/unset select field
    $('.nav-tabs a[href="#so_list"]').click(function()
    {          
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
                            if(data.roles != 3){
                                content +="<td>";
                                content +="<a class='btn btn-outline-secondary btn-sm viewI'data-bs-toggle='tooltip' target='blank' data-bs-placement='top'title='View Payment History' data-id='"+row.id+"' href='../public/view_oa_payment_history/"+row.enc_id+"'><i class='fas fa-eye' style='color: #344bc6;'></i></a>";
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
                            
                            if(data.roles != 3){
                                content1 +="<td>";
                                content1 +="<a class='btn btn-outline-secondary btn-sm viewI'data-bs-toggle='tooltip' target='blank' data-bs-placement='top'title='View Payment History' data-id='"+row.id+"' href='../public/view_oa_payment_history/"+row.enc_id+"'><i class='fas fa-eye' style='color: #344bc6;'></i></a>";
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
               
            }
        });
    }
   
</script>
@endpush