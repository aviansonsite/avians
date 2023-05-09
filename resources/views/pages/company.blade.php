@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); ?>
@section('title',"Company Profile | $title")
@push('page_css')
<style>
    .form-floating>.form-control, .form-floating>.form-select {
        height: calc(2.8rem + 1px) !important;
        padding: 1rem .75rem;
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
                        <h4 class="mb-sm-0 font-size-18">Company Profile</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Company Profile</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-4">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-3">
                                        <h5 class="text-primary">Welcome !</h5>
                                        <p>Company's Profile</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{asset('assets/images/profile-img.png')}}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <?php 
                            $logo=$u_obj[0]->logo;
                            $pan_file=$u_obj[0]->pan_file; 
                            $gst_file=$u_obj[0]->gst_file;
                            $iso_file=$u_obj[0]->iso_file;
                        ?>   
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <img src='{{asset("files/company/$logo")}}' alt="" class="img-thumbnail">
                                    </div>
                                
                                    <div class="pt-2">

                                        <div class="row">
                                            <div class="col-12">
                                                <h5 class="font-size-15">{{$u_obj[0]->company_name}}</h5>
                                                
                                                    
                                                <div class="font-size-12"><strong> Mob. </strong>: <a href="tel:{{$u_obj[0]->primary_mobile}}">{{$u_obj[0]->primary_mobile}}</a> /<a href="tel:{{$u_obj[0]->alternate_mobile}}">{{$u_obj[0]->alternate_mobile}}</a></div><br>

                                                <div class="font-size-12"><strong>Email</strong> : <br><a href="mailto:{{$u_obj[0]->company_email}}">{{$u_obj[0]->company_email}} </a><br> 
                                                    <a href="mailto:{{$u_obj[0]->account_email}}">{{$u_obj[0]->account_email}}</a></div><br>


                                                <div class="font-size-12"><strong>Website</strong> : <br><a href="{{$u_obj[0]->website}}" target="_blank">{{$u_obj[0]->website}}</a></div><br>


                                                <div><a target="_blank" class="btn btn-sm btn-primary" href='{{asset("files/company/$logo")}}'>View Previous Logo</a></div><br>

                                                <div> <a target="_blank" class="btn btn-sm btn-primary" href='{{asset("files/company/$gst_file")}}'>View Previous GST File</a></div><br>
                                                
                                            </div>
                                        </div>
                                        
                                    </div>
                                    @include('common.alert')
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex flex-wrap">
                                <h4 class="card-title mb-4">Company Profile</h4>
                            </div>
                            {!! Form::open(['class'=>"form-horizontal row",'method'=>"post",'url'=>'edit_company','enctype'=>'multipart/form-data','files' => 'true' ,'id'=>'edit_company_form']) !!}
                                <input type="hidden" name="c_id" value="{{$u_obj[0]->id}}">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="cname" placeholder="Enter Company Name" value="{{$u_obj[0]->company_name}}" required name="company_name" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="50">
                                        <label for="cname">Company Name</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="website" placeholder="Enter Company Website" value="{{$u_obj[0]->website}}" required name="company_website">
                                        <label for="website">Company Website</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email1" placeholder="Enter Company Email" value="{{$u_obj[0]->company_email}}" required name="company_email">
                                        <label for="email1">Company Email</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="email2" placeholder="Enter Accounts Email" value="{{$u_obj[0]->account_email}}" required name="accounts_email">
                                        <label for="email2">Accounts Email</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="mobile1" placeholder="Enter Primary Mobile Number" value="{{$u_obj[0]->primary_mobile}}" required name="primary_mobile" pattern="[789]\d{9}">
                                        <label for="mobile1">Primary Mobile Number</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="mobile2" placeholder="Enter Alternate Mobile Number" value="{{$u_obj[0]->alternate_mobile}}" required name="alternate_mobile" pattern="[789]\d{9}">
                                        <label for="mobile2">Alternate Mobile Number</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="address" placeholder="Enter Address" required name="address" rows="2" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="200">{{$u_obj[0]->address}}</textarea>
                                        <label for="address">Address</label>
                                    </div>
                                </div>


                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="city" placeholder="Enter City" value="{{$u_obj[0]->city}}" required name="city" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);">
                                        <label for="city">City</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="state" placeholder="Enter State" value="{{$u_obj[0]->state}}" required name="state" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);">
                                        <label for="state">State</label>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="pincode" placeholder="Enter Pincode" value="{{$u_obj[0]->pincode}}" required name="pincode">
                                        <span id="pinerror"></span>
                                        <label for="pincode">Pincode</label>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="iec_code" placeholder="Enter IEC Code" value="{{$u_obj[0]->iec_code}}" required name="iec_code" pattern="\d*" maxlength="10">
                                        <label for="iec_code">IEC Code</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="file" class="form-control" id="logo" placeholder="Enter Logo"  name="logo">
                                        <label for="logo">Logo </label>
                                    </div>
                                    <span id="lerror"></span>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="gst_number" placeholder="Enter GST Number" value="{{$u_obj[0]->gst_number}}" required name="gst_number" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);" maxlength="15">
                                        <label for="gst_number">GST Number</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="file" class="form-control" id="gst_file" placeholder="Enter GST File" name="gst_file">
                                        <label for="gst_file">GST File</label>
                                    </div>
                                    <span id="gerror"></span>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="bike_pkm_rate" placeholder="Enter Bike Per KM Rate" value="{{$u_obj[0]->bike_pkm_rate}}" required name="bike_pkm_rate" maxlength="3">
                                        <span id="bpkmrerror"></span>
                                        <label for="bike_pkm_rate">Bike Per KM Rate</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="car_pkm_rate" placeholder="Enter Car Per KM Rate" value="{{$u_obj[0]->car_pkm_rate}}" required name="car_pkm_rate" maxlength="3">
                                        <span id="bpkmrerror"></span>
                                        <label for="car_pkm_rate">Car Per KM Rate</label>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary w-md">Update</button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            
        </div> <!-- container-fluid -->
    </div>
