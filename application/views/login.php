<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SBM | Dashboard Signin</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.toast.min.css">
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

<body>
    <div class="loader hide"></div>
    <div class="login-page">
        <div class="container d-flex align-items-center">
            <div class="form-holder has-shadow">
                <div class="row">
                    <!-- Logo & Information Panel-->
                    <div class="col-lg-6">
                        <div class="info d-flex align-items-center">
                            <div class="content">
                                <div class="logo">
                                    <h1 class="lang_auth_heading"><?php echo lang('auth_heading');?></h1>
                                </div>
                                <p class="lang_auth_subtitle"><?php echo lang('auth_subtitle_1');?><br> <?php echo lang('auth_subtitle_2');?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Form Panel    -->
                    <div class="col-lg-6">
                        <div class="form d-flex align-items-center">
                            <div class="content">
                                <form id="login-form" class="form-validate mb-4">
                                    <div class="form-group">
                                        <input id="login-username" type="text" name="name" required data-msg="Please enter your username" class="input-material">
                                        <label for="login-username" class="label-material lang_auth_username"><?php echo lang('auth_username');?></label>
                                    </div>
                                    <div class="form-group">
                                        <input id="login-password" type="password" name="password" required data-msg="Please enter your password" class="input-material">
                                        <label for="login-password" class="label-material lang_auth_password"><?php echo lang('auth_password');?></label>
                                    </div>
                                    <button type="submit" class="btn btn-primary lang_auth_login"><?php echo lang('auth_login');?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyrights text-center">
            <p>2019 &copy; <a href="http://sbmtec.com" class="external" target="_blank">SBM</a> technology.</p>
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
    <script src="<?php echo base_url(); ?>assets/vendor/chart.js/Chart.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.toast.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/front.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/cookie.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/language.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/custom.js"></script>
</body>

</html>
