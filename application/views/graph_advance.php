
<?php
if (isset($this->session->userdata['logged_in'])) {
  $username = ($this->session->userdata['logged_in']['username']);
  $email = ($this->session->userdata['logged_in']['email']);
  $id = ($this->session->userdata['logged_in']['_id']);
  $current_project = ($this->session->userdata['current_project']);
} else {
  header("location: main/login");
} ?>


<!-- 
<link href="<?php echo base_url(); ?>vendor/bootstrap_sort/bootstrap-sortable.css" rel="stylesheet" type="text/css"> -->

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
      <h3 class="page-header">Visualization : <?=$project?> : <?=$project_analysis?></h3>
    </div>
    <!-- /.col-lg-12 -->
  </div>

  <!-- /.row -->
  <div class="row">
    <div class="col-lg-12">

      <div class="col-lg-12 uk-margin"></div>

<!-- 
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

     <?php    } ?> -->
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
<style type="text/css">
.scrollit {
  overflow:scroll;
  height:350px;
}
#table {
  width: 100%;
}
#thead, #tbody, #tr, #td, #th { display: block; }
#tr:after {
  content: ' ';
  display: block;
  visibility: hidden;
  clear: both;

}
#thead th {
  height: 50px;
  width: 19.7%;
  float: left;
  text-align: center;
  /*font-size: 13px;*/
  /*white-space: nowrap;*/
}
#tbody {
  height: 350px;
  overflow-y: auto;
}
#tbody  td{
  width: 20%;
  float: left;
}


#tbody  td, thead th {
  width: 20%;

}

</style>




