

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
            /* Prevent body overflow */
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow: hidden; /* Prevent scrollbar on body */
            }

            /* Ensure the layout container fits the viewport */
            #layoutSidenav {
                display: flex;
                height: 100vh; /* Full viewport height */
            }

            /* Sidebar navigation */
            #layoutSidenav_nav {
                flex: 0 0 auto; /* Sidebar doesn't grow or shrink */
            }

            /* Main content area */
            #layoutSidenav_content {
                flex: 1;
                overflow-y: auto; /* Allow scrolling within the content area if needed */
                height: 100vh; /* Match the viewport height */
            }

            /* Main content padding */
            main {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            /* Container for cards and graph */
            .container-fluid {
                flex: 1;
                overflow-y: auto; /* Allow scrolling within the container if content overflows */
                padding-bottom: 2rem; /* Add padding to ensure content isn't cut off */
            }

            /* Sidebar navigation links */
            .sb-sidenav-menu .nav-link {
                color: #FFF0DC;
                padding: 15px 25px;
                margin: 5px 10px;
                border-radius: 8px;
            }

            /* Center the graph container */
            .graph-container {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-top: 1rem;
                margin-bottom: 1rem;
            }

            /* Style the graph card */
            .graph-card {
                width: 60%;
                max-width: 800px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                background-color: #f8f9fa;
            }

            /* Style the chart header */
            .graph-card .card-header {
                background-color: #543A14;
                color: #FFF0DC;
                font-weight: bold;
            }

            /* Ensure the chart canvas is responsive */
            #userTypeChart {
                width: 100% !important;
                height: 300px !important; /* Reduced height to fit better */
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand">
            <!-- Navbar Brand -->
            <a class="navbar-brand ps-3" href="#">
                <i class="fas fa-home" style="font-size: 2.5rem; color:rgb(202, 197, 190); margin-left: 70px; margin-top: 50px;"></i>
            </a>

            <!-- Sidebar Toggle -->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search -->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            </form>
            <!-- Navbar -->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/login">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav" style="margin-top: 70px;">
                            <a class="nav-link" href="<?= base_url('/BoardingHouse'); ?>">
                                Dashboard
                            </a>
                            <a class="nav-link collapsed" href="<?= base_url('BoardingHouse/user'); ?>" onclick="handleRedirect(event, '#collapseUser', '<?= base_url('BoardingHouse/user'); ?>')">
                                User
                            </a>
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
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <ol class="breadcrumb mb-4">
                        </ol>
                        <div class="d-flex flex-row align-items-center justify-content-center gap-3">
                            <div class="col-xl-2 col-md-6">
                                <div class="card text-white mb-4" style="background-color: #543A14;">
                                    <div class="card-body">USER</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="<?= base_url('BoardingHouse/user'); ?>">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-6">
                                <div class="card text-white mb-4" style="background-color: #543A14;">
                                    <div class="card-body">OWNER</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="<?= base_url('BoardingHouse/owner'); ?>">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-6">
                                <div class="card text-white mb-4" style="background-color: #543A14;">
                                    <div class="card-body">TENANT</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="<?= base_url('BoardingHouse/tenant'); ?>">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-6">
                                <div class="card text-white mb-4" style="background-color: #543A14;">
                                    <div class="card-body">NOTIFICATIONS</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="<?= base_url('BoardingHouse/notification'); ?>">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Graph Section -->
                        <div class="graph-container">
                            <div class="graph-card card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    User Type Distribution
                                </div>
                                <div class="card-body">
                                    <canvas id="userTypeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Include Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Prepare data for the chart
            const userCounts = <?= json_encode(isset($userCounts) ? $userCounts : []); ?>;
            const labels = userCounts.map(item => item.userType);
            const data = userCounts.map(item => item.count);

            // Create the bar chart
            const ctx = document.getElementById('userTypeChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Users',
                        data: data,
                        backgroundColor: 'rgba(84, 58, 20, 0.7)', // Match the card color with transparency
                        borderColor: 'rgba(84, 58, 20, 1)', // Solid border color
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Allow the chart to adjust its aspect ratio
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                stepSize: 1, // Ensure whole numbers for counts
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'User Type',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        </script>
    </body>
</html>