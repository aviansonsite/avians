@extends('common.master')
<?php $title=config('constants.PROJECT_NAME'); ?>
@section('title',"User Profile | $title")
@push('page_css')
<style>
    .form-floating>.form-control, .form-floating>.form-select {
        height: calc(2.8rem + 1px) !important;
        padding: 1rem .75rem;
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

    /* .loader {
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
    } */

    .img-thumbnailnew {
    padding: 0.25rem;
    border-radius: 0.25rem;
    max-width: 100%;
    height: auto;
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
                        <h4 class="mb-sm-0 font-size-18">Profile</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profile</li>
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
                                        <p>{{$u_obj->name}}'s Profile</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{asset('assets/images/profile-img.png')}}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="avatar-lg profile-user-wid mb-4">
                                        <?php $logo=$u1_obj[0]->logo; ?>
                                        <img src='{{asset("files/company/$logo")}}' alt="" class="img-thumbnailnew">
                                    </div>
                                </div>

                                <div class="col-sm-9">
                                    <div class="pt-4">

                                        <div class="row">
                                            <div class="col-12">
                                                <h5 class="font-size-15">{{$u_obj->name}}</h5>
                                                <span class="font-size-12 text-muted">
                                                    <?php 
                                                        $roles=explode(',',$u_obj->role);
                                                        $l=sizeof($roles);
                                                    ?>
                                                    @for($i=0;$i<$l;$i++)
                                                        @if($roles[$i] == 0)
                                                            SUPER ADMIN
                                                        @endif

                                                        @if($roles[$i] == 1)
                                                            Admin
                                                        @endif

                                                        @if($roles[$i] == 2)
                                                            ACCOUNTANT
                                                        @endif

                                                        @if($roles[$i] == 3)
                                                            TECHNICIAN
                                                        @endif

                                                        @if($i<$l-1)
                                                            , 
                                                        @endif
                                                    @endfor
                                                </span><br>
                                                <span class="font-size-12">Mob. : {{$u_obj->mobile}}</span><br>
                                                <span class="font-size-12">Email : {{$u_obj->email}}</span>
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
                    
                    <!-- end row -->
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex flex-wrap">
                                        <h4 class="card-title mb-4">Update Profile</h4>
                                    </div>
                                    {!! Form::open(['class'=>"form-horizontal",'method'=>"post",'url'=>'profile_edt']) !!}
                                        <input type="hidden" name="edit_id" value="{{$u_obj->id}}">
                                        <div class="col-md-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingnameInput" placeholder="Enter Name" value="{{$u_obj->name}}" required name="name">
                                                <label for="floatingnameInput">Name</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingnameInput" placeholder="Enter Mobile" value="{{$u_obj->mobile}}" required name="mobile">
                                                <label for="floatingnameInput">Mobile</label>
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control" id="floatingemailInput" placeholder="Enter Email address" value="{{$u_obj->email}}" required name="email">
                                                <label for="floatingemailInput">Email address</label>
                                            </div>
                                        </div>

                                        <div>
                                            <button type="submit" class="btn btn-primary w-md">Update</button>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex flex-wrap">
                                        <h4 class="card-title mb-4">Update Password</h4>
                                    </div>
                                    {!! Form::open(['class'=>"form-horizontal",'method'=>"post",'url'=>'check_pass','id'=>'update_password_form']) !!}
                                        <input type="hidden" name="edit_id" value="{{$u_obj->id}}">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter Current Password" required>
                                            <label for="current_password">Current Password</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter New Password" required>
                                            <label for="new_password">New Password</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter Confirm Password" required>
                                            <label for="confirm_password">Confirm Password</label>
                                            <span id="msg"></span>
                                        </div>
                                        
                                        <div>
                                            <button type="submit" class="btn btn-primary w-md">Update</button>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
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
    $('#update_password_form').submit(function(e){
        var newpassword=$('#new_password').val();
        var confirmpassword=$('#confirm_password').val();

        if(newpassword !== confirmpassword)
        {
            $('#msg').fadeIn();
            $('#msg').text("Password Doesn't Matched").css('color','red');
            $('#msg').fadeOut(3000);
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush