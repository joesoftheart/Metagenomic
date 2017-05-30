<?ob_start();?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/sb-admin-2-custom.css">

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>vendor/bootstrap/css/bootstrap.min.css">

    <!-- MetisMenu CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>vendor/metisMenu/metisMenu.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/sb-admin-2.css">

    <!-- Morris Charts CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>vendor/morrisjs/morris.css">

    <!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>vendor/font-awesome/css/font-awesome.min.css">



    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<?php
if (isset($this->session->userdata['logged_in'])) {

    header("location: login/user_login_process");
}
?>
<body background="<?php echo base_url();?>images/backgroud-login-new.jpg">
<?php
if (isset($logout_message)) {
    echo "<div class='message'>";
    echo $logout_message;
    echo "</div>";
}
?>
<?php
if (isset($message_display)) {
    echo "<div class='message'>";
    echo $message_display;
    echo "</div>";
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <?php echo form_open('main/user_login_process'); ?>
                    <?php
                    echo "<div class='error_msg'>";
                    if (isset($error_message)) {
                        echo $error_message;
                    }
                    echo validation_errors();
                    echo "</div>";
                    ?>
                    <form role="form" method="post" action="index">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="username" name="username" type="text" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="*******" name="password" type="password" value="">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                </label>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <p> <input type="submit" name="login" value="login" class="btn  btn-success "/>
                                <a href="<?php echo base_url() ?>main/user_registration_show">To SignUp Click Here</a>
                                <?php echo form_close(); ?>
<!--                                <input type="submit" name="signup" value="signup" class="btn  btn-primary "/>-->
                            </p>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script type="text/javascript" src="<?php echo base_url();?>vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap C1ore JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/metisMenu/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>js/sb-admin-2.js"></script>

</body>

</html>
