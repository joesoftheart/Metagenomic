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
            <?php// echo "User :" . $username . "   Email :" . $email . "   ID :" . $id . "    PROJECT_SESS :" . $current_project ;?>
            <br>
            <?php foreach ($rs as $r) {
             //   echo "Name project :" . $r['project_name'];
            }
             ?>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li <?php if ($controller_name == 'projects'){
                    echo "class=active";} ?>><?php if ($controller_name == 'projects'){?>Current project<?php } else {?><a href="<?php echo site_url('projects/index/'.$current_project)?>">Current project</a><?php } ?></li>
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

    <?php
    $keywords_split_line = array();
    $progress = "owncloud/data/$user/files/$project/output/progress.txt";


    if(file_exists($progress)){
        if ($file_progress = fopen($progress, "r")) {
            $keywords_split_line = preg_split("/[\n]/", fread($file_progress, filesize($progress)));
            print_r($keywords_split_line);


        }
        $num = count($keywords_split_line);
        echo "Runinggggggggggggggg";
        echo $num;

        ?>
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar"
                 aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
                40%
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table"  width="100%" border="0">

                     <tr>
                         <td><?php if(in_array("check_file",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>check_file</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("check_oligos",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>check_oligos</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("make_contigs_oligos",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>make_contigs_oligos</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("make_contigs_summary",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>make_contigs_summary</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("screen_seqs",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>screen_seqs</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("classify_system",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>classify_system</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("phylotype_count",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>phylotype_count</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("sub_sample_summary",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>sub_sample_summary</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("plot_graph",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>plot_graph</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("plot_graph_r_heatmap",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>plot_graph_r_heartmap</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("plot_graph_r_NMD",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>plot_graph_r_NMD</td>
                     </tr>
                     <tr>
                         <td><?php if(in_array("plot_graph_r_Rare",$keywords_split_line)){?><i class="fa fa-check"><?php }?></td>
                         <td>plot_graph_r_Rare</td>
                     </tr>



                </table>
            </div>


        </div>




        <?php
    }else{
        echo "No Runinggggggggggg";


    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="uk-child-width-1-6\@xl" uk-grid>
                <div>
                    <ul class="uk-tab-right" uk-switcher="animation: uk-animation-fade" uk-tab>
                        <li class="uk-active "><a class="uk-text-capitalize uk-text-bold" href="#">Standard Mode</a></li>
                        <li><a class="uk-text-capitalize uk-text-bold" href="#">Advance Mode</a></li>

                    </ul>
                    <ul class="uk-switcher">

                        <li>
                            <div>

                                <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade">
                                    <li ><a href="#">Run</a></li>
                                    <li><a href="#">Result && Graph</a></li>
                                </ul>
                                <ul  class="uk-switcher uk-margin">
                                    <li>
                                        <!-- Standard run -->
                                        <?php echo form_open_multipart('projects/standard_run/'.$current_project);?>

                                            <div class="col-lg-8 col-lg-offset-2">
                                                <label>1. Preprocess & Prepare in taxonomy </label><br><br>

                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-1">
                                                        <label>** Screen reads  :</label>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <table  border="0">
                                                            <tr>
                                                                <td>maximum ambiguous :</td>
                                                                <td><input class="uk-input" type="text" name="cmd" value="" placeholder="8" disabled></td>
                                                            </tr>
                                                            <tr>
                                                                <td>maximum homopolymer :</td>
                                                                <td><input class="uk-input" type="text" name="cmd" value="" placeholder="8" disabled></td>
                                                            </tr>
                                                            <tr>
                                                                <td>minimum reads length :</td>
                                                                <td><input class="uk-input" type="text" name="cmd" value="" placeholder="260" disabled></td>
                                                            </tr>
                                                            <tr>
                                                                <td>maximum reads length :</td>
                                                                <td><input class="uk-input" type="text" name="cmd" value="" placeholder="260" disabled></td>
                                                            </tr>
                                                        </table>

                                                    </div>
                                                </div><br>
                                                <br>
                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-1">
                                                        <label>**Alignment step :</label>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <select class="uk-select uk-margin" disabled>
                              <option>silva.v4.fasta</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-1">
                                                        <label>**Pre-cluster step :</label>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <select class="uk-select" disabled>
                                                            <option>diffs=2</option>

                                                        </select>
                                                    </div>


                                                </div><br><br>
                                                <div class="row">
                                                    <div class="col-lg-5 col-lg-offset-1">
                                                        <label>**Prepare the taxonomy classification :</label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <table>
                                                            <tr>
                                                                <td>database :</td>
                                                                <td>
                                                                    <select class="uk-select" disabled>
                                                                        <option>gg_13_8_99.fasta</option>
                                                                    </select>
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td>cutoff :</td>
                                                                <td><input class="uk-input" type="text" name="cutoff" value="" placeholder="80" disabled></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Test to remove taxon :</td>
                                                                <td> <textarea class="uk-textarea" type="textarea" name="texonomy" value="" placeholder="Chloroplast-Mitochondria-Eukaryota-unknown" disabled></textarea>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div><br><br>





                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <label>2. Prepare phylotype</label>

                                                    </div>
                                                    <div class="col-lg-7">
                                                        **The number of total reads/group after the preprocess<br>
                                                        **subsample detect form file<br>
                                                        subsample :<input class="uk-input uk-width-1-4" value="5000" disabled>
                                                    </div>
                                                </div><br>


                                                <div class="row">

                                                    <div class="col-lg-5">
                                                        <label>3.Analysis :</label>
                                                    </div>

                                                    <div class="col-lg-7">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    Level 2 is used for analysis :
                                                                </td>
                                                                <td>
                                                                    <select class="uk-select" disabled>
                                                                        <option>1</option>
                                                                        <option selected>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>


                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-1">
                                                        <label>3.2 Beta diversity analysis</label>
                                                    </div>
                                                    <div class="col-lg-6">

                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-2">
                                                        <label>**calculators</label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <ul>
                                                            <li>sobs</li>
                                                            <li>chao</li>
                                                            <li>shannon</li>
                                                            <li>simpson</li>
                                                        </ul>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-1">
                                                        <label>3.1 Alpha diversity analysis</label>
                                                    </div>
                                                    <div class="col-lg-6">

                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-2">
                                                        <label></label>
                                                    </div>
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


                                                <div class="row">
                                                    <div class="col-lg-4 col-lg-offset-2">
                                                        <label>**optional</label>
                                                    </div>
                                                    <div class="col-lg-6">

                                                        <ul>

                                                            <?php
                                                            foreach ($rs as $r) {
                                                                $sample_folder = $r['project_path'];

                                                            }
                                                            $project = basename($sample_folder);
                                                            $path_owncloud = "../owncloud/data/" . $username . "/files/" . $project ."/data/input/";
                                                            $file_files = array( 'design');
                                                            $file_metadata = array('metadata');
                                                            $check_file = '0';
                                                            $check_metadata = '0';
                                                            $result_folder = array();
                                                            $result_file = array();

                                                            if (is_dir($path_owncloud)) {
                                                                $select_folder = array_diff(scandir($path_owncloud, 1),array('.','..'));
                                                                $cdir = scandir($path_owncloud);
                                                                foreach ($cdir as $key => $value) {

                                                                    if (!in_array($value, array('.', '..'))) {
                                                                        if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                                                                            $result_folder[$value] = $value;

                                                                        } else {

                                                                            $type = explode( '.', $value );
                                                                            $type = array_reverse( $type );
                                                                            if(in_array( $type[0], $file_files ) ) {

                                                                                $check_file = 'have_files';
                                                                            }

                                                                            if(in_array($type[0],$file_metadata)){

                                                                                $check_metadata = 'have_metadata';

                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }





                                                            ?>

                                                            <?php if ($check_file == '0') { ?>
                                                            <li>Please upload file.design?    <?php echo form_upload('design'); ?></li>
                                                            <?php }  ?>

                                                            <?php if ($check_metadata == '0') { ?>
                                                            <li>Please upload file.metadata?    <?php echo form_upload('metadata'); ?></li>
                                                            <?php } ?>
                                                        </ul>

                                                    </div>
                                                </div>





                                                <div class="row uk-margin">

                                                    <p id="show_prephy"></p>
                                                </div>
<!--                                                --><?php //foreach ($rs_process as $rs_p){
//                                                    $status = $rs_p['number_process'];
//
//                                                } ?>
                                                <button id="btn_prepro"  name="submit" class="btn btn-default pull-right" >Submit</button>
                                            </div>
                                        </form>
                                    </li>
                                    <li>

                                        <?php

                                        foreach ($rs as $r) {
                                            $sample_folder = $r['project_path'];
                                        }
                                        $project = basename($sample_folder);
                                        $user = $this->session->userdata['logged_in']['username'];

                                        $path = "../owncloud/data/$user/files/$project/output/";



                                        ?>



                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>Ven diagram</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?><?php echo $path ?>final.tx.2.subsample.2.sharedsobs.S1_1_16s_S1-S2_1_16s_S3-S3_1_16s_S5-S4_1_16s_S7.svg">
                                            </div>
                                            <div class="col-lg-6">
                                                <b>Heatmap</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?><?php echo $path ?>heartmap.png">
                                            </div>
                                        </div>

                                        <hr class="uk-divider-icon">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>*Heatmap-Jclass</b><br>
                                                <img class="img-thumbnail" height="50%" width="50%" src="<?php echo base_url(); ?>uploads/final.tx.jclass.2.lt.ave.heatmap.sim.svg">
                                            </div>
                                            <div class="col-lg-6">
                                                <b>*Heatmap-Thetayc</b><br>
                                                <img class="img-thumbnail" height="50%" width="50%"  src="<?php echo base_url(); ?>uploads/final.tx.thetayc.2.lt.ave.heatmap.sim.svg">
                                            </div>
                                        </div>
                                        <hr class="uk-divider-icon">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>Rarefaction</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?><?php echo $path ?>Rare.png">
                                            </div>
                                            <div class="col-lg-6">
                                                <b>RelativePhylum</b>
                                                <img class="img-thumbnail"  src="<?php echo base_url(); ?><?php echo $path ?>Abun.png">
                                            </div>
                                        </div>
                                        <hr class="uk-divider-icon">
                                        <b>NMDS</b>
                                        <div class="row">
                                            <div class="col-lg-6 col-lg-offset-3">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?><?php echo $path ?>NMD.png">
                                            </div>

                                        </div>
                                        <hr class="uk-divider-icon">
                                        <b>Alpha</b>
                                        <div class="row">
                                            <div class="col-lg-6 col-lg-offset-3">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?><?php echo $path ?>Alpha.png">
                                            </div>

                                        </div>

                                    </li>
                                </ul>

                            </div>
                       </li>
     
                <?php echo form_close();?>
              <!-- End Standard run -->

             <!-- ADVANCE  -->

                        <li>
                            <div>
                                <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade" >
                                    <li class="pre"><a href="#">Preprocess & Prepare in taxonomy </a> </li>
                                    <li class="pre2"><a href="#">Prepare phylotype </a></li>
                                    <li><a href="#">Analysis</a></li>
                                    <li><a href="#">Result & visualization</a></li>
                                </ul>
                                <ul  class="uk-switcher uk-margin">


                                  <!--Preprocess && Prepare in taxonomy -->
                                  
                                    <li>
                                        <div class="Pre-test">
                                         <form name="Pre-form" method="post" action="#" enctype="multipart/form-data" > 

                                           <input type="hidden" name="username" value="<?=$username?>">
                                           <input type="hidden" name="project" value="<?=$current_project?>">
                                            <div class="col-lg-8 col-lg-offset-2">

                                                <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Select option run your project  </label></div>
                                               


                                                 <div class="col-lg-10 col-lg-pull-1"><label> Screen reads </label></div>
                                                 <div class="form-inline col-lg-12">
                                                      <label class="col-lg-6"> maximum ambiguous : </label>
                                                      <input id="mbig" class="form-control" type="number" name="maximum_ambiguous" min="0" placeholder="maximum ambiguous" onblur="checkvalue()">
                                                    
                                                 </div>
                                                 <div class="form-inline col-lg-12 uk-margin">
                                                     <label class="col-lg-6"> maximum homopolymer : </label>
                                                     <input id="mhomo" class="form-control" type="number" name="maximum_homopolymer" min="0" placeholder="maximum homopolymer" onblur="checkvalue2()">
                                                 </div>
                                                 <div class="form-inline col-lg-12">
                                                     <label class="col-lg-6"> minimum reads length : </label>
                                                     <input id="miniread" class="form-control" type="number" name="minimum_reads_length" min="0" placeholder="minimum reads length" onblur="checkvalue3()">
                                                 </div>
                                                 <div class="form-inline col-lg-12 uk-margin">
                                                    <label class="col-lg-6"> maximum reads length : </label>
                                                    <input id="maxread"class="form-control" type="number" name="maximum_reads_length" min="0" placeholder="maximum reads length" onblur="checkvalue4()">
                                                 </div>
                                 
                                              

                                                 <div class="col-lg-10 col-lg-pull-1"><label> Alignment step :</label></div>
                                                  <div class="col-lg-5">
                                                      <select class="uk-select" name="alignment">
                                                      <option value="silva" selected> Silva </option>
                                                      <option value="gg"> Greengenes </option>
                                                      <option value="rdp"> RDP </option>
                                                  </select>
                                                  </div>
                                                  <label class="col-lg-1"> OR </label>
                                                  <div class="col-lg-5 "> 
                                                     Limit fasta size file 800 MB   
                                                    <input type="file" name="customer" id="custo_mer"> 
                                                    <div class="progress progress-striped active">
                                                            <div id="bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100"  style="width:0%;">
                                                                <div class="percent">0%</div >
                                                            </div>
                                                    </div>
                                                    <div id="status"></div>
                                                  </div>
                                                <div class="col-lg-12 uk-margin"></div>
                                                <div class="col-lg-10 col-lg-pull-1"><label> Pre-cluster step :</label></div>
                                                    <div class="col-lg-12">
                                                        <select class="uk-select" name="diffs">
                                                            <option value="0">diffs = 0</option>
                                                            <option value="1">diffs = 1</option>
                                                            <option value="2" selected>diffs = 2</option>
                                                            <option value="3">diffs = 3 </option>
                                                        </select>
                                                    </div>
                                             


                                                <div class="col-lg-12 uk-margin"> </div>
                                                <label class="col-lg-10 col-lg-pull-1">Prepare the taxonomy classification :</label>
                                                    <div class="col-lg-4">
                                                        <select class="uk-select" name="classify">
                                                             <option value="silva"> Silva </option>
                                                             <option value="gg" selected> Greengenes </option>
                                                             <option value="rdp"> RDP </option>
                                                        </select>
                                                    </div>

                                                <label class="col-lg-3 "> with cutoff : </label>
                                                <div class="col-lg-2 col-lg-pull-1">    
                                                    <input class="uk-input" type="number" name="cutoff" min="50" value="80">   
                                                </div>
                                                <label class="col-lg-2 col-lg-pull-1">(>=50)</label> 




                                               <div class="col-lg-12 uk-margin"> </div>


                                              <label class="col-lg-10 col-lg-pull-1"> Remove sequences from multiple taxons.</label>
                                              <label class="col-lg-10 col-lg-pull-1"> Please separating them with dashes : </label>

                                                 <div class="col-lg-12">  
                                                
                                                     <div class="radio">
                                                       <label>
                                                           <input name="optionsRadios"  value="0" type="radio" checked> default 
                                                      </label>
                                                    </div>

                                                    <div class="radio">
                                                       <label >
                                                           <input name="optionsRadios" value="1" type="radio"> 
                                                          
                                                            <select class="uk-select" name="taxon">
                                                                <option value="k__Bacteria;k__Bacteria_unclassified-k__Archaea">k__Bacteria;k__Bacteria_unclassified-k__Archaea</option>
                                                                <option value="k__Archaea_unclassified">k__Archaea_unclassified</option>
                                                                <option value="Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified" selected>Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified</option>
                                                                
                                                            </select>
                                                      </label>
                                                    </div>
                                                </div>


                                               <div class="col-lg-12 uk-margin"> </div>
                                               <div class="col-lg-4">
                                                  <input id="sub-test"  class="btn btn-default" value="Run Preprocess "> 
                                               </div>
                                               <div class="col-lg-8">
                                                   <input type="reset" class="btn btn-default" value="Clear" >
                                                </div>

                                                <div class="col-lg-12 uk-margin"> </div>
                                            </div><!-- close row form -->
                                        
                                        </form>
                                     </div> <!-- Pre-test -->

                                              <div class="Pre-show" style="display:none"> Process Queue 
                                               <div id="time">30</div>
                                               <div id="test_run">run queue</div>
                                               <br/>
                                                        
                                                <button id="back-test" class="btn btn-default">back</button>
                                            </div>
                                       
                                    </li>
                                    
                      <!--End Preprocess && Prepare in taxonomy -->



                       <!--Prepare phylotype -->
                                    <li >

                                       <div class="Pre-test2">
                                        <div class="col-lg-8 col-lg-offset-2">

                                             <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>The number of total reads/group after the preprocess</label></div>
                                             <div class="col-lg-10 col-lg-pull-1"><label> show data in count group :</label></div>
                                             <div class="row uk-margin">
                                                <div class="col-lg-9">
                                                    <textarea class="form-control"  rows="5"  id="show_group" ></textarea>
                                                </div>
                                                <div class="col-lg-8 col-lg-push-9">
                                                        <button class="btn btn-default" data-toggle="modal" data-target="#myModal">
                                                            Back
                                                        </button>
                                                </div>
                                             </div>
  
                                           
                                             
                                             <!-- Phylotype-form -->
                                             <form name="Phylotype-form" method="post"  > 

                                                 <input type="hidden" name="username" value="<?=$username?>">
                                                 <input type="hidden" name="project" value="<?=$current_project?>">
                                                 <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Please put the number to subsampled file  </label></div>
                                             
                                                 <div class="row uk-margin">
                                                    <div class="col-lg-8">
                                                          <label>sub sample :</label>
                                                          <input id="sub_sample" class="uk-input" type="number" min="0" name="subsample" >
                                                    </div>
                                                 </div>

                                                 <div class="col-lg-12 uk-margin"> </div>
                                                 <div class="col-lg-4">
                                                      <input  id="sub-test2" class="btn btn-default" value="Run Preprocess">
                                                 </div>
                                                 <div class="col-lg-8">
                                                       <input type="reset" class="btn btn-default" value="Clear" >
                                                 </div>

                                            </form><!-- close  form -->

                                                 <div class="col-lg-12 uk-margin"> </div>
                                            <!-- Modal -->
                                                 <div class="panel-body">    
                                                 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                     <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                <h4 class="modal-title" id="myModalLabel">Preprocess</h4>
                                                            </div>
                                                         <div class="modal-body">
                                                          Do you want to re-run preprocess again ?
                                                         </div>
                                                        <div class="modal-footer">                                        
                                                            <button id="back_preprocess" class="btn btn-primary" data-dismiss="modal">Yes</button>
                                                            <button class="btn btn-default" data-dismiss="modal">No</button>
                                                        </div>
                                                        </div>
                                                        <!-- /.modal-content -->
                                                    </div>
                                                     <!-- /.modal-dialog -->
                                                </div>
                                                </div>
                                             <!-- End Modal -->

                                          </div>

                                        </div> <!-- Pre-test2 -->

                                            <div class="Pre-show2" style="display:none"> Process Queue Sub Sample 
                                               <div id="time2">20</div>
                                               <div id="test_run2">run sub sample </div>
                                                <br/>
                                                        
                                                <button id="back-test2" class="btn btn-default">back subsample</button>
            
                                            </div>

                                    </li>

                                <!--End Prepare phylotype analysis-->



                               <!-- Analysis -->
                                 <li>

                                     <div class="Pre-test3">

                                            <!-- Analysis-form -->
                                            <form name="Analysis-form" method="post" enctype="multipart/form-data"  > 

                                                 <input type="hidden" name="username" value="<?=$username?>">
                                                 <input type="hidden" name="project" value="<?=$current_project?>">

                                                 <div class="col-lg-8 col-lg-offset-2">

                                                    <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Please select level that you want to analyse :</label></div>        
                                                    <div class="Greengene" style="display:none">
                                                    <div class="col-lg-5 col-lg-pull-1">
                                                        <label> Greengenes :   </label>
                                                    </div>
                                                    <div class="col-lg-5 col-lg-pull-4">
                                                      <select class="uk-select" id="g_level">
                                                             <option value="1"> species </option>
                                                             <option value="2" selected> genus </option>
                                                             <option value="3"> family </option>
                                                             <option value="4"> order </option>
                                                             <option value="5"> class </option>
                                                             <option value="6"> phylum </option>   
                                                        </select>
                                                    </div>
                                                    </div>
                                                    <div class="Silva_RDP" style="display:none">
                                                    <div class="col-lg-5 col-lg-pull-1">
                                                        <label> Silva/RDP :   </label>
                                                    </div>
                                                    <div class="col-lg-5 col-lg-pull-4">
                                                      <select class="uk-select" id="sr_level">
                                                             <option value="1"> genus </option>
                                                             <option value="2"> family </option>
                                                             <option value="3"> order </option>
                                                             <option value="4"> class</option>
                                                             <option value="5"> phylum </option>   
                                                        </select>
                                                    </div>
                                                    </div>
                                                    <div class="Otu" style="display:none">
                                                    <div class="col-lg-5 col-lg-pull-1">
                                                        <label> OTU :   </label>
                                                    </div>
                                                    <div class="col-lg-5 col-lg-pull-4">
                                                       <select class="uk-select" id="o_level">
                                                             <option value="0.03" selected> 0.03 </option>
                                                             <option value="0.05"> 0.05 </option>
                                                             <option value="0.10"> 0.10 </option>
                                                             <option value="0.20"> 0.20</option>
                                                            
                                                        </select>
                                                    </div>
                                                    </div>  
                     
                                                 <div class="col-lg-10 col-lg-pull-1 uk-margin"><label> 3.1 Alpha diversity analysis : </label></div>
                                                 <div class="col-lg-10 col-lg-push-1 "><label> Summary alpha statistical analysis </label></div>
                                                 <div class="col-lg-8 col-lg-push-2 ">  
                                
                                                    <div class="radio">
                                                        <label >
                                                           <input name="optionsRadios" value="1" type="radio"> set the size of your smallest group :
                                                           <input class="uk-input" id="alpha" name="size_alpha" type="number" min="0"> 
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                           <input name="optionsRadios" id="myradio"  type="radio" checked> No need set the size
                                                        </label>
                                                    </div>
                                                 </div>

                                                 <div class="col-lg-10 col-lg-pull-1 uk-margin"><label> 3.2 Beta diversity analysis : </label></div>
                                                 <div class="col-lg-10 col-lg-push-1 "><label> Summary beta statistical analysis </label></div>
                                                 <div class="col-lg-8 col-lg-push-2 ">  
                                
                                                     <div class="radio">
                                                        <label >
                                                           <input name="optionsRadios1" value="1" type="radio"> set the size of your smallest group :
                                                           <input class="uk-input" id="beta" name="size_beta" type="number" min="0" > 
                                                        </label>
                                                     </div>
                                                     <div class="radio">
                                                        <label>
                                                           <input name="optionsRadios1" id="myradio1" type="radio" checked> No need set the size
                                                        </label>
                                                     </div>
                                                 </div>

                                                 <div class="col-lg-11 col-lg-push-1 uk-margin"><label> Venn Diagram</label></div>
                                                     <label class="col-lg-11 col-lg-push-2 ">Please put the sample name : </label>
                                                    
                                                     <label class="col-lg-2 col-lg-push-2 ">
                                                         <select class="uk-select" name="venn1" id="venn1">
                 
                                                        </select>
                                                     </label>
                                                     <label class="col-lg-2 col-lg-push-2 ">
                                                         <select class="uk-select" name="venn2" id="venn2">
   
                                                        </select>
                                                     </label>
                                                     <label class="col-lg-2 col-lg-push-2 ">
                                                         <select class="uk-select" name="venn3" id="venn3">
                                                            
                                                         </select>
                                                     </label>
                                                     <label class="col-lg-2 col-lg-push-2 ">
                                                         <select class="uk-select" name="venn4" id="venn4">
                                                           
                                                         </select>
                                                     </label>
                                              
                                                     <div class="col-lg-12 uk-margin"> </div>
                                                     <div class="col-lg-10 col-lg-push-1"><label> UPGMA tree with calculator : </label></div>
                                                     <div class="col-lg-5 col-lg-push-2 ">
                                                         <select class="uk-select" name="upgma">
                                                             <option value="1"> thetayc </option>
                                                             <option value="2"> morisitahorn </option>
                                                             <option value="3"> jclass </option>
                                                             <option value="4"> braycurtis</option>
                                                             <option value="5"> lennon </option>
                                                             <option value="6"> sorabund </option>     
                                                         </select>
                                                     </div>
                                                     <div class="col-lg-12 uk-margin"> </div>
                                                     <div class="col-lg-10 col-lg-push-1"><label> PCOA : </label></div>
                                                     <div class="col-lg-5 col-lg-push-2 ">
                                                         <select class="uk-select" name="pcoa">
                                                             <option value="1"> thetayc </option>
                                                             <option value="2"> morisitahorn </option>
                                                             <option value="3"> jclass </option>
                                                             <option value="4"> braycurtis</option>
                                                             <option value="5"> lennon </option>
                                                             <option value="6"> sorabund </option>     
                                                         </select>
                                                     </div>
                                                     <div class="col-lg-12 uk-margin"> </div>
                                                     <div class="col-lg-10 col-lg-push-1"><label> NMDS : </label></div>
                                                     <div class="col-lg-4 col-lg-push-2 ">
                                                         <select class="uk-select" name="nmds">
                                                             <option value="1"> 2D </option>
                                                             <option value="2"> 3D </option>        
                                                         </select>
                                                     </div>
                                                     <div class="col-lg-5 col-lg-push-3 ">
                                                         <select class="uk-select" name="nmds_cal">
                                                             <option value="1"> thetayc </option>
                                                             <option value="2"> morisitahorn </option>
                                                             <option value="3"> jclass </option>
                                                             <option value="4"> braycurtis</option>
                                                             <option value="5"> lennon </option>
                                                             <option value="6"> sorabund </option>     
                                                         </select>
                                                     </div>
                                                     <div class="col-lg-12 uk-margin"> </div>
                                                    
                                                    
                                                     <div class="col-lg-10 col-lg-push-1" id="plus">
                                                           <label> Optional <span class="glyphicon glyphicon-plus-sign" id="plus_option"> </span> </label>
                                                           
                                                     </div>
                                                     <div class="col-lg-10 col-lg-push-1" id="move" style="display:none">
                                                           <label> Optional <span class="glyphicon glyphicon-minus-sign" id="move_option"></span> </label>
                                                           
                                                     </div>

                                                     <div class="optional" style="display:none">
                                                     <div class="col-lg-8 col-lg-push-2 "> 
                                                         <label> Create file design           
                                                          <a href="<?php echo site_url('Run_advance/create_file_design');?>" target="_blank">
                                                               <input type="button" value="create design" id="check_design">  
                                                          </a>
                                                         </label>
                                                         <div>
                                                         <p id="pass_design" class="fa fa-file-text-o"> No file design </p>
                                                         </div>

                                                     </div>
                                                     <div class="col-lg-8 col-lg-push-2 ">                
                                                     <div class="radio">
                                                         <label >
                                                         <input name="optionsRadios2" value="1" type="radio"> Amova
                                                         </label>
                                                     </div>
                                                     <div class="radio">
                                                         <label>
                                                         <input name="optionsRadios2"  value="0" type="radio"> Homova
                                                         </label>
                                                     </div>
                                                     </div>
                                                     <div class="col-lg-10 col-lg-push-2 uk-margin"> 
                                                         <label> Create file metadata  
                                                             <a href="<?php echo base_url('Run_advance/create_file_metadata'); ?>"  target="_blank">
                                                               <input type="button" value="create metadata" id="check_metadata">  
                                                          </a>
                                                         </label>
                                                         <div>
                                                           <p id="pass_metadata" class="fa fa-file-text-o"> No file metadata </p>
                                                         </div>
                                                     </div>
                                                     <div class="col-lg-12 col-lg-push-2"> 
                                                     <div class="radio">
                                                         <label class="col-lg-6">
                                                             <input name="optionsRadios3" value="1" type="radio"> correlation with metadata 
                                                         </label>
                                                         <label class="col-lg-6">
                                                             <input name="optionsRadios3"  value="0" type="radio"> correlation of each OTU
                                                         </label>
                                                     </div>  
                                                     </div> 
                                                     <div class="col-lg-12 col-lg-push-3 uk-margin"> 
                                                     <div class="col-lg-2 col-lg-pull-1">
                                                            Method 
                                                     </div>
                                                     <div class="col-lg-3 col-lg-pull-1">
                                                         <select class="uk-select" name="method">
                                                             <option value="1"> spearman </option>
                                                             <option value="2"> pearson </option>     
                                                         </select>
                                                     </div>
                                                         <div class="col-lg-4 col-lg-pull-1">
                                                               Number of axes 
                                                         </div>
                                                         <div class="col-lg-2 col-lg-pull-2">
                                                            <select class="uk-select" name="axes">
                                                                  <option value="2"> 2 </option>
                                                                  <option value="3"> 3 </option>
                                                             </select>
                                                         </div>
                                                     </div>
                                                     </div>


                                                     <div class="col-lg-12 uk-margin"> </div>   
                                                     <div class="col-lg-4 col-lg-push-2">
                                                          <input  id="sub-test3" class="btn btn-default" value="Run Preprocess">
                                                     </div>
                                                     <div class="col-lg-8 col-lg-push-2">
                                                          <input type="reset" class="btn btn-default" value="Clear" >
                                                     </div>
                                                     <div class="col-lg-12 uk-margin"> </div>
                                                 </div>

                                             </form>  <!-- end Analysis form-->  
                                     </div> <!-- Pre-test3 -->

                                             <div class="Pre-show3" style="display:none"> Process Queue Analysis
                                                <div id="time3">30</div>
                                                <div id="test_run3">run analysis </div>
                                                <br/>
                                                        
                                                <button id="back-test3" class="btn btn-default">back analysis</button>
                                             </div>
                                 </li>

                                <!-- End Analysis -->




                                   <!-- Result && Graph -->
                                    <li >
                                       <div class="row">
                                            <div class="col-lg-6">
                                                <b>Ven diagram</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>owncloud/data/joesoftheart/files/SAMPLE-WES-2023/data/output/Rare.png">
                                            </div>
                                            <div class="col-lg-6">
                                                <b>Heatmap</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/Fig3_heatmaptest.jpg">
                                            </div>
                                        </div>

                                        <hr class="uk-divider-icon">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>*Heatmap-Jclass</b><br>
                                                <img class="img-thumbnail" height="50%" width="50%" src="<?php echo base_url(); ?>uploads/final.tx.jclass.2.lt.ave.heatmap.sim.svg">
                                            </div>
                                            <div class="col-lg-6">
                                                <b>*Heatmap-Thetayc</b><br>
                                                <img class="img-thumbnail" height="50%" width="50%"  src="<?php echo base_url(); ?>uploads/final.tx.thetayc.2.lt.ave.heatmap.sim.svg">
                                            </div>
                                        </div>
                                        <hr class="uk-divider-icon">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>Rarefaction</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/Fig1_rarefactionSoil.jpg">
                                            </div>
                                            <div class="col-lg-6">
                                                <b>RelativePhylum</b>
                                                <img class="img-thumbnail"  src="<?php echo base_url(); ?>uploads/Rplot.jpeg">
                                            </div>
                                        </div>
                                        <hr class="uk-divider-icon">
                                        <b>NMDS</b>
                                        <div class="row">
                                            <div class="col-lg-6 col-lg-offset-3">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/Fig4_NMDS.jpg">
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
        <?php }  ?>
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

    <script>
       //$(document).ready(function () {
//            $("#btn_prephy").click(function () {
//                $.ajax({
//                    type: "POST",
//                    url: "<?php //echo base_url();?>//projects/run_prepare_phylotype",
//                    data: {text: $("#text").val()},
//                    dataType: "text",
//                    cache:false,
//                    success:
//                        function(data){
//                            $("#show_prephy").html(data);
//                        }
//                });// you have missed this bracket
//                return false;
//            });
//
//        });

    </script>


   <!--  Advance Script -->
    <script type="text/javascript">
        $(document).ready(function () {

            $("#sub-test").click(function () {
               
                  var username = document.forms["Pre-form"]["username"].value;
                  var project  = document.forms["Pre-form"]["project"].value;
                  var maximum_ambiguous = document.forms["Pre-form"]["maximum_ambiguous"].value;
                  var maximum_homopolymer = document.forms["Pre-form"]["maximum_homopolymer"].value;
                  var minimum_reads_length = document.forms["Pre-form"]["minimum_reads_length"].value;
                  var maximum_reads_length = document.forms["Pre-form"]["maximum_reads_length"].value;
                  var alignment = document.forms["Pre-form"]["alignment"].value;
                  var customer = document.forms["Pre-form"]["customer"].value;
                  var diffs  = document.forms["Pre-form"]["diffs"].value;
                  var classify = document.forms["Pre-form"]["classify"].value;
                  var cutoff = document.forms["Pre-form"]["cutoff"].value;
                  var optionsRadios = document.forms["Pre-form"]["optionsRadios"].value;
                  var taxon = document.forms["Pre-form"]["taxon"].value;
                  
                  var array_data = new Array(username,project,maximum_ambiguous,maximum_homopolymer,minimum_reads_length,maximum_reads_length,alignment,customer,diffs,classify,cutoff,optionsRadios,taxon);
      
                  if(maximum_ambiguous != "" && maximum_homopolymer != "" && minimum_reads_length != "" && maximum_reads_length != ""){
                        $(".Pre-test").hide();
                        $(".Pre-show").show();
                        getvalue(array_data);
                   }    
                
            });

            $("#plus_option").click(function(){
                 $(".optional").show();
                 $("#move").show();
                 $("#plus").hide();
            });

            $("#move_option").click(function(){
                $(".optional").hide();
                 $("#move").hide();
                 $("#plus").show();
            });

            $("#back-test").click(function(){
                 $(".Pre-show").hide();
                 $(".Pre-test").show();
            });

            $("#back-test2").click(function(){
                 $(".Pre-show2").hide();
                 $(".Pre-test2").show();
            });

            $("#back-test3").click(function(){
                 $(".Pre-show3").hide();
                 $(".Pre-test3").show();
            });

           $("#back_preprocess").click(function(){
              $('.uk-child-width-expand > .pre2').prev('li').find('a').trigger('click');   
            });

            $("#sub-test2").click(function () {
               
                  var username = document.forms["Phylotype-form"]["username"].value;
                  var project  = document.forms["Phylotype-form"]["project"].value;
                  var sample = document.forms["Phylotype-form"]["subsample"].value;
                  var array_data = new Array(username,project,sample);
                  
                  if(sample != ""){
                         $(".Pre-test2").hide();
                         $(".Pre-show2").show();
                         //console.log(username+" "+project+" "+sample);
                         document.getElementById('alpha').value = sample;
                         document.getElementById('beta').value = sample;
                         document.getElementById('myradio').value = sample;
                         document.getElementById('myradio1').value = sample;
                         get_subsample(array_data);
                  }   
    
            });

            $("#sub-test3").click(function () {
                  var username = document.forms["Analysis-form"]["username"].value;
                  var project  = document.forms["Analysis-form"]["project"].value;
                  var level    = document.forms["Analysis-form"]["level"].value;
                  var optionsRadios ;
                  var size_alpha;
                  var optionsRadios1;
                  var size_beta;
                  var venn1 , venn2, venn3 ,venn4;
                  var upgma;
                  var pcoa;
                  var nmds, nmds_cal;

                  var array_data = new Array(username,project);

                  alert("level : "+level);
                  //$(".Pre-test3").hide();
                  //$(".Pre-show3").show();
                 // get_analysis(array_data);
            });

            $("#check_design").click(function () {
                 var user = "<?php echo $username ?>";
                 var project = "<?php echo $current_project ?>";
                 var time = 10;
                 var interval = null;
                 interval = setInterval(function(){   
                 time--;
                    if(time === 0){
                     $.ajax({ 
                       type:"post",
                       datatype:"json",
                       url:"<?php echo base_url('Run_advance/check_file_design');?>?user="+user+"&project_id="+project,
                         success:function(data){
                             var design = JSON.parse(data);
                             if(design != "0"){
                                   clearInterval(interval);
                                   $('#pass_design').text(" "+design);
                             }
                             else{  
                                  time = 5;  
                             } 
                         }
                     });
                   }

                 },1000);      
            });

             $("#check_metadata").click(function(){
                 var user = "<?php echo $username ?>";
                 var project = "<?php echo $current_project ?>";
                 var time = 10;
                 var interval = null;
                 interval = setInterval(function(){   
                 time--;
                    if(time === 0){
                     $.ajax({ 
                       type:"post",
                       datatype:"json",
                       url:"<?php echo base_url('Run_advance/check_file_metadata');?>?user="+user+"&project_id="+project,
                         success:function(data){
                             var metadata = JSON.parse(data);
                             if(metadata  != "0"){
                                   clearInterval(interval);
                                   $('#pass_metadata').text(" "+metadata);
                             }
                             else{  
                                  time = 5;  
                             } 
                         }
                     });
                   }

                 },1000);      
                  
            });

        });
         
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

            var bar = $('#bar');
            var percent = $('.percent');
            var status = $('#status');
          
           $.ajax({
                   type:"post",
                   dataType: 'text',
                   url:"<?php echo base_url('Run_advance/check_fasta'); ?>",
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

        function get_analysis(array_data){
           var data_value = array_data;
           $.ajax({
                   type:"post",
                   datatype:"json",
                   url:"<?php echo base_url('Run_advance/run_analysis'); ?>",
                   data:{data_analysis: data_value},
                   success:function(data){
                        //var job_sample = $.parseJSON(data);
                        var job_analysis = JSON.parse(data);
                        ckeck_analysis(job_analysis);
                   },
                   error:function(e){
                     console.log(e.message);
                   }
           });
  
        }

        function ckeck_analysis(job_analy){

          var time = 30;
          var interval = null;
          interval = setInterval(function(){   
              time--;
              $('#time3').html(time);
              if(time === 0){
                $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_advance/check_analysis'); ?>",
                    data:{job_analysis: job_analy },
                    success:function(data){
                      var analysis = JSON.parse(data);
                     //var sample_data = $.parseJSON(data);
                      if( analysis == "0"){
                         clearInterval(interval);
                      }else{
                         
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


       function get_subsample(array_data){
           var data_value = array_data;
           $.ajax({
                   type:"post",
                   datatype:"json",
                   url:"<?php echo base_url('Run_advance/run_sub_sample'); ?>",
                   data:{data_sample: data_value},
                   success:function(data){
                        var job_sample = $.parseJSON(data);
                        check_subsample(job_sample);
                   },
                   error:function(e){
                     console.log(e.message);
                   }
           });
   
       }


       function check_subsample(jobsample){
         
          var time = 20;
          var interval = null;
          interval = setInterval(function(){   
              time--;
              $('#time2').html(time);
              if(time === 0){
                $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_advance/check_subsample'); ?>",
                    data:{job_sample: jobsample },
                    success:function(data){
                   
                     var sample_data = $.parseJSON(data);
                      if(sample_data[0] == "0"){

                           if(sample_data[1] =="gg"){
                               $('.Greengene').show();
                               $('.Silva_RDP').hide();
                               $('.Otu').hide();
                               document.getElementById('g_level').setAttribute("name","level");

                           }else if((sample_data[1] == "silva") || (sample_data[1] == "rdp")) {
                               $('.Greengene').hide();
                               $('.Silva_RDP').show();
                               $('.Otu').hide();
                               document.getElementById('sr_level').setAttribute("name","level");
                           }
                           else{

                               $('.Greengene').hide();
                               $('.Silva_RDP').hide();
                               $('.Otu').show();
                               document.getElementById('o_level').setAttribute("name","level");

                           }

                           $('#test_run2').html('Run queue sub sample complete');
                            clearInterval(interval);
                             $('.uk-child-width-expand > .pre2').next('li').find('a').trigger('click'); 
                             $(".Pre-show2").hide();
                             $(".Pre-test2").show();
                             
                             var group = "";
                             group += "<option value=0> </option>";
                             for (var i=0; i < sample_data[2].length; i++) {
                                   group += "<option value="+sample_data[2][i]+">"+sample_data[2][i]+"</option>"; 
                                   
                             }

                             $('#venn1').html(group);
                             $('#venn2').html(group);
                             $('#venn3').html(group);
                             $('#venn4').html(group);

                      }else{
                         
                         time = 20;  
  
                      }  
                    },
                    error:function(e){
                      console.log(e.message);
                    }
                });
              }
          },1000);
        }

        function getvalue(array_data){
            var data_value = array_data;
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_advance/get_json'); ?>",
                    data:{data_array: data_value},
                    success:function(data){
                     //console.log(data);
                      var data_job = $.parseJSON(data);
                      //console.log("q_id :" + data_job[0]);
                      //console.log("q_name :" + data_job[1]);
                     
                      checkrun(data_job);
                    },
                    error:function(e){
                      console.log(e.message);
                    }
            });
            
        }

        function checkrun(job_val){
         
          var time = 30;
          var interval = null;
          interval = setInterval(function(){   
              time--;
              $('#time').html(time);
              if(time === 0){
                $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_advance/check_run'); ?>",
                    data:{data_job: job_val },
                    success:function(data){
                      //console.log("data : " + JSON.parse(data));
                     var data_up = $.parseJSON(data);
                      if(data_up[0] == "0"){
                           $('#test_run').html('Run queue complete');
                            clearInterval(interval);
                            get_prepare(data_up);
                           
                      }else{
                         var show_data = data_up[0];
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

        function get_prepare(data){
            $.ajax({ 
                    type:"post",
                    datatype:"json",
                    url:"<?php echo base_url('Run_advance/read_count'); ?>",
                    data:{data_count: data },
                    success:function(data){
                     var d_group  = "";  
                     var d_count = $.parseJSON(data);
                     for(var i=0;i < d_count.length; i++){
               
                         if(i == d_count.length-1 ){
                             document.getElementById('sub_sample').value = Number(d_count[i]);
                             document.getElementById('show_group').value = d_group;
                             $('.uk-child-width-expand > .pre').next('li').find('a').trigger('click'); 
                             $(".Pre-show").hide();
                             $(".Pre-test").show();
                         }else{
                            d_group += d_count[i];
                         }

                     }
                      
                    },
                    error:function(e){
                      console.log(e.message);
                    }
                });
        }

        
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



        
    </script>
<!--  End Advance Script -->

