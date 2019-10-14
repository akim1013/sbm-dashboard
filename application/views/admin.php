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
    <div class="loader hide"></div>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="search-panel">
                <div class="search-inner d-flex align-items-center justify-content-center">
                    <div class="close-btn"><?php echo lang('btn_close');?> <i class="fa fa-close"></i></div>
                    <form id="searchForm" action="#">
                        <div class="form-group">
                            <input type="search" name="search" placeholder="What are you searching for...">
                            <button type="submit" class="submit"><?php echo lang('btn_search');?></button>
                        </div>
                    </form>
                </div>
            </div>
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
                    <div class="list-inline-item dropdown">
                        <a id="languages" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link language dropdown-toggle">
                            <img src="<?php echo base_url(); ?>assets/img/<?php
                                if(lang('site_lang') == 'English'){
                                    echo 'US';
                                }else{
                                    echo 'TW';
                                }
                            ?>.png"
                            alt="English">
                            <span class="d-none d-sm-inline-block"><?php echo lang('site_lang')?></span>
                        </a>
                        <div aria-labelledby="languages" class="dropdown-menu" style="z-index: 1000">
                            <a style="z-index: 1000" rel="nofollow" href="<?php echo base_url(); ?>langswitch/switchLanguage/english" class="dropdown-item">
                                <img src="<?php echo base_url(); ?>assets/img/US.png" alt="English" class="mr-2">
                                <span>English</span>
                            </a>
                            <a style="z-index: 1000" rel="nofollow" href="<?php echo base_url(); ?>langswitch/switchLanguage/chinese" class="dropdown-item">
                                <img src="<?php echo base_url(); ?>assets/img/TW.png" alt="English" class="mr-2">
                                <span>中文</span>
                            </a>
                        </div>
                    </div>
                    <!-- Log out               -->
                    <div class="list-inline-item logout">
                        <a class="logout" href="#" class="nav-link"> <span class="d-none d-sm-inline"><?php echo lang('logout')?> </span><i class="icon-logout"></i></a>
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
                    <a href="#all-shops" aria-expanded="false" data-toggle="collapse"> <i class="icon-user"></i>Users</a>
                </li>
                <li>
                    <a class="logout" href="#"> <i class="icon-logout"></i>Logout </a>
                </li>
            </ul>
        </nav>
        <!-- Sidebar Navigation end-->
        <div class="page-content">
            <div class="page-header">
                <div class="container-fluid">
                    <div class="h5 no-margin-bottom">User management</div>
                </div>
            </div>
            <div class="page-dashboard">
                <div class="block margin-bottom-sm">
                    <form id="new_user" class="form-inline container-fluid d-flex align-items-center justify-content-center">
                        <div class="form-group">
                            <input name="name" type="text" placeholder="User name" class="mr-sm-3 form-control" required>
                        </div>
                        <div class="form-group">
                            <input name="email" type="email" placeholder="Email address" class="mr-sm-3 form-control form-control" required>
                        </div>
                        <div class="form-group">
                            <select name="database" type="text" placeholder="Database name" class="mr-sm-3 form-control form-control">
                                <option selected value="null">Select database</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="shop" type="text" placeholder="Shop name" class="mr-sm-3 form-control form-control">
                                <option selected value="null">Select Shop</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input name="password" type="password" placeholder="Password" class="mr-sm-3 form-control form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Add new user" class="btn btn-primary">
                        </div>
                    </form>
                </div>
                <div class="block margin-bottom-sm">
                    <div class="table-responsive container-fluid d-flex align-items-center justify-content-center">
                        <table class="table user-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Database</th>
                                    <th>Shop</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
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
    <div id="confirm-delete" class="confirm-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="Confirm Modal" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <p>Are you sure to delete this user?</p>
                    <button type="button" name="button" class="confirm-delete btn btn-primary" data-dismiss="modal">Confirm</button>
                    <button type="button" name="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript files-->
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/popper.js/umd/popper.min.js">
    </script>
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery.cookie/jquery.cookie.js">
    </script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.toast.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/front.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/admin.js"></script>
</body>

</html>
