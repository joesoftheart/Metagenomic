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

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";

                } ?>
                <?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a href="<?php echo site_url('main') ?>">Home</a><?php } ?>
              </li>

                <li class="active">SRA</li>
            </ol>
            <h3 class="page-header">SRA</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">

            <div class="col-lg-12 uk-margin"></div>

            <?php echo form_open_multipart('Run_sra/create/' . $current_project); ?>


            <div class="panel-body">
                <div class="panel-group" id="accordion">

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">1. Create a project
                                    file +
                                </a>
                            </h4>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p> Please fill the infomation of you project (Unknown or inapplicable fields can be
                                    filled in with 'missing')</p>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>UserName:</label>
                                        <input class="form-control" name="username" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-12"></div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Last:</label>
                                        <input class="form-control" name="last" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>First:</label>
                                        <input class="form-control" name="first" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input class="form-control" id="emails" name="email" type="email"
                                               onblur="chk_email()" required>
                                        <span id="email_status"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12"></div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Center:</label>
                                        <input class="form-control" name="center" type="text" required>
                                        <p class="help-block">CENTER your University or Center Name</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Type:</label>
                                        <input class="form-control" name="type" type="text" required>
                                        <p class="help-block">TYPE institute</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Website:</label>
                                        <input class="form-control" name="website" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-12"></div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>ProjectName:</label>
                                        <input class="form-control" name="projectname" type="text" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>ProjectTitle:</label>
                                        <input class="form-control" name="projecttitle" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Description:</label>
                                        <input class="form-control" name="description" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-12"></div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Grant id:</label>
                                        <input class="form-control" name="grantid" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Agency:</label>
                                        <input class="form-control" name="agency" type="text" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Title:</label>
                                        <input class="form-control" name="title" type="text" required>
                                    </div>
                                </div>


                            </div> <!--class="panel-body" -->
                        </div> <!--id="collapse1" class="panel-collapse collapse"-->
                    </div> <!--class="panel panel-info" -->


                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                    2. Create detail the experiment data
                                </a>
                            </h4>
                        </div>
                        <div id="collapse2" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p> Please select the infomation about the different mimarks packages</p>

                                <label class="col-lg-12 ">
                                    <i class="fa fa-plus-circle" id="default_show"> Default</i>
                                    <i class="fa fa-minus-circle" id="default_hide" style="display:none"> Default</i>
                                </label>
                                <div class="col-lg-12 col-lg-push-1" id="default_data" style="display:none">
                                    <input name="optionsRadios" value="miscellaneous" type="radio" required>
                                    miscellaneous
                                </div>

                                <label class="col-lg-12 ">
                                    <i class="fa fa-plus-circle" id="environment_show"> Environment</i>
                                    <i class="fa fa-minus-circle" id="environment_hide" style="display:none">
                                        Environment</i>
                                </label>
                                <div class="col-lg-12 col-lg-push-1" id="environment_data" style="display:none">
                                    <input name="optionsRadios" value="air" type="radio" required> air
                                    <br/>
                                    <input name="optionsRadios" value="host_associated" type="radio" required>
                                    host_associated<br/>
                                    <input name="optionsRadios" value="soil" type="radio" required> soil<br/>
                                    <input name="optionsRadios" value="wastewater" type="radio" required>
                                    wastewater<br/>
                                    <input name="optionsRadios" value="water" type="radio" required> water<br/>
                                    <input name="optionsRadios" value="sediment" type="radio" required> sediment<br/>
                                    <input name="optionsRadios" value="microbial" type="radio" required>
                                    microbial

                                </div>

                                <label class="col-lg-12 ">
                                    <i class="fa fa-plus-circle" id="plant_show"> Plant</i>
                                    <i class="fa fa-minus-circle" id="plant_hide" style="display:none"> Plant</i>
                                </label>
                                <div class="col-lg-12 col-lg-push-1" id="plant_data" style="display:none">
                                    <input name="optionsRadios" value="plant_associated" type="radio" required>
                                    plant_associated

                                </div>

                                <label class="col-lg-12 ">
                                    <i class="fa fa-plus-circle" id="human_show"> Human</i>
                                    <i class="fa fa-minus-circle" id="human_hide" style="display:none"> Human</i>
                                </label>
                                <div class="col-lg-12 col-lg-push-1" id="human_data" style="display:none">
                                    <input name="optionsRadios" value="human_associated" type="radio" required>
                                    human_associated <br/>
                                    <input name="optionsRadios" value="human_gut" type="radio" required> human_gut <br/>
                                    <input name="optionsRadios" value="human_oral" type="radio" required> human_oral
                                    <br/>
                                    <input name="optionsRadios" value="human_skin" type="radio" required> human_skin
                                    <br/>
                                    <input name="optionsRadios" value="human_vaginal" type="radio" required>
                                    human_vaginal <br/>
                                </div>


                            </div><!--class="panel-body" -->
                        </div><!--id="collapse2" class="panel-collapse collapse"-->
                    </div><!--class="panel panel-info" -->


                </div><!--class="panel-group" id="accordion"-->
            </div><!--class="panel-body"-->


            <div class="col-lg-12 uk-margin">
                <button type="submit" class="btn btn-primary">
                    Create project file
                </button>
            </div>

            <?php echo form_close(); ?>


        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('#default_show').click(function () {
            $('#default_data').show();
            $('#default_show').hide();
            $('#default_hide').show();

        });

        $('#environment_show').click(function () {
            $('#environment_data').show();
            $('#environment_show').hide();
            $('#environment_hide').show();
        });

        $('#plant_show').click(function () {
            $('#plant_data').show();
            $('#plant_show').hide();
            $('#plant_hide').show();
        });

        $('#human_show').click(function () {
            $('#human_data').show();
            $('#human_show').hide();
            $('#human_hide').show();
        });

        $('#default_hide').click(function () {
            $('#default_data').hide();
            $('#default_hide').hide();
            $('#default_show').show();

        });

        $('#environment_hide').click(function () {
            $('#environment_data').hide();
            $('#environment_hide').hide();
            $('#environment_show').show();
        });

        $('#plant_hide').click(function () {
            $('#plant_data').hide();
            $('#plant_hide').hide();
            $('#plant_show').show();
        });

        $('#human_hide').click(function () {
            $('#human_data').hide();
            $('#human_hide').hide();
            $('#human_show').show();
        });

    });

</script>

