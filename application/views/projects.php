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
            <?php // echo "User :" . $username . "   Email :" . $email . "   ID :" . $id . "    PROJECT_SESS :" . $current_project ;?>
            <br>
            <?php foreach ($rs as $r) {
                   $type_primers =  $r['project_platform_type'];
            }
            ?>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li <?php if ($controller_name == 'projects') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'projects') { ?>Current projects<?php } else { ?><a
                        href="<?php echo site_url('projects/index/' . $current_project) ?>">Current
                            project</a><?php } ?></li>
            </ol>
        </div>

    </div>

    <?php

    foreach ($rs as $r) {
        $sample_folder = $r['project_path'];
    }
    $project = basename($sample_folder);
    $user = $this->session->userdata['logged_in']['username'];

    $path = "../owncloud/data/$user/files/$project/output/";


    ?>

    
<div class="row">
     <div class="col-lg-12">
     <div class="uk-child-width-1-6\@xl" uk-grid>
     <div>
         <ul class="uk-tab-right" uk-switcher="animation: uk-animation-fade" uk-tab>
         <li class="uk-active ">
            <a class="uk-text-capitalize uk-text-bold" href="#">
                Standard Mode 
              <i class="fa fa-question-circle-o" aria-hidden="true" title="“Standard Mode” was designed in such a way that will optimize and generate the meaningful information/result/output for most of the samples submitted. In many cases, however, data requires a fine adjustment to the pipeline parameter for meaningful output/result. Thus the “Advance Mode” could be more appreciated." uk-tooltip></i>
             </a>
         </li>
         <li>
             <a class="uk-text-capitalize uk-text-bold" href="1" onclick="advance_mode(this);">Advance Mode 
             <i class="fa fa-question-circle-o" aria-hidden="true" title="“Advance Mode”, is designed for the optimum use of the software.  Users can make changes to the pipeline parameter which in turns increase the flexibility of the software. This mode allows the pipeline to be adjusted so that it will be able to handle with different types of data. Hence, it will be more applicable to different type of experiments. Introductions and recommendations to steps including quality control, align sequences & clean alignment, pre-cluster sequences & chimera detection, classify sequences, remove bacterial sequences, OTU preparation, which are provided in the section below. " uk-tooltip></i>
             </a>
         </li>
         </ul>

         <ul class="uk-switcher">
         <li>
             <div>
             <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade">
             <li><a href="#">Run</a></li>
             <!--<li><a href="#">Result & Graph</a></li>-->
             </ul>
                 <ul class="uk-switcher uk-margin">
                 <li>
                 <!-- Standard run -->
                 <?php echo form_open_multipart('projects/standard_run/' . $current_project) ?>
             <div class="panel panel-info ">
             <div class="panel-heading">Run Standard</div>
                <div class="panel-body">
                <div class="panel panel-default">
                <div class="panel-heading">
                <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">1.Quality control #1 
                    <i class="fa fa-question-circle-o" aria-hidden="true" title="In “Quality Control” section, every setting is made to control the overall result’s reliability and resolution. The term ‘ambiguous’ refers to n-base, an unknown/unidentified base, hence it is of our best interest to keep the value of ‘maximum ambiguous’ as low as possible. The same trend was applied to the ‘maximum homopolymer’ since homopolymer refers to the repeated bases (<8 is recommended). The minimum and maximum read length recommended for this software is between 100 – 250 base pairs. Although, depending on different dataset, the parameter setting of the software may varies." uk-tooltip></i></a>
                </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="row">
                <div class="col-lg-2">

                <label>Screen reads </label>

                </div>
                <div class="col-lg-7">

                <table border="0" class="uk-table uk-table-middle">

                <div class="form-group">

                <tr>
                    <td>maximum ambiguous :</td>
                    <td><input class="uk-input form-control " type="text" name="max_amb" value="8" readonly="readonly" placeholder="8">
                    </td>
                </tr>
                <tr>
                    <td>maximum homopolymer :</td>
                    <td><input class="uk-input form-control" type="text" name="max_homo" value="8" readonly="readonly" placeholder="8">
                    </td>
                </tr>

                <tr>
                    <td>minimum reads length :</td>
                    <td><input class="uk-input form-control" type="text" name="min_read" value="100" readonly="readonly" placeholder="100">
                    </td>
                </tr>

                <tr>
                    <td>maximum reads length :</td>
                    <td><input class="uk-input form-control" type="text" name="max_read" value="260" readonly="readonly" placeholder="260">
                    </td>
                 </tr>

                 </div>

                 </table>

                 </div>

                </div>


                    


                </div>
                </div>
                </div>
                <div class="panel panel-default">
                <div class="panel-heading">
                <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">2.Align sequence & Clean alignment #2
                <i class="fa fa-question-circle-o" aria-hidden="true" title="There are 3 options for the “Alignment Step” in total which are SILVA, Greengenes and RDP. The default was set to be SILVA due to the fact that this database has a better performance at doing alignment than other databases. (REFERENCE) " uk-tooltip></i>
                </a>
                </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">

                <div class="row">
                <div class="col-lg-2">
                <label>Alignment step </label>
                </div>
                <div class="col-lg-7">
                <div class="form-group">
            <select class="uk-select uk-margin" name="align_seq" readonly="readonly">
                <option>silva.v4.fasta</option>
            </select>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                     3.Pre-cluster sequence & Chimera detection #3 
                     <i class="fa fa-question-circle-o" aria-hidden="true" title="This step is performed to check for the strand similarities, in other words, to check for a possible error in some strands. The main objective of this step is to de-noise the sequences, thus providing a better resolution. To check for errors the recommended number of different bases between strand are <2. [Pre-Cluster Sequences]
