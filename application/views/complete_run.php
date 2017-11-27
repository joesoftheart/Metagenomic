<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>
<div id="page-wrapper">
    <div class="row">
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>
    <div class="row">


    <div class="col-lg-4 col-sm-6 text-center mb-4">
<!--        <a href="--><?php //echo base_url();?><!--complete_run/on_check_remove_progress/--><?php //echo $current_project?><!--"> <img class="img-circle" src="--><?php //echo base_url() ?><!--images/restart2.png" alt=""></a>-->
    </div>
    <div class="col-lg-4 col-sm-6 text-center mb-4">
        <a href="<?php echo base_url();?>graph_result/index/<?php echo $current_project?>"><img class="img-circle" src="<?php echo base_url() ?>images/graph_result2.png" alt=""></a>

    </div>
    <div class="col-lg-4 col-sm-6 text-center mb-4">
        <a href="<?php echo base_url();?>report_pdf/fpdf/<?php echo $current_project?>"><img class="img-circle" src="<?php echo base_url() ?>images/report2.png" alt=""></a>

    </div>
    </div>





</div>