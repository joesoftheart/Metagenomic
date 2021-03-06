<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Metagenomics</title>


    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/sb-admin-2-custom.css">

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/bootstrap/css/bootstrap.css">

    <!-- MetisMenu CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/metisMenu/metisMenu.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/sb-admin-2.css">

    <!-- Morris Charts CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/morrisjs/morris.css">

    <!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/font-awesome/css/font-awesome.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>vendor/datatables-plugins/dataTables.bootstrap.css">

    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>vendor/datatables-responsive/dataTables.responsive.css">


    <!-- Uikit Design -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/uikit.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#search-box").keyup(function () {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>search_projects/search",
                    data: 'search=' + $(this).val(),
                    beforeSend: function () {
                    },
                    success: function (data) {
                        $("#suggesstion-box").show();
                        $("#suggesstion-box").html(data);
                        $("#search-box").css("background", "#FFF");
                    }
                });
            });
        });

    </script>

    <!-- Material Design fonts -->
    <!--    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">-->
    <!--    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
    <!---->
    <!-- Bootstrap Material Design -->
    <!--    <link rel="stylesheet" type="text/css" href="-->
    <?php //echo base_url(); ?><!--css/bootstrap-material-design.css">-->
    <!--    <link rel="stylesheet0" type="text/css" href="--><?php //echo base_url(); ?><!--css/ripples.min.css">-->
    <!--    <link href="https://fezvrasta.github.io/snackbarjs/dist/snackbar.min.css" rel="stylesheet">-->


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <!
    [endif]-->

</head>


<body>
<nav class="navbar navbar-default navbar-static-top " role="navigation" style="margin-bottom: 0">
    <div id="wrapper">

        <!-- Navigation -->

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo site_url('main') ?>"><i class="fa fa-codepen fa-1x"></i> Amplicon
                Metagenomic</a>
        </div>
        <!-- /.navbar-header -->
        <?php $username = $this->session->userdata["logged_in"]["username"]; ?>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-envelope fa-fw "></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-messages">
                    <?php $data_m = $this->header->getMessage(); ?>
                    <?php foreach ($data_m as $rs_v) { ?>
                        <li>
                            <a href="<?php echo site_url('view_message/view_message/' . $rs_v['_id']) ?>">
                                <div>
                                    <strong><?php echo $rs_v['message_title']; ?></strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div><?php echo $rs_v['message_detail']; ?></div>
                            </a>
                        </li>
                    <?php } ?>
                    <li>
                        <a class="text-center" href="<?php echo site_url('Messages') ?>">
                            <strong>Read All Messages</strong>
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
                <!-- /.dropdown-messages -->
            </li>

            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-tasks">
                    <?php $data_p = $this->header->getProgressProject();
                    foreach ($data_p as $dtp) { ?>
                        <li>
                            <a href="<?php echo site_url('projects/index/' . $dtp['_id']) ?>">
                                <div>
                                    <p>
                                        <strong><?php echo $dtp['project_name'] ?></strong>
                                        <?php $value = $this->header->getProgress($dtp['project_path']); ?>
                                        <span class="pull-right text-muted"><?php echo $this->header->getProgress($dtp['project_path']); ?>
                                            % complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar"
                                             aria-valuenow="40"
                                             aria-valuemin="0" aria-valuemax="100" style="width: <?= $value ?>%">
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                    <?php } ?>
                    <li>
                        <a class="text-center" href="#">
                            <strong>See All Tasks</strong>
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
                <!-- /.dropdown-tasks -->
            </li>
            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <?php $data_n = $this->header->getNotification(); ?>
                    <?php foreach ($data_n as $r) { ?>
                        <li>
                            <a href="<?php echo site_url('view_notification/view_notification/' . $r['_id']) ?>">
                                <div>
                                    <?php if ($r['status'] == 'message') { ?>
                                        <i class="fa fa-envelope fa-fw"></i> <?php echo $r['subject'] ?>
                                    <?php } else if ($r['status'] == 'reboot') { ?>
                                        <i class="fa fa-upload fa-fw"></i> <?php echo $r['subject'] ?>
                                    <?php } else if ($r['status'] == 'delay') { ?>
                                        <i class="fa fa-bolt fa-fw"></i> <?php echo $r['subject'] ?>
                                    <?php } ?>

                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                    <?php } ?>
                    <li>
                        <a class="text-center" href="<?php echo site_url('notification_all') ?>">
                            <strong>See All Alerts</strong>
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
                <!-- /.dropdown-alerts -->
            </li>
            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-user fa-fw"></i>
                    Welcome : <u><?php echo $username ?></u> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="<?php echo site_url('profile') ?>"><i class="fa fa-user fa-fw"></i> User Profile</a>
                    </li>
                    <li><a href="<?php echo site_url('setting') ?>"><i class="fa fa-gear fa-fw"></i> Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="<?php echo site_url('main/logout') ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                        <div class="input-group custom-search-form">
                            <input id="search-box" type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                        <div id="suggesstion-box"></div>

                        <!-- /input-group -->
                    </li>
                     <li>
                        <a href="#project"><i class="fa fa-bar-chart-o fa-fw"></i> Projects<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo site_url('new_projects') ?>">New project</a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('all_projects') ?>">All projects</a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('share_projects') ?>">Share projects</a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('Run_sra/sra_projects') ?>"> SRA</a>
                            </li>
                            
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li>
                        <a href="<?php echo site_url('main') ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                    </li>
                    

                   <!--  <li>
                        <a href="#sample"><i class="fa fa-table fa-fw"></i> Samples<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo site_url('all_samples') ?>">All samples</a>
                            </li>

                            <li>
                                <a href="<?php echo site_url('all_samples') ?>">...</a>
                            </li>
                        </ul>
                    </li> -->
                    <!-- <li>
                        <a href="<?php echo site_url('used_resource') ?>"><i class="fa fa-usb"></i> Used Resources</a>
                    </li> -->
                   <!--  <li>
                        <a href="#stat"><i class="fa fa-files-o fa-fw"></i> Statistics<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="<?php echo site_url('general_statistic') ?>">General statistics</a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('backend_spec') ?>">Backend spec</a>
                            </li>
                        </ul> -->
                        <!-- /.nav-second-level -->
                   <!--  </li> -->
                    <li>
                        <a href="<?php echo site_url('document') ?>"><i class="fa fa-files-o"></i> Documents</a>
                    </li>
                     <li>
                        <a href="<?php echo site_url('#') ?>"><i class="fa fa-comments"></i> Contact us</a>
                    </li>
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
</nav>