In addition to that, it is possible that the (PCR-based) amplification steps in NGS-platform may cause some errors. This type of errors may cause certain mismatched cases between primer/dimer and their template strand. [Chimera Detection]" uk-tooltip></i></a>
                    </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse"></i>
                <div class="panel-body">
                <div class="row">
                <div class="col-sm-2">
                <label>Pre-cluster step</label>
                </div>
                 <div class="col-lg-7">
                 <div class="form_control">
                 <select class="uk-select" name="diffs" readonly="readonly">
                    <option value="2">diffs=2</option>
                 </select>
                 </div>
                 </div>
                 </div>
                 </div>
                 </div>
                 </div>
                 <div class="panel panel-default">
                 <div class="panel-heading">
                 <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">4.Classify sequences & Remove non-bacterial sequence #4 
                        <i class="fa fa-question-circle-o" aria-hidden="true" title="To prepare for a taxonomy classification, the a recommended database is Greengenes (set as default). Similar to the reason being the AM software, this consists of a software known as PICRUst, which was designed to predict the function of samples submitted. Furthermore, Greengenes database works at the best performance together with this software. Optionally, if the users are not interested in predicting functions and would like to change the database, SILVA and RDP are also available.
    Greengenes database is the only database that provide information down to species level, while SILVA and RDP can provide only information at the level of genus. In contrast to Greengenes, RDP database was cleaner and SILVA was mostly use for alignment (SILVA > RDP, in terms of taxa). “Taxon Elimination” is designed to remove the unwanted data (i.e., helping to clean the result). The default was set to eliminate the taxon of chloroplast, mitochondria, eukaryote, and unknown. Optionally, users can eliminate other information, which include an option to remove archaea-unknown and archaea-unknown & bacteria-unknown" uk-tooltip></i>
                    </a>
                 </h4>
                 </div>
                 <div id="collapseFour" class="panel-collapse collapse">
                 <div class="panel-body">
                 <div class="col-lg-3">
                 <label>Prepare for taxonomy classification </label>
                 </div>
                <div class="col-lg-6">
                <table class="uk-table uk-table-middle">
                 <tr>
                 <td>database</td>
                 <td>
                 <div class="form-group">
                <select class="uk-select " name="db_taxon" readonly="readonly">
                 <option>gg_13_8_99.fasta</option>
                </select>
                </div>
                 </td>
                 </tr>
                 <tr>
                 <td>cutoff</td>
                 <td>
                <div class="form-group">
                    <input class="uk-input form-control" type="text" name="cutoff"   alue="80" placeholder="80" readonly="readonly">
                </div>
                </td>
                </tr>
                <tr>
                <td>taxon elimination</td>
                <td>
                <div class="form-group">
                    <textarea class="uk-textarea form-control" name="rm_taxon" 
                    readonly="readonly">Chloroplast-Mitochondria-Eukaryota-unknown
                    </textarea>
                </div>
                </td>
                </tr>
                </table>
                </div>
                </div>
                </div>
                </div>
                <div class="panel panel-default">
                <div class="panel-heading">
                <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">5.Alpha/Beta diversity analysis #5
                <i class="fa fa-question-circle-o" aria-hidden="true" title="Alpha – Diversity: “Alpha – Diversity” is set as a default setting. This analysis will calculate the statistic for the community of bacteria within the group. A total of 5 statistical analysis will be provided in a table such as nseq, cover, sobs, Chao and Shannon. Beta – Diversity: “Beta – Diversity” will analyze the community of bacteria between different groups. During this step choices to view diagrams for different statistical calculator can be made. A total 7 statistical analysis will be provided in a table, i.e. lennon, jclass, moristahorn, sorabund, thetan, thetayc and braycurtis.  " uk-tooltip>
                </i></a>
                </h4>
                </div>
                <div id="collapseFive" class="panel-collapse collapse">
                <div class="panel-body">
                <div class="row">
                <div class="col-lg-2">
                <label>Alpha diversity</label>
                 </div>
                <div class="col-lg-7 col-lg-offset-3">
                 **subsamples detect from files<br>
                 subsamples <input class="uk-input uk-width-1-4" value="5000" disabled>
                 </div>
                 </div>
                 <div class="row">
                 <div class="col-lg-2">
                 <label>Beta diversity</label>
                 </div>
                 <div class="col-lg-7 col-lg-offset-3">
                 **The number of total reads/groups after the preprocess<br>
                 **subsamples detect from files<br>
                 subsamples <input class="uk-input uk-width-1-4" value="5000" disabled>
                  <div class="row">
                <table>
                 <tr>
                 <td>
                    Level 2 is used for analysis :
                 </td>
                 <td>
                <div class="form-group">
                     <select class="uk-select" name="tax_level" readonly="readonly">
                     <option>1</option>
                     <option value="2" selected> 2 </option>
                     <option>3</option>
                     <option>4</option>
                     <option>5</option>
                     <option>6</option>
                     </select>
                </div>
                </td>
                </tr>
                </table>
                 <div class="col-lg-6">
                 <ul>
                    <li>venn diagram</li>
                    <li>UPGMA tree</li>
                    <ul>
                    <li>Thetayc</li>
                    <li>Jclass</li>
                    </ul>
                    <li>PCOA</li>
                    <ul>
                    <li>Thetayc</li>
                     <li>Jclass</li>
                    </ul>
                     <li>NMDS</li>
                    <ul>
                    <li>Thetayc</li>
                    <li>Jclass</li>
                     </ul>
                 </ul>
                 </div>
                 </div>
                 </div>
                 </div>
                <div class="row">
                <div class="col-lg-2">
                <label>Option</label>
                </div>
                <div class="col-lg-6">
                <ul>
                <?php
                    foreach ($rs as $r) {
                        $sample_folder = $r['project_path'];
                    }
                    $project = basename($sample_folder);
                    $path_owncloud = "../owncloud/data/" . $username . "/files/" . $project . "/input/";
                    $file_files = array('design');
                    $file_metadata = array('metadata');
                    $file_oligos = array('oligos');
                    $check_file = '0';
                    $check_metadata = '0';
                    $check_oligos = '0';
                    $result_folder = array();
                    $result_file = array();
                    if (is_dir($path_owncloud)) {
                         $select_folder = array_diff(scandir($path_owncloud, 1), array('.', '..'));
                    $cdir = scandir($path_owncloud);
                    foreach ($cdir as $key => $value) {
                            if (!in_array($value, array('.', '..'))) {
                                if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                                    $result_folder[$value] = $value;
                                } else {
                                        $type = explode('.', $value);
                                        $type = array_reverse($type);
                                        if (in_array($type[0], $file_files)) {
                                            $check_file = 'have_files';
                                        }
                                        if (in_array($type[0], $file_metadata)) {
                                            $check_metadata = 'have_metadata';
                                        }
                                        if (in_array($type[0], $file_metadata)) {
                                            $file_oligos = 'have_oligos';
                                         }
                                 }
                            }
                     }
                    }
                ?>
                <?php if ($check_file == '0') { ?>
                    <li>Please upload file.design? 
                        <?php echo form_upload('design','','required'); ?>  
                    </li>
                <?php } ?>
                     <?php if ($check_metadata == '0') { ?>
                     <li>Please upload file.metadata? <?php echo form_upload('metadata','','required'); ?>
                     </li>
                <?php } ?>
                     <li>
                <?php if ($check_oligos == '0') { ?>
                        Please upload file.oligos? <?php echo form_upload('oligos','','required'); ?>
                <?php if ($type_primers != ""){ ?>
                         <button  type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal">Create Oligos</button>
                <?php } ?>
                <?php } ?></li>
                </ul>
                </div>
                </div>
                </div>
                </div>
                </div>
                <button type="submit" name="save" class="btn btn-default pull-right">
                  Submit
                 </button>
                 </div>
                 </div>
                 </li>
                 </ul>
                 </div>
                <?php echo form_close() ?>
                            <!-- <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                                Launch Demo Modal
                            </button> -->
                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form id="contact_form" action="<?php echo base_url() ?>projects/save_oligos/" method="POST">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                            <div class="input_fields_wrap_primer">
                                                <button class="add_field_button_primer">Add More Fields primer</button> <button class="add_field_button_barcode">Add More Fields bar</button>
                                                <div><input type="text" value="primer" name="col1[]"><input type="text" name="col2[]"><input type="text" value="NONE" name="col3[]"></div>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="input_fields_wrap_barcode">

                                                <div><input type="text" value="barcode" name="colbar1[]"><input type="text" name="colbar2[]"><input type="text" value="NONE" name="colbar3[]"><input type="text"  name="colbar4[]"></div>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                        </form>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>

                        </li>
       <!-- End Standard run -->


     <!-- ADVANCE  -->
     <li>
         <link href="<?php echo base_url();?>tooltip/smart_wizard_theme_arrows.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/loading.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/tooltip.css" rel="stylesheet" />
         <script src="<?php echo base_url();?>tooltip/tooltip.js" type="text/javascript"></script>
        <!--  <script src="<?php echo base_url();?>tooltip/html2canvas.js" type="text/javascript"></script> -->

         <div class="sw-theme-arrows">
             <ul class="nav-tabs step-anchor" uk-switcher="animation: uk-animation-fade">
                 <li class="pre"><a href="#">Step 1<br />Preprocess & Prepare in taxonomy</a></li>
                 <li class="pre2"><a href="#">Step 2<br />Prepare <?=$project_analysis ?> </a></li>
                 <li class="pre3"><a href="#">Step 3<br />Analysis</a></li>
                 <li><a href="#">Step 4<br />Result & graph</a></li>
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
                     <div class="col-lg-4">
                        <input id="sub-test" class="btn btn-primary" value="Run Preprocess ">  
                     </div>
                     </div>

                  
                                                

             </form><!-- close row form -->
             </div> <!-- Pre-test -->
             <div class="row">
                 <div class="col-lg-11 ">
                    <div class="Pre-show" style="display:none"> 
                      
                      <div class="loader">
                          <p class="h1">Process Queue Preprocess</p>
                          <span></span>
                          <span></span>
                          <span></span>
                      </div>
                     <!-- <div id="time">0</div> -->
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
                                 <a  data-toggle="collapse" data-parent="#accordion" href="#collapse13" >1. Show data in count group 
                                 <i class="fa fa-question-circle-o"></i>        
                                 
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse13" class="panel-collapse collapse">
                             <div class="panel-body">    

                                        <label>The number of total reads/group after the preprocess</label>
                                        <p class="col-lg-10">  show data in count group </p>                                         
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


                         <div class="panel panel-default">
                             <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse14"> 2. Sub Sample 
                                    <i class="fa fa-question-circle-o"></i>  
                                    
                                     </a> 
                                 </h4>
                             </div>
                             <div id="collapse14" class="panel-collapse collapse">
                             <div class="panel-body">

                                  <!-- Phylotype-form -->
                                  <form name="Phylotype-form" method="post">
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

                          <div class="col-lg-12 uk-margin"></div>
                          <div class="row">
                          <div class="col-lg-1"></div>
                          <div class="col-lg-4">
                              <input id="sub-test2" class="btn btn-primary disabled" value="Run Preprocess">   
                         </div>
                         </div>
                         <div class="col-lg-12 uk-margin"></div>

                 </form><!-- close  form --> 

                 </div>
                 </div>        

             </div>  <!-- /.col-lg-11 -->
             </div><!-- /.row -->


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
                          <p class="h1">Process Queue Prepare</p>
                          <span></span>
                          <span></span>
                          <span></span>
                      </div>
                     <!-- <div id="time2">0</div> -->
                     <div class="col-lg-5 col-lg-push-1 "> <b>Status : </b></div>
                     <div class="col-lg-5 col-lg-pull-3" id="test_run2">sub sample</div>
                     
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


      <!-- Analysis -->                              
     <li>

     <div class="Pre-test3">
        
      <!-- /.row -->
                <div class="row">
                <div class="col-lg-11">

                 <!-- Analysis-form -->
                <form name="Analysis-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="username" value="<?= $username ?>">
                    <input type="hidden" name="project" value="<?= $current_project ?>">


                 <!-- .panel-heading -->
                 <div class="panel-body">
                     <div class="panel-group" id="accordion">

                       <div class="panel panel-info">
                         <div class="panel-heading">          
                             <h4 class="panel-title">
                                 <a  data-toggle="collapse" data-parent="#accordion" href="#collapse12" >1. Taxonomy Level 
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div8');"></i>         
                                
                                 </a>  
                             </h4>
                         </div>
                         <div id="collapse12" class="panel-collapse collapse">
                             <div class="panel-body">

                                <label>Please select level that you want to analyse </label>
                               
                                     <div class="Greengene">

                                     <p class="col-lg-6"> Greengenes : </p>            
                                        <div class="col-lg-6 col-lg-pull-4">
                                             <select class="uk-select" id="g_level">
                                                 <option value="1"> species</option>
                                                 <option value="2" selected> genus</option>
                                                 <option value="3"> family</option>
                                                 <option value="4"> order</option>
                                                 <option value="5"> class</option>
                                                 <option value="6"> phylum</option>
                                             </select>
                                         </div>
                                     </div>

                                     <div class="Silva_RDP" style="display:none">
                                     <p class="col-lg-6"> 
                                            Silva / RDP : </p>
                                             <div class="col-lg-6 col-lg-pull-4">
                                                 <select class="uk-select" id="sr_level">
                                                     <option value="1"> genus</option>
                                                     <option value="2"> family</option>
                                                     <option value="3"> order</option>
                                                     <option value="4"> class</option>
                                                     <option value="5"> phylum</option>
                                                 </select>
                                             </div>
                                     </div>

                                     <div class="Otu" style="display:none">
                                     <p class="col-lg-6"> OTU : </p>
                                             <div class="col-lg-6 col-lg-pull-5">
                                                 <select class="uk-select" id="o_level">
                                                     <option value="0.03" selected> 0.03</option>
                                                     <option value="0.05"> 0.05</option>
                                                     <option value="0.10"> 0.10</option>
                                                     <option value="0.20"> 0.20</option>
                                                 </select>
                                             </div>
                                     </div>
                             </div>
                         </div>
                         </div>

                        <div class="panel panel-default">
                         <div class="panel-heading">          
                             <h4 class="panel-title">
                                 <a  data-toggle="collapse" data-parent="#accordion" href="#collapse6" >2. Alpha – Diversity 
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div6');"></i>           
                                
                                 </a> 
                             </h4>
                         </div>
                         <div id="collapse6" class="panel-collapse collapse">
                             <div class="panel-body">       
                                    <label> Alpha diversity analysis </label>
                                    <label> Summary alpha statistical analysis </label>
                                            <div class="radio">
                                             <label> <input name="optionsRadios" value="1" type="radio"> set  the size of your smallest group :
                                             <input class="uk-input" id="alpha" name="size_alpha" type="number" min="0" onkeypress='return validateNumber(event)'>        
                                             </label>
                                             </div>
                                             <div class="radio">
                                             <label> <input name="optionsRadios" id="myradio" type="radio" checked> No need set the size </label>
                                            </div>
                             </div>
                         </div>
                         </div>
                         <div class="panel panel-info">
                             <div class="panel-heading">
                                 <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse7">3. Beta – Diversity 
                                    <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div7');"></i>  
                                    
                                     </a>
                                 </h4>
                             </div>
                             <div id="collapse7" class="panel-collapse collapse">
                             <div class="panel-body">
                                     <label>Beta diversity analysis </label>
                                     <label> Summary beta statistical analysis </label>
                                            <div class="radio">
                                                 <label> <input name="optionsRadios1" value="1" type="radio"> set the size of your smallest group :
                                                 <input class="uk-input" id="beta" name="size_beta" type="number" min="0" onkeypress='return validateNumber(event)' >               
                                                 </label>
                                            </div>
                                            <div class="radio">
                                                 <label><input name="optionsRadios1" id="myradio1" type="radio" checked> No need set the size  </label>     
                                            </div>

                             </div>
                         </div>
                         </div>
                         <div class="panel panel-default">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse8">4. Venn Diagram 
                                 <i class="fa fa-question-circle-o"></i>                           
                                 
                                 </a>
                             </h4>
                         </div>
                         <div id="collapse8" class="panel-collapse collapse">
                             <div class="panel-body">

                                  <label class="col-lg-11"> Venn Diagram</label>       
                                        <p class="col-lg-11">Please put the sample name</p>
                                        <label class="col-lg-3 "><select class="uk-select" name="venn1" id="venn1"></select></label>
                                        <label class="col-lg-3 "><select class="uk-select" name="venn2" id="venn2"></select></label>
                                        <label class="col-lg-3 "><select class="uk-select" name="venn3" id="venn3"></select></label>               
                                        <label class="col-lg-3"><select class="uk-select" name="venn4" id="venn4"></select> </label>
                             </div>
                         </div>
                         </div>
                         <div class="panel panel-info">
                         <div class="panel-heading">
                              <h4 class="panel-title">
                                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse9">5. UPGMA tree with calculator 
                                 <i class="fa fa-question-circle-o"></i>  
                                 
                                 </a>
                         </h4>
                         </div>
                         <div id="collapse9" class="panel-collapse collapse">
                         <div class="panel-body">
                             <div class="col-lg-10">
                             <label  class="col-lg-7"> Community structure </label>
                             <div class="col-lg-7 col-lg-push-1 ">
                                 <input type='checkbox' name='upgma_st[]' value='braycurtis'>braycurtis <br/> 
                                 <input type='checkbox' name='upgma_st[]' value='thetan'>thetan <br/>
                                 <input type='checkbox' name='upgma_st[]' value='thetayc'>thetayc <br/>
                                 <input type='checkbox' name='upgma_st[]' value='morisitahorn'> morisitahorn <br/>   
                                 <input type='checkbox' name='upgma_st[]' value='sorabund'>  sorabund                
                             </div>
                             </div>
                             <div class="col-lg-10">
                             <label class="col-lg-7"> Community membership </label>                          
                             <div class="col-lg-7 col-lg-push-1 ">
                                 <input type='checkbox' name='upgma_me[]' value='jclass'>jclass <br/>
                                 <input type='checkbox' name='upgma_me[]' value='lennon '>lennon
                             </div>
                             </div>
                             </div>

                         </div>
                         </div>
                         <div class="panel panel-default">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse10">6. Ordination Method
                                <i class="fa fa-question-circle-o"></i>  
                                 
                                 </a> 
                             </h4>
                         </div>
                         <div id="collapse10" class="panel-collapse collapse">
                         <div class="panel-body">

                                <label class="col-lg-10"> PCoA : <input name="func" type="radio" id="radio_pcoa" value="usepcoa"> Use PCoA</label>
                                <div class="col-lg-7 col-lg-push-2 ">
                                     <label class="col-lg-7"> Community structure</label>
                                     <div class="col-lg-7 col-lg-push-1 ">
                                         <input type='checkbox' name='pcoa_st[]' value='thetayc' checked disabled> thetayc <br/> 
                                         <input type='checkbox' name='pcoa_st[]' value='braycurtis' class="pcoa" disabled> braycurtis <br/>
                                         <input type='checkbox' name='pcoa_st[]' value='thetan' class="pcoa" disabled> thetan <br/>
                                         <input type='checkbox' name='pcoa_st[]' value='morisitahorn' class="pcoa" disabled> morisitahorn <br/>
                                         <input type='checkbox' name='pcoa_st[]' value='sorabund' class="pcoa" disabled> sorabund        
                                     </div>
                                </div>
                                <div class="col-lg-7 col-lg-push-2 ">
                                     <label class="col-lg-7">Community membership </label>
                                     <div class="col-lg-7 col-lg-push-1 ">
                                         <input type='checkbox' name='pcoa_me[]' value='jclass' class="pcoa" disabled> jclass <br/>
                                         <input type='checkbox' name='pcoa_me[]' value='lennon ' class="pcoa" disabled> lennon                          
                                     </div>
                                 </div>
                                 <div class="col-lg-12 uk-margin"></div>
                                 <label class="col-lg-10 "> NMDS : <input  name="func" type="radio" id="radio_nmds" value="usenmds"> Use NMDS</label>
                                <div class="col-lg-4 col-lg-push-2 ">
                                     <select class="uk-select" name="nmds">
                                         <option value="2"> 2D</option>
                                         <option value="3"> 3D</option>
                                     </select>
                                </div>
                                <div class="col-lg-9 col-lg-push-2 ">
                                     <label class="col-lg-7">Community structure</label>
                                     <div class="col-lg-9 col-lg-push-1 ">
                                          <input type='checkbox' name='nmds_st[]' value='thetayc' checked disabled> thetayc <br/>  
                                         <input type='checkbox' name='nmds_st[]' value='braycurtis'class="nmds" disabled> braycurtis <br/>       
                                         <input type='checkbox' name='nmds_st[]' value='thetan' class="nmds" disabled> thetan <br/>               
                                         <input type='checkbox' name='nmds_st[]' value='morisitahorn'class="nmds" disabled> morisitahorn <br/>                   
                                         <input type='checkbox' name='nmds_st[]' value='sorabund' class="nmds" disabled> sorabund                          
                                     </div>
                                </div>
                                <div class="col-lg-9 col-lg-push-2 ">
                                     <label class="col-lg-7">Community membership</label>
                                     <div class="col-lg-9 col-lg-push-1 ">
                                         <input type='checkbox' name='nmds_me[]' value='jclass' class="nmds" disabled> jclass <br/>
                                         <input type='checkbox' name='nmds_me[]' value='lennon' class="nmds" disabled> lennon
                                                                   
                                     </div>
                                 </div>
                                                                    
                                                            
                            
                         </div>
                         </div>
                         </div>

   
