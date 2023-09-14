<?php
$roles=Session::get('ROLES');
$role=explode(',',$roles);
$count=count($role);

?>
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav">

                        
                        <!-- Only dashboard -->
                        @if($roles!=0 && $roles!=3)
                            <li class="nav-item">
                                <a href="{{route('dashboard')}}" class="waves-effect nav-link">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-dashboards">Dashboard</span>
                                </a>
                            </li>
                        
                        @endif
                        <!-- Super Admin & Admin -->
                        @if($roles==0 || $roles==1)

                            <li class="nav-item">
                                <a href="{{route('user.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-user"></i>
                                    <span key="t-dashboards">Manage Users</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('so.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Manage OA</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('visit_so.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Visit OA</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('manage_labour_payment.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Manage Expense Requests</span>
                                </a>
                            </li>
                        @endif

                        <!-- Accountant -->
                        @if($roles==0 || $roles==2)

                            <li class="nav-item">
                                <a href="{{route('labour_payment.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-user"></i>
                                    <span key="t-dashboards">Technician Payment</span>
                                </a>
                            </li>
                        @endif

                        <!-- labour -->
                        @if($roles==3)
                            <li class="nav-item">
                                <a href="{{route('attendance.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Attendance</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('income.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-dashboards">Income</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('expense.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Expenses</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('transfer_other_technician.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Transfer other Technician</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="waves-effect nav-link" id="oa_hist" data-bs-toggle="modal" data-bs-target="#oaHistoryModal">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">OA History</span>
                                </a>
                            </li>
                        @endif
                        <!--Not Accountant -->
                        @if($roles!=2)
                            <li class="nav-item">
                                <a href="{{route('travel_expense.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Travel Expense</span>
                                </a>
                            </li>
                        @endif
                        @if($roles!=3)
                            <li class="nav-item">
                                <a href="{{route('SO_payment_history.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">OA Payment History</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-user" role="button"
                                >
                                    <i class="bx bx-file me-2"></i><span key="t-specification">Reports</span> <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-settings">
                                    <a href="{{route('site_exp_report.page')}}" class="dropdown-item">
                                    <span key="t-customers">Site Expenses</span>
                                    </a>
                                </div>
                            </li>
                        @endif
                        <!-- labour -->
                        @if($roles==1)
                            <li class="nav-item">
                                <a href="{{route('technician_attendance.page')}}" class="waves-effect nav-link">
                                    <i class="bx bx-file"></i>
                                    <span key="t-dashboards">Technician Attendance</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>
        </div>
    </div>