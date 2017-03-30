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
            <ul class="breadcrumb">
                <li><a href="#">Home</a><span class="divider">/</span> </li>
                <li><a href="#">library</a><span class="divider">/</span> </li>
                <li><a href="#">data</a><span class="divider">/</span> </li>
            </ul>



        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header">share with you</h4>

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
                <tbody><?php if ($rs != null) { ?>
                <?php foreach ($rs as $r) { ?>
                        <?php if ($r['project_permission'] == "share"){ ?>
                    <tr class="odd gradeX">
                        <td><?php echo $r['project_name'];?></td>
                        <td><?php echo  $r['project_title'];?></td>
                        <td><?php echo $r['project_permission']; ?></td>
                        <td class="center"><?php echo $r['project_type'];?></td>
                        <td class="center"><?php echo "555"; ?></td>
                    </tr>
                        <?php  } ?>
                <?php  } ?>
                <?php  } ?>
                </tbody>
            </table>
            <!-- /.table-responsive -->





            <h4 class="page-header">other people share</h4>

            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example2">
                <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Project Title</th>
                    <th>Permission</th>
                    <th>Type</th>
                    <th>Samples</th>
                </tr>
                </thead>
                <tbody><?php if ($rs != null) { ?>
                    <?php foreach ($rs as $r) { ?>
                        <?php if ($r['project_permission'] == "public") {?>
                        <tr class="odd gradeX">
                            <td><?php echo $r["project_name"]?></td>
                            <td><?php echo $r["project_title"]?></td>
                            <td><?php echo $r["project_permission"] ?></td>
                            <td class="center"><?php echo $r["project_type"]?></td>
                            <td class="center"><?php echo "555" ?></td>
                        </tr>
                            <?php } ?>
                    <?php  } ?>
                <?php  } ?>
                </tbody>
            </table>
            <!-- /.table-responsive -->






        </div>
    </div>


</div>