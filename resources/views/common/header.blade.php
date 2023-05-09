<?php
use App\Models\CompanyProfileModel;
$com=CompanyProfileModel::all();
$logo=$com[0]->logo;
$roles=Session::get('ROLES');
?>
             <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="{{route('dashboard')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src='{{asset("files/company/$logo")}}' alt="" height="30">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('files/company/logo.png')}}" alt="" height="30">
                                </span>
                            </a>

                            <a href="{{route('dashboard')}}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src='{{asset("files/company/$logo")}}' alt="" height="30">
                                </span>
                                <span class="logo-lg">
                                    <img src='{{asset("files/company/logo.png")}}' alt="" height="30">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>


                    </div>

                    <div class="d-flex">
                     
                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                                <i class="bx bx-fullscreen"></i>
                            </button>
                        </div>

                     
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src='{{asset("files/company/$logo")}}'
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{Session::get('NAME')}}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="{{route('profile.page')}}"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                                
                                @if($roles==0)
                                <a class="dropdown-item" href="{{route('company.profile.page')}}"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Company Profile</span></a>
                                @endif
                                
                                <a class="dropdown-item text-danger" href="{{route('admin.logout')}}"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                            </div>
                        </div>

                      
            
                    </div>
                </div>
            </header>