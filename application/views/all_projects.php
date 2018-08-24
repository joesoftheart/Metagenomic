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
                <li class="active">All projects</li>

            </ol>
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
                    <th>Status</th>
                    <th>Manage</th>

                </tr>
                </thead>
                <tbody>
                <?php 
                    $count = 0;
                    foreach ($rs as $r) { ?>
                    <tr class="odd gradeX">
                        <td><?php echo $r["project_name"] ?></td>
                        <td><?php echo $r["project_title"] ?></td>
                        <td><?php echo $r["project_permission"] ?></td>
                        <td class="center"><?php echo $r["project_type"] ?></td>
                        <td class="center"><?php echo  $r["project_num_sam"]; ?></td>
                        
                        <td>
                             <?php if($count == 0){ ?>
                             <div class="progress progress-striped active">
                                 <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                 <span class="text-muted" style="color:#FFFFFF">complete</span>
                                 </div>
                            </div>
                            <?php  $count++; }else{ ?>

                                 <div class="progress progress-striped active">
                                 <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">
                                 <span class="pull-right text-muted" style="color:#FFFFFF">80%</span>
                                 </div>
                            </div>


                            <?php   } ?>

                        </td>
                        <td align="center"><?php echo anchor("edit_project/edit_project/" . $r['_id'], "Edit", array('class' => 'btn btn-default btn-sm')) ?>
                            &nbsp;
                            <?php echo anchor("all_projects/delete_project/" . $r['_id'], "Delete", array('class' => 'btn btn-default btn-sm'))
                            ?>&nbsp;<a href="#modal-sections<?php echo $r['_id'] ?>" class="btn btn-default btn-sm"
                                       uk-toggle> Share</a>

                             &nbsp;<button  data-toggle="modal" data-target="#myModal" class="btn btn-default btn-sm" > Upload</button>
                        </td>


                        <div id="modal-sections<?php echo $r['_id'] ?>" uk-modal="center: true">
                            <form action="all_projects/share_project_to" method="post">
                                <div class="uk-modal-dialog">
                                    <button class="uk-modal-close-default" type="button" uk-close></button>
                                    <div class="uk-modal-header">
                                        <h5 class="uk-text-bold">Project Name : <?php echo $r['project_name']; ?></h5>
                                    </div>
                                    <div class="uk-modal-body">
                                        <div class="uk-grid-divider uk-child-width-expand@s" uk-grid>
                                            <div><p>Share : </p><span class="uk-label uk-label-success"><i
                                                            class="fa fa-folder-open-o fa-2x"></i> <?php echo $r['project_name']; ?></span>
                                            </div>
                                            <div><i class="fa fa-arrow-right fa-2x fa-align-center"></i></div>
                                            <div><p>To :</p><select class="uk-select" name="id_receiver"
                                                                    id="id_receiver">

                                                    <?php foreach ($rs_user as $ru) { ?>
                                                        <option value="<?php echo $ru['_id']; ?>"><?php echo $ru['user_name']; ?></option>
                                                    <?php } ?>

                                                </select></div>
                                        </div>
                                        <input type="hidden" name="id_owner" id="id_owner" value="<?php echo $id; ?>">
                                        <input type="hidden" name="id_project" id="id_project"
                                               value="<?php echo $r['_id']; ?>">

                                    </div>
                                    <div class="uk-modal-footer uk-text-right">
                                        <button class="uk-button uk-button-default uk-modal-close" type="button">
                                            Cancel
                                        </button>
                                        <button id="btn_share" class="uk-button uk-button-primary" type="submit">Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </tr>
                <?php } ?>
                <p id="show_success"></p>
                </tbody>
            </table>
            <!-- /.table-responsive -->

        </div>
    </div>
</div>

<script>
    //    $(document).ready(function () {
    //        $("#btn_share").click(function () {
    //            $.ajax({
    //                type: "POST",
    //                url: "<?php //echo base_url();?>//all_projects/share_project_to",
    //                data: {id_owner: $("#id_owner").val(),id_project: $("#id_project").val(),id_receiver: $("#id_receiver").val()},
    //                dataType: "text",
    //                cache:false,
    //                success:
    //                    function(data){
    //                        $("#show_success").html(data);
    //                    }
    //            });// you have missed this bracket
    //            return false;
    //        });
    //
    //    });

</script>

    

                           
                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="myModalLabel">Upload files</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div id="showup">
                                                 <input type="file" >
                                            </div>
                                            <div id="showdown" style="display:none;">
                                                 <center>
                                                     <img src="<?php echo base_url('tooltip/Spinner.gif');?>" width="150px" height="150px">
                                                 
                                                 <p> Uploading your data</p>
                                                 <p> Do not close this window !!</p>

                                                 </center>
                                                  
                                            </div>

                                            <div id="showsuccess" style="display:none;">
                                                 <center>
                                                    <button type="button" class="btn btn-info btn-circle btn-xl"><i class="fa fa-check"></i></button>
                                                 
                                                 <p style="margin-top: 20px;"> Upload Success</p>
                                                 </center>
                                                  
                                            </div>
                                           
                                        </div>
                                        <div class="modal-footer">

                                            <div id="btnup">

                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button type="button" id="upload_sample" class="btn btn-primary">Upload</button>
                                            </div>

                                            <div  id="btndown" style="display:none;">

                                               <button id="done_up" type="button" class="btn btn-default" data-dismiss="modal">Done</button>
                                              
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->

<script type="text/javascript">
    

$(document).ready(function(){

     $('#upload_sample').click(function(){

        $('#showup').hide();
        $('#showdown').show();

         var time = 3;
         var interval = null;
         interval = setInterval(function(){   
         time--;
            if(time === 0){

                clearInterval(interval);
                $('#btnup').hide();
                 $('#showdown').hide();
                $('#showsuccess').show();
                $('#btndown').show();         
            }

          },1000);
     });


    $('#done_up').click(function(){

         $('#showsuccess').hide();
         $('#btndown').hide(); 
         $('#showup').show(); 
         $('#btnup').show(); 

     });      
       

});

</script>
                      