@stop
@push('page_js')
<script>
    $('#edit_company_form').submit(function(e){
        var ext1 = $('#logo').val().split('.').pop().toLowerCase();
        if($.inArray(ext1, ['png','jpg','jpeg']) == -1 && ext1 != '')
        {
            $('#lerror').html('Only .jpg, .jpeg, .png allowed').css('color','red');
            e.preventDefault();
             return false;
        }

        var ext2 = $('#pan_file').val().split('.').pop().toLowerCase();
        if($.inArray(ext2, ['png','jpg','jpeg','pdf']) == -1 && ext2 != '')
        {
            $('#perror').html('Only .jpg, .jpeg, .png , .pdf allowed').css('color','red');
            e.preventDefault();
             return false;
        }

        var ext3 = $('#gst_file').val().split('.').pop().toLowerCase();
        if($.inArray(ext3, ['png','jpg','jpeg','pdf']) == -1 && ext3 != '')
        {
            $('#gerror').html('Only .jpg, .jpeg, .png , .pdf allowed').css('color','red');
            e.preventDefault();
             return false;
        }

        var ext4 = $('#iso_file').val().split('.').pop().toLowerCase();
        if($.inArray(ext4, ['png','jpg','jpeg','pdf']) == -1 && ext4 != '')
        {
            $('#ierror').html('Only .jpg, .jpeg, .png , .pdf allowed').css('color','red');
            e.preventDefault();
            return false;
        }
    });
</script>
<script>
    $('#pincode').keyup(function(){
        var len=$(this).val().length;
        var value=$(this).val();
       
        if(len>6)
        {
            $('#pinerror').fadeIn();
            $('#pinerror').text('Only 6 Digits Allowed').css('color','red');
            $('#pinerror').fadeOut(3000);
            var vals=value.substring(0, 6);
            $(this).val(vals);
        }
    });
</script>
@endpush