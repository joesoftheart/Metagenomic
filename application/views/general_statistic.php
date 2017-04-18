<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
} else {
    header("location: main/login");
} ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php echo "User :" . $username . "   Email :" . $email . "   ID :" . $id;?>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li>Statistics</li>
                <li class="active">General statistic</li>

            </ol>
        </div>
    </div>

    <div class="row">

        <?php

        $rs_u = 0;
        foreach ($rs_users as $rs_user) {
            $rs_u++;
        }


        $rs_p = 0;
        foreach ($rs_projects as $rs_pro) {
            $rs_p++;
        }


        $rs_t = 0;
        foreach ($rs_ticket as $rs_tic) {
            $rs_t++;
        }





        // $num_users = conut();?>
        <div class="col-lg-12">
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-users fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $rs_u; ?></div>
                                    <div>Users</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php
                $file_read = array( 'fastq','jpg');
                $path_owncloud = "../owncloud/data/" . $username . "/files/";
                $cdir = array();
                $result_folder = array();
                $result_files = array();
                if (is_dir($path_owncloud)) {
                    $select_folder = array_diff(scandir($path_owncloud, 1), array('.', '..'));
                    $cdir = scandir($path_owncloud);

                    foreach ($cdir as $key => $value) {
                        if (!in_array($value, array('.', '..'))) {
                            if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                                $result_folder[$value] = $value;
                            } else {


                                $result_files[$value] = $value;
                            }


                        }
                    }
                }


                $num_folder = count($result_folder);
                $num_files = count($result_files);

                $count_files = 0;
                if ($cdir != null) {
                    foreach ($cdir as $key => $value) {
                        if (!in_array($value, array('.', '..'))) {
                            if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                                $file_in_dir = scandir($path_owncloud . "/" . $value);

                                foreach ($file_in_dir as $key => $value) {
                                    $type = explode('.', $value);
                                    $type = array_reverse($type);
                                    if (in_array($type[0], $file_read)) {
                                        $count_files++;
                                    }
                                }


                            }

                        }
                    }
                }


                // echo "Test :".$count_test . "Num file :".$num_files;
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-folder-open fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $rs_p?></div>
                                    <div>Projects</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>

                <?php














                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-file-text-o fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">
                                        <?php echo $count_files; ?></div>
                                    <div>Samples </div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-support fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $rs_t; ?></div>
                                    <div>Support Tickets!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>