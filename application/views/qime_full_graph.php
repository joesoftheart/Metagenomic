<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);

} else {
    header("location: main/login");
}

?>

<script src="<?php echo base_url('js/jquery-3.2.1.js'); ?>"></script>


<!-- Bootstrap Core CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/bootstrap/css/bootstrap.css">


<!-- Custom Fonts -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/font-awesome/css/font-awesome.min.css">

<!-- Uikit Design -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/uikit.css">

<!-- Bootstrap C1ore JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/bootstrap/js/bootstrap.min.js"></script>


<!-- <button type="button" id="btnAdd">Add new Rows </button>
<button type="button" id="btnRemoveRow">Remove Rows</button> -->

<!-- <button type="button" id="btnAddCol">Add new Column</button>
<button type="button" id="btnRemoveCol">Remove Column</button> -->


<nav class="navbar navbar-default  navbar-static-top " role="navigation" style="margin-bottom: 0">
    <div id="wrapper">

        <!-- Navigation -->

        <div class="navbar-header">

            <label class="navbar-brand"><i class="fa fa-codepen fa-1x"></i> Amplicon Metagenomic</label>
        </div>
        <!-- /.navbar-header -->

    </div>
</nav>
<div class="col-lg-12 uk-margin"></div>


<div class="container-fluid">

 <iframe width="100%" height="100%" src="<?php echo base_url('cdotu/index.html');?>">
     
 </iframe>  

</div>


                   
   

