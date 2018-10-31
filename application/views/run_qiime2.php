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
        <div class="col-lg-12 ">
           
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            
 <ol class="breadcrumb">
   <li <?php if($controller_name == 'main'){ echo "class=active";} ?> >
            
         <?php if ($controller_name == 'main') { ?> Home         
         <?php } else { ?>     
                 <a href="<?php echo site_url('main') ?>">Home</a> 
         <?php } ?>    
    </li>
    <li <?php if($controller_name == 'projects'){ echo "class=active"; }?>>
         <?php if ($controller_name == 'projects') { ?>  Current projects
         <?php } else { ?>
            <a href="<?php echo site_url('projects/index/' . $current_project) ?>">
               Current project
            </a>
        <?php } ?>  /  Qiime 2
   </li> 
</ol>
          
     </div>
 </div>


<div class="row">
    <div class="col-lg-12">
    <div class="uk-child-width-1-6\@xl" uk-grid>
    <div>
        <ul class="uk-tab-right" uk-switcher="animation: uk-animation-fade" uk-tab>
 
        <li class="uk-active" >
                <a class="uk-text-capitalize uk-text-bold" href="1" onclick="advance_mode(this);">Qiime 2  </a>
        </li>
        </ul>

        <ul class="uk-switcher">


        <!-- mothur + qiime  -->
         <li>
         <link href="<?php echo base_url();?>tooltip/smart_wizard_theme_arrows.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/loading.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/tooltip.css" rel="stylesheet" />
         <script src="<?php echo base_url();?>tooltip/tooltip.js" type="text/javascript"></script>
       

         <div class="sw-theme-arrows">
             <ul class="nav-tabs step-anchor" uk-switcher="animation: uk-animation-fade">
                 <li class="pre"><a href="#">Step 1<br />Data ( Preprocess )</a></li>
                 <li class="pre2"><a href="#">Step 2<br />Data ( Prepare data of analysis )</a></li>
                 <li class="pre3"><a href="#">Step 3<br />Result && Graph</a></li>
               
             </ul>
         
         <ul class="uk-switcher uk-margin">


         <!--Preprocess && Prepare in taxonomy -->
         <li>

         <!-- .panel-group -->
         <div class="panel-group" id="accordion"></div>
    
             <div class="Pre-test">
           
                 <!-- /.row -->
                <div class="row">

                <div class="col-lg-12">


                 <!-- .panel-heading -->
                 <div class="panel-body">
                 <div class="panel-group" id="accordion">

                        <div class="panel panel-info">
                         <div class="panel-heading">          
                             <h4 class="panel-title">
                                 <a  data-toggle="collapse" data-parent="#accordion" href="#collapse1" >1. Generate Map file  
                                 <i class="fa fa-question-circle-o" ></i>       
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse1" class="panel-collapse collapse">

                             <div class="panel-body">       
                                 <div class="col-lg-12 uk-margin"></div> 

                                 <div class="col-lg-12">
                                    <div class="form-group">
                                            <label>Upload Map file</label>
                                            <input type="file" name="sample_metadata" id="sample_metadata_up">
                                    </div>
                                    <label>OR </label><br/>
                                    <button class="btn btn-success" id="btnAddCol">
                                        add group
                                    </button>
                                    <button class="btn btn-danger" id="btnRemoveCol">
                                        remove group
                                    </button>
                                 </div>
                                 <div class="col-lg-12 uk-margin"> </div>
                                 <!--<form>-->
                                 <div class="col-lg-12 table-responsive">
                                    <form name="myform" id="myform" method="post">
                      
                                    <table id="blacklistgrid">
                                        <tr id="Row1">
                                            <td><input type="text" value="#SampleID" readonly="readonly"/></td>
                                            <td><input type="text" value="groupA" readonly="readonly"/></td> 
                                        </tr>
                                        <tr id="Row12">
                                            <td><input type="text" value="#q2:types" readonly="readonly"/></td>
                                            <td><input type="text" value="categorical" readonly="readonly"/></td> 
                                        </tr>

                                         <?php  for($i = 0; $i < count($sampleName) ;$i++) { ?>
        
                                            <tr id="Row2">
                                            <td>
                                                <input type="text" value="<?=$sampleName[$i]?>" readonly="readonly"/>
                                            </td>
                                            <td>
                                                <input type="text" value="" />
                                            </td>
                                            </tr>

                                         <?php } ?>

                                     </table>
                                    </form>
                                     <!--</form>-->
                                 </div> 
                                 <div class="col-lg-12 uk-margin">
                                      <button class="btn btn-info" onclick="getExcel()">
                                        create file
                                      </button> 
                                 </div>

                             </div>
                         </div>
                         </div>

       

                         <div class="panel panel-info">
                             <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"> 2. Align Sequences & Clean Alignment
                                     <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div2');"></i>
                                     </a> 
                                 </h4>
                             </div>


                             <div id="collapse2" class="panel-collapse collapse">
                                <div class="panel-body">


         <form name="Pre-form" method="post" action="#" enctype="multipart/form-data">
             <input type="hidden" name="username" value="<?= $username ?>">
             <input type="hidden" name="project" value="<?= $current_project ?>">
             <input type="hidden" name="chkmap" id="chkmap" value="nomap">

                                 <label> Pick OTUs </label>
                                     <select class="uk-select" name="pick_otus" id="select_pick_otus">
                                        <option  value="OpenReference" selected> Open-Reference</option>
                                         <option value="CloseReference"> Close-Reference</option>
                                         <option value="Denovoclustering"> Denovo clustering</option>
                                     </select>
                                   

                                   <div  id="row_option_pick_otus"> 

                                    
                                  <img src="<?php echo site_url('images/img_silva.png');?>" width="65%" height="55%">


                                       <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v_full" type="radio" class="radio_pick_otus"> 
                                             gg_13_8_99full (Default)
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v1-v3" type="radio" class="radio_pick_otus"> 
                                           V1–V3 region with primers 27F and 534R
                                           &nbsp;<i class="fa fa-question-circle-o"onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div16');">  
                                           </i>
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v3-v4" type="radio" class="radio_pick_otus">
                                           V3 – V4 region with primers 341F and 802R
                                             &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div17');"></i>
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v4" type="radio" class="radio_pick_otus">
                                           V4 region with primers 515F and 806R
                                             &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div18');">
                                             </i>
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v3-v5" type="radio" class="radio_pick_otus">
                                           V3-V5 region with primer 341F and 909R
                                             &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div19');">
                                            </i>
                                          </label>
                                         </div>
                                         <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v4-v5" type="radio" class="radio_pick_otus"> 
                                           V4-V5 region with primers 518F and 926R
                                           &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div20');">
                                            </i>
                                          </label>
                                         </div>

                                         <div class="radio">
                                         <label>
                                           <input name="option_gg" value="v5-v6" type="radio" class="radio_pick_otus"> 
                                           V5-V6 region with primers 785F and 1081R
                                           &nbsp;<i class="fa fa-question-circle-o" >
                                            </i>
                                          </label>
                                         </div>


                                   </div>

                                    <script type="text/javascript">
                                       
                                         $(function(){
                                            $('#select_pick_otus').change(function(){ 
                                            if($('#select_pick_otus').val() == 'Denovoclustering'){
                                                     $('.radio_pick_otus').attr("disabled",true); 
                                               }else{
                                                   $('.radio_pick_otus').attr("disabled",false);   
                                               }
                                            });

                                         });
                                   </script>

                                 <div class="col-lg-12 uk-margin"></div>
                                    
                                </div>
                             </div>
                         </div>

                         <div class="panel panel-info">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse3"> 3. Precent identity
                                 <i class="fa fa-question-circle-o" onmouseover="#" ></i>
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse3" class="panel-collapse collapse">
                             <div class="panel-body">
                                    <label class="col-lg-12"> 
                                        Precent identity 
                                    </label> 
                                    <div class="col-lg-3 ">
                                         <div class="form-group">
                                             
                                             <input id="precent" class="form-control" type="number" name="precent_identity" min="50" value="97" onblur="checkvalue()" onkeypress='return validateNumber(event)'>
                                         </div>
                                    </div>
                                    <div class="col-lg-3 ">
                                          <div class="form-group">
                                             <label> (Default = 97) </label> 
                                         </div>
                                    </div>                    
                             </div>
                         </div>
                         </div>

         </form><!-- close row form-->
          

                         </div> <!-- class="panel-group"-->
                        </div> <!--  class="panel-body" -->
                 </div>  <!-- /.col-lg-12 -->
                 </div><!-- /.row -->

    
                     <div class="col-lg-12 uk-margin"></div>
                     <div class="row">
                     <div class="col-lg-1"></div>
                     <div class="col-lg-6">
                        <button  id="sub-test" readonly="readonly" class="btn btn-primary">Run Preprocess</button>
                     
                     </div>
                     </div>

           
             </div> <!-- Pre-test -->

             <div class="row">
                 <div class="col-lg-11 ">
                    <div class="Pre-show" style="display:none"> 
                      
                      <div class="loader">
                          <p class="h1"> Qiim2 Data Preprocess</p>
                          <span></span>
                          <span></span>
                          <span></span>
                      </div>
                     <div class="col-lg-5 col-lg-push-1 "> <b>Status : </b></div>
                     <div class="col-lg-5 col-lg-pull-3" id="test_run">Wait Queue</div>
                    
                     <div class="col-lg-11 col-lg-push-1 uk-margin">
                     <div class="progress progress-striped active">
                         <div id="bar_pre" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                               <div class="percent_pre">0%</div>
                         </div>
                     </div>
                      
                     </div> 
                     </div> 
                 </div> 
             </div>
             
    </li>
    <!--End Preprocess && Prepare in taxonomy -->


     <!--Prepare phylotype -->
     <li>

        <link href="<?php echo base_url();?>tooltip/bootstrap-toggle.min.css" rel="stylesheet">
        <script src="<?php echo base_url();?>tooltip/bootstrap-toggle.min.js"></script>

        <div class="Pre-test2">
         
           <!-- /.row -->
             <div class="row">
             <div class="col-lg-11">

                <!-- .panel-heading -->
                 <div class="panel-body">
                 <div class="panel-group" id="accordion">

                 <div class="panel panel-info">
                    <div class="panel-heading">          
                        <h4 class="panel-title">
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse13" >1. Show data in after data processing 
                            <i class="fa fa-question-circle-o"></i>        
                            </a>
                         </h4>
                    </div>
                    <div id="collapse13" class="panel-collapse collapse">
                        <div class="panel-body"> 
                            <label>The number of total reads/group after the preprocess</label>
                             <p class="col-lg-10">  show data in after data processing </p>
                                <div class="row uk-margin">
                                    <div class="col-lg-10 col-lg-push-1">
                                        <textarea class="form-control" rows="5" id="show_group" readonly="readonly"></textarea>
                                    </div>
                                    <div class="col-lg-10 col-lg-push-1 uk-margin">
                                     <button class="btn btn-primary" data-toggle="modal" data-target="#myModal_back"> Back Preprocess</button>
                                     </div>
                                </div>   
                        </div>
                    </div>
                </div>

       

                <div class="panel panel-info">
                             <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse14"> 2. Sub Sample 
                                    <i class="fa fa-question-circle-o"></i>  
                                    
                                     </a> 
                                 </h4>
                             </div>
                             <div id="collapse14" class="panel-collapse collapse">
                             <div class="panel-body">

        <form name="Pre-form2" method="post" action="#" enctype="multipart/form-data">
        <input type="hidden" name="username" value="<?= $username ?>">
        <input type="hidden" name="project" value="<?= $current_project ?>">
        <input type="hidden" id="max_num_subsample" name="max_subsample" >

                                           <label>Please put the number to subsampled file </label>
                                           <div class="row uk-margin">
                                           <div class="col-lg-5 col-lg-push-1">
                                                 <label>sub sample :</label>
                                            </div>
                                            <div class="col-lg-5 col-lg-pull-2">
                                                 <input id="sub_sample" class="uk-input" type="number"  min="0" name="subsample" onkeypress='return validateNumber(event)'>
                                            </div>
                                            </div>
                             </div>
                             </div>
                 </div>

                 <div class="panel panel-info">
                    <div class="panel-heading">          
                        <h4 class="panel-title">
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse16" >3. Select Group core diversity analysis 
                            <i class="fa fa-question-circle-o"></i>        
                            </a>
                         </h4>
                    </div>
                    <div id="collapse16" class="panel-collapse collapse">
                        <div class="panel-body">    
                            
                                <p class="fa fa-cog col-lg-11"> Group core diversity analysis </p>
                                <div class="col-lg-3 ">
                                <div class="form-group">
                                     <select class="uk-select" name="core_group" id="core_group" >
                                     </select>
                                </div>
                                </div> 
                                <div class="col-lg-8"><p class="opt1" style="display: none;"> warning : This group can not use for the next statisical analysis in below option.</p></div>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse18"> Options 
                             <i class="fa fa-question-circle-o"></i>  
                            </a> 
                        </h4>
                    </div>
                     <div id="collapse18" class="panel-collapse collapse">
                        <div class="panel-body">

                            <fieldset class="optionset1" disabled>
                            <p class="fa fa-cog col-lg-11"> Permanova</p>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="permanova" id="permanova">
                                 </select>
                                </div>
                            </div>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="opt_permanova" >
                                    <option value="none">none</option>
                                    <option value="weight">weight</option>
                                    <option value="unweight">unweight</option>
                                </select>
                                </div>
                            </div>
                            </fieldset>


                            <div class="col-lg-12 uk-margin">
                               <p class="fa fa-cog"> PICRUSt  and STAMP :</p>  
                                <input id="toggle-event2" type="checkbox" data-toggle="toggle" data-size="small" data-onstyle="success" data-offstyle="danger" >
                            </div>

                            <div class="col-lg-10">
                             <fieldset class="optionset2" disabled>

                            <div class="col-lg-10 col-lg-push-1">
                            <div class="form-group">
                            <label class="col-lg-8 col-lg-pull-1 uk-margin">PICRUSt</label>
                            <div class="col-lg-12">
                             <label>• Default Please select level of KEGG pathway : </label>
                            <label class="radio-inline">
                                <input name="kegg"  value="L1" type="radio">1
                            </label>
                            <label class="radio-inline">
                            <input name="kegg"  value="L2" checked type="radio">2
                             </label>
                            </div>
                            </div>
                            </div>

                            <div class="col-lg-12 uk-margin"></div>

                            <div class="col-lg-10 col-lg-push-1">
                            <div class="form-group">
                            <label class="col-lg-8 col-lg-pull-1 uk-margin">STAMP</label>
                            </div>
        
                            <div class="col-lg-10 ">
                            <div class="form-group">      
                             <label>• Sample comparison : </label>

                            <select class="uk-select" name="sample_comparison" id="sample_name">
         
                            </select>
                             </div>
                            <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                            </div>
         
                            <div class="col-lg-10 ">
                            <div class="form-group">                                   
                            <label>• Selected statistical test : </label>
                            <select class="uk-select" name="statistical_test">
                                <option value="0"></option>
                                <option value="Bootstrap">Bootstrap</option>
                                <option value="Chi-square">Chi-square test</option>
                                <option value="Chi-square2">Chi-square test(w/Yates' correction)</option>
                                <option value="Difference">Difference between proportions</option>
                                <option value="Fisher">Fisher 's exact test</option>
                                <option value="G‐test">G‐test</option>
                                <option value="G‐test2">G‐test (w/ Yates' correction)</option>
                                <option value="Hypergeometric">Hypergeometric</option>
                                <option value="Permutation">Permutation</option>
                            </select>
                            </div>
                            <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                            </div>

                            <div class="col-lg-10 ">
                                <div class="form-group">
                                <label>• CI method : </label>
                                <select class="uk-select" name="ci_method">
                                    <option value="0"></option>
                                    <option value="DP1">DP: Newcombe‐Wilson</option>
                                    <option value="DP2">DP: Asymptotic</option>
                                    <option value="DP3">DP: Asymptotic-CC</option>
                                    <option value="OR1">OR: Haldane adjustment</option>
                                    <option value="RP1">RP: Asymptotic</option>
                                </select>
                                </div>
                                <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                             </div>

                            <div class="col-lg-10 ">
                            <div class="form-group">
                                <label>• P‐value : </label>
                            <select class="uk-select" name="p_value">
                                <option value="0">None</option>
                                <option value="0.05">0.05</option>
                                <option value="0.01">0.01</option>
                            </select>
                            </div>
                             <p class="opt2" style="display: none;"> <font color="red">*Required</font></p>
                            </div>
                            </div>
                            </fieldset>
                            </div> 

                         </div>
                    </div>
                 </div>

                 <div class="col-lg-12 uk-margin"></div>
                    <div class="row">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-4">
                       <!--  disabled -->
                    <input id="sub-test2" readonly="readonly" class="btn btn-primary disabled" value="Run Pick OTUs">
                     </div>
                    </div>
                 <div class="col-lg-12 uk-margin"></div>
                 </div>
                 </div>        

             </div>  <!-- /.col-lg-11 -->
             </div><!-- /.row -->
         </form><!-- close row form -->

            <div class="col-lg-12 uk-margin"></div>
             <!-- Modal -->
                 <div class="panel-body">
                     <div class="modal fade" id="myModal_back" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;"> 
                     <div class="modal-dialog">
                     <div class="modal-content">
                     <div class="modal-header">
                     <button type="button" class="close"data-dismiss="modal" aria-hidden="true">×</button>
                     <h4 class="modal-title" id="myModalLabel"> Preprocess</h4>                                                     
                     </div>
                     <div class="modal-body">
                     Do you want to re-run preprocess again ?
                     </div>
                     <div class="modal-footer">
                         <button id="back_preprocess" class="btn btn-primary" data-dismiss="modal">Yes </button>
                         <button class="btn btn-default" data-dismiss="modal">No </button>   
                     </div>
                     </div> <!-- /.modal-content -->
                     </div> <!-- /.modal-dialog -->
                     </div>
                </div><!-- End Modal --> 



        </div> <!-- Pre-test2 -->

        <div class="row">
                 <div class="col-lg-11">
                    <div class="Pre-show2" style="display:none"> 
                      <div class="loader">
                          <p class="h1">Qiim2 Data Analysis</p>
                          <span></span>
                          <span></span>
                          <span></span>
                      </div>
                     <div class="col-lg-5 col-lg-push-1 "> <b>Status : </b></div>
                     <div class="col-lg-5 col-lg-pull-3" id="test_run2">Wait Queue</div>
                     <div class="col-lg-11 col-lg-push-1 uk-margin">
                     <div class="progress progress-striped active">
                         <div id="bar_pre2" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                         <div class="percent_pre2">0%</div>
                     </div>
                     </div>
                     </div>
                     </div> 
                </div> 
         </div>

     </li>
     <!--End Prepare phylotype analysis-->


     <!-- Result && Graph -->                              
    <li>

        <div class="Pre-test3">

             <hr class="uk-divider-icon">
            <div class="panel-body">       
            <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
       
            <div class="alert alert-info">
            <center>

                <a href="<?php echo site_url('qiime2_report/graph_qiime_full/');?><?=$current_project?>" target="_blank">
                <button type="button" class="btn btn-success  btn-circle btn-xl">
                <i class="fa fa-file-image-o"></i>
                </button>
                 </a>
                <h4>Graph Qime</h4>

            </center>
            </div>    
            </div>
            </div>
            </div>

             <hr class="uk-divider-icon">
            <div class="panel-body">       
            <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
       
            <div class="alert alert-info">
            <center>

                 <a href="<?php echo site_url('Qiime2_report/test_report/');?><?=$current_project?>" target="_blank">
                 <button type="button" class="btn btn-info btn-circle btn-xl">
                  <i class="fa fa-file-word-o"></i>
                 </button>
                 </a>
                <h4>  Report  </h4>


            </center>
            </div>    
            </div>
            </div>
            </div>

            
           
        </div> <!-- Pre-test3 -->
    </li> 
   <!-- End Result && Graph -->

   
    

     </ul> 
     <!-- class="uk-switcher uk-margin" -->

    </div>
    </li>
    <!--  mothur + qiime  -->

    </ul>
    <!-- end class="uk-switcher" -->  

    </div>
    </div>
    </div>
</div>



<script>


    $('#core_group').change(function(){
        var data_name = document.getElementById('core_group').value;
        if(data_name != "none"){
           on_switch(data_name);
        }else{$(".optionset1").attr("disabled", true);}  
    });


    var console_event2 = false;
    $('#toggle-event2').change(function(){
        console_event2 = $(this).prop('checked');
        if($(this).prop('checked')){
          $(".optionset2").removeAttr("disabled")
          $(".opt2").show();
         
        }else{
          $(".optionset2").attr("disabled", true);
          $(".opt2").hide();
           
        }
    });


    function on_switch(data_name){
     var user = "<?php echo $username ?>";
     var project = "<?php echo $current_project ?>";
   
     $.ajax({ 
        type:"post",
        datatype:"json",
        url:"<?php echo site_url('Run_qiime2/read_map_json/');?>"+user+"/"+project,
        data:{data_group: data_name},
        success:function(data){
                var data_sw = $.parseJSON(data);
                console.log(data_sw);
                if(data_sw[0] == "on"){ 
                    $(".optionset1").removeAttr("disabled");
                    $(".opt1").hide();

                    var name_group =  "<option value='none'>none</option>";
                         name_group += "<option value="+data_sw[1]+">"+data_sw[1]+"</option>";

                    $('#permanova').html(name_group);
                   

                }else{
                    $(".optionset1").attr("disabled", true);
                    $(".opt1").show();
                 }
        },
        error:function(e){
            console.log(e.message);
        }
    });          
}

    function checkvalue(){
             var precent = document.getElementById('precent');
             if(precent.value == ""){
                $('#precent').css("border","1px solid #FF0000");  
             }else{
                $('#precent').css("border","1px solid #e1ede1");
             }
    }
    function validateNumber(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46) {
                    return true;
            } else if ( key < 48 || key > 57 ) {
                return false;
            } else {
                return true;
            }
    }

    $(document).on('change', '#sample_metadata_up', function(){
               
            var file_data = $('#sample_metadata_up').prop('files')[0];
            var form_data = new FormData();
                form_data.append('file', file_data);
            var file_name = file_data.name;
            var file_size = file_data.size;
            var file_mb = (file_data.size/1024/1024).toFixed(0); // MB
                   
            var type = file_name.substring(file_name.lastIndexOf('.')+1);
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            var i = parseInt(Math.floor(Math.log(file_size) / Math.log(1024)));
            var f_size = Math.round(file_size / Math.pow(1024, i), 2) + ' ' + sizes[i];
                  
                if(type == 'tsv'){
                    if(file_size == 0){
                        alert('Size : '+file_size +' Bytes');  
                    }
                    else if(file_mb <= 800){
                        //alert(file_name+' '+f_size+' '+type);
                        get_fasta(form_data);
                    }else{
                        alert('file is too large : '+ f_size);
                       
                    } 
                }else{ 
                    alert('file is not tsv');
                   
                }            
    });


    function get_fasta(file_data){
            var user = "<?php echo $username;?>";
            var project = "<?php echo $current_project;?>";
            $.ajax({
                   type:"post",
                   dataType: 'text',
                   url:"<?php echo base_url('Run_qiime2/metadata_upload');?>/"+user+"/"+project,
                   data: file_data,
                   cache: false,
                   processData: false,
                   contentType: false,
                    beforeSend: function () {
                        console.log("beforeSend");
                    },
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            console.log(evt.loaded);
                        }, false);
                       return xhr;
                    },
                    complete: function (xhr) {
                          if(xhr.responseText == '0'){
                                alert(xhr.responseText);
                          }else { 
                               alert(xhr.responseText);
                               document.getElementById('chkmap').value = "map";                             
                          }
                    },   
                   error:function(e){
                      console.log(e.message);
                   }
           });
    }


    function getExcel() {

         var user = "<?php echo $username ?>";
         var project = "<?php echo $current_project ?>";
         var excel = "",  check_val = "";

        $("#blacklistgrid").find("tr").each(function () {
            var sep = "";
            $(this).find("input").each(function () {
                excel += sep + $(this).val();
                check_val += $(this).val() + sep;
                sep = "\t";
             });
            excel += "\n";
        });
        var count = true;
        var res = check_val.split("\t");
            for(var i = 0; i < res.length - 1; i++) {
                console.log(res[i]);
                if (res[i] == "") {count = false; }
            }
            if(count == false){
                alert("Please insert value");
            }else{
                $.ajax({
                        type: "post",
                        datatype: "json",
                        url: "<?php echo base_url('Run_qiime2/genmap');?>/" + user + "/" + project,
                        data: {data_excel: excel},
                        success: function (data) {
                                var user_file = $.parseJSON(data);
                                alert("Create map " + user_file + " success");
                                //check_map();
                                document.getElementById('chkmap').value = "map";
                        }, error: function (e) {
                                console.log(e.message);
                        }
                });
            }
    }



   
