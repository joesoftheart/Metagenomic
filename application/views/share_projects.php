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
        <div class="col-lg-12"><br>
            <?php $controller_name = $this->uri->segment(1); ?>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li class="active">Share projects</li>

            </ol>



        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header">your share</h4>

            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                <tr>
                    <th>Owner Name</th>
                    <th>Project Name<</th>
                    <th>Receiver Name</th>
                    <th>Manage</th>
                </tr>
                </thead>
                <tbody><?php if ($rs != null) { ?>
                <?php foreach ($rs as $r) { ?>

                    <tr class="odd gradeX">
                        <td><?php echo $r['owner_name'];?></td>
                        <td><?php echo  $r['project_name'];?></td>
                        <td><?php echo $r['receiver_name'] ?></td>
                        <td class="center"><?php echo anchor('share_projects/delete_your_share/'.$r['id_share'],"Delete",array('class' => 'btn btn-default btn-sm')); ?></td>
                    </tr>
                        <?php  } ?>
                <?php  } ?>
                </tbody>
            </table>
            <!-- /.table-responsive -->





<!--            <h4 class="page-header">other people share</h4>-->
<!---->
<!--            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example2">-->
<!--                <thead>-->
<!--                <tr>-->
<!--                    <th>Project Name</th>-->
<!--                    <th>Project Title</th>-->
<!--                    <th>Permission</th>-->
<!--                    <th>Type</th>-->
<!--                    <th>Samples</th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody>--><?php //if ($rs != null) { ?>
<!--                    --><?php //foreach ($rs as $r) { ?>
<!--                        --><?php //if ($r['project_permission'] == "public") {?>
<!--                        <tr class="odd gradeX">-->
<!--                            <td>--><?php //echo $r["project_name"]?><!--</td>-->
<!--                            <td>--><?php //echo $r["project_title"]?><!--</td>-->
<!--                            <td>--><?php //echo $r["project_permission"] ?><!--</td>-->
<!--                            <td class="center">--><?php //echo $r["project_type"]?><!--</td>-->
<!--                            <td class="center">--><?php //echo "555" ?><!--</td>-->
<!--                        </tr>-->
<!--                            --><?php //} ?>
<!--                    --><?php // } ?>
<!--                --><?php // } ?>
<!--                </tbody>-->
<!--            </table>-->
<!--            <!-- /.table-responsive -->






        </div>
    </div>


</div>