<link href="<?php echo base_url();?>tooltip/bootstrap-toggle.min.css" rel="stylesheet">
<script src="<?php echo base_url();?>tooltip/bootstrap-toggle.min.js"></script>

     <div class="panel panel-info">
     <div class="panel-heading">
     <h4 class="panel-title">

     <!-- data-toggle="collapse" data-parent="#accordion" href="#collapse11" -->
      <a  class="YourID" id="YourIN" >
      7. Optional
         <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div12');"></i>                        
     </a> 
    </h4>
    </div>
    <div id="collapse11" class="panel-collapse collapse">
    <div class="panel-body">
    
    <div class="col-lg-12 uk-margin">
        <h5>
         <b>7.1  Other Statistical Analysis :</b>
         <input id="toggle-event1" type="checkbox" data-toggle="toggle" data-size="small" data-onstyle="success" data-offstyle="danger" >           
        </h5>
     </div>
    <div class="col-lg-12">
    <fieldset class="optionset1" disabled>
           <div class="col-lg-12">
                             <div class="col-lg-6"> 
                                 <label> Create file design 
                                <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div11');">
                                </i> 
                              <a href="<?php echo site_url();?>createdesign/<?=$current_project?>" target="_blank"><input type="button" class="btn btn-outline btn-info" value="create design" id="check_design" ></a> 
                              </label>         
                                 <div>
                                     <p id="pass_design" class="fa fa-file-text-o" > No file design </p>                    
                                     <input type="hidden" id="p_design" name="f_design" value="nodesign">
                                     <img id="img_design" >
                                </div>
                                <p class="opt1" style="display: none;"> <font color="red">*Required</font></p>
                             </div>

                             <div class="col-lg-6 col-lg-pull-1">                             
                            <input type="checkbox" id="amova_id" name="amova"  value="amova" > Amova 
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div9');">
                                 </i>
                                 <br/>

                            <input type="checkbox" id="homova_id" name="homova"  value="homova" > Homova 
                                 <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div10');"></i> 
                                 <br/>

                            <input type="checkbox" id="anosim_id" name="anosim"  value="anosim" > Anosim 
                                 <i class="fa fa-question-circle-o"></i>  
                            </div> 
         </div>
        <div class="col-lg-12">
              <br>** homova , anosim <br> &nbsp;&nbsp;- if file design has less than 2 groups, P value can not be calculated.
        </div>
          
         
         <div class="col-lg-12 uk-margin">
            <hr class="uk-divider-icon">
                             <div class="col-lg-5"> 
                                 <label> Create file metadata 
                                     <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div13');">
                                </i> 
                              <a href="<?php echo base_url();?>createmetadata/<?=$current_project?>"  target="_blank"><input type="button" class="btn btn-outline btn-info" value="create metadata" id="check_metadata" >
                                    </a>
                                </label>   
                             <div>
                                 <p id="pass_metadata" class="fa fa-file-text-o"> No file metadata </p>
                                 <input type="hidden" id="p_metadata" name="f_metadata" value="nometadata">
                                 <img id="img_metadata">
                             </div>
                              <p class="opt1" style="display: none;"> <font color="red">*Required</font></p>

                             </div>

                            <div class="col-lg-4">
                                    <input type="checkbox" id="correlation_meta"  value="meta"  > correlation with metadata
                                     <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div14');">
                                     </i> 
                            </div>
                            <div class="col-lg-2">
                                <select class="uk-select" name="method_meta" >
                                    <option value="spearman"> spearman </option>
                                    <option value="pearson"> pearson </option>     
                                </select>
                            </div>
                            <div class="col-lg-1">
                               <!--  <select class="uk-select"  name="axes_meta" >
                                    <?php  if($project_analysis =='OTUs'){ ?>
                                            <option value="0.03"> 0.03 </option>
                                            <option value="0.05"> 0.05 </option>
                                    <?php }else{  ?>
                                        <option value="2"> 2 </option>
                                        <option value="3" id="setm3"> 3 </option>
                                        <option value="1" id="setm1"> 1 </option>
                                       
                                       
                                    <?php } ?>
                                </select> -->
                            </div>
                      
         </div>
         <div class="col-lg-12">
            <hr class="uk-divider-icon">                   
                            <div class="col-lg-6">
                                    <input type="checkbox" id="correlation_otu"  value="otu" > correlation of each OTU 
                                     <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div15');">
                                     </i> 
                            </div>
                            <div class="col-lg-3 col-lg-pull-2">
                                     <select class="uk-select"  name="method_otu" >
                                         <option value="spearman"> spearman </option>
                                         <option value="pearson"> pearson </option>     
                                     </select>
                            </div>
                            <div class="col-lg-2 col-lg-pull-2">
                                    <!-- <select class="uk-select"  name="axes_otu" >
                                        <?php  if($project_analysis =='OTUs'){ ?>
                                            <option value="0.03"> 0.03 </option>
                                            <option value="0.05"> 0.05 </option>
                                        <?php }else{  ?>
                                            <option value="2"> 2 </option>
                                        <option value="3" id="seto3"> 3 </option>
                                        <option value="1" id="seto1"> 1 </option>
                                        <?php } ?>
                                    </select>   -->
                            </div>
                            <div class="col-lg-6">
                                <p class="opt1" style="display: none;"> <font color="red">*Required</font></p>
                            </div>
         </div>


        </fieldset>
        </div>
    <div class="col-lg-12 uk-margin">
    <h5>
    <b>7.2  PICRUSt  and STAMP :</b>
        <input id="toggle-event2" type="checkbox" data-toggle="toggle" data-size="small" data-onstyle="success" data-offstyle="danger" >
    </h5> 
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
                <option value="Fisher" selected>Fisher 's exact test</option>
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
                 <option value="DP1" selected>DP: Newcombe‐Wilson</option>
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
                 <option value="0.05" selected>0.05</option>
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
        </div>
        </div>
         <div class="col-lg-12 uk-margin"></div>
         <div class="col-lg-4 col-lg-push-2">
            <input id="sub-test3" class="btn btn-primary" value="Run Preprocess">
         </div>
    <div class="col-lg-12 uk-margin"></div>

    </form>  <!-- end Analysis form-->

    <script type="text/javascript">
            
          $(document).on("click", ".YourID", function() {
                var getid = $(".YourID").attr("id");
                if(getid == "YourIN"){
                     $('.YourID').attr('id','YourOUT');
                     $('#myModal1').modal('show');
                    
                }else{
                     $('.YourID').attr('id','YourIN'); 
                     $("#collapse11").collapse('toggle'); 
                }      
          });

          $(document).on("click", "#close_modal", function() { 
                  $('#myModal1').modal('toggle');
                  $("#collapse11").collapse('toggle');
          });
        
    </script>

    <!-- Modal -->
    <div class="panel-body">
        <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" style="display: none;"> 
                     <div class="modal-dialog">
                     <div class="modal-content">
                     <div class="modal-header">
                     <button type="button" id="close_modal" class="close" aria-hidden="true">×</button>
                     <h4 class="modal-title" id="myModalLabel1"> 7. Optional</h4>                                                     
                   </div>
        <div class="modal-body">
                   <h5>This step is the optional analysis. You can select other statistics and/or determine the predicted metabolic functions using PICRUSt and evaluate them statistically with STAMP.</h5>
        </div>
        <div class="modal-footer">
             <button class="btn btn-primary" id="close_modal" aria-hidden="true" >OK </button>
        </div>
        </div> <!-- /.modal-content -->
        </div> <!-- /.modal-dialog -->                                         
        </div>
    </div><!-- End Modal -->   

    </div>  <!-- /.col-lg-11 -->
    </div><!-- /.row -->
    </div> <!-- Pre-test3 -->

    <div class="row">
                 <div class="col-lg-11">
                    <div class="Pre-show3" style="display:none"> 
                      
                      <div class="loader">
                          <p class="h1">Process Queue Analysis</p>
                          <span></span>
                          <span></span>
                          <span></span>
                      </div>
                     <!-- <div id="time3">0</div> -->
                     <div class="col-lg-5 col-lg-push-1 "> <b>Status : </b></div>
                     <div class="col-lg-5 col-lg-pull-3" id="test_run3">Wait Queue</div>

                     <div class="col-lg-11 col-lg-push-1 uk-margin">
                     <div class="progress progress-striped active">
                         <div id="bar_pre3" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                         <div class="percent_pre3">0%</div>
                     </div>
                     </div>
                    
                     </div> 
                     </div> 
                 </div> 
    </div>

    </li>  <!-- End Analysis -->

   
     <!-- Result && Graph -->
     <li>

        <hr class="uk-divider-icon">
            <div class="panel-body">       
            <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
               

            <div class="alert alert-info">
            <center>

            <button type="button" class="btn btn-warning  btn-circle btn-xl" id="btn_graph">
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

         <hr class="uk-divider-icon">
             <div class="panel-body">
             <div class="row">
             <div class="col-lg-6 col-lg-offset-3">

             <div class="alert alert-info">
             <center>
            
            <button type="button" class="btn btn-danger  btn-circle btn-xl" id="">
                <i class="fa fa-refresh"></i>
              </button>
                     <h4>Re-Run Mothur</h4>
             </center>
            
             </div>    
             </div>
             </div>
             </div>

    
     </li>
     <!-- End Result && Graph -->


            </ul>
            </div>
          </li>
          <!-- End EDVANCE  -->
        </ul>
         <!-- end class="uk-switcher" -->
      </div>
    </div>
    </div>

