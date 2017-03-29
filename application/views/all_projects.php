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
            <h1 class="page-header">All Projects</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">

                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Project Title</th>
                            <th>Permission</th>
                            <th>Type</th>
                            <th>Samples</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rs as $r) { ?>
                        <tr class="odd gradeX">
                            <td><?php echo $r["project_name"]?></td>
                            <td><?php echo  $r["project_title"]?></td>
                            <td><?php echo $r["project_permission"] ?></td>
                            <td class="center"><?php echo $r["project_type"]?></td>
                            <td class="center"><?php echo "555" ?></td>
                        </tr>
                        <?php  } ?>
                        </tbody>
                    </table>
                    <!-- /.table-responsive -->

        </div>
    </div>
</div>