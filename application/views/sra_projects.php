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
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">SRA projects</li>

            </ol>
            <h3 class="page-header">SRA Projects</h3>
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
                    <th>Project_analysis</th>
                    <th>Type</th>
                    <th>SRA</th>


                </tr>
                </thead>
                <tbody>
                <?php foreach ($rs as $r) { ?>
                    <tr class="odd gradeX">
                        <td><?php echo $r["project_name"] ?></td>
                        <td><?php echo $r["project_analysis"] ?></td>
                        <td class="center"><?php echo $r["project_type"] ?></td>
                        <td>  <?php echo anchor("#" . $r['_id'], "SRA", array('class' => 'btn btn-outline btn-success btn-sm', 'id' => $r['_id'], 'onclick' => 'checkfile(this.id); return false')) ?>

                        </td>


                    </tr>
                <?php } ?>

                </tbody>
            </table>
            <!-- /.table-responsive -->

        </div>
    </div>
</div>
<script>

    function checkfile(id) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: "<?php echo site_url('Run_sra/checkFiles/');?>",
            data: {getid: id},
            cache: false,
            success: function (data) {
                if (data == "T") {
                    window.location.href = "<?php echo site_url('Run_sra/index/"+id+"');?>";
                } else {
                    alert("don't have stability.files");

                }
            }
        });
    }

</script>
