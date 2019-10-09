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
                    <div class="h5 no-margin-bottom">Sales and transaction overview</div>
                </div>
            </div>
            <div class="page-dashboard">
                <section class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="stats-3-block block d-flex">
                                    <div class="stats-3"><strong class="d-block _netsale">0</strong><span class="d-block detail sale_detail">Turnover</span>
                                        <div class="progress progress-template progress-small">
                                            <div role="progressbar" style="width: 35%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-template progress-bar-small dashbg-1"></div>
                                        </div>
                                    </div>
                                    <div class="stats-3 d-flex justify-content-between text-center">
                                        <div class="item"><strong class="d-block strong-sm _discount">0</strong><span class="d-block span-sm detail">Discount</span>
                                            <div class="line"></div><small><span class=""></span></small>
                                        </div>
                                        <div class="item"><strong class="d-block strong-sm _promotion">0</strong><span class="d-block span-sm detail">Promotion</span>
                                            <div class="line"></div><small><span class=""></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="stats-2-block block d-flex">
                                    <div class="stats-2 d-flex">
                                        <div class="stats-2-content"><strong class="d-block _transaction_count">0</strong><span class="d-block detail transaction_detail">Transaction count</span>
                                        </div>
                                    </div>
                                    <div class="stats-2 d-flex">
                                        <div class="stats-2-content"><strong class="d-block _average_bill">0</strong><span class="d-block detail">Average bill</span>
                                        </div>
                                    </div>
                                    <div class="stats-2 d-flex">
                                        <div class="stats-2-content"><strong class="d-block _tip">0</strong><span class="d-block detail">Tips</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Detail views -->
                <section id="sale_detail" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="drills-chart block">
                                    <div id="sale_detail_line" class="_bar_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="transaction_detail" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="drills-chart block">
                                    <div id="transaction_detail_line" class="_bar_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- main views -->
                <section id="comparison_none" class="no-padding-top no-padding-bottom hide text-center">
                    <h3></h3>
                </section>
                <section id="comparison_pie" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row d-flex align-items-stretch">
                            <div class="col-md-6">
                                <div id="sale_comparison_pie" class="stats-with-chart-1 block _pie_chart"></div>
                            </div>
                            <div class="col-md-6">
                                <div id="transaction_comparison_pie" class="stats-with-chart-1 block _pie_chart"></div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="comparison_bar" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="drills-chart block">
                                    <div id="sale_comparison_bar" class="_line_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="monthly_sale_bar" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="drills-chart block">
                                    <div id="monthly_growth_line" class="_line_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="monthly_transaction_bar" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="drills-chart block">
                                    <div id="monthly_transaction_line" class="_line_chart"></div>
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
    <script src="/assets/chart/highcharts.js"></script>
    <script src="/assets/chart/modules/exporting.js"></script>
    <script src="/assets/chart/modules/export-data.js"></script>
    <script src="/assets/chart/themes/dark-unica.js"></script>
    <script src="/assets/js/front.js"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>

</html>
