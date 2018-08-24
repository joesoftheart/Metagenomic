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
        <?php } ?>  / Mothur + Qiime 
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
                <a class="uk-text-capitalize uk-text-bold" href="1" onclick="advance_mode(this);">Mothur + Qiime  </a>
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
                 <li class="pre"><a href="#">Step 1<br />Mothur ( Preprocess )</a></li>
                 <li class="pre2"><a href="#">Step 2<br />Qiime ( Pick OTUs )</a></li>
                 <li class="pre3"><a href="#">Step 3<br />Result && Graph</a></li>
               
             </ul>
         
         <ul class="uk-switcher uk-margin">


         <!--Preprocess && Prepare in taxonomy -->
         <li>

         <!-- .panel-group -->
         <div class="panel-group" id="accordion"></div>
    
             <div class="Pre-test">
             <form name="Pre-form" method="post" action="#" enctype="multipart/form-data">

                 <input type="hidden" name="username" value="<?= $username ?>">
                 <input type="hidden" name="project" value="<?= $current_project ?>">
                 <!-- /.row -->
                <div class="row">

                <div class="col-lg-12">


                 <!-- .panel-heading -->
                 <div class="panel-body">
                 <div class="panel-group" id="accordion">

                        <div class="panel panel-info">
                         <div class="panel-heading">          
                             <h4 class="panel-title">
                                 <a  data-toggle="collapse" data-parent="#accordion" href="#collapse1" >1. Quality Control  
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div1');"></i>       
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse1" class="panel-collapse collapse">

                             <div class="panel-body">       
                                 <label class="col-lg-10"> Screen reads </label>
                                 <div class="col-lg-10">

                                 <table border="0" class="uk-table uk-table-middle" >
                                     <tr>
                                        <td>maximum ambiguous </td>
                                        <td><input id="mbig" class="form-control" type="number" name="maximum_ambiguous" min="0" placeholder="maximum ambiguous" onblur="checkvalue()" onkeypress='return validateNumber(event)'></td> 
                                     </tr>
                                     <tr>
                                         <td>maximum homopolymer </td>
                                         <td><input id="mhomo" class="form-control" type="number" name="maximum_homopolymer"  min="0" placeholder="maximum homopolymer" onblur="checkvalue2()" onkeypress='return validateNumber(event)'></td>
                                     </tr>
                                     <tr>
                                         <td>minimum reads length </td>
                                         <td><input id="miniread" class="form-control" type="number" name="minimum_reads_length" min="0" placeholder="minimum reads length" onblur="checkvalue3()" onkeypress='return validateNumber(event)'></td>
                                     </tr>                              
                                     <tr>
                                         <td> maximum reads length </td>
                                         <td><input id="maxread" class="form-control" type="number" name="maximum_reads_length" min="0" placeholder="maximum reads length" onblur="checkvalue4()" onkeypress='return validateNumber(event)'></td>
                                     </tr>
                                 </table>

                                 </div>
                             </div>
                         </div>
                         </div>
                         <div class="panel panel-default">
                             <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"> 2. Align Sequences & Clean Alignment
                                     <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div2');"></i>
                                     </a> 
                                 </h4>
                             </div>
                             <div id="collapse2" class="panel-collapse collapse">
                                <div class="panel-body">
                                 <label> Alignment step </label>
                                    <select class="uk-select" name="alignment" id="type_alignment">
                                        <option  value="silva" selected> Silva</option>
                                         <option value="gg"> Greengenes</option>
                                         <option value="rdp"> RDP</option>
                                     </select>
                                    


                                   <div  id="row_option_silva"> 

                                    
                                  <img src="<?php echo site_url('images/img_silva.png');?>" width="65%" height="55%">


                                       <div class="radio">
                                         <label>
                                           <input name="option_silva" value="v_full" type="radio" class="radio_silva"> 
                                             Silva (Default)
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_silva" value="v1-v3" type="radio" class="radio_silva"> 
                                           V1–V3 region with primers 27F and 534R
                                           &nbsp;<i class="fa fa-question-circle-o"onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div16');">  
                                           </i>
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_silva" value="v3-v4" type="radio" class="radio_silva">
                                           V3 – V4 region with primers 341F and 802R
                                             &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div17');"></i>
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_silva" value="v4" type="radio" class="radio_silva">
                                           V4 region with primers 515F and 806R
                                             &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div18');">
                                             </i>
                                          </label>
                                        </div>
                                        <div class="radio">
                                         <label>
                                           <input name="option_silva" value="v3-v5" type="radio" class="radio_silva">
                                           V3-V5 region with primer 341F and 909R
                                             &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div19');">
                                            </i>
                                          </label>
                                         </div>
                                         <div class="radio">
                                         <label>
                                           <input name="option_silva" value="v4-v5" type="radio" class="radio_silva"> 
                                           V4-V5 region with primers 518F and 926R
                                           &nbsp;<i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div20');">
                                            </i>
                                          </label>
                                         </div>

                                   </div>

                                <script type="text/javascript">
                                       
                                         $(function(){
                                            //$('#row_option_silva').hide();
                                            $('#type_alignment').change(function(){
                                               
                                            if($('#type_alignment').val() == 'silva'){

                                                $('#row_option_silva').show();

                                               }else{

                                                $('#row_option_silva').hide(); 
                                                $(".radio_silva").prop('checked', false);

                                               }

                                            });

                                         });

                                 </script>

                                 <div class="col-lg-12 uk-margin"></div>
                                     <label> OR Upload file fasta </label>
                                        (Limit size 800 MB) 
                                         <input type="file" name="customer" id="custo_mer">
                                         <div class="progress progress-striped active">
                                         <div id="bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0"  aria-valuemax="100" style="width:0%;">
                                         <div class="percent">0%</div>
                                         </div>
                                         </div>
                                         <div id="status"></div>
                                </div>
                             </div>
                         </div>
                         <div class="panel panel-info">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse3"> 3. Pre-Clusters Sequences & Chimera Detection
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div3');" ></i>
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse3" class="panel-collapse collapse">
                             <div class="panel-body">
                                 <label> Pre-cluster step </label> 
                                     <select class="uk-select" name="diffs">
                                     <option value="0">diffs = 0</option>
                                     <option value="1">diffs = 1</option>
                                     <option value="2" selected>diffs = 2</option>
                                     <option value="3">diffs = 3</option>
                                     </select>
                                                        
                             </div>
                         </div>
                         </div>
                         <div class="panel panel-default">
                         <div class="panel-heading">
                              <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse4"> 4. Classify Sequences
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div4');"></i>
                                 </a> 
                         </h4>
                         </div>
                         <div id="collapse4" class="panel-collapse collapse">
                         <div class="panel-body">
                             <label>Prepare the taxonomy classification</label>
                                 <select class="uk-select" name="classify">
                                    <option value="silva"> Silva</option>
                                    <option value="gg" selected> Greengenes</option>
                                    <option value="rdp"> RDP</option>
                                 </select>
                             <div class="col-lg-12"><br/></div>
                             <label class="col-lg-10"> with cutoff</label>
                             <div class="col-lg-5">
                                 <input class="uk-input" type="number" name="cutoff" min="50" value="80">     
                             </div>
                             <div class="col-lg-2"> <label>(>=50)</label></div>
                         </div>
                         </div>
                         </div>
                         <div class="panel panel-info">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse5"> 5. Remove Bacterial Sequences
                                <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div5');"></i>
                                 </a> 
                             </h4>
                         </div>
                         <div id="collapse5" class="panel-collapse collapse">
                         <div class="panel-body">
                             <label> Taxon elimination</label>
                             <div class="radio">
                             <label>
                                <input name="optionsRadios" value="0" type="radio" checked> default        
                             </label>
                             </div>
                             <div class="radio">
                             <label>
                             <input name="optionsRadios" value="1" type="radio">
                                <select class="uk-select" name="taxon">
                                <option value="k__Bacteria;k__Bacteria_unclassified-k__Archaea">
                                     k__Bacteria;k__Bacteria_unclassified-k__Archaea
                                </option>
                                <option value="k__Archaea_unclassified">
                                     k__Archaea_unclassified
                                 </option>
                                 <option value="Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified" selected>   
                                     Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified
                                 </option>
                                 </select>
                             </label>
                             </div>
                         </div>
                         </div>
                         </div>

                         </div>
                        </div>
                 </div>  <!-- /.col-lg-11 -->
                 </div><!-- /.row -->

    
                     <div class="col-lg-12 uk-margin"></div>
                     <div class="row">
                     <div class="col-lg-1"></div>
                     <div class="col-lg-6">
                      <input id="sub-test" class="btn btn-primary" value="Run Preprocess">  
                     </div>
                     </div>

      
             </form><!-- close row form -->
             </div> <!-- Pre-test -->

             <div class="row">
                 <div class="col-lg-11 ">
                    <div class="Pre-show" style="display:none"> 
                      
                      <div class="loader">
                          <p class="h1"> Mothur Preprocess</p>
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
           <form name="Pre-form2" method="post" action="#" enctype="multipart/form-data">

            <input type="hidden" name="username" value="<?= $username ?>">
            <input type="hidden" name="project" value="<?= $current_project ?>">
           <!-- /.row -->
             <div class="row">
             <div class="col-lg-11">

                <!-- .panel-heading -->
                 <div class="panel-body">
                 <div class="panel-group" id="accordion">

                 <div class="panel panel-info">
                    <div class="panel-heading">          
                        <h4 class="panel-title">
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse13" >1. Generate Map file 
                            <i class="fa fa-question-circle-o"></i>        
                            </a>
                         </h4>
                    </div>
                    <div id="collapse13" class="panel-collapse collapse">
                        <div class="panel-body">    
                            <p class="fa fa-gears col-lg-11"> Click button generate map file</p>
                            <div class="col-lg-2">                      
                                 <div class="col-lg-10">
                                    <a href="<?php echo site_url();?>Run_mothur_qiime/excel_map" target="_blank"><input type="button" class="btn btn-outline btn-info" value="generate map file" id="check_map" disabled="disabled" ></a>
                                   <!--  disabled="disabled"  -->
                                 </div>
                            </div>
                            <div class="col-lg-11 uk-margin">
                                <div class="col-lg-10">
                                     <input type="hidden" id="p_map" name="f_map" value="nomap" >
                                    <p class="fa fa-file-text-o" id="text_map"> No map file.</p>
                                     <img id="img_map" >
                                </div>
                            </div>          
                        </div>
                    </div>
                </div>

                 <div class="panel panel-info">
                    <div class="panel-heading">          
                        <h4 class="panel-title">
                            <a  data-toggle="collapse" data-parent="#accordion" href="#collapse15" >2. Beta diversity index 
                            <i class="fa fa-question-circle-o"></i>        
                            </a>
                         </h4>
                    </div>
                    <div id="collapse15" class="panel-collapse collapse">
                        <div class="panel-body">    
                            
                            <div class="col-lg-6"> 
                            <div class="form-group">
                                <select class="uk-select" name="" id="">
                                    <option>abund_jaccard</option>
                                    <option>binary_lennon</option>
                                    <option>binary_sorensen_dice</option>
                                    <option>bray_curtis</option>
                                    <option>morisita_horn</option>
                                    <option>unweighted_unifrac</option>
                                    <option>weighted_unifrac</option>
                                 </select>
                            </div>                         
                            </div>    
                        </div>
                    </div>
                </div>
    
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse14"> Options 
                             <i class="fa fa-question-circle-o"></i>  
                            </a> 
                        </h4>
                    </div>
                     <div id="collapse14" class="panel-collapse collapse">
                        <div class="panel-body">
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
                            <p class="fa fa-cog col-lg-11"> Anosim</p>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                    <select class="uk-select" name="anosim" id="anosim"> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="opt_anosim" >
                                    <option value="none">none</option>
                                    <option value="weight">weight</option>
                                    <option value="unweight">unweight</option>
                                </select>
                                </div>
                            </div>
                            <p class="fa fa-cog col-lg-11"> Adonis</p>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                    <select class="uk-select" name="adonis" id="adonis">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 ">
                                <div class="form-group">
                                <select class="uk-select" name="opt_adonis" >
                                    <option value="none">none</option>
                                    <option value="weight">weight</option>
                                    <option value="unweight">unweight</option>
                                </select>
                                </div>
                            </div>

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
                    <input id="sub-test2" class="btn btn-primary disabled" value="Run Pick OTUs">
                     </div>
                    </div>
                 <div class="col-lg-12 uk-margin"></div>
                 </div>
                 </div>        

             </div>  <!-- /.col-lg-11 -->
             </div><!-- /.row -->
         </form><!-- close row form -->
        </div> <!-- Pre-test2 -->

        <div class="row">
                 <div class="col-lg-11">
                    <div class="Pre-show2" style="display:none"> 
                      <div class="loader">
                          <p class="h1">Qiime Pick OTUs</p>
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

                 <button type="button" class="btn btn-warning  btn-circle btn-xl" id="">
                <i class="fa fa-file-image-o"></i>
              </button>
                <h4>Graph</h4>



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


            <button type="button" class="btn btn-info btn-circle btn-xl" id="btn_reports">
              <i class="fa fa-file-word-o"></i>
            </button>
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


  

