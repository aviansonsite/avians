<!doctype html>
<html lang="en">
<head>
        
        <meta charset="utf-8" />
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="{{config('constants.PROJECT_NAME')}}" name="description" />
        <meta content="{{config('constants.AUTHOR_NAME')}}" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">
        @stack('datatable_css')
        <!-- Bootstrap Css -->
        {!! Html::style('assets/css/bootstrap.min.css') !!}
        <!-- Icons Css -->
        {!! Html::style('assets/css/icons.min.css') !!}
        {!! Html::style('assets/libs/toastr/build/toastr.min.css') !!}
        <!-- App Css-->
        {!! Html::style('assets/css/app.min.css') !!}
        @stack('page_css')
        <style>
            .btn-group-sm>.btn, .btn-xs {
                padding: .25rem .5rem;
                font-size: .51094rem;
                border-radius: .2rem;
            }
        </style>
        
    </head>

    <body data-topbar="dark" data-layout="horizontal">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->
         <div id="preloader">
            <div id="status">
                <div class="spinner-chase">
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                </div>
            </div>
        </div>
        <!-- Begin page -->
        <div id="layout-wrapper">

            
            @include('common.header')

            <!-- ========== Left Sidebar Start ========== -->
            @include('common.sidebar')
            <!-- Left Sidebar End -->

            

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                @section('content')
                @show
            
                @include('common.footer')
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right Sidebar -->
        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        {!! Html::script('assets/libs/jquery/jquery.min.js') !!}
        {!! Html::script('assets/libs/jquery/jquery-ui.js') !!}
        {!! Html::script('assets/libs/bootstrap/js/bootstrap.bundle.min.js') !!}
        {!! Html::script('assets/libs/metismenu/metisMenu.min.js') !!}
        {!! Html::script('assets/libs/simplebar/simplebar.min.js') !!}
        {!! Html::script('assets/libs/node-waves/waves.min.js') !!}
        {!! Html::script('assets/libs/toastr/build/toastr.min.js') !!}

        <!-- toastr init -->
        {!! Html::script('assets/js/pages/toastr.init.js') !!}
        
        <!-- @stack('datatable_js') -->
       
        @stack('datatable_js')
        
        {!! Html::script('assets/js/app.js') !!}
        @stack('page_js')

        
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

            $(function () {
              $('[rel="tooltip"]').tooltip();
              $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        <script>
            function openInWindow(href)
            {
                window.open(href, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');
            }
        </script>
      
    </body>

</html>