</script>
  

<script type="text/javascript">

$(document).ready(function (){ 

        var myform = $('#myform');
        var col_num = 4;
        var alphas = '';
        $('#btnAddCol').click(function () {
            myform.find('tr').each(function () {
                var trow = $(this);
                var numdel = col_num-3;
                alphas = String.fromCharCode(numdel + 65);
                if (trow.index() === 0) {
                    trow.append('<td><input type="text" readonly="readonly" value=group'+alphas+' /></td>');
                } else {
                    trow.append('<td><input type="text" /></td>');
                }
            });
            col_num += 1;
        });
        $('#btnRemoveCol').click(function (){
            var column_count = $('#blacklistgrid #Row1 td').length;
            if (column_count > 2) {
                $('table tr').find('td:eq(-1),th:eq(-1)').remove();
                col_num -= 1;
            }
        });



       var status = "<?=$status?>";
       var step_run = "<?=$step_run?>";
       var id_job = "<?=$id_job?>";
       var current = "<?=$current?>";

       if(status != "null"){
            var send_data = new Array(id_job,current);
            if(step_run == "1"){
                alert("Data Preprocess"); 
                $('li.pre').attr('id','active');
                $(".Pre-test").hide();
                $(".Pre-show").show();
                $('#test_run').html('Checking Run Preprocess');
                checkrun(send_data);

            }else if(step_run == "2"){
                  alert("Data Analysis"); 
                  $('li.pre').attr('id','done');
                  $('li.pre2').attr('id','active');
                  $(".Pre-test2").hide();
                  $(".Pre-show2").show();
                  $('#test_run2').html('Checking Run Analysis');
                  $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
                  checkrun2(send_data);

            }else if(step_run == "3"){
                    alert("Result & Graph");  
                    $('li.pre').attr('id','active'); 
            }

       }else{ $('li.pre').attr('id','active'); } 


       $("#back_preprocess").click(function(){
                 $('.sw-theme-arrows > .nav-tabs > .pre2').prev('li').find('a').trigger('click');                 
                 $('li.pre').attr('id','active');
                 $('li.pre2').attr('id','');
                 $('#sub-test2').attr('class','btn btn-primary disabled');
        }); 



       $("#sub-test").click(function(){
               
             var username  = document.forms["Pre-form"]["username"].value;
             var project   = document.forms["Pre-form"]["project"].value;
             var chkmap    = document.forms["Pre-form"]["chkmap"].value;
             var pick_otus = document.forms["Pre-form"]["pick_otus"].value;
             var option_gg = document.forms["Pre-form"]["option_gg"].value;
             var precent_identity = document.forms["Pre-form"]["precent_identity"].value;

             if(pick_otus == "Denovoclustering"){
                option_gg = "denovo";
             }
             var array_data = new Array(username,project,chkmap,pick_otus,option_gg,precent_identity);
             if(chkmap != "nomap" && option_gg != "" && precent_identity != "" ){
                if(Number(precent_identity) >= 50){
                     $(".Pre-test").hide();
                     $(".Pre-show").show();
                     getvalue(array_data);  
                }else{
                    alert("precent_identity less 50");
                }
                             
            }else{
                alert("Null");
            }
        });


       $("#sub-test2").click(function(){
               
                var username = document.forms["Pre-form2"]["username"].value;
                var project  = document.forms["Pre-form2"]["project"].value;
                var max_subsample = document.forms["Pre-form2"]["max_subsample"].value;
                var subsample = document.forms["Pre-form2"]["subsample"].value;

                var core_group = document.forms["Pre-form2"]["core_group"].value;
                var permanova = document.forms["Pre-form2"]["permanova"].value;
                var opt_permanova = document.forms["Pre-form2"]["opt_permanova"].value;
                
                var kegg = document.forms["Pre-form2"]["kegg"].value;
                var sample_comparison = document.forms["Pre-form2"]["sample_comparison"].value;
                var statistical_test = document.forms["Pre-form2"]["statistical_test"].value;
                var ci_method = document.forms["Pre-form2"]["ci_method"].value;
                var p_value = document.forms["Pre-form2"]["p_value"].value;


                 var array_data = new Array(username,project,subsample,core_group,permanova,opt_permanova,kegg,sample_comparison,statistical_test,ci_method,p_value);

                //check options picrust and stamp
                var open_opt = "null";
                if(console_event2){
                     if(sample_comparison != "0" && statistical_test !="0" && ci_method != "0" &&  p_value !="0" ){ 
                            open_opt = true;
                     }else{
                            open_opt = false; 
                     }
                 }

                if(console_event2){
                     console.log('use options');
                    if((Number(subsample) <= Number(max_subsample)) && (core_group != "none")){
                        if(!open_opt){
                             alert("Please select all stamp");
                        }else{
                             $(".Pre-test2").hide();
                             $(".Pre-show2").show();
                             getvalue2(array_data,open_opt); 
                        } 
                    }else if(Number(subsample) > Number(max_subsample)){
                         alert("input subsample greater than "+max_subsample);
                    }else if(core_group == "none"){
                        alert("Please select group core diversity analysis");
                    }

                }else if(!console_event2 ){
                    console.log('no options');
                    if((Number(subsample) <= Number(max_subsample)) && (core_group != "none")){
                            $(".Pre-test2").hide();
                            $(".Pre-show2").show();
                            getvalue2(array_data,open_opt); 

                    }else if(Number(subsample) > Number(max_subsample)){
                         alert("input subsample greater than "+max_subsample);
                    }else if(core_group == "none"){
                        alert("Please select group core diversity analysis");
                    }
                }   
         });
   
 });


