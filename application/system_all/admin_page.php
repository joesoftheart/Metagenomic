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
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);

} else {
    header("location: login");
}
?>
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css">
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="profile">
    <?php
    echo "Hello <b id='welcome'><i>" . $username . "</i> !</b>";
    echo "<br/>";
    echo "<br/>";
    echo "Welcome to Admin Page";
    echo "<br/>";
    echo "<br/>";
    echo "Your Username is " . $username;
    echo "<br/>";
    echo "Your Email is " . $email;
    echo "<br/>";
    ?>
    <b id="logout"><a href="logout">Logout</a></b>
</div>
<br/>
</body>
</html>