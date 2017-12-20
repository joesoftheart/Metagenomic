
<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
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
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active"> Graph </li>
            </ol>
            <h3 class="page-header">Visualization : <?=$project?></h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
         
          <div class="col-lg-12 uk-margin"></div>


      <?php 

      sort($data_img);
      for($i=0; $i < sizeof($data_img) ; $i++) {   ?>
      
     <div class="panel-body">
     	 <label><?=$data_img[$i]?></label>
     <div class="row">
     <div class="col-lg-8 col-lg-offset-2">
         
       <?php 

         $src = null;
         $img_source = 'img_user/'.$user.'/'.$project.'/'.$data_img[$i];
         $img_code = base64_encode(file_get_contents($img_source));
        
         if($data_img[$i] == end($data_img)){
            $src = 'data:image/svg+xml;base64,'.$img_code;
         }else{
             $src = 'data:'.mime_content_type($img_source).';base64,'.$img_code;
         }

          echo '<img src="',$src,'"/>';  

         ?>
 	
     </div>
     </div>
     </div>
     <hr class="uk-divider-icon">

     <?php    } ?>




        </div>
    </div>

 <?php

    if($project_analysis == "OTUs"){

      $file_groups_ave_std_summary = "final.opti_mcc.groups.ave-std.summary";
      $file_summary = "final.opti_mcc.summary";
                    
    }else{
                     
       $file_groups_ave_std_summary = "final.tx.groups.ave-std.summary";
       $file_summary = "final.tx.summary";            
    }

 ?>

     <div class="panel-body">
             <!-- Table groups.ave-std.summary -->
        <label><?php echo $file_groups_ave_std_summary; ?></label>
         <div class="row">
            <div class="col-lg-12">
            <div class="table-responsive">
                    
                           
             <table class="table table-striped table-bordered table-hover" style="text-align: center">
                               
                <tbody id="body_tg">

                <?php  
                 for($i =0 ; $i < sizeof($tg_body); $i++ ){

                  	echo "<tr>";
                  	echo "<td>".$tg_body[$i][0]."</td>";
      			  	echo "<td>".$tg_body[$i][1]."</td>";
      				echo "<td>".$tg_body[$i][2]."</td>";
     				echo "<td>".$tg_body[$i][3]."</td>";
      				echo "<td>".$tg_body[$i][4]."</td>";
      				echo "<td>".$tg_body[$i][5]."</td>";
      				echo "<td>".$tg_body[$i][6]."</td>";
      				echo "<td>".$tg_body[$i][7]."</td>";
      				echo "<td>".$tg_body[$i][8]."</td>";
      				echo "<td>".$tg_body[$i][9]."</td>";
      				echo "<td>".$tg_body[$i][10]."</td>";
      				echo "</tr>";

                  }
                ?>
                               
                </tbody>
                </table>
                         
            </div>
            </div>
          </div>    <!-- End Table groups.ave-std.summary -->

        <hr class="uk-divider-icon">

        <label><?php echo $file_summary; ?></label>
        <div class="row">
            <div class="col-lg-12">
            <div class="table-responsive">  
<table class="table table-striped table-bordered dataTable" style="text-align: center">
                                
<tbody id="body_ts">
  <?php                

     for($i=0 ; $i < sizeof($ts_body); $i++ ){

      if($i == 0){

              echo "<tr>";
              echo "<td colspan='2'>".$ts_body[$i][0]."</td>";
              echo "<td>".$ts_body[$i][1]."</td>";
              echo "<td>".$ts_body[$i][2]."</td>";
              echo "<td>".$ts_body[$i][3]."</td>";
              echo "<td>".$ts_body[$i][4]."</td>";
              echo "<td>".$ts_body[$i][5]."</td>";
              echo "<td>".$ts_body[$i][6]."</td>";
              echo  "<td>".$ts_body[$i][7]."</td>";
              echo "<td>".$ts_body[$i][8]."</td>";
              echo "<td>".$ts_body[$i][9]."</td>";
              echo "</tr>";

      }else{
            echo "<tr>";
            echo "<td>".$ts_body[$i][0]."</td>";
            echo "<td>".$ts_body[$i][1]."</td>";
            echo "<td>".$ts_body[$i][2]."</td>";
            echo "<td>".$ts_body[$i][3]."</td>";
            echo "<td>".$ts_body[$i][4]."</td>";
            echo "<td>".$ts_body[$i][5]."</td>";
            echo "<td>".$ts_body[$i][6]."</td>";
            echo "<td>".$ts_body[$i][7]."</td>";
            echo "<td>".$ts_body[$i][8]."</td>";
            echo "<td>".$ts_body[$i][9]."</td>";
            echo "<td>".$ts_body[$i][10]."</td>";
            echo "</tr>";

      }

   }
              
 ?>
  </tbody>
</table>
                       

            </div>
            </div>
            <div class="col-lg-12 uk-margin"></div>
                 <center>
                  <input  class="btn btn-outline btn-info" value="Download all zip" id="zipall"> 
                 </center> 
         </div><!-- End Table file_summary -->   
    </div> 



</div>


<script type="text/javascript">  

document.getElementById("zipall").onclick = function(){

    $.ajax({ 
          type:"post",
          datatype:"json",
          url:"<?php echo base_url('chkdir'); ?>",
          data:{current:"<?=$id_project?>"},
             success:function(data){
                var dir = JSON.parse(data); 
                if(dir == "TRUE"){
                   location.href="<?php echo site_url();?>dowfile/<?=$id_project?>";           
                }else{
                    alert("FALSE");
                }
              
            }
                   
     });

};
</script>

        	
















