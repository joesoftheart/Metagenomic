<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>

<style>

</style>
<div id="page-wrapper">



    <div class="row">

        <div class="col-lg-12">
            <?php //echo "User :" . $username . "   Email :" . $email . "   ID :" . $id;?>
            <?php // echo "Current project" . $current_project ?>
            <?php $controller_name = $this->uri->segment(1); ?>

            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li><?php if ($current_project == null) { ?>Current project<?php } else { ?><a
                        href="<?php echo site_url('projects/index/' . $current_project) ?>">Current
                            project</a><?php } ?></li>
            </ol>
            <h5 class="page-header">Projects</h5>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!--Start Show Project Default = 4 -->
    <div class="row">
        <div class="col-lg-12">
            <?php $i = 0 ?>
            <?php foreach ($rs as $r) { ?>
            <?php if ($i <= 3) { ?>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-primary">
                    <a href="<?php echo site_url('projects/index/' . $r['_id']) ?>" style="color:#FFFFFF">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-2">
                                <i class="fa fa-book fa-3x"></i>
                            </div>
                            <div class="col-xs-10 text-right">
                                <?php
                                $file_read = array('fastq');
                                $project_path = $r['project_path'];
                                $project_name = basename($project_path);
                                $path_owncloud = "../owncloud/data/$username/files/$project_name/input";
                                $show = $this->manage_file->num_file($file_read, $path_owncloud);
                                ?>
                                <div class="huge"><h5 style="color: #FFFFFF"><?php echo $show;?> samples</h5></div>
                                <div style="text-align: left"><?= $r['project_name']; ?></div>
                            </div>
                        </div>
                    </a>
                 </div>
             
            </div>
            </div>

        <?php $i++;  } ?>  
        <?php    }     ?> <!--foreach ($rs as $r) -->
    
    <!-- End Show Project Default = 4 -->

     <!-- Start Hide Project morethan > 4 -->
     
       <div class="row">
       <div class="col-lg-12">

       <div id="toggle-animation"   aria-hidden="true" hidden="hidden">
                
            <?php $j = 0 ?>
            <?php foreach ($rs as $rt) { ?>
            <?php if ($j > 3) { ?>
            <div class="col-lg-3 col-md-6">
                   
                    <div class="panel panel-primary">       
                        <div class="panel-heading">

                            <div class="row">
                             <a href="<?php echo site_url('projects/index/' . $rt['_id']) ?>" style="color:#FFFFFF">
                             <div class="col-xs-2">
                                  <i class="fa fa-book fa-3x"> </i>
                             </div>
                             <div class="col-xs-10 text-right">
                            
                                <?php
                                $file_read = array('fastq');
                                $project_path = $rt['project_path'];
                                $project_name = basename($project_path);
                                $path_owncloud = "../owncloud/data/$username/files/$project_name/input";
                                $show = $this->manage_file->num_file($file_read, $path_owncloud);
                                ?>
                             <div class="huge">
                                    <h5 style="color: #FFFFFF;">
                                       <?php echo $show;?> samples
                                    </h5>
                             </div>
                             <div style="text-align: left">
                                    <?= $rt['project_name']; ?>
                             </div>
                             </div>
                           
                             </a>
                             </div>
                        </div>
                    </div>
            </div> <!-- class="col-lg-3 col-md-6"-->
            <?php }

                  $j++; ?>

             <?php } ?> <!--foreach ($rs as $rt) -->

        </div> <!-- id="toggle-animation"-->
        </div> <!-- class="col-lg-12 -->
        </div> <!-- class="row"-->
   
     <!-- End Hide Project morethan > 4 -->

     

     <!-- Start "show more >>" -->
      <?php if ($j > 4) { ?>
                <button id="text_pro" onclick="toggleTextPro()" href="#toggle-animation"
                        class="uk-button uk-button-link uk-navbar-right" type="button"
                        uk-toggle="target: #toggle-animation; animation: uk-animation-fade">show more >>
                </button><?php } ?>
            <div id="show"> </div>
    <!-- End "show more >>" -->    



    </div>   <!-- class="row" Show Project Default = 4 -->
    
    </div>
</div>
</div>
<!-- /#page-wrapper -->