<script type="text/javascript">

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
     })  

function checkvalue(){
             var mbig = document.getElementById('mbig');
             if(mbig.value == ""){
                $('#mbig').css("border","1px solid #FF0000");  
             }else{
                $('#mbig').css("border","1px solid #e1ede1");
             }
}

function checkvalue2(){
           var mhomo = document.getElementById('mhomo');
            if(mhomo.value == ""){
                $('#mhomo').css("border","1px solid #FF0000");  
             }else{
                $('#mhomo').css("border","1px solid #e1ede1");
             }
}

function checkvalue3(){
           var miniread = document.getElementById('miniread');
            if(miniread.value == ""){
                $('#miniread').css("border","1px solid #FF0000");  
             }else{
                $('#miniread').css("border","1px solid #e1ede1");
             }
}

function checkvalue4(){
           var maxread = document.getElementById('maxread');
           if(maxread.value == ""){
                $('#maxread').css("border","1px solid #FF0000");  
             }else{
                $('#maxread').css("border","1px solid #e1ede1");
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

$(document).on('change', '#custo_mer', function(){
               
               var file_data = $('#custo_mer').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                   var file_name = file_data.name;
                   var file_size = file_data.size;
                   var file_mb = (file_data.size/1024/1024).toFixed(0); // MB
                   
                   var type = file_name.substring(file_name.lastIndexOf('.')+1);
                   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                     var i = parseInt(Math.floor(Math.log(file_size) / Math.log(1024)));
                     var f_size = Math.round(file_size / Math.pow(1024, i), 2) + ' ' + sizes[i];
                  
                   if(type == 'fasta' || type == 'align'){
                        if(file_size == 0){
                            alert('Size : '+file_size +' Bytes');
                            document.getElementById('custo_mer').value = ""; 
                        }
                        else if(file_mb <= 800){
                          //alert(file_name+' '+f_size+' '+type);
                          get_fasta(form_data);

                        }else{
                            alert('file is too large : '+ f_size);
                            document.getElementById('custo_mer').value = ""; 
                        } 
                    }
                    else{ 
                        alert('file is not fasta or align');
                        document.getElementById('custo_mer').value = ""; 
                     }
                 
});


function get_fasta(file_data){
            var user = "<?php echo $username;?>";
            var project = "<?php echo $current_project;?>";
            var bar = $('#bar');
            var percent = $('.percent');
            var status = $('#status');
          
           $.ajax({
                   type:"post",
                   dataType: 'text',
                   url:"<?php echo base_url('Run_mothur_qiime/check_fasta');?>/"+user+"/"+project,
                   data: file_data,
                   cache: false,
                   processData: false,
                   contentType: false,
                    beforeSend: function () {
                        console.log("beforeSend");
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        //Download progress
                        xhr.upload.addEventListener("progress", function (evt) {
                             //console.log(evt.loaded);
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                bar.width(Math.round(percentComplete * 100) + "%");
                                percent.html(Math.round(percentComplete * 100) + "%");
                                
                            }
                        }, false);
                       return xhr;
                    },
                    complete: function (xhr) {
                         
                          if(xhr.responseText == '0'){
                                alert("File is not fasta");
                                status.html("File is not fasta");
                                document.getElementById('custo_mer').value = ""; 
                                bar.width(Math.round(0) + "%");
                                percent.html(Math.round(0) + "%");
                          }else { 
                               alert(xhr.responseText); 
                               status.html(xhr.responseText);
                               bar.width(Math.round(0) + "%");
                               percent.html(Math.round(0) + "%");
                          }
                         
                                
                    },   
                   error:function(e){
                      console.log(e.message);
                   }
           });
}


$(document).ready(function (){ 

       var status = "<?=$status?>";
       var step_run = "<?=$step_run?>";
       var id_job = "<?=$id_job?>";
       var current = "<?=$current?>";

       if(status != "null"){
          var send_data = new Array(id_job,current);
          if(step_run == "1"){
                alert("Preprocess"); 
                $('li.pre').attr('id','active');
                $(".Pre-test").hide();
                $(".Pre-show").show();
                $('#test_run').html('Checking Run Preprocess');
                checkrun(send_data);

          }else if(step_run == "2"){
              alert("Pick OTUs"); 
              $('li.pre').attr('id','done');
              $('li.pre2').attr('id','active');
              $(".Pre-test2").hide();
              $(".Pre-show2").show();
              $('#test_run2').html('Checking Run Pick OTUs');
              $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
              checkrun2(send_data);
          }
       }else{

           $('li.pre').attr('id','active');
       }  
       
       
       
        $("#sub-test").click(function () {
               
                  var username = document.forms["Pre-form"]["username"].value;
                  var project  = document.forms["Pre-form"]["project"].value;
                  var maximum_ambiguous = document.forms["Pre-form"]["maximum_ambiguous"].value;
                  var maximum_homopolymer = document.forms["Pre-form"]["maximum_homopolymer"].value;
                  var minimum_reads_length = document.forms["Pre-form"]["minimum_reads_length"].value;
                  var maximum_reads_length = document.forms["Pre-form"]["maximum_reads_length"].value;

                  var alignment = document.forms["Pre-form"]["alignment"].value;
                  var option_silva = document.forms["Pre-form"]["option_silva"].value;

                  var customer = document.forms["Pre-form"]["customer"].value;
                  var diffs  = document.forms["Pre-form"]["diffs"].value;
                  var classify = document.forms["Pre-form"]["classify"].value;
                  var cutoff = document.forms["Pre-form"]["cutoff"].value;
                  var optionsRadios = document.forms["Pre-form"]["optionsRadios"].value;
                  var taxon = document.forms["Pre-form"]["taxon"].value;

                  if(alignment == 'silva'){
                     alignment = option_silva;
                  }
                  
                  var array_data = new Array(username,project,maximum_ambiguous,maximum_homopolymer,minimum_reads_length,maximum_reads_length,alignment,customer,diffs,classify,cutoff,optionsRadios,taxon);
                 
                  if(maximum_ambiguous != "" && maximum_homopolymer != "" && minimum_reads_length != "" && maximum_reads_length != "" && alignment != ""){

                        $(".Pre-test").hide();
                        $(".Pre-show").show();
                        getvalue(array_data);
                   }    
        });

        $("#sub-test2").click(function () {
               
                var username = document.forms["Pre-form2"]["username"].value;
                var project  = document.forms["Pre-form2"]["project"].value;
                var f_map = document.forms["Pre-form2"]["f_map"].value;
                var permanova = document.forms["Pre-form2"]["permanova"].value;
                var anosim = document.forms["Pre-form2"]["anosim"].value;
                var adonis = document.forms["Pre-form2"]["adonis"].value;
                var opt_permanova = document.forms["Pre-form2"]["opt_permanova"].value;
                var opt_anosim = document.forms["Pre-form2"]["opt_anosim"].value;
                var opt_adonis = document.forms["Pre-form2"]["opt_adonis"].value;

                var array_data = new Array(username,project,f_map,permanova,anosim,adonis,opt_permanova,opt_anosim,opt_adonis);
                if(f_map == 'Noerror'){

                     $(".Pre-test2").hide();
                     $(".Pre-show2").show();
                     getvalue2(array_data); 
                }
                
         });


    $("#check_map").click(function () { 
        var user = "<?=$username ?>";
        var project = "<?=$current_project ?>";
        var data_arr = new Array(user,project);
        var time = 10;
        var interval = null;
            interval = setInterval(function(){   
            time--;
            $('#img_map').attr("src","<?php echo $srcload;?>"); 
                if(time === 0){
                    $.ajax({ 
                        type:"post",
                        datatype:"json",
                        url:"<?php echo base_url('Run_mothur_qiime/check_map_file');?>/"+user+"/"+project,
                        success:function(data){
                            var map = JSON.parse(data);
                             if(map == 'Noerror'){
                                 clearInterval(interval);
                                 getGroup(data_arr);  
                                 $('#text_map').text(" No errors or warnings were found in mapping file.");
                                 document.getElementById('p_map').value = map;
                                 $('#img_map').attr("src","<?php echo $src;?>");  
                             }
                             else{ time = 5;  } 
                        }
                    });
                }

             },1000);                                   
     });

    
 });


function getGroup(data_arr){

     var data_value = data_arr;
     $.ajax({
        type:"post",
        datatype:"json",
        url:"<?php echo base_url('Run_mothur_qiime/map_json'); ?>",
        data:{data: data_value},
        success:function(data){
            var reData = $.parseJSON(data);
            if(reData[0] == "1"){
                var name_group = "";
                name_group += "<option value='none'>none</option>";
                for (var i=0; i < reData[1].length; i++){
                        name_group += "<option value="+reData[1][i]+">"+reData[1][i]+"</option>";    
                }
                $('#permanova').html(name_group);
                $('#anosim').html(name_group);
                $('#adonis').html(name_group);   
            }                
        },
        error:function(e){
             console.log(e.message);
        }             
    });                       
}

function getvalue(array_data){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_mothur_qiime/run_mothur'); ?>",
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
                url:"<?php echo base_url('Run_mothur_qiime/check_run_mothur'); ?>",
                data:{data_job: job_val },
                success:function(data){
                //console.log("data : " + JSON.parse(data));
                var data_up = $.parseJSON(data);
                   if(data_up[0] == "0"){
                            $('#test_run').html('Queue complete');
                            clearInterval(interval);

                             $('.sw-theme-arrows > .nav-tabs > .pre').next('li').find('a').trigger('click'); 
                             $(".Pre-show").hide();
                             $(".Pre-test").show();
                             
                             $('li.pre').attr('id','done');
                             $('li.pre2').attr('id','active');
                             $('#sub-test2').attr('class','btn btn-primary');
                             $('#check_map').attr("disabled",false);
                           
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



function getvalue2(array_data){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_mothur_qiime/mothur_qiime1'); ?>",
                    data:{data_array: data_value},
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
                url:"<?php echo base_url('Run_mothur_qiime/check_run_mothur_qiime1'); ?>",
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


</script> 
<!--  End Advance Script -->

   