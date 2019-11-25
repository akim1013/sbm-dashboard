<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo lang('site_title');?></title>
    <meta name="description" content="SBM tech, MeetFresh">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/daterangepicker.css" />
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/font-awesome/css/font-awesome.min.css">
    <!-- Custom Font Icons CSS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font.css">
    <!-- Google fonts - Muli-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body style="background-color: #2d3035;">
    <div class="loader hide"></div>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="navbar-header">
                    <!-- Navbar Header-->
                    <a href="index.html" class="navbar-brand">
                        <div class="brand-text brand-big visible text-uppercase"><strong class="text-primary">SBM</strong><strong><?php echo lang('sbm_tech');?></strong></div>
                        <div class="brand-text brand-sm"><strong class="text-primary">SBM</strong><strong><?php echo lang('sbm_tech');?></strong></div>
                    </a>
                    <!-- Sidebar Toggle Btn-->
                    <button class="sidebar-toggle"><i class="fa fa-long-arrow-left"></i></button>
                </div>

                <div class="right-menu d-flex align-items-center no-margin-bottom">
                    <div id="reportrange"><span></span></div>
                    <div class="list-inline-item dropdown">
                        <a id="languages" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">
                            <span class="d-none d-sm-inline-block"><?php echo lang('site_lang')?></span>
                        </a>
                        <div aria-labelledby="languages" class="dropdown-menu" style="z-index: 1000">
                            <a style="z-index: 1000" rel="nofollow" href="<?php echo base_url(); ?>langswitch/switchLanguage/english" class="dropdown-item">
                                <span>English</span>
                            </a>
                            <a style="z-index: 1000" rel="nofollow" href="<?php echo base_url(); ?>langswitch/switchLanguage/chinese" class="dropdown-item">
                                <span>中文</span>
                            </a>
                        </div>
                    </div>
                    <!-- Log out               -->
                    <div class="list-inline-item logout">
                        <a class="logout" href="#" class="nav-link"> <span class="d-none d-sm-inline"><?php echo lang('lb_logout');?> </span><i class="icon-logout"></i></a>
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
                <div class="avatar"><img src="<?php echo base_url(); ?>assets/img/logo.jpg" alt="..." class="img-fluid rounded-circle"></div>
                <div class="title">
                    <h1 class="h5">SBM <?php echo lang('sbm_technology');?></h1>
                    <p><?php echo lang('sbm_dashboard');?></p>
                </div>
            </div>
            <!-- Sidebar Navidation Menus--><span class="heading"><?php echo lang('lb_main');?></span>
            <ul class="list-unstyled">
                <li class="active">
                    <a id="overall_view" href="#"> <i class="icon-chart"></i>Overall view </a>
                </li>
                <li>
                    <a id="detail_comparison" href="#"> <i class="icon-chart"></i>Detail comparison </a>
                </li>
                <li>
                    <a id="refresh" style="cursor: pointer;"> <i class="fa fa-refresh"></i><?php echo lang('lb_refresh');?> </a>
                </li>
                <li>
                    <a class="logout" href="#"> <i class="icon-logout"></i><?php echo lang('lb_logout');?> </a>
                </li>
            </ul><span class="heading"><?php echo lang('lb_extras');?></span>
            <ul class="list-unstyled">
                <li>
                    <a href="#all-shops" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-scribd"></i><?php echo lang('lb_all_shops');?> </a>
                    <ul id="all-shops" class="collapse list-unstyled "></ul>
                </li>

                <li class="export-data hide">
                    <a href="#export" aria-expanded="false" data-toggle="collapse"> <i class="fa fa-paste"></i><?php echo lang('lb_export');?> </a>
                    <ul id="export" class="collapse list-unstyled">
                        <li><a id="export_xls" style="cursor: pointer">Export detail comparison(XLS)</a></li>
                        <li><a id="export_csv" style="cursor: pointer">Export detail comparison(CSV)</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- Sidebar Navigation end-->
        <div class="page-content">
            <div class="page-header">
                <div class="container-fluid">
                    <div class="h5 no-margin-bottom"><?php echo lang('lb_overview');?> - <div class="shop-name"></div></div>
                </div>
            </div>
            <div class="page-dashboard">
                <section class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-desktop block">
                                    <div class="title"><strong>Total values</strong></div>
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th><?php echo lang('lb_turnover');?>[$]</th>
                                                <th><?php echo lang('lb_discount');?>[$]</th>
                                                <th><?php echo lang('lb_promotion');?>[$]</th>
                                                <th><?php echo lang('lb_transaction_count');?></th>
                                                <th><?php echo lang('lb_average_bill');?>[$]</th>
                                                <th><?php echo lang('lb_tips');?>[$]</th>
                                                <th><?php echo lang('lb_tax');?>[$]</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="color: #f55; cursor: pointer" class="_netsale">0</td>
                                                <td class="_discount">0</td>
                                                <td class="_promotion">0</td>
                                                <td style="color: #f55; cursor: pointer" class="_transaction_count">0</td>
                                                <td class="_average_bill">0</td>
                                                <td class="_tip">0</td>
                                                <td class="_tax">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-mobile block">
                                    <div class="title"><strong>Total values</strong></div>
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th><?php echo lang('lb_turnover');?>[$]</th>
                                                <th><?php echo lang('lb_discount');?>[$]</th>
                                                <th><?php echo lang('lb_promotion');?>[$]</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="color: #f55; cursor: pointer" class="_netsale">0</td>
                                                <td class="_discount">0</td>
                                                <td class="_promotion">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-sm" style="margin-top: 10px;">
                                        <thead>
                                            <tr>
                                                <th><?php echo lang('lb_transaction_count');?></th>
                                                <th><?php echo lang('lb_average_bill');?>[$]</th>
                                                <th><?php echo lang('lb_tips');?>[$]</th>
                                                <th><?php echo lang('lb_tax');?>[$]</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="color: #f55; cursor: pointer" class="_transaction_count">0</td>
                                                <td class="_average_bill">0</td>
                                                <td class="_tip">0</td>
                                                <td class="_tax">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="turnover_detail" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="block">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="t_comparison">
                                        <div class="row" style="height: 100%;">
                                            <div class="col-6">
                                                <div id="yt_comparison"></div>
                                            </div>
                                            <div class="col-6 text-center">
                                                <div class="row" style="height: 50%; margin-top: 30px;">
                                                    <div class="col-6" style="text-align: center">
                                                        <div class="dashtext-3" style="margin-bottom: 20px;">
                                                            <strong>Yesterday</strong>
                                                        </div>
                                                        <span class="yt_val" style="font-size: 17px; font-weight: 800;">0</span>
                                                    </div>
                                                    <div class="col-6" style="text-align: center">
                                                        <div class="dashtext-3" style="margin-bottom: 20px;">
                                                            <strong>Today</strong>
                                                        </div>
                                                        <span class="t_val" style="font-size: 17px; font-weight: 800;">0</span>
                                                    </div>
                                                </div>
                                                <span class="t_growth_percent dashtext-2" style="font-size: 25px;">+0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div id="w_comparison" class="t_comparison"></div>
                                </div>
                                <div class="col-md-3">
                                    <div id="wl_comparison" class="t_comparison"></div>
                                </div>
                                <div class="col-md-3">
                                    <div id="m_comparison" class="t_comparison"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Detail views -->
                <section id="sale_detail" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="block">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div id="sale_detail_line" class="_bar_chart"></div>
                                    <button type="button" class="btn btn-primary shop_article_detail hide" style="margin-top: 20px" name="button">See more...</button>
                                </div>
                                <div class="col-lg-4">
                                    <div id="payment_detail_line" class="_bar_chart"></div>
                                </div>
                            </div>
                            <div class="row shop_article_detail_box">
                            </div>
                        </div>
                    </div>
                </section>
                <section id="transaction_detail" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
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
                <section id="comparison_bar" class="no-padding-top no-padding-bottom hide">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6" style="height: calc(100% - 20px)">
                                <div class="block">
                                    <div class="title"><strong>Turnover details by shops</strong></div>
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="50%"><?php echo lang('lb_shop');?></th>
                                                <th width="20%"><?php echo lang('lb_turnover');?> [$] <i class="fa fa-sort sort turnover_comparison_sort"></i></th>
                                                <th width="20%"><?php echo lang('lb_transaction');?> <i class="fa fa-sort sort transaction_comparison_sort"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody class="sale_comparison_table">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-6">
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
            <div class="page-comparison hide">
                <section id="comparison_detail" class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="block table_detail_comparison">
                            <table id="detail_comparison_table" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date range</th>
                                        <th colspan="3" class="start_date" style="cursor: pointer">Jun 04, 2018 ~ Jun 10, 2018</th>
                                        <th colspan="3" class="end_date" style="cursor: pointer">Jun 11, 2018 ~ Jun 17, 2018</th>
                                        <th>Variance</th>
                                        <th id="apply_filter" style="cursor: pointer; color: #f55;">Apply filter</th>
                                    </tr>
                                    <tr>
                                        <th width="15%"></th>
                                        <th width="10%">Qty</th>
                                        <th width="10%">Amount</th>
                                        <th width="10%">% Sales</th>
                                        <th width="10%">Qty</th>
                                        <th width="10%">Amount</th>
                                        <th width="10%">% Sales</th>
                                        <th width="12.5%">Qty</th>
                                        <th width="12.5%">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <footer class="footer">
                <div class="footer__block block no-margin-bottom">
                    <div class="container-fluid text-center">
                        <p class="no-margin-bottom">2019 &copy; <a href="http://sbmtec.com" target="_blank">SBM</a> <?php echo lang('sbm_technology');?>.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- JavaScript files-->
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/daterangepicker.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/popper.js/umd/popper.min.js">
    </script>
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery.cookie/jquery.cookie.js">
    </script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/chart/highcharts.js"></script>
    <script src="<?php echo base_url(); ?>assets/chart/modules/exporting.js"></script>
    <script src="<?php echo base_url(); ?>assets/chart/modules/export-data.js"></script>
    <script src="<?php echo base_url(); ?>assets/chart/themes/dark-unica.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/front.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dashboard.js"></script>
</body>

</html>
