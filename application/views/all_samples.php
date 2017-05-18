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
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li class="active">All sample</li>

            </ol>
            <h1 class="page-header">All samples</h1>
                <div class="row">
                    <div class="col-lg-12">
                                   <a class="btn btn-primary" target="_blank" href="http://localhost/owncloud">upload to owncloud</a>
                    </div>
                </div>
            <br>
            <div class="row">
                <div class="col-lg-12 ">
                    <?php
                    $path_owncloud = "../owncloud/data/" . $username . "/files/";


                    $result_folder = array();
                    $result_file = array();

                    if (is_dir($path_owncloud)) {
                        $select_folder = array_diff(scandir($path_owncloud, 1),array('.','..'));
                        $cdir = scandir($path_owncloud);
                        foreach ($cdir as $key => $value) {

                            if (!in_array($value, array('.', '..'))) {
                                if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                                    $result_folder[$value] = $value;

                                } else {

                                    $result_file[$value] = $value;
                                }
                            }
                        }
                    }


                    ?>








                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Collapsible Accordion Panel Group
                        </div>
                        <!-- .panel-heading -->
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                <?php foreach ($result_folder as $r) { ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne<?php echo $r?>" aria-expanded="false" class="collapsed"><i class="fa fa-folder-open-o fa-1x"></i>  <?=$r; ?></a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne<?php echo $r?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                            <?php
                                            $path_owncloud_files = "../owncloud/data/" . $username . "/files/".$r;
                                            if (is_dir($path_owncloud_files)) {
                                                $select_files_in_folder = array_diff(scandir($path_owncloud_files, 1), array('.', '..'));


                                                foreach ($select_files_in_folder as $list_file) {
                                                    echo "<ul>";
                                                    echo "<li>";
                                                    echo $list_file;
                                                    echo "</li>";
                                                    echo "</ul>";


                                                }
                                            }



                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php  } ?>
                            </div>
                        </div>
                        <!-- .panel-body -->
                    </div>



                </div>


            </div>

        </div>
    </div>


</div>