</div>


    <script>
        $(document).ready(function () {
            $("#btn_test_run").click(function () {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url();?>projects/standard_run/<?php echo $current_project;?>"
                });// you have missed this bracket
                return false;


            });
        });

    </script>

<!--  Advance Script -->


<script type="text/javascript">  
  var console_event1 = false;
  var console_event2 = false;
 $('#toggle-event1').change(function(){
     console_event1 = $(this).prop('checked');
     if($(this).prop('checked')){
          $(".optionset1").removeAttr("disabled");
          $(".opt1").show();
         
     }else{
          $(".optionset1").attr("disabled", true);
          $(".opt1").hide();
     }

  })

 $('#toggle-event2').change(function(){
     console_event2 = $(this).prop('checked');
     if($(this).prop('checked')){
          $(".optionset2").removeAttr("disabled")
          $(".opt2").show();
          console.log(console_event2);
     }else{
          $(".optionset2").attr("disabled", true);
          $(".opt2").hide();
          console.log(console_event2);
     }
  })

document.getElementById("btn_reports").onclick = function(){

    $.ajax({ 
          type:"post",
          datatype:"json",
          url:"<?php echo base_url('ckprorun'); ?>",
          data:{current:"<?=$current_project?>"},
             success:function(data){
                var chk = JSON.parse(data); 
                if(chk == "t"){
                   location.href="<?php echo base_url();?>Advance_report/view_report/<?php echo $current_project?>";           
                }else{
                    alert("run mode advance");
                }
              
            }
                   
     });

};