function getvalue(array_data){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_qiime2/run_qiime2_preprocess'); ?>",
                    data:{data_array: data_value},
                    success:function(data){
                      var data_job = $.parseJSON(data);
                      checkrun(data_job);
                    },
                    error:function(e){
                      console.log(e.message);
                    }
            });
            
}

function checkrun(job_val){
          
        $('#bar_pre').width(1+"%");
        var time = 30;
        var interval = null;
        interval = setInterval(function(){   
            time--;
            if(time === 0){
            $.ajax({ 
                type:"post",
                datatype:"json",
                url:"<?php echo base_url('Run_qiime2/check_run_qiime2_preprocess'); ?>",
                data:{data_job: job_val },
                success:function(data){
                //console.log("data : " + JSON.parse(data));
              
                var data_up = $.parseJSON(data);
                   if(data_up[0] == "0"){

                        $('#test_run').html('Queue complete');
                        clearInterval(interval);

                        var sample_value ="";
                        for(var i=0; i < data_up[1][0].length;i++){
                            sample_value += data_up[1][0][i];
                        } 
                        document.getElementById('show_group').value = sample_value;
                        document.getElementById('sub_sample').value = Number(data_up[1][1]); 
                        document.getElementById('max_num_subsample').value = Number(data_up[1][1]);
                        $('#sub_sample').attr({'max': Number(data_up[1][1])});
                         getGroup();

                        $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
                        $(".Pre-show").hide();
                        $(".Pre-test").show();
                        $('li.pre').attr('id','done');
                        $('li.pre2').attr('id','active');
                        $('#sub-test2').attr('class','btn btn-primary');
                             
                           
                   }else{
                         var show_data = data_up[0];
                         var show_num  = data_up[1];
                         $('#bar_pre').width(show_num+"%");
                         $('.percent_pre').html(show_num+"%");
                         $('#test_run').html(show_data);
                         time = 30;  
                   }  
                },
                error:function(e){
                      console.log(e.message);
                    }
            });
            }
        },1000);
}



