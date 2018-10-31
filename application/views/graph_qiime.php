
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
      <h3 class="page-header">Visualization : </h3>
    </div>
    <!-- /.col-lg-12 -->
  </div>

  <!-- /.row -->
  <div class="row">
    <div class="col-lg-12">

      <div class="col-lg-12 uk-margin"></div>


    </div>
  </div>


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
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed"> Taxonomy classification </a>
                  </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                  <div class="panel-body">
                     
                         <p class="fa fa-bookmark"> &nbsp;&nbsp;Phylum level </p> 
                         <br/>
                         <center>
                           <div class="row">

                              <div class="col-md-7">
                          
                                  <img src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/taxonomy_classification/bar_plot.png"  width="600px" height="850px">

                              </div> 
                            
                             <div class="col-md-5 col-md-pull-1">
                                  <img src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/taxonomy_classification/bar_plot_legend.png" width="600px" height="150px">
                               
                             </div>
                           </div>
                          </center>


                          <br/>
                         <p class="fa fa-bookmark"> &nbsp;&nbsp;Genus level </p> 
                          <br/>
                         <center>
                           <img src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/taxonomy_classification/heatmap.png" width="550px" height="550px">
                         </center>
                   

                  </div>
              </div>
            </div>



            <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="false"> Alpha diversity analysis</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                         <div class="panel-body">

                           <p class="fa fa-bookmark"> &nbsp;&nbsp; Statistical analysis summary </p> 
                           <br/> 
                          <center>
                          <table class="table table-striped table-bordered table-hover" width="80%">
                          <?php   foreach ($alpha_diversity as $value) { ?>

                                  <tr>
                              
                                 <?php foreach ($value as $val) { ?>
                                  
                                   <td> <?=$val?></td>
                                   
                                 <?php } ?>

                                  </tr>
                              
                          <?php  } ?>
                         </table>
                        </center>

                          <br/>
                          <p class="fa fa-bookmark"> &nbsp;&nbsp;Chao </p> 
                          <br/>
                         <center>
                           <img src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/alpha_diversity_analysis/boxplots_chao.png" width="550px" height="550px">
                         </center>

                         <br/>
                         
                         <p class="fa fa-bookmark"> &nbsp;&nbsp;Shannon </p> 
                         <br/>
                         <center>
                           <img src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/alpha_diversity_analysis/boxplots_shannon.png" width="550px" height="550px">
                         </center>


                        <br/>
                         <p class="fa fa-bookmark"> &nbsp;&nbsp;Rarefaction curve </p> 
                          <br/>
                         <center>
                           <img src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/alpha_diversity_analysis/Rarefactionqiime.png" width="550px" height="550px">
                         </center>
                          

                       

                        
            
                         </div>
                    </div>
            </div>



            <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                         <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="false"> Beta diversity analysis </a>
                      </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">


                         
                          <br/> 

                          <p class="fa fa-bookmark"> &nbsp;&nbsp;jaccard </p> 
                          <center>
                          <div class="table-responsive">  
                          <table class="table table-striped table-bordered table-hover" width="80%">
                          <?php   foreach ($jaccard as $value2) { ?>

                                  <tr>
                              
                                 <?php foreach ($value2 as $val2) { ?>
                                  
                                   <td> <?=$val2?></td>
                                   
                                 <?php } ?>

                                  </tr>
                              
                          <?php  } ?>
                         </table>
                         </div>
                         </center>

                           
                          <br/> 
                          <p class="fa fa-bookmark"> &nbsp;&nbsp;morisita</p> 
                          <center>
                          <div class="table-responsive">  
                          <table class="table table-striped table-bordered table-hover" width="80%">
                          <?php   foreach ($moris as $value3) { ?>

                                  <tr>
                              
                                 <?php foreach ($value3 as $val3) { ?>
                                  
                                   <td> <?=$val3?></td>
                                   
                                 <?php } ?>

                                  </tr>
                              
                          <?php  } ?>
                         </table>
                         </div>
                         </center>

                          <br/> 
                           
                         <p class="fa fa-bookmark"> Oridination method </p> 
                           <br/>
                       

                         <iframe  width="100%" height="500px" src="<?php echo base_url();?>data_report_qiime/<?=$user?>/<?=$project?>/beta_diversity_analysis/2d_plots_coordinate/unweighted_unifrac_pc_2D_PCoA_plots.html">
     
                          </iframe>  


                        </div>
                    </div>
           </div>


            <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                         <a data-toggle="collapse" data-parent="#accordion" href="#collapsefour" class="collapsed" aria-expanded="false"> Optional output </a>
                      </h4>
                    </div>
                    <div id="collapsefour" class="panel-collapse collapse" aria-expanded="false">
                        <div class="panel-body">

                          <?php 
                            $png_stamp = "data_report_qiime/$user/$project/optional_output/bar_plot_STAMP.png";
                            if(file_exists($png_stamp)){ ?>
                            
                              <p class="fa fa-bookmark"> Bar plot </p> 
                              <br/>
                              <center>
                                 <img src="<?php echo base_url()?>data_report_qiime/<?=$user?>/<?=$project?>/optional_output/bar_plot_STAMP.png" width="650px" height="650px">
                              </center>

                            <?php }else{
                                echo "You did not select the option, Grapg did not be displayed.";
                            }
                             
                           ?>


                           
                              
                                  
                         

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



<div class="row">

      <div class="col-lg-12 uk-margin"> </div>
      <center>
          <input  class="btn btn-outline btn-info" value="Download all zip" id="zipall">
      </center> 
</div><!-- End Table file_summary -->   


</div> 
<!-- "/panel-body" -->
</div>
<!-- "/page-wrapper" -->


<script src="<?php echo base_url(); ?>vendor/bootstrap_sort/bootstrap-sortable.js"></script>



<script type="text/javascript"> 

  document.getElementById("zipall").onclick = function(){
    
        $.ajax({ 
          type:"post",
          datatype:"json",
          url:"<?php echo base_url('qiime_report/check_dirzip'); ?>",
          data:{current:"<?=$current_project?>"},
          success:function(data){
            var dir = JSON.parse(data); 
            if(dir == "TRUE"){
             location.href="<?php echo site_url();?>qiime_report/down_zip/<?=$current_project?>";           
           }else{
            alert("FALSE");
          }

        }

      });
  };
</script>



