document.getElementById("btn_graph").onclick = function(){
    
    $.ajax({ 
          type:"post",
          datatype:"json",
          url:"<?php echo base_url('chkdir'); ?>",
          data:{current:"<?=$current_project?>"},
             success:function(data){
                var dir = JSON.parse(data); 
                if(dir == "TRUE"){
                   location.href=" <?php echo site_url('showimg/'.$current_project); ?>";           
                }else{
                    alert("run mode advance");

                }
              
            }
                   
     });

};


$(document).ready(function (){ 

          $('li.pre').attr('id','active');

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

                  var username = document.forms["Phylotype-form"]["username"].value;
                  var project  = document.forms["Phylotype-form"]["project"].value;
                  var ch_numsub = document.forms["Phylotype-form"]["max_subsample"].value;
                  var sample = document.forms["Phylotype-form"]["subsample"].value;
                  var array_data = new Array(username,project,sample);
                
                    if((sample != "") && (Number(sample) <= Number(ch_numsub))){
                         $(".Pre-test2").hide();
                         $(".Pre-show2").show();
                         get_subsample(array_data);
                    }else{
                        alert("input subsample greater than "+ch_numsub);
                    }   
    
            });

            $("#back_preprocess").click(function(){
                 $('.sw-theme-arrows > .nav-tabs > .pre2').prev('li').find('a').trigger('click');                 
                 $('li.pre').attr('id','active');
                 $('li.pre2').attr('id','');
                 $('#sub-test2').attr('class','btn btn-primary disabled');
             });


            var design_stop = "";
            var metadata_stop = "";
            var check_ven_all = null;
            var amova = null;
            var homova = null;
            var anosim = null;
            var correlation_meta = null;
            var correlation_otu  = null;
            var checkconrun = "close";
            var checkstatus1 = "close";
            var checkstatus2 = "close";

