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
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/timepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.toast.min.css">
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
    <div class="loader hide"><span>Loading...</span></div>
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
                            <a id="lang_en" style="z-index: 1000" rel="nofollow" href="<?php echo base_url(); ?>langswitch/switchLanguage/english" class="dropdown-item">
                                <span>English</span>
                            </a>
                            <a id="lang_ch" style="z-index: 1000" rel="nofollow" href="<?php echo base_url(); ?>langswitch/switchLanguage/chinese" class="dropdown-item">
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
                    <a id="overall_view" href="#"> <i class="icon-chart"></i><?php echo lang('lb_overall_view');?> </a>
                </li>
                <li>
                    <a id="payment_view" href="#"> <i class="icon-chart"></i>Payment detail </a>
                </li>
                <li>
                    <a id="month_view" href="#"> <i class="icon-chart"></i>Monthly sales report </a>
                </li>
                <li>
                    <a id="year_view" href="#"> <i class="icon-chart"></i>Yearly sales report </a>
                </li>
                <li>
                    <a id="detail_comparison" href="#"> <i class="icon-chart"></i><?php echo lang('lb_detail_comparison');?> </a>
                </li>
                <li>
                    <a id="operator_present" href="#"> <i class="icon-chart"></i>Presence Control</a>
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
                        <li><a id="export_xls" style="cursor: pointer"><?php echo lang('lb_export_detail_comparison');?>(XLS)</a></li>
                        <li><a id="export_csv" style="cursor: pointer"><?php echo lang('lb_export_detail_comparison');?>(CSV)</a></li>
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
                                    <div class="title"><strong><?php echo lang('lb_total_values');?></strong></div>
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
                                    <div class="title"><strong><?php echo lang('lb_total_values');?></strong></div>
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
                                                            <strong><?php echo lang('lb_yesterday');?></strong>
                                                        </div>
                                                        <span class="yt_val" style="font-size: 17px; font-weight: 800;">0</span>
                                                    </div>
                                                    <div class="col-6" style="text-align: center">
                                                        <div class="dashtext-3" style="margin-bottom: 20px;">
                                                            <strong><?php echo lang('lb_today');?></strong>
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
                                    <button type="button" class="btn btn-primary shop_article_detail hide" style="margin-top: 20px" name="button"><?php echo lang('lb_see_more');?></button>
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
                                    <div class="title"><strong><?php echo lang('lb_turnover_details_by_shops');?></strong></div>
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
            <div class="page-payment hide">
                <section class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="block">
                            <div style="margin-bottom: 20px;">
                                <div id="payment_date_range" style="width: 250px; cursor: pointer; display: inline-block;"></div>
                                <select class="payment_shop_list form-control form-control-sm" style="display: inline-block"></select>
                                <button type="button" name="button" class="btn btn-primary btn-sm payment_view_apply" style="display: inline-block; margin-left: 20px;">Apply</button>
                                <button type="button" name="button" class="btn btn-primary btn-sm payment_view_export disabled" style="display: inline-block; margin-left: 20px;">Export</button>
                            </div>
                            <table id="payment_table" class="table table-sm">
                                <thead>
                                    <tr class="payment_table_header">
                                        <th>Date</th>
                                        <th>Gross sale</th>
                                        <th>Tax</th>
                                        <th>Net sale</th>
                                        <th>Cash</th>
                                        <th>Credit card</th>
                                        <th>Clover</th>
                                        <th>Uber eat</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="page-monthly hide">
                <section class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="block">
                            <div style="margin-bottom: 20px;">
                                <div id="monthly_date_range" style="width: 250px; cursor: pointer; display: inline-block;"></div>
                                <select class="monthly_shop_list form-control form-control-sm" style="display: inline-block"></select>
                                <button type="button" name="button" class="btn btn-primary btn-sm monthly_view_apply" style="display: inline-block; margin-left: 20px;">Apply</button>
                                <button type="button" name="button" class="btn btn-primary btn-sm monthly_view_export disabled" style="display: inline-block; margin-left: 20px;">Export</button>
                            </div>
                            <table id="monthly_table" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th width='6.3%'>Date</th>
                                        <th width='4.3%'>Day</th>
                                        <th width='5.3%'>Temp*</th>
                                        <th width='5.3%'>Projected daily sales*</th>
                                        <th width='5.3%'>Accumulated projected</th>
                                        <th width='5.3%'>Achievements (%)</th>
                                        <th width='5.3%'>Daily sales</th>
                                        <th width='5.3%'>Accumulated daily</th>
                                        <th width='5.3%'>Netsale</th>
                                        <th width='5.3%'>Accumulated netsale</th>
                                        <th width='5.3%'>Guest count</th>
                                        <th width='5.3%'>Accumulated GC</th>
                                        <th width='5.3%'>Cups sold</th>
                                        <th width='5.3%'>Accumulated cups</th>
                                        <th width='5.3%'>AC</th>
                                        <th width='5.3%'>Accumulated AC</th>
                                        <th width='5.3%'>Minus coffee</th>
                                        <th width='5.3%'>Accumulated minus coffee</th>
                                        <th>Remark*</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="page-yearly hide">
                <section class="no-padding-top no-padding-bottom">
                    <div class="container-fluid">
                        <div class="block">
                            <div style="margin-bottom: 20px;">
                                <div id="yearly_date_range" style="width: 250px; cursor: pointer; display: inline-block;"></div>
                                <select class="yearly_shop_list form-control form-control-sm" style="display: inline-block"></select>
                                <button type="button" name="button" class="btn btn-primary btn-sm yearly_view_apply" style="display: inline-block; margin-left: 20px;">Apply</button>
                                <button type="button" name="button" class="btn btn-primary btn-sm yearly_view_export disabled" style="display: inline-block; margin-left: 20px;">Export</button>
                            </div>
                            <table id="yearly_table" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="__year">2019</th>
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Apr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Aug</th>
                                        <th>Sep</th>
                                        <th>Oct</th>
                                        <th>Nov</th>
                                        <th>Dec</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
                                        <th><?php echo lang('lb_date_range');?></th>
                                        <th colspan="3" class="start_date" style="cursor: pointer">Jun 04, 2018 ~ Jun 10, 2018</th>
                                        <th colspan="3" class="end_date" style="cursor: pointer">Jun 11, 2018 ~ Jun 17, 2018</th>
                                        <th><?php echo lang('lb_variance');?></th>
                                        <th id="apply_filter" style="cursor: pointer; color: #f55;"><?php echo lang('lb_apply_filter');?></th>
                                    </tr>
                                    <tr>
                                        <th width="15%"></th>
                                        <th width="10%"><?php echo lang('lb_qty');?><i class="fa fa-sort sort article_sort" sort_attr="1" sort_dir="d" style="margin-left: 5px;"></i></th>
                                        <th width="10%"><?php echo lang('lb_amount');?><i class="fa fa-sort sort article_sort" sort_attr="2" sort_dir="d" style="margin-left: 5px;"></i></th>
                                        <th width="10%">% <?php echo lang('lb_sales');?></th>
                                        <th width="10%"><?php echo lang('lb_qty');?><i class="fa fa-sort sort article_sort" sort_attr="3" sort_dir="d" style="margin-left: 5px;"></i></th>
                                        <th width="10%"><?php echo lang('lb_amount');?><i class="fa fa-sort sort article_sort" sort_attr="4" sort_dir="d" style="margin-left: 5px;"></i></th>
                                        <th width="10%">% <?php echo lang('lb_sales');?></th>
                                        <th width="12.5%"><?php echo lang('lb_qty');?><i class="fa fa-sort sort article_sort" sort_attr="5" sort_dir="d" style="margin-left: 5px;"></i></th>
                                        <th width="12.5%"><?php echo lang('lb_amount');?><i class="fa fa-sort sort article_sort" sort_attr="6" sort_dir="d" style="margin-left: 5px;"></i></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="page-present hide">
                <section>
                    <div class="container-fluid">
                        <div class="block row">
                            <div class="col-md-5">
                                <div class="row" style="height: 40px; text-align: center">
                                    <div class="col-md-12">
                                        <div class="presence_date popover_hover" style="cursor: pointer; display: inline-block; margin: 5px; line-height: 30px;" data-container="body" data-toggle="popover" data-placement="top" data-content="Click to change start date"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="group">
                                            <div class="group-title check_toggle popover_hover" style="cursor: pointer" data-container="body" data-toggle="popover" data-placement="top" data-content="Click to check all">
                                                SHOPS
                                            </div>
                                            <div class="group-content">
                                                <div class="form-group shop_multiselect">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4">
                                        <div class="group">
                                            <div class="group-title check_toggle popover_hover" style="cursor: pointer" data-container="body" data-toggle="popover" data-placement="top" data-content="Click to check all">
                                                TILLS
                                            </div>
                                            <div class="group-content">
                                                <div class="form-group till_multiselect">
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="group">
                                            <div class="group-title check_toggle popover_hover" style="cursor: pointer" data-container="body" data-toggle="popover" data-placement="top" data-content="Click to check all">
                                                OPERATORS
                                            </div>
                                            <div class="group-content">
                                                <div class="form-group operator_multiselect">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="text-align: center;">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary operator_filter" type="button" name="button" style="margin: 20px;">Apply filter</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row" style="height: 40px;"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="group">
                                            <div class="group-title">
                                                PRESENCE OF OPERATORS
                                            </div>
                                            <div class="group-content">
                                                <table class="presence_operators table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th width="15%">Operator</th>
                                                            <th width="15%">From time (In)</th>
                                                            <th width="15%">To time (Out)</th>
                                                            <th width="5%">Hours</th>
                                                            <th width="5%">OT</th>
                                                            <th width="5%">Rate</th>
                                                            <th width="5%">Charge</th>
                                                            <th width="5%">Adjust</th>
                                                            <th width="30%">History</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="text-align: center;">
                                    <div class="col-md-12">
                                        <button class="export_presence_data btn btn-primary disabled" type="button" name="button" style="margin: 20px;">Export</button>
                                        <button class="save_presence_data btn btn-primary disabled" type="button" name="button" style="margin: 20px;">Save</button>
                                        <button class="load_presence_data btn btn-primary" type="button" name="button" style="margin: 20px;" data-toggle="modal" data-target="#presence_loaded_data_modal">Load</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="adjustment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                        <div role="document" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header"><strong id="exampleModalLabel" class="modal-title">Adjustment</strong>
                                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                    <p>Operator working hour adjustment</p>
                                    <div class="row" style="position: relative;">
                                        <div class="col-sm-4">
                                            <small class="operator-name"></small>
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="original_in_timestamp timestamp-input form-control form-control-sm" value="">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="original_out_timestamp timestamp-input form-control form-control-sm" value="">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px" style="position: relative;">
                                        <div class="col-sm-4">
                                            <small>New timestamp</small>
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="new_in_timestamp timestamp-input form-control form-control-sm" value="">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="new_out_timestamp timestamp-input form-control form-control-sm" value="">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px">
                                        <div class="col-sm-4">
                                            <small>Adjust reason</small>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="adjust_reason form-control form-control-sm" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                    <button type="button" class="btn btn-primary adjust_done">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="presence_loaded_data_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
                        <div role="document" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header"><strong id="exampleModalLabel" class="modal-title">Presence data</strong>
                                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                    <p>Operator presence data stored in database</p>
                                    <table class="loaded_presence_data_table table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Manager</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                </div>
                            </div>
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
    <script src="<?php echo base_url(); ?>assets/js/timepicker.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.toast.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/front.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/cookie.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/language.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dashboard.js"></script>
</body>

</html>
