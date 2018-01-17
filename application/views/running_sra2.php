<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>


<link href="<?php echo base_url(); ?>tooltip/loading.css" rel="stylesheet"/>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">SRA</li>
            </ol>
            <h3 class="page-header">SRA2 Running</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">

            <div class="col-lg-12 uk-margin"></div>


            <div class="loader">
                <p class="h1">Process Queue SRA</p>
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="col-lg-5 col-lg-push-1 "><b>Status :</b></div>
            <div class="col-lg-5 col-lg-pull-3" id="runsra">Wait Queue</div>


        </div>
    </div>

    <?php echo form_open_multipart('Run_sra/formMail/' . $id_project, array('id' => 'myform')); ?>

    <?php echo form_close(); ?>


</div>


<script>
    $(document).ready(function () {

        var j_id = "<?php echo $id_job?>";
        var p_id = "<?php echo $id_project?>";
        var getData = new Array(j_id, p_id);
        var time = 5;
        var interval = null;
        interval = setInterval(function () {
            time--;
            if (time === 0) {
                $.ajax({
                    cache: false,
                    type: "post",
                    datatype: "json",
                    url: "<?php echo site_url('Run_sra/check_script2');?>",
                    data: {data: getData},
                    success: function (data) {

                        var end = $.parseJSON(data);
                        if (end[0] != "0") {
                            time = 5;
                            $("#runsra").html(end[0]);

                        } else {
                            clearInterval(interval);
                            // $("#runsra").html(end[1]);
                            document.getElementById('myform').submit();

                        }

                    },
                    error: function (e) {
                        console.log(e.message);
                    }
                });
            }

        }, 1000);

    });


</script>

