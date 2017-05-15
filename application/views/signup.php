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
    header("location: http://localhost/Metagenomic/main/user_login_process");
}
?>
<body>


<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                        <div class="form-group">
                            <div class='error_msg'><?php echo validation_errors(); ?></div>
                            <?php
                            echo form_open('main/new_user_registration');  ?>
                            <label>Create Username : </label>
                            <br/>
                            <input type="text" name="username" class="form-control"/>
                            <div class='error_msg'>
                            <?php if (isset($message_display)) {
                                echo $message_display;
                            }  ?>
                            </div>
                            <br/>
                            <label>Email :</label>
                            <br/>
                            <input type="email" name="email_value" class="form-control"/>
                            <br/>
                            <br/>
                            <label>Password :</label>
                            <br/>
                            <input type="password" name="password" class="form-control">
                            <br/>
                            <br/>
                            <button class="btn btn-default" type="submit" name="submit">Signup</button>
                            <a href="<?php echo base_url() ?> ">For Login Click Here</a>
                            <?php
                            echo form_close();
                            ?>

                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script type="text/javascript" src="<?php base_url();?>vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap C1ore JavaScript -->
<script type='text/javascript' src="<?php base_url(); ?>vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script type='text/javascript' src="<?php base_url(); ?>vendor/metisMenu/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script type='text/javascript' src="<?php base_url(); ?>js/sb-admin-2.js"></script>

</body>

</html>
