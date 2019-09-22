<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SBM | Dashboard</title>
    <meta name="description" content="SBM tech, MeetFresh">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/daterangepicker.css" />
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="/assets/vendor/font-awesome/css/font-awesome.min.css">
    <!-- Custom Font Icons CSS-->
    <link rel="stylesheet" href="/assets/css/font.css">
    <!-- Google fonts - Muli-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="/assets/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="/assets/css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body style="background-color: #2d3035;">
    <div class="loader hide"></div>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="search-panel">
                <div class="search-inner d-flex align-items-center justify-content-center">
                    <div class="close-btn">Close <i class="fa fa-close"></i></div>
                    <form id="searchForm" action="#">
                        <div class="form-group">
                            <input type="search" name="search" placeholder="What are you searching for...">
                            <button type="submit" class="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="navbar-header">
                    <!-- Navbar Header-->
                    <a href="index.html" class="navbar-brand">
                        <div class="brand-text brand-big visible text-uppercase"><strong class="text-primary">SBM</strong><strong>Tech</strong></div>
                        <div class="brand-text brand-sm"><strong class="text-primary">SBM</strong><strong>Tech</strong></div>
                    </a>
                    <!-- Sidebar Toggle Btn-->
                    <button class="sidebar-toggle"><i class="fa fa-long-arrow-left"></i></button>
                </div>

                <div class="right-menu d-flex align-items-center no-margin-bottom">
                    <div id="reportrange"><span></span></div>
                    <div class="list-inline-item"><a href="#" class="search-open nav-link"><i class="icon-magnifying-glass-browser"></i></a></div>
                    <!-- Log out               -->
                    <div class="list-inline-item logout">
                        <a class="logout" href="#" class="nav-link"> <span class="d-none d-sm-inline">Logout </span><i class="icon-logout"></i></a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="d-flex align-items-stretch">
        <!-- Sidebar Navigation-->
        <nav id="sidebar">
            <!-- Sidebar Header-->
            <div class="sidebar-header d-flex align-items-center">
                <div class="avatar"><img src="http://sbmtec.com/wp-content/uploads/2019/03/VariPOS-819-front-VFD-500x500.jpg" alt="..." class="img-fluid rounded-circle"></div>
                <div class="title">
                    <h1 class="h5">SBM technology</h1>
                    <p>Dashboard</p>
                </div>
            </div>
            <!-- Sidebar Navidation Menus--><span class="heading">Main</span>
            <ul class="list-unstyled">
                <li class="active">
                    <a href="#all-shops" aria-expanded="false" data-toggle="collapse"> <i class="icon-chart"></i>All Shops </a>
                    <ul id="all-shops" class="collapse list-unstyled "></ul>
                </li>
                <li>
                    <a href="#"> <i class="fa fa-refresh"></i>Refresh </a>
                </li>
                <li>
                    <a class="logout" href="#"> <i class="icon-logout"></i>Logout </a>
                </li>
            </ul><span class="heading">Extras</span>
            <ul class="list-unstyled">
                <li>
                    <a href="#"> <i class="	fa fa-paste"></i>Export </a>
                </li>
            </ul>
        </nav>
        <!-- Sidebar Navigation end-->
        <div class="page-content">
            <div class="page-header">
                <div class="container-fluid">
                    <div class="h5 no-margin-bottom">Comparison of shops KPI</div>
                </div>
            </div>
            <div class="page-dashboard">
                <section class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="stats-3-block block d-flex">
                                    <div class="stats-3"><strong class="d-block turnover">0</strong><span class="d-block">Total turnover</span>
                                        <div class="progress progress-template progress-small">
                                            <div role="progressbar" style="width: 35%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-template progress-bar-small dashbg-1"></div>
                                        </div>
                                    </div>
                                    <div class="stats-3 d-flex justify-content-between text-center">
                                        <div class="item"><strong class="d-block strong-sm discount">0</strong><span class="d-block span-sm">Discount</span>
                                            <div class="line"></div><small class="discount_percent">0%</small>
                                        </div>
                                        <div class="item"><strong class="d-block strong-sm promotion">0</strong><span class="d-block span-sm">Promotion</span>
                                            <div class="line"></div><small class="promotion_percent">0%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="stats-2-block block d-flex">
                                    <div class="stats-2 d-flex">
                                        <div class="stats-2-content"><strong class="d-block transactions">0</strong><span class="d-block">Transactions</span>
                                            <div class="progress progress-template progress-small">
                                                <div role="progressbar" style="width: 60%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-template progress-bar-small dashbg-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stats-2 d-flex">
                                        <div class="stats-2-content"><strong class="d-block average_bill">0</strong><span class="d-block">Average bill</span>
                                            <div class="progress progress-template progress-small">
                                                <div role="progressbar" style="width: 35%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-template progress-bar-small dashbg-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="margin-bottom-sm">
                    <div class="container-fluid">
                        <div class="row d-flex align-items-stretch">
                            <div class="col-lg-4">
                                <div class="stats-with-chart-1 block">
                                    <div class="title"> <strong class="d-block">Transactions per hour</strong></div>
                                    <div class="row d-flex align-items-end justify-content-between">
                                        <div class="col-5">
                                            <div class="text"><strong class="d-block dashtext-3">$740</strong><span class="d-block">20, Sept 2019</span><small class="d-block">320 Sales</small></div>
                                        </div>
                                        <div class="col-7">
                                            <div class="bar-chart chart">
                                                <canvas id="salesBarChart1"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="stats-with-chart-1 block">
                                    <div class="title"> <strong class="d-block">Total sales per hour</strong></div>
                                    <div class="row d-flex align-items-end justify-content-between">
                                        <div class="col-5">
                                            <div class="text"><strong class="d-block dashtext-2">80%</strong><span class="d-block">20, Sept 2019</span><small class="d-block">+35 Sales</small></div>
                                        </div>
                                        <div class="col-7">
                                            <div class="bar-chart chart">
                                                <canvas id="salesBarChart2"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="stats-with-chart-1 block">
                                    <div class="title"> <strong class="d-block">Total sales by weekday</strong></div>
                                    <div class="row d-flex align-items-end justify-content-between">
                                        <div class="col-5">
                                            <div class="text"><strong class="d-block dashtext-2">80%</strong><span class="d-block">Sept 2019</span><small class="d-block">+35 Sales</small></div>
                                        </div>
                                        <div class="col-7">
                                            <div class="bar-chart chart">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="no-padding-bottom">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                Sales comparison by months
                                <div class="drills-chart block">
                                    <canvas id="lineChart1"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                Transactions comparison by months
                                <div class="line-cahrt block">
                                    <canvas id="lineCahrt"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="stats-with-chart-2 block">
                                    <div class="title"><strong class="d-block">Sales comparison</strong></div>
                                    <div class="piechart chart">
                                        <canvas id="pieChartHome1"></canvas>
                                        <div class="text"><strong class="d-block turnover">0</strong><span class="d-block">Turnover</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="stats-with-chart-2 block">
                                    <div class="title"><strong class="d-block">Transaction comparison</strong></div>
                                    <div class="piechart chart">
                                        <canvas id="pieChartHome2"></canvas>
                                        <div class="text"><strong class="d-block transactions">0</strong><span class="d-block">Transactions</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="stats-with-chart-2 block">
                                    <div class="title"><strong class="d-block">Discount comparison</strong></div>
                                    <div class="piechart chart">
                                        <canvas id="pieChartHome3"></canvas>
                                        <div class="text"><strong class="d-block discount">0</strong><span class="d-block">Discounts</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <footer class="footer">
                <div class="footer__block block no-margin-bottom">
                    <div class="container-fluid text-center">
                        <p class="no-margin-bottom">2019 &copy; <a href="http://sbmtec.com" target="_blank">SBM</a> technology.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- JavaScript files-->
    <script src="/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/assets/vendor/jquery/moment.min.js"></script>
    <script src="/assets/vendor/jquery/daterangepicker.min.js"></script>
    <script src="/assets/vendor/popper.js/umd/popper.min.js">
    </script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/vendor/jquery.cookie/jquery.cookie.js">
    </script>
    <script src="/assets/vendor/jquery-validation/jquery.validate.min.js"></script>

    <script src="/assets/js/front.js"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>

</html>
