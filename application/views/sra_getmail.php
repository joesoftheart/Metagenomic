<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>


<script>
    function chk_email() {
        jQuery.ajax({
            url: "<?php echo site_url('Run_sra/checkEmail');?>",
            data: 'email=' + $('#emails').val(),
            type: "POST",
            success: function (data) {
                $("#email_status").html(data);
            }

        })

    }
</script>


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
            <h3 class="page-header">SRA Mail OR Download</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">


            <div class="col-lg-12 uk-margin"></div>

            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Send Email OR Download
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills">
                            <li class="active"><a href="#home-email" data-toggle="tab" aria-expanded="true">Semd email
                                    to Ncbi</a>
                            </li>
                            <li class=""><a href="#home-download" data-toggle="tab" aria-expanded="false">Download file
                                    submisstion</a>
                            </li>
                            <li class=""><a href="#home-rerun" data-toggle="tab" aria-expanded="false">Re-run SRA</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <div class="col-lg-12 uk-margin"></div>
                            <div class="tab-pane fade active in" id="home-email">

                                <div class="col-lg-8">
                                    <br/>
                                    <p class="fa fa-send-o"> Limit quota send email :
                                        <?php echo $quota_send_email; ?>
                                    </p>

                                    <?php echo form_open('Run_sra/inGetMail/' . $id_project, array('id' => 'formGetMail')); ?>

                                    <div class="form-group">
                                        <label>Name</label>
                                        <input class="form-control" name="name" placeholder="Name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email User</label>
                                        <input class="form-control" name="email" placeholder="email user"
                                               onblur="chk_email()" id="emails" required>
                                        <span id="email_status"></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Subject</label>
                                        <input class="form-control" name="subject" placeholder="subject" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Message</label>
                                        <textarea class="form-control" name="message" rows="4">
                      </textarea>
                                    </div>
                                    <button type="submit" class="btn btn-outline btn-success">
                                        send mail
                                    </button>

                                    <?php echo form_close(); ?>

                                    <?php
                                    $mes = $this->session->flashdata('message');
                                    if ($mes != '') {
                                        echo '<script>alert("' . $mes . '")</script>';
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class="tab-pane fade" id="home-download">
                                <div class="col-lg-12 uk-margin"></div>
                                <div class="col-lg-8">
                                    <p class="fa fa-download"> &nbsp;
                                        <label> Click button Download file submisstion.xml </label>
                                    </p>
                                    <div class="col-lg-8 uk-margin">
                                        <button type="button" class="btn btn-outline btn-info" id="down_xml">
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <div class="tab-pane fade" id="home-rerun">
                                <div class="col-lg-12 uk-margin"></div>
                                <div class="col-lg-4">
                                    <p class="fa fa-refresh"> &nbsp;
                                        <label> Click button Re-run SRA</label>
                                    </p>
                                    <div class="col-lg-6 uk-margin">
                                        <button type="button" class="btn btn-outline btn-info" id="re_run">
                                            Re-run SRA
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- class="tab-content"-->

                    </div><!-- /.panel-body -->
                </div>
            </div><!-- /.panel -->
        </div> <!-- /.col-lg-12 -->


    </div><!-- class="col-lg-12" -->
</div><!-- row -->


</div>

<script>
    document.getElementById("down_xml").onclick = function () {
        var project = "<?php echo $id_project ?>";
        $.ajax({
            type: "post",
            datatype: "json",
            url: "<?php echo site_url('Run_sra/ChdownXml'); ?>",
            data: {current: "<?php echo $id_project?>"},
            success: function (data) {
                var mes = JSON.parse(data);
                if (mes == "true") {
                    location.href = "<?php echo site_url('Run_sra/loadXml/"+project+"');?>";
                } else {
                    alert(mes);
                }
            }

        });

    }

    document.getElementById("re_run").onclick = function () {
        var id = "<?php echo $id_project ?>";
        window.location.href = "<?php echo site_url('Run_sra/reRun/"+id+"');?>";

    }

</script>