function getGroup(){

     var user = "<?=$username ?>";
     var project = "<?=$current_project ?>";
     var data_value = new Array(user,project);
    
     $.ajax({
        type:"post",
        datatype:"json",
        url:"<?php echo base_url('Run_qiime2/getgroup'); ?>",
        data:{data: data_value},
        success:function(data){
            var reData = $.parseJSON(data);
            if(reData[0] == "1"){
                var name_group = "";
                name_group += "<option value='none'>none</option>";
                for (var i=0; i < reData[1].length; i++){
                        name_group += "<option value="+reData[1][i]+">"+reData[1][i]+"</option>";    
                }
                $('#core_group').html(name_group);
               
                /*start div sample-name*/
                var samname = "";
                samname += "<option value='0'> </option>";
                for (var i=0; i < reData[2].length; i++) {

                     samname += "<option value="+reData[2][i]+">"+reData[2][i]+"</option>";    
                }
                $('#sample_name').html(samname);
                 /*end div sample-name*/ 
            }                
        },
        error:function(e){
             console.log(e.message);
        }             
    });                       
}



function getvalue2(array_data,open_opt){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_qiime2/run_qiime2_analysis'); ?>",
                    data:{data_array: data_value, data_opt:open_opt},
                    success:function(data){
                      var data_job = $.parseJSON(data);
                      checkrun2(data_job);
                    },
                    error:function(e){
                      console.log(e.message);
                    }
            });
            
}

