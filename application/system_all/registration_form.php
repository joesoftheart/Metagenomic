<style>
    #main{
        width:960px;
        margin:50px auto;
        font-family:raleway;
    }

    span{
        color:red;
    }

    h2{
        background-color: #FEFFED;
        text-align:center;
        border-radius: 10px 10px 0 0;
        margin: -10px -40px;
        padding: 30px;
    }

    #login{

        width:300px;
        float: left;
        border-radius: 10px;
        font-family:raleway;
        border: 2px solid #ccc;
        padding: 10px 40px 25px;
        margin-top: 70px;
    }

    input[type=text],input[type=password], input[type=email]{
        width:99.5%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ccc;
        padding-left: 5px;
        font-size: 16px;
        font-family:raleway;
    }

    input[type=submit]{
        width: 100%;
        background-color:#FFBC00;
        color: white;
        border: 2px solid #FFCB00;
        padding: 10px;
        font-size:20px;
        cursor:pointer;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    #profile{
        padding:50px;
        border:1px dashed grey;
        font-size:20px;
        background-color:#DCE6F7;
    }

    #logout{
        float:right;
        padding:5px;
        border:dashed 1px gray;
        margin-top: -168px;
    }

    a{
        text-decoration:none;
        color: cornflowerblue;
    }

    i{
        color: cornflowerblue;
    }

    .error_msg{
        color:red;
        font-size: 16px;
    }

    .message{
        position: absolute;
        font-weight: bold;
        font-size: 28px;
        color: #6495ED;
        left: 262px;
        width: 500px;
        text-align: center;
    }
</style>
<html>
<?php
if (isset($this->session->userdata['logged_in'])) {
    header("location: http://localhost/Metagenomic/login/user_login_process");
}
?>
<head>
    <title>Registration Form</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css">
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="main">
    <div id="login">
        <h2>Registration Form</h2>
        <hr/>
        <?php
        echo "<div class='error_msg'>";
        echo validation_errors();
        echo "</div>";
        echo form_open('login/new_user_registration');
        echo form_label('Create Username : ');
        echo"<br/>";
        echo form_input('username');
        echo "<div class='error_msg'>";
        if (isset($message_display)) {
            echo $message_display;
        }
        echo "</div>";
        echo"<br/>";
        echo form_label('Email : ');
        echo"<br/>";
        $data = array(
            'type' => 'email',
            'name' => 'email_value'
        );
        echo form_input($data);
        echo"<br/>";
        echo"<br/>";
        echo form_label('Password : ');
        echo"<br/>";
        echo form_password('password');
        echo"<br/>";
        echo"<br/>";
        echo form_submit('submit', 'Sign Up');
        echo form_close();
        ?>
        <a href="<?php echo base_url() ?> ">For Login Click Here</a>
    </div>
</div>
</body>
</html>