$("#sub-test3").click(function () {
                var username = document.forms["Analysis-form"]["username"].value;
                var project  = document.forms["Analysis-form"]["project"].value;
                var level    = document.forms["Analysis-form"]["level"].value;
                
                var ch_alpha = document.forms["Analysis-form"]["optionsRadios"].value;
                var size_alpha = document.forms["Analysis-form"]["size_alpha"].value;

                var ch_beta = document.forms["Analysis-form"]["optionsRadios1"].value; 
                var size_beta = document.forms["Analysis-form"]["size_beta"].value;
                  
                var venn1 = document.forms["Analysis-form"]["venn1"].value;
                var venn2 = document.forms["Analysis-form"]["venn2"].value; 
                var venn3 = document.forms["Analysis-form"]["venn3"].value;
                var venn4 = document.forms["Analysis-form"]["venn4"].value;
                  
                var nmds = document.forms["Analysis-form"]["nmds"].value ;  
                 
                var file_design = document.forms["Analysis-form"]["f_design"].value;
                var file_metadata = document.forms["Analysis-form"]["f_metadata"].value;


                var method_meta = document.forms["Analysis-form"]["method_meta"].value;
                //var axes_meta = document.forms["Analysis-form"]["axes_meta"].value;
                var axes_meta = "0";
                
                var method_otu = document.forms["Analysis-form"]["method_otu"].value;
                 // var axes_otu = document.forms["Analysis-form"]["axes_otu"].value;
                var axes_otu = "0";

                var func_use = document.forms["Analysis-form"]["func"].value;

                   var upgma_st = document.getElementsByName('upgma_st[]');
                   var upgma_me = document.getElementsByName('upgma_me[]');

                   var pcoa_st = document.getElementsByName('pcoa_st[]');
                   var pcoa_me = document.getElementsByName('pcoa_me[]');

                   var nmds_st = document.getElementsByName('nmds_st[]');
                   var nmds_me = document.getElementsByName('nmds_me[]');

                   var d_upgma_st = create_var(upgma_st);
                   var d_upgma_me = create_var(upgma_me);

                   var d_pcoa_st = create_var(pcoa_st);
                   var d_pcoa_me = create_var(pcoa_me);

                   var d_nmds_st = create_var(nmds_st);
                   var d_nmds_me = create_var(nmds_me);


     var kegg = document.forms["Analysis-form"]["kegg"].value;
     var sample_comparison = document.forms["Analysis-form"]["sample_comparison"].value;
     var statistical_test =  document.forms["Analysis-form"]["statistical_test"].value;
     var ci_method = document.forms["Analysis-form"]["ci_method"].value;
     var p_value = document.forms["Analysis-form"]["p_value"].value;
     
      design_stop = "stop";
      metadata_stop = "stop";

    
var array_data = new Array(username, project, level, ch_alpha, size_alpha, ch_beta, size_beta, venn1, venn2, venn3, venn4, d_upgma_st, d_upgma_me, d_pcoa_st, d_pcoa_me, nmds, d_nmds_st, d_nmds_me, file_design, file_metadata, amova, homova, anosim, correlation_meta, method_meta, axes_meta, correlation_otu, method_otu, axes_otu, kegg, sample_comparison, statistical_test, ci_method, p_value ,console_event1 ,console_event2,func_use);

// check condition run 
    if(username != "" && project != "" &&  level != "" &&  venn1 != "0" && venn2 != "0" && check_ven_all == "start" ){
            if((d_upgma_st != 0 || d_upgma_me != 0 ) && (d_pcoa_st != 0 || d_pcoa_me != 0 || d_nmds_st != 0 || d_nmds_me != 0 )){
                 checkconrun = "open";     
            }else{  checkconrun = "close"; }
    }else{  checkconrun = "close";  } 
                  
    if( file_design != "nodesign" &&
        (amova != null || homova != null || anosim !=null)){ 
         checkstatus1 = "open";                     
    }else{ checkstatus1 = "close"; }
           
    if(sample_comparison != "0" && statistical_test !="0" && ci_method != "0" &&   p_value !="0" ){
        checkstatus2 = "open";            
    }else{ checkstatus2 = "close"; } 
 
// end check condition run


// Run  
if(console_event1 && !console_event2){
    if((checkconrun == "open") && (checkstatus1 == "open")){
            console.log(array_data);
            console.log("Options 7.1");
            $(".Pre-test3").hide();
            $(".Pre-show3").show();
            get_analysis(array_data);
           }
         
}else if(console_event2 && !console_event1){
        if((checkconrun == "open") && (checkstatus2 == "open")){
            console.log(array_data);
            console.log("Options 7.2");
             $(".Pre-test3").hide();
             $(".Pre-show3").show();
             get_analysis(array_data);
            }

}else if(console_event1 && console_event2){
         if((checkconrun == "open") && (checkstatus1 == "open") && (checkstatus2 == "open" )){
            console.log(array_data);
            console.log("All options");
            $(".Pre-test3").hide();
             $(".Pre-show3").show();
             get_analysis(array_data);
           }
           
}else{
        if(checkconrun == "open"){
            console.log(array_data);
            console.log("No options");
            $(".Pre-test3").hide();
            $(".Pre-show3").show();
            get_analysis(array_data); 
        
        }
}
                  
             
});


