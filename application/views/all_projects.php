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
                            <th>Manage</th>
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
                            <td><?php echo anchor("edit_project/edit_project/".$r['_id'],"Edit")?>&nbsp;
                                <?php echo anchor("all_projects/delete_project/".$r['_id'],"Delete")
                                ?><a href="#modal-sections<?php echo $r['_id']?>" uk-toggle> Open</a></td>

                            <?php echo form_open();?>
                            <div id="modal-sections<?php echo $r['_id']?>" uk-modal="center: true">
                                <div class="uk-modal-dialog">
                                    <button class="uk-modal-close-default" type="button" uk-close></button>
                                    <div class="uk-modal-header">
                                        <h2 class="uk-modal-title">Project Name : <?php echo $r['project_name'];?></h2>
                                    </div>
                                    <div class="uk-modal-body">
                                        <p><?php echo $r['project_name'];?><br><?php echo $r['project_title'];?><br> </p>
                                        <p>Share to :</p>
                                        <input type="hidden" name="id_owner" id="id_owner" value="<?php echo $id;?>">
                                        <input type="hidden" name="id_project" id="id_project" value="<?php echo $r['_id'];?> ">
                                        <select name="id_receiver" id="id_receiver">

                                            <?php foreach ($rs_user as $ru) { ?>
                                            <option value="<?php echo $ru['_id']; ?>"><?php echo $ru['user_name'];  ?></option>
                                            <?php   } ?>

                                        </select>
                                    </div>
                                    <div class="uk-modal-footer uk-text-right">
                                        <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                                        <button id="btn_share" class="uk-button uk-button-primary" type="button">Save</button>
                                    </div>
                                </div>
                            </div>
                            <?php form_close() ?>
                        </tr>
                        <?php  } ?>
                        <p id="show_success"></p>
                        </tbody>
                    </table>
                    <!-- /.table-responsive -->

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#btn_share").click(function () {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>all_projects/share_project_to",
                data: {id_owner: $("#id_owner").val(),id_project: $("#id_project").val(),id_receiver: $("#id_receiver").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show_success").html(data);
                    }
            });// you have missed this bracket
            return false;
        });

    });

</script>