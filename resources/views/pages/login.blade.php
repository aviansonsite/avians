<!doctype html>
<html lang="en">

    
<!-- Mirrored from themesbrand.com/skote/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Jun 2021 13:59:01 GMT -->
<head>
        
        <meta charset="utf-8" />
        <title>Login | {{config('constants.PROJECT_NAME')}}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">

        <!-- Bootstrap Css -->
        {!! Html::style('assets/css/bootstrap.min.css') !!}
        <!-- Icons Css -->
        {!! Html::style('assets/css/icons.min.css') !!}
        {!! Html::style('assets/libs/toastr/build/toastr.min.css') !!}
        <!-- App Css-->
        {!! Html::style('assets/css/app.min.css') !!}

    </head>

    <body>
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card  border border-primary overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                     <div class="col-5 text-center">
                                     <?php $logo=$u1_obj[0]->logo; ?>
                                        <img src='{{asset("files/company/$logo")}}' alt="" style="height: 67px;margin-top: 35px;">
                                    </div>
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p>Sign in to continue to {{config('constants.PROJECT_NAME')}}.</p>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <!-- <div class="auth-logo">
                                    <a href="{{route('login.page')}}" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="">
                                                <?php $logo=$u1_obj[0]->logo; ?>
                                                <img src='{{asset("files/company/$logo")}}' alt="" class="" height="50">
                                            </span>
                                        </div>
                                    </a>

                                    <a href="{{route('login.page')}}" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="">
                                                <img src='{{asset("files/company/$logo")}}' alt="" class="" height="50">
                                            </span>
                                        </div>
                                    </a>
                                </div> -->
                                <div class="p-2">
                                    {!! Form::open(['class'=>"form-horizontal",'method'=>"post",'url'=>'checkLogin']) !!}
                                    {{ csrf_field() }}
                                    @include('common.alert')

                                        <div class="col-md-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="username" placeholder="Enter Mobile Number" required name="emp_number" onkeyup="var start = this.selectionStart;var end = this.selectionEnd;this.value = this.value.toUpperCase();this.setSelectionRange(start, end);"  maxlength="10">
                                                <label for="username">Employee Number</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-floating mb-3 input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" id="password" placeholder="Enter Password" required name="password" aria-label="Password" aria-describedby="password-addon">
                                                <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                                <label for="password">Password</label>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <i class="mdi mdi-lock me-1"></i> Forgot your password? Please Contact Project Admin.
                                        </div>
                                    {!! Form::close() !!}
                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <div>
                                <p>© {{date('Y')}} {{config('constants.PROJECT_NAME')}} All Rights TO <i class="mdi mdi-heart text-danger"></i> <a href="https://www.ideatesystemsindia.com/" target="_blank">Ideate Systems India Pvt. Ltd.</a></p>
                            </div>
                        </div>

                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Forgot Password</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    {!! Form::open(['url'=>"forgot_email",'method'=>'post']) !!}
                                    <div class="modal-body">
                                        <div class="col-md-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="forgot_email" placeholder="Enter Mobile Number" required name="mobile" pattern="[789]\d{9}">
                                                <label for="forgot_email">Registered Email</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

        <!-- JAVASCRIPT -->
        {!! Html::script('assets/libs/jquery/jquery.min.js') !!}
        {!! Html::script('assets/libs/bootstrap/js/bootstrap.bundle.min.js') !!}
        {!! Html::script('assets/libs/metismenu/metisMenu.min.js') !!}
        {!! Html::script('assets/libs/simplebar/simplebar.min.js') !!}
        {!! Html::script('assets/libs/node-waves/waves.min.js') !!}
        
        <!-- App js -->
        {!! Html::script('assets/libs/toastr/build/toastr.min.js') !!}

        <!-- toastr init -->
        {!! Html::script('assets/js/pages/toastr.init.js') !!}
       
        <!-- App js -->
        {!! Html::script('assets/js/app.js') !!}
        
        <script>
            "use strict"; // Start of use strict

            var smsg  = $("#smsg").text();
            var emsg  = $("#emsg").text();
        
            $(document).ready(function () {
                if (smsg!=''){
                    /*function3(smsg);*/
                    toastr.options.timeOut = 5000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.options.showEasing= 'swing';
                    toastr.options.hideEasing= 'linear';
                    toastr.options.showMethod= 'fadeIn';
                    toastr.options.hideMethod= 'fadeOut';
                    toastr.options.closeButton= true;
                    toastr.success(smsg);
                }

                if (emsg!=''){
                    toastr.options.timeOut = 5000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.options.showEasing= 'swing';
                    toastr.options.hideEasing= 'linear';
                    toastr.options.showMethod= 'fadeIn';
                    toastr.options.hideMethod= 'fadeOut';
                    toastr.options.closeButton= true;
                    toastr.error(emsg);
                }

            });

             
        </script>
    </body>

<!-- Mirrored from themesbrand.com/skote/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 10 Jun 2021 13:59:01 GMT -->
</html>