var ven1,ven2,ven3,ven4;     
$('#venn1').change(function(){
        ven1 = $('#venn1').val();
        if(ven1 != '0'){
            if(ven1 === ven2 || ven1 === ven3 || ven1 === ven4){
                 alert('Duplicate value');
                 check_ven_all = "stop";
            }else{check_ven_all = "start"; }
        }
                
});
$('#venn2').change(function(){
         ven2 = $('#venn2').val();
         if(ven2 != '0'){
            if(ven2 === ven1 || ven2 === ven3 ||ven2 === ven4){
                alert('Duplicate value');
                check_ven_all = "stop";
            }else{check_ven_all = "start";}           
        }
});
$('#venn3').change(function(){
        ven3 = $('#venn3').val();
        if(ven3 != '0'){
             if(ven3 === ven1 || ven3 === ven2 || ven3 === ven4 ){
                alert('Duplicate value');
                check_ven_all = "stop";
            }else{check_ven_all = "start";} 
        }
 });
$('#venn4').change(function(){
        ven4 = $('#venn4').val();
        if(ven4 != '0'){
            if(ven4 === ven1 || ven4 === ven2 || ven4 === ven3){
                 alert('Duplicate value');
                 check_ven_all = "stop";
            }else{check_ven_all = "start"; }
        }
 });

