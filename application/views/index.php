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
                    <h5 class="page-header">Projects</h5>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-9">

                    <div class="uk-child-width-1-2 uk-child-width-1-4@s uk-grid-match" uk-grid >
                        <?php $i = 0 ?>
                        <?php foreach ($rs as $r) {  ?>
                            <?php if ($i < 4){  ?>
                            <div  class="uk-animation-toggle">
                                <a href="<?php echo  site_url('projects/index/'.$r['_id'])?>">
                                <div  class="uk-card uk-card-default uk-card-small uk-animation-fade uk-animation-fast">
                                    <h5 class="uk-card-title uk-text-small"><?=$r['name_project'];?></h5>
                                    <div class="uk-nav-center"><i class="fa fa-file fa-3x"></i></div><?php echo $i ?>
                                    <p class="uk-text-center">Fade</p>
                                </div></a>
                            </div>
                        <?php $i++; }  ?>

                    <?php } ?>
                </div><br>

                    <div id="toggle-animation" class="uk-child-width-1-2 uk-child-width-1-4@s uk-grid-match" uk-grid aria-hidden="true" hidden="hidden">
                        <?php foreach ($rs as $r) {  ?>
                            <?php if ($i >= 4){  ?>
                                <div  class="uk-animation-toggle">
                                    <a href="<?php echo site_url('projects')?>">
                                    <div id="toggle-animation" class="uk-card uk-card-default uk-card-small uk-animation-fade uk-animation-fast">
                                        <h5 class="uk-card-title uk-text-small"><?=$r['name_project'];?></h5>
                                        <div class="uk-nav-center"><i class="fa fa-file fa-3x"></i></div><?php echo $i ?>
                                        <p class="uk-text-center">Fade</p>
                                    </div></a>
                                </div>
                            <?php $i++;}  ?>

                        <?php } ?>
                    </div>
                    <button id="text_pro" onclick="toggleTextPro()" href="#toggle-animation" class="uk-button uk-button-link uk-navbar-right" type="button" uk-toggle="target: #toggle-animation; animation: uk-animation-fade">show more >></button>


                    <h5 class="page-header">samples</h5>
                    <div class="uk-child-width-1-2 uk-child-width-1-4@s uk-grid-match" uk-grid >
                        <?php $i = 0 ?>
                        <?php foreach ($rs as $r) {  ?>
                            <?php if ($i < 4){  ?>
                                <div  class="uk-animation-toggle">
                                    <div  class="uk-card uk-card-default uk-card-small uk-animation-fade uk-animation-fast">
                                        <h5 class="uk-card-title uk-text-small"><?=$r['name_project'];?></h5>
                                        <div class="uk-nav-center"><i class="fa fa-file fa-3x"></i></div><?php echo $i ?>
                                        <p class="uk-text-center">Fade</p>
                                    </div>
                                </div>
                            <?php $i++; }   ?>

                        <?php } ?>
                    </div><br>

                    <div id="toggle-animation2" class="uk-child-width-1-2 uk-child-width-1-4@s uk-grid-match" uk-grid aria-hidden="true" hidden="hidden">
                        <?php foreach ($rs as $r) {  ?>
                            <?php if ($i >= 4){  ?>
                                <div  class="uk-animation-toggle">
                                    <div id="toggle-animation2" class="uk-card uk-card-default uk-card-small uk-animation-fade uk-animation-fast">
                                        <h5 class="uk-card-title uk-text-small"><?=$r['name_project'];?></h5>
                                        <div class="uk-nav-center"><i class="fa fa-file fa-3x"></i></div><?php echo $i ?>
                                        <p class="uk-text-center">Fade</p>
                                    </div>
                                </div>
                            <?php $i++; }  ?>

                        <?php } ?>
                    </div>
                    <button id="text_sam" onclick="toggleTextSam()" href="#toggle-animation2" class="uk-button uk-button-link uk-navbar-right" type="button" uk-toggle="target: #toggle-animation2; animation: uk-animation-fade">show more >></button>





                </div>
                <div class="col-lg-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Notifications Panel
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-comment fa-fw"></i> New Comment
                                    <span class="pull-right text-muted small"><em>4 minutes ago</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                    <span class="pull-right text-muted small"><em>12 minutes ago</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                                    <span class="pull-right text-muted small"><em>27 minutes ago</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-tasks fa-fw"></i> New Task
                                    <span class="pull-right text-muted small"><em>43 minutes ago</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                    <span class="pull-right text-muted small"><em>11:32 AM</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-bolt fa-fw"></i> Server Crashed!
                                    <span class="pull-right text-muted small"><em>11:13 AM</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-warning fa-fw"></i> Server Not Responding
                                    <span class="pull-right text-muted small"><em>10:57 AM</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-shopping-cart fa-fw"></i> New Order Placed
                                    <span class="pull-right text-muted small"><em>9:49 AM</em>
                                    </span>
                                </a>
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-money fa-fw"></i> Payment Received
                                    <span class="pull-right text-muted small"><em>Yesterday</em>
                                    </span>
                                </a>
                            </div>
                            <!-- /.list-group -->
                            <a href="#" class="btn btn-default btn-block">View All Alerts</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
            </div>

        </div>
        </div>
        <!-- /#page-wrapper -->


        <script>
            var status_pro = "less_pro";

            function toggleTextPro()
            {


                if (status_pro == "less_pro") {
                    document.getElementById("text_pro").innerText = "show less >>";
                    status_pro = "more_pro";
                } else if (status_pro == "more_pro") {
                    document.getElementById("text_pro").innerText = "show more >>";
                    status_pro = "less_pro"
                }
            }
        </script>
        <script>
            var status = "less_sam";

            function toggleTextSam()
            {


                if (status == "less_sam") {
                    document.getElementById("text_sam").innerText = "show less >>";
                    status = "more_sam";
                } else if (status == "more_sam") {
                    document.getElementById("text_sam").innerText = "show more >>";
                    status = "less_sam"
                }
            }
        </script>