<!-- .panel-heading -->
        <div class="panel-body">
          <div class="panel-group" id="accordion">

               
            <div class="panel panel-info">
              <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">Taxonomy classification </a>
                  </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                  <div class="panel-body">
                  
                    <p class="fa fa-bookmark"> &nbsp;&nbsp;Phylum level </p> 
                    <br/>

                    <center>
                       <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/taxonomy_classification/Abun.png');?>"  width="650px" height="650px" >    
                    </center>

                    <br>
                    <p class="fa fa-bookmark">&nbsp;&nbsp; Genus level </p>
                      <center>
                       <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/taxonomy_classification/heartmap.png');?>"  width="850px" height="650px" > 
                      </center>
                    <br>

                      <p class="fa fa-bookmark">
                         &nbsp;&nbsp;Taxonomy summary
                      </p> 
                      <br/> 

                      <style>
                        #krona img{
                           opacity: 0.6;
                           transition: 0.3s;
                           display: inline-block;
                           cursor: pointer; 
                        }
                       
                        #krona img:hover{
                             
                            opacity: 1 ;
                        }
                      </style>

                        &nbsp;&nbsp; <img src="<?php echo base_url('krona_img/krona.png');?>" width="150px" height="100px">
                        <a  id="krona" href="<?php echo site_url('run_advance/krona/'.$user.'/'.$project);?>" target="_blank">
                           <img  src="<?php echo base_url('krona_img/butoncolor.png');?>" width="70px" height="70px">
                        </a> 

                  </div>
              </div>
            </div>

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="false">Alpha diversity analysis</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                         <div class="panel-body">

                          <p class="fa fa-bookmark"> &nbsp;&nbsp;Statistical analysis summary </p> 

                          <div class="table-responsive">     

                              <table class="table table-striped table-bordered table-hover" width="500px" style="text-align: center">

                               <tbody id="body_tg">

                          <?php  
                            for($i =0 ; $i < sizeof($tg_body); $i++ ){


                            if($i == 0){
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
                            }else{

                              echo "<tr>";
                              echo "<td>".$tg_body[$i][0]."</td>";
                              echo "<td>".$tg_body[$i][1]."</td>";
                              echo "<td>".number_format($tg_body[$i][2],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][3],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][4],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][5],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][6],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][7],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][8],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][9],4)."</td>";
                              echo "<td>".number_format($tg_body[$i][10],4)."</td>";
                              echo "</tr>";
                            }

                           } ?>

                             </tbody>
                             </table>
                          </div>

                          <br/>
                          <p class="fa fa-bookmark"> &nbsp;&nbsp;Box plot </p><br/> 
                            <center> 

                            <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/alpha_diversity_analysis/Alpha.png');?>" width="450px" height="450px" >  
                            </center>  
                          <br>
                          <p class="fa fa-bookmark">&nbsp;&nbsp; Rarefaction curve </p><br/>
                          <center>
                            <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/alpha_diversity_analysis/Rare.png');?>" width="450px" height="450px" >  
                           </center>               
                         </div>
                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                         <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="false">Beta diversity analysis</a>
                      </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">
                            <p class="fa fa-bookmark"> &nbsp;&nbsp;Venn diagram </p> <br/>
                            <center>
                             <img src="<?php echo base_url( 'data_report_mothur/'.$user.'/'.$project.'/beta_diversity_analysis/sharedsobs.png');?>" width="550px" height="550px" >
                            </center>
                            <br/>
                            <p class="fa fa-bookmark"> &nbsp;&nbsp;Statistical analysis summary</p> 

                            <div class="table-responsive">  

                            <table id="dataTables-example"  class="table table-bordered sortable no-footer"  style="text-align: center;">
                              <thead>                    
                                 <tr id="body_ts">
                               <?php                

                                for($i=0 ; $i < sizeof($ts_body); $i++ ){

                                  if($i == 0){

                                     // echo "<th>";
                                     echo "<th colspan='2' >".$ts_body[$i][0]."</th>";
                                     echo "<th>".$ts_body[$i][1]."</th>";
                                     echo "<th>".$ts_body[$i][2]."</th>";
                                     echo "<th>".$ts_body[$i][3]."</th>";
                                     echo "<th>".$ts_body[$i][4]."</th>";
                                     echo "<th>".$ts_body[$i][5]."</th>";
                                     echo "<th>".$ts_body[$i][6]."</th>";
                                     echo "<th>".$ts_body[$i][7]."</th>";
                                     echo "<th>".$ts_body[$i][8]."</th>";
                                     echo "<th>".$ts_body[$i][9]."</th>";
                                     echo "</tr></thead>";

                                   }else{
                                      echo "<tr>";
                                      echo "<td>".$ts_body[$i][0]."</td>";
                                      echo "<td>".$ts_body[$i][1]."</td>";
                                      echo "<td>".number_format($ts_body[$i][2],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][3],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][4],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][5],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][6],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][7],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][8],4)."</td>";
                                      echo "<td>".number_format($ts_body[$i][9],4)."</td>";
                                      echo "<td>".number_format((float)$ts_body[$i][10],4)."</td>";
                                      echo "</tr>";
                                   }

                                } ?>
                               </tr>
                               </table>
                            </div> 
                            <br/>
                            <p class="fa fa-bookmark"> &nbsp;&nbsp;Dendrogram </p>
                            <br/> 

                                <!-- Graph Tree -->
                                   <?php  foreach ($tree as  $val_tree) {  ?>
                                      <center>
                                          <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/beta_diversity_analysis/'.$val_tree);?>"  width="450px" height="450px" >
                                      <br/>  
                                      <?php  echo $val_tree; ?>  
                                      </center> 

                                  <?php }  ?>
                                <!-- / Graph Tree -->
                            <br/>
                            <p class="fa fa-bookmark"> &nbsp;&nbsp;Ordination method</p>
                            <br/>  

                           <!-- check graph PCoA -->
                           <?php if($pcoa != null){  ?>

                              <!-- Graph PCoA -->
                              &nbsp;&nbsp;&nbsp;<p class="fa fa-bookmark"> &nbsp;&nbsp;PCoA </p>
                              <?php  foreach ($pcoa as $val_pcoa) {  ?>
                                      <center>
                                          <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/beta_diversity_analysis/'.$val_pcoa);?>"  width="450px" height="450px" >
                                      <br/>  
                                      <?php  echo $val_nmd; ?>  
                                      </center> 
                              <?php }  ?>
                               <!-- /Graph PCoA -->

                              <!-- Graph NMDs_Biplot -->
                               &nbsp;&nbsp;&nbsp;<p class="fa fa-bookmark"> &nbsp;&nbsp;Biplot </p>
                                   <?php  foreach ($biplot as $val_biplot) {  ?>
                                      <center>
                                          <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/beta_diversity_analysis/'.$val_biplot);?>"  width="450px" height="450px" >
                                      <br/>  
                                      <?php  echo $val_biplot; ?>  
                                      </center> 

                                 <?php }  ?>
                                 <!-- / Graph NMDs_Biplot -->
                              
                                  
                             <!-- check graph NMD -->
                            <?php }else if($nmd != null){  ?>

                                <!-- Graph NMD -->
                               &nbsp;&nbsp;&nbsp; <p class="fa fa-bookmark"> &nbsp;&nbsp;NMD </p>
                                <?php  foreach ($nmd as $val_nmd) {  ?>
                                      <center>
                                          <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/beta_diversity_analysis/'.$val_nmd);?>"  width="450px" height="450px" >
                                      <br/>  
                                      <?php  echo $val_nmd; ?>  
                                      </center>
                                <?php }  ?>
                                <!-- / Graph NMD --> 


                                <!-- Graph NMDs_Biplot -->
                                &nbsp;&nbsp;&nbsp;<p class="fa fa-bookmark"> &nbsp;&nbsp;Biplot </p>
                                   <?php  foreach ($biplot as $val_biplot) {  ?>
                                      <center>
                                          <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/beta_diversity_analysis/'.$val_biplot);?>"  width="450px" height="450px" >
                                      <br/>  
                                      <?php  echo $val_biplot; ?>  
                                      </center> 

                                  <?php }  ?>
                                 <!-- / Graph NMDs_Biplot -->
      
                             <?php  } ?>
           
                        </div>
                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                         <a data-toggle="collapse" data-parent="#accordion" href="#collapsefour" class="collapsed" aria-expanded="false">Optional output</a>
                      </h4>
                    </div>
                    <div id="collapsefour" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">

                          <p class="fa fa-bookmark"> &nbsp;&nbsp;PICRUst and STAMP</p>

                          <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="table"  >

                             <?php   $path = FCPATH."owncloud/data/".$user."/files/".$project."/output/myResultsPathwayL2.tsv";

                            if(file_exists($path)){

                                $myfile = fopen($path,'r') or die ("Unable to open file");
                                $row = 0;
                              while(($lines = fgets($myfile)) !== false){
                                $line = explode("\t", $lines);
                                if($row == 0){
                                  echo " <thead id='thead'> <tr id='tr'>"; 
                                  echo "<th id='th'>".$line[0]."</th>".
                                  "<th id='th'>".$line[1]. "</th>".
                                  "<th id='th'>".str_replace('1',$line[2] ,$line[6] ). "</th>".
                                  "<th id='th'>".str_replace('2',$line[3] ,$line[7] ). "</th>".
                                  "<th id='th'>".$line[9]. "</th>";
                                  echo "</tr></thead><tbody id='tbody'>";
                                }else{
                                    echo "<tr id='tr'>"; 
                                    echo "<td id='td' class='filterable-cell' align='left'>".$line[0]."</td>".
                                    "<td id='td' class='filterable-cell' align='left'>".$line[1]. "</td>".
                                    "<td id='td' class='filterable-cell' align='center'>".number_format(floatval($line[6]),3,'.',''). "</td>".
                                    "<td id='td' class='filterable-cell' align='center'>".number_format(floatval($line[7]),3,'.',''). "</td>".
                                    "<td id='td' class='filterable-cell' align='center'>".sprintf("%.3e",$line[9]). "</td>";
                                      echo "</tr>";
                                }
                               $row++;
                             }    
                                fclose($myfile);  
                            } ?>

                              </tbody>
                              </table>

                               <br/>
                               <p class="fa fa-bookmark"> &nbsp;&nbsp;Bar plot</p><br/>
                               <center>
                               <img src="<?php echo base_url('data_report_mothur/'.$user.'/'.$project.'/optional_output/bar_plot_STAMP.png');?>"  width="650px" height="650px">
                               </center>    
                        </div>
           
                        </div>
                    </div>
                </div>

          </div>
        </div>
 <!-- .panel-body -->



<div class="panel-body">

 <!--  <label>myResultsPathwayL2.tsv</label> -->
  <div class="row">
    <div class="col-lg-12">
      
      
    </div>
  </div>    <!-- End Table  -->




<!-- <hr class="uk-divider-icon"> -->
<!-- Table groups.ave-std.summary -->
<!-- <label><?php echo $file_groups_ave_std_summary; ?></label> -->
<div class="row">
  <div class="col-lg-12">
    
  </div>
</div>    <!-- End Table groups.ave-std.summary -->

<!-- <hr class="uk-divider-icon">

<label><?php echo $file_summary; ?></label> -->
<div class="row">
  <div class="col-lg-12">
    
 </div>
      <div class="col-lg-12 uk-margin"> </div>
      <center>
          <input  class="btn btn-outline btn-info" value="Download all zip" id="zipall">
      </center> 
</div><!-- End Table file_summary -->   


</div> 
<!-- "/panel-body" -->
</div>
<!-- "/page-wrapper" -->



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



<script src="<?php echo base_url(); ?>vendor/bootstrap_sort/bootstrap-sortable.js"></script>



