$('#correlation_meta').change(function(){
        if($(this).is(':checked')){
            correlation_meta = $('#correlation_meta').val();
        }else{correlation_meta = null;}
});
$('#correlation_otu').change(function(){
        if($(this).is(':checked')){
            correlation_otu = $('#correlation_otu').val();
        }else{correlation_otu = null; }
});
$('#amova_id').change(function(){
        if($(this).is(':checked')){
                amova = $("#amova_id").val();
        }else{amova = null;}
 });
$('#homova_id').change(function(){
        if($(this).is(':checked')){
            homova = $("#homova_id").val();
        }else{ homova = null;}
});
$('#anosim_id').change(function(){
        if($(this).is(':checked')){
            anosim = $("#anosim_id").val();
        }else{anosim = null;}
});

});

</script> 

<!--  End Advance Script -->

    <script>
        function printContent(el) {
            var restorepage = document.body.innerHTML;
            var printcontent = document.getElementById(el).innerHTML;
            document.body.innerHTML = printcontent;
            window.print();
            document.body.innerHTML = restorepage;

        }


        $(document).ready(function() {
            var max_fields      = 10; //maximum input boxes allowed
            var wrapper         = $(".input_fields_wrap_primer"); //Fields wrapper
            var add_button      = $(".add_field_button_primer"); //Add button ID

            var x = 1; //initlal text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).append('<div><input type="text" value="primer" name="col1[]"/><input type="text" name="col2[]"/><input type="text" value="NONE" name="col3[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })

            var max_fields_bar      = 10; //maximum input boxes allowed
            var wrapper_bar         = $(".input_fields_wrap_barcode"); //Fields wrapper
            var add_button_bar      = $(".add_field_button_barcode"); //Add button ID

            var y = 1; //initlal text box count
            $(add_button_bar).click(function(e){ //on add input button click
                e.preventDefault();
                if(y < max_fields_bar){ //max input box allowed
                    y++; //text box increment
                    $(wrapper_bar).append('<div><input type="text" value="barcode" name="colbar1[]"/><input type="text" name="colbar2[]"/><input type="text"  name="colbar3[]"/><input type="text" value="NONE" name="colbar4[]"/><a href="#" class="remove_field_barcode">Remove bar</a></div>'); //add input box
                }
            });

            $(wrapper_bar).on("click",".remove_field_barcode", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); y--;
            })




        });
    </script>

             
             



