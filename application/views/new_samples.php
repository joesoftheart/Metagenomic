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
            <br>
            <ul class="breadcrumb">
                <li><a href="#">Home</a> <span class="divider">/</span></li>
                <li><a href="#">Library</a> <span class="divider">/</span></li>
                <li class="active">Data</li>
            </ul>
            <h1 class="page-header">New Projects</h1>
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
                    $select_folder = array_diff(scandir($path_owncloud, 1),array('.','..'));
                    ?>




                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Collapsible Accordion Panel Group
                        </div>
                        <!-- .panel-heading -->
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                <?php foreach ($select_folder as $r) { ?>
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
                                            $select_files_in_folder = array_diff(scandir($path_owncloud_files, 1),array('.','..'));


                                            foreach ($select_files_in_folder as $list_file){
                                                echo "<ul>";
                                                echo "<li>";
                                                echo $list_file;
                                                echo "</li>";
                                                echo "</ul>";


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