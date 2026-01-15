<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>BOARDING HOUSE</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

        <link href="<?= base_url("assets/");?>css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        <style>
            /* Sidebar navigation links */
            .sb-sidenav-menu .nav-link {
                color: #FFF0DC; /* Light brown text for contrast with dark background */
                padding: 15px 25px; /* Increased padding for better spacing */
                margin: 5px 10px; /* Margin for separation between links */
                border-radius: 8px; /* Rounded corners for a modern look */
            }
        </style>

    </head>
    <body class="sb-nav-fixed" >
        <nav class="sb-topnav navbar navbar-expand" >
            <!-- Navbar Brand-->
            <!-- Navbar Brand -->
            <a class="navbar-brand ps-3" href="#">
                <i class="fas fa-home" style="font-size: 2.5rem; color:rgb(202, 197, 190); margin-left: 70px; margin-top: 50px;"></i>
            </a>

            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <!-- <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div> -->
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <!-- <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li> -->
                        <li><a class="dropdown-item" href="/login">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">  <!-- style="background-color:rgb(70, 44, 4);" -->
                        <div class="nav" style="margin-top: 70px;">
                            <!-- <div class="sb-sidenav-menu-heading">Core</div> -->
                            <a class="nav-link" href="<?= base_url('/BoardingHouse'); ?>">
                                <!-- <div class="sb-nav-link-icon"></div> -->
                                Dashboard
                            </a>
                            <!-- <div class="sb-sidenav-menu-heading">Users</div> -->
                            <!-- User Section -->
                            <a class="nav-link collapsed" href="<?= base_url('BoardingHouse/user'); ?>" onclick="handleRedirect(event, '#collapseUser', '<?= base_url('BoardingHouse/user'); ?>')">
                                User
                            </a>
                            

                            <!-- Owner Section -->
                            <a class="nav-link collapsed" href="<?= base_url('BoardingHouse/owner'); ?>" onclick="handleRedirect(event, '#collapseOwner', '<?= base_url('BoardingHouse/owner'); ?>')">
                                Owner
                            </a>
                            
                            <a class="nav-link collapsed" href="<?= base_url('BoardingHouse/tenant'); ?>">
                                
                                Tenant
                                
                            </a>
                            <a class="nav-link collapsed" href="<?= base_url('BoardingHouse/notification'); ?>">
                                
                                Notification
                                
                            </a>
                            <a class="nav-link collapsed" href="/login">
                                
                                Logout
                                
                            </a>
                            
                        </div>
                    </div>
                    <!-- <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Admin
                    </div> -->
                </nav>
            </div>
            <div id="layoutSidenav_content">