function checkrun2(job_val){
          
        $('#bar_pre2').width(1+"%");
        var time = 30;
        var interval = null;
        interval = setInterval(function(){   
            time--;
            if(time === 0){
            $.ajax({ 
                type:"post",
                datatype:"json",
                url:"<?php echo base_url('Run_qiime2/check_run_qiime2_analysis'); ?>",
                data:{data_job: job_val },
                success:function(data){
                //console.log("data : " + JSON.parse(data));
                   var data_up = $.parseJSON(data);
                   if(data_up[0] == "0"){
                            $('#test_run2').html('Queue complete');
                            clearInterval(interval); 
                            $('.sw-theme-arrows > .nav-tabs > .pre2').next('li').find('a').trigger('click'); 
                            $(".Pre-show2").hide();
                            $(".Pre-test2").show();    
                   }else{
                         var show_data = data_up[0];
                         var show_num  = data_up[1];
                         $('#bar_pre2').width(show_num+"%");
                         $('.percent_pre2').html(show_num+"%");
                         $('#test_run2').html(show_data);
                         time = 30;  
                   }  
                },
                error:function(e){
                      console.log(e.message);
                    }
            });
            }
        },1000);
}


document.getElementById("btn_reports").onclick = function(){

    location.href= "<?php echo site_url();?>Qiime_report/index/<?=$current_project?>";
    // $.ajax({ 
    //       type:"post",
    //       datatype:"json",
    //       url:"<?php echo base_url('ckprorun'); ?>",
    //       data:{current:"<?=$current_project?>"},
    //          success:function(data){
    //             var chk = JSON.parse(data); 
    //             if(chk == "t"){
                             
    //             }
              
    //         }         
    //  });

};



</script> 
<!--  End Advance Script -->

   