<!--            <div class="uk-child-width-1-2 uk-child-width-1-5@s uk-grid-match" uk-grid>-->
<!--                --><?php //$i = 0 ?>
<!--                --><?php //foreach ($rs as $r) { ?>
<!--                    --><?php //if ($i <= 4) { ?>
<!--                        <div class="uk-animation-toggle">-->
<!--                            <a href="--><?php //echo site_url('projects/index/' . $r['_id']) ?><!--">-->
<!--                                <div class="uk-card uk-card-default  uk-card-small uk-animation-fade uk-animation-fast uk-card-hover">-->
<!--                                    <div class="uk-card-badge uk-label">10/11/12</div>-->
<!--                                    <div class="uk-card-title uk-text-small uk-text-center">--><?//= $r['project_name']; ?><!--</div>-->
<!--                                    <div class="uk-nav-left" style="margin-left: 10%"><i class="fa fa-file fa-3x"></i></div>-->
<!--                                    <br>-->
<!--                                </div>-->
<!--                            </a>-->
<!--                        </div>-->
<!---->
<!--                        --><?php //$i++;
//                    } ?>
<!--                --><?php //} ?>
<!---->
<!---->
<!--            </div>-->

          
          
              
           
            <!---->
            <!--                    <h5 class="page-header">samples</h5>-->
            <!--                    <div class="uk-child-width-1-2 uk-child-width-1-4@s uk-grid-match" uk-grid >-->
            <!--                        --><?php //$i = 0 ?>
            <!--                        --><?php //foreach ($rs as $r) {  ?>
            <!--                            --><?php //if ($i < 4){  ?>
            <!--                                <div  class="uk-animation-toggle">-->
            <!--                                    <div  class="uk-card uk-card-default uk-card-small uk-animation-fade uk-animation-fast">-->
            <!--                                        <h5 class="uk-card-title uk-text-small">-->
            <? //=$r['project_name'];?><!--</h5>-->
            <!--                                        <div class="uk-nav-center"><i class="fa fa-file fa-3x"></i></div>--><?php //echo $i ?>
            <!--                                        <p class="uk-text-center">Fade</p>-->
            <!--                                    </div>-->
            <!--                                </div>-->
            <!--                            --><?php //$i++; }   ?>
            <!---->
            <!--                        --><?php //} ?>
            <!--                    </div><br>-->
            <!---->
            <!--                    <div id="toggle-animation2" class="uk-child-width-1-2 uk-child-width-1-4@s uk-grid-match" uk-grid aria-hidden="true" hidden="hidden">-->
            <!--                        --><?php //foreach ($rs as $r) {  ?>
            <!--                            --><?php //if ($i >= 4){  ?>
            <!--                                <div  class="uk-animation-toggle">-->
            <!--                                    <div id="toggle-animation2" class="uk-card uk-card-default uk-card-small uk-animation-fade uk-animation-fast">-->
            <!--                                        <h5 class="uk-card-title uk-text-small">-->
            <? //=$r['project_name'];?><!--</h5>-->
            <!--                                        <div class="uk-nav-center"><i class="fa fa-file fa-3x"></i></div>--><?php //echo $i ?>
            <!--                                        <p class="uk-text-center">Fade</p>-->
            <!--                                    </div>-->
            <!--                                </div>-->
            <!--                            --><?php //$i++; }  ?>
            <!---->
            <!--                        --><?php //} ?>
            <!--                    </div>-->
            <!--                    <button id="text_sam" onclick="toggleTextSam()" href="#toggle-animation2" class="uk-button uk-button-link uk-navbar-right" type="button" uk-toggle="target: #toggle-animation2; animation: uk-animation-fade">show more >></button>-->
            <!---->
            <!---->


<script>
    var status_pro = "less_pro";

    function toggleTextPro() {


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

    function toggleTextSam() {


        if (status == "less_sam") {
            document.getElementById("text_sam").innerText = "show less >>";
            status = "more_sam";
        } else if (status == "more_sam") {
            document.getElementById("text_sam").innerText = "show more >>";
            status = "less_sam"
        }
    }
</script>


<script>
    //    $(document).ready(function(){
    //        $("#search").keyup(function(){
    //            if($("#search").val().length>3){
    //                $.ajax({
    //                    type: "POST",
    //                    url: "<?php //echo base_url();?>//main/index",
    //                    cache: false,
    //                    data:'search='+$("#search").val(),
    //                    success: function(response){
    //                        $("#show").html(response);
    //                    },
    //                    error: function(){
    //                        alert('Error while request..');
    //                    }
    //                });
    //            }
    //            return false;
    //        });
    //    });


    //    $(document).ready(function(){
    //
    //        $("#bt").click(function()
    //        {
    //            $.ajax({
    //                type: "POST",
    //                url: "<?php //echo base_url();?>//ajax_test/call_data",
    //                data: {text: $("#text").val()},
    //                dataType: "text",
    //                cache:false,
    //                success:
    //                    function(data){
    //                        $("#show").html(data);
    //                    }
    //            });// you have missed this bracket
    //            return false;
    //        });
    //    });
//    $('.click-project').click(function () {
//        url:
//
//    })
</script>

