<?php
session_start();
require("config.php");
////code
 
if(!isset($_SESSION['auser']))
{
	header("location:index.php");
}
?>
<div class="header" style="background-color:#7bb13c;">

    <div class="header-left">
        <a href="dashboard.php" class="logo">
            <span style="font-size: 24px; font-weight: bold;  color:white;">Expense <span> Voyoge </span>
        </a>
    </div></span>

    <!-- /Logo -->

    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fe fe-text-align-left"></i>
            </a>



    <!-- Mobile Menu Toggle -->
    <a class="mobile_btn" id="mobile_btn">
        <i class="fa fa-bars"></i>
    </a>
    <!-- /Mobile Menu Toggle -->

    <!-- Header Right Menu -->
    <ul class="nav user-menu">


        <!-- User Menu -->
        <!-- <h4 style="color:white;margin-top:13px;text-transform:capitalize;"><?php echo $_SESSION['auser'];?></h4> -->
        <li class="nav-item dropdown app-dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <span class="user-img"><img class="rounded-circle" src="assets/img/avatar-01.png" width="31"
                        alt="Ryan Taylor"></span>
            </a>

            <div class="dropdown-menu">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img src="assets/img/avatar-01.png" alt="User Image" class="avatar-img rounded-circle">
                    </div>
                    <div class="user-text">
                        <h6><?php echo $_SESSION['auser'];?></h6>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                </div>
                <a class="dropdown-item" href="profile.php">Profile</a>
                <a class="dropdown-item" href="../index.php">Logout</a>
            </div>
        </li>

        <!-- /User Menu -->

    </ul>
    <!-- /Header Right Menu -->

</div>
<!-- /Header Right Menu -->

</div>

<!-- header --->



<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li>
                    <a href="dashboard.php"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>

                <li class="menu-title">
                    <span>All Users</span>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-user"></i> <span> All Users </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a href="adminlist.php"> Admin </a></li>
                        <li><a href="userlist.php"> Users </a></li>

                    </ul>
                </li>

                <li class="menu-title">
                    <span>State & City</span>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-location"></i> <span>State & City</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a href="cityadd.php"> State / City </a></li>

                    </ul>
                </li>

                <li class="menu-title">
                    <span>Trip Management</span>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fe fe-map"></i> <span> Trip / Travel</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a href="tripadd.php"> Add Trip</a></li>
                        <li><a href="tripview.php"> View Trip </a></li>
                        <li><a href="addblog.php"> Add Blog </a></li>
                        <li><a href="addagent.php"> Add agent </a></li>

                    </ul>
                </li>



                <li class="menu-title">
                    <span>Query</span>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fe fe-comment"></i> <span> Contact,Feedback </span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a href="contactview.php"> Contact </a></li>
                        <li><a href="feedbackview.php"> Feedback </a></li>
                    </ul>
                </li>
               
             

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->