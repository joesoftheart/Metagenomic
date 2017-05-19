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
    <div class="row">
        <div class="col-lg-12">
            <div class="uk-child-width-1-6\@xl" uk-grid>
                <div>
                    <ul class="uk-tab-right" uk-switcher="animation: uk-animation-fade" uk-tab>
                        <li class="uk-active"><a href="#">Standard</a></li>
                        <li><a href="#">Advance</a></li>

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
                                                                <td>ximum homopolymer :</td>
                                                                <td><input class="uk-input" type="text" name="cmd" value="" placeholder="8" disabled></td>
                                                            </tr>
                                                            <tr>
                                                                <td>manimum reads length :</td>
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
                                                            <li>anao</li>
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
                                                            <li>Please upload file.design?    <?php echo form_upload('design'); ?></li>
                                                            <li>Please upload file.metadata?    <?php echo form_upload('metadata'); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>





                                                <div class="row uk-margin">

                                                    <p id="show_prephy"></p>
                                                </div>
                                                <?php foreach ($rs_process as $rs_p){
                                                    $status = $rs_p['number_process'];

                                                } ?>
                                                <button id="btn_prepro"  name="submit" class="btn btn-default pull-right" >Submit</button>
                                            </div>
                                        </form>
                                    </li>
                                    <li>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>Ven diagram</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/sharedsobs.svg">
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
                                </ul>

                            </div>
                       </li>
              <!-- End Standard run -->
                <?php echo form_close();?>
                            <!-- End Standard run -->

                        <!-- ADVANCE  -->

                        <li>
                            <div>
                                <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade">
                                    <li><a href="#">Preprocess & Prepare in taxonomy </a></li>
                                    <li><a href="#">Prepare phylotype </a></li>
                                    <li><a href="#">Analysis</a></li>
                                    <li><a href="#">Result & visualization</a></li>
                                </ul>
                                <ul  class="uk-switcher uk-margin">


                                  <!--Preprocess && Prepare in taxonomy -->
                                    <li>
                                         <form method="post"  action="<?php echo base_url('Run_advance/form_value');?>"  >

                                           <input type="hidden" name="username" value="<?=$username?>">
                                           <input type="hidden" name="project" value="<?=$current_project?>">
                                            <div class="col-lg-8 col-lg-offset-2">

                                                <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Select option run your project  </label></div>
                                               


                                                 <div class="col-lg-10 col-lg-pull-1"><label> Screen reads </label></div>
                                                 <div class="form-inline col-lg-12">
                                                      <label class="col-lg-6"> maximum ambiguous : </label>
                                                      <input class="form-control" type="number" name="maximum_ambiguous" min="0" placeholder="maximum ambiguous" required>
                                                 </div>
                                                 <div class="form-inline col-lg-12 uk-margin">
                                                     <label class="col-lg-6"> maximum homopolymer : </label>
                                                     <input class="form-control" type="number" name="maximum_homopolymer" min="0" placeholder="maximum homopolymer" required>
                                                 </div>
                                                 <div class="form-inline col-lg-12">
                                                     <label class="col-lg-6"> minimum reads length : </label>
                                                     <input class="form-control" type="number" name="minimum_reads_length" min="0" placeholder="minimum reads length" required>
                                                 </div>
                                                 <div class="form-inline col-lg-12 uk-margin">
                                                    <label class="col-lg-6"> maximum reads length : </label>
                                                    <input class="form-control" type="number" name="maximum_reads_length" min="0" placeholder="maximum reads length" required>
                                                 </div>
                                 
                                              

                                                 <div class="col-lg-10 col-lg-pull-1"><label> Alignment step :</label></div>
                                                  <div class="col-lg-5">
                                                      <select class="uk-select" name="alignment">
                                                      <option value="silva"> Silva </option>
                                                      <option value="gg"> GG </option>
                                                      <option value="rpd"> RPD </option>
                                                  </select>
                                                  </div>
                                                  <label class="col-lg-1"> OR </label>
                                                  <div class="col-lg-5 ">    
                                                    <input class="uk-input" type="text" name="customer" value="" placeholder="customer">  
                                                  </div>
                                                <div class="col-lg-12 uk-margin"></div>
                                                <div class="col-lg-10 col-lg-pull-1"><label> Pre-cluster step :</label></div>
                                                    <div class="col-lg-12">
                                                        <select class="uk-select" name="diffs">
                                                            <option value="0">diffs = 0</option>
                                                            <option value="1">diffs = 1</option>
                                                            <option value="2">diffs = 2</option>
                                                            <option value="3">diffs = 3 </option>
                                                        </select>
                                                    </div>
                                             


                                                <div class="col-lg-12 uk-margin"> </div>
                                                <label class="col-lg-10 col-lg-pull-1">Prepare the taxonomy classification :</label>
                                                    <div class="col-lg-4">
                                                        <select class="uk-select" name="classify">
                                                             <option value="silva"> Silva </option>
                                                             <option value="gg"> GG </option>
                                                             <option value="rdp"> RDP </option>
                                                        </select>
                                                    </div>
                                                <label class="col-lg-3 "> with cutoff : </label>
                                                <div class="col-lg-2 col-lg-pull-1">    
                                                    <input class="uk-input" type="number" name="cutoff" min="50" value="50">   
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
                                                           <input class="uk-input" name="taxon" size="50" type="text" placeholder="Chloroplast-Mitochondria-Eukaryota-unknown"> 
                                                      </label>
                                                    </div>
                                                </div>


                                               <div class="col-lg-12 uk-margin"> </div>
                                               <div class="col-lg-4">
                                                  <button id=""  type="submit" class="btn btn-default">Run Preprocess</button>  
                                               </div>
                                               <div class="col-lg-8">
                                                   <button id="" type="reset" class="btn btn-default">Clear</button>
                                                </div>

                                                <div class="col-lg-12 uk-margin"> </div>
                                            </div><!-- close row form -->
                                        
                                        </form>

                                    </li>
                                  <!--End Preprocess && Prepare in taxonomy -->



                                  <!--Prepare phylotype -->
                                    <li >
                                        <div class="col-lg-8 col-lg-offset-2">

                                        <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>The number of total reads/group after the preprocess</label></div>
                                             <div class="col-lg-10 col-lg-pull-1"><label> show data in count gruop :</label></div>
                                             <div class="row uk-margin">
                                                <div class="col-lg-9">
                                                    <textarea class="form-control"  rows="5"  name="" ></textarea>
                                                </div>
                                                <div class="col-lg-8 col-lg-push-9">
                                                     <button  type="submit" class="btn btn-default">Back</button>  
                                                </div>
                                             </div>

                                        <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Please put the number to subsampled file  </label></div>
                                            
                                            <div class="row uk-margin">
                                                <div class="col-lg-8">
                                                <label>sub sample :</label>
                                                    <input class="uk-input" type="text" name="cutoff" value="" placeholder="5000">
                                                </div>
                                            </div>

                                            <div class="col-lg-12 uk-margin"> </div>
                                            <div class="col-lg-4">
                                                  <button id=""  type="submit" class="btn btn-default">Run Preprocess</button>  
                                            </div>
                                            <div class="col-lg-8">
                                                   <button id="" type="reset" class="btn btn-default">Clear</button>
                                            </div>


                                            <div class="col-lg-12 uk-margin"> </div>



                                        </div><!-- close row form -->
                                    </li>

                                <!--End Prepare phylotype analysis-->



                               <!-- Analysis -->
                                    <li >
                                        <div class="col-lg-8 col-lg-offset-2">

                                          <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Please select level that you want to analyse :</label></div>
                                            
                                          <div class="col-lg-5 col-lg-pull-1"><label> GG : </label></div>
                                                  <div class="col-lg-5 col-lg-pull-5">
                                                      <select class="uk-select" name="">
                                                             <option value="1"> 1 </option>
                                                             <option value="2"> 2 </option>
                                                             <option value="3"> 3 </option>
                                                             <option value="4"> 4 </option>
                                                             <option value="5"> 5 </option> 
                                                             <option value="6"> 6 </option>   
                                                        </select>
                                                  </div>

                                            <div class="col-lg-10 col-lg-pull-1 uk-margin"><label> 3.1 Alpha diversity analysis : </label></div>
                                            <div class="col-lg-10 col-lg-push-1 "><label> Summary alpha statistical analysis </label></div>
                                                <div class="col-lg-8 col-lg-push-2 ">  
                                
                                                    <div class="radio">
                                                        <label >
                                                           <input name="optionsRadios" value="1" type="radio"> set the size of your smallest group :
                                                           <input class="uk-input" name="" type="number" > 
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                           <input name="optionsRadios"  value="0" type="radio" checked> No need set the size
                                                        </label>
                                                    </div>
                                               </div>

                                            <div class="col-lg-10 col-lg-pull-1 uk-margin"><label> 3.2 Beta diversity analysis : </label></div>
                                            <div class="col-lg-10 col-lg-push-1 "><label> Summary beta statistical analysis </label></div>
                                                <div class="col-lg-8 col-lg-push-2 ">  
                                
                                                    <div class="radio">
                                                        <label >
                                                           <input name="optionsRadios1" value="1" type="radio"> set the size of your smallest group :
                                                           <input class="uk-input" name="" type="number" > 
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                           <input name="optionsRadios1"  value="0" type="radio" checked> No need set the size
                                                        </label>
                                                    </div>
                                               </div>

                                               <div class="col-lg-10 col-lg-push-1 uk-margin"><label> Venn Diagram</label></div>
                                                     <label class="col-lg-8 col-lg-push-2 ">
                                                        Please put the sample name : <input class="uk-input" name="" type="text" > 
                                                    </label>
                                              
                                              <div class="col-lg-12 uk-margin"> </div>
                                              <div class="col-lg-10 col-lg-push-1"><label> UPGMA tree with calculator : </label></div>
                                                    <div class="col-lg-5 col-lg-push-2 ">
                                                      <select class="uk-select" name="">
                                                             <option value="1"> thetayc </option>
                                                             <option value="2"> morisitahorn </option>
                                                             <option value="3"> jclass </option>
                                                             <option value="4"> braycurtis</option>
                                                             <option value="5"> lennon </option>
                                                             <option value="5"> sorabund </option>     
                                                        </select>
                                                   </div>

                                            <div class="col-lg-12 uk-margin"> </div>
                                            <div class="col-lg-10 col-lg-push-1"><label> PCOA : </label></div>
                                                   <div class="col-lg-5 col-lg-push-2 ">
                                                      <select class="uk-select" name="">
                                                             <option value="1"> thetayc </option>
                                                             <option value="2"> morisitahorn </option>
                                                             <option value="3"> jclass </option>
                                                             <option value="4"> braycurtis</option>
                                                             <option value="5"> lennon </option>
                                                             <option value="5"> sorabund </option>     
                                                        </select>
                                                   </div>


                                            <div class="col-lg-12 uk-margin"> </div>
                                            <div class="col-lg-10 col-lg-push-1"><label> NMDS : </label></div>
                                                   <div class="col-lg-4 col-lg-push-2 ">
                                                      <select class="uk-select" name="">
                                                             <option value="1"> 2D </option>
                                                             <option value="2"> 3D </option>
                                                                
                                                        </select>
                                                   </div>
                                                   <div class="col-lg-5 col-lg-push-3 ">
                                                      <select class="uk-select" name="">
                                                             <option value="1"> thetayc </option>
                                                             <option value="2"> morisitahorn </option>
                                                             <option value="3"> jclass </option>
                                                             <option value="4"> braycurtis</option>
                                                             <option value="5"> lennon </option>
                                                             <option value="5"> sorabund </option>     
                                                        </select>
                                                   </div>

                                            <div class="col-lg-12 uk-margin"> </div>
                                            <div class="col-lg-10 col-lg-push-1"><label> Optional : </label></div>

                                            <div class="col-lg-8 col-lg-push-2 "> 
                                                   <label> Please upload file design ? 
                                                          <input type="file">
                                                   </label>

                                            </div>

                                            <div class="col-lg-8 col-lg-push-2 ">                
                                                    <div class="radio">
                                                        <label >
                                                           <input name="optionsRadios2" value="1" type="radio"> Amora
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                           <input name="optionsRadios2"  value="0" type="radio"> Homova
                                                        </label>
                                                    </div>
                                             </div>
                                            
                                            <div class="col-lg-10 col-lg-push-2 uk-margin"> 
                                                   <label> Please upload file metadata ? 
                                                          <input type="file">
                                                   </label>
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
                                                         <select class="uk-select" name="">
                                                             <option value="1"> spearman </option>
                                                             <option value="2"> pearson </option>
                                                                
                                                        </select>
                                                   </div>
                                                
                                                        <div class="col-lg-4 col-lg-pull-1">
                                                               Number of axes 
                                                        </div>
                                                        <div class="col-lg-2 col-lg-pull-2">
                                                            <select class="uk-select" name="">
                                                                  <option value="2"> 2 </option>
                                                                  <option value="3"> 3 </option>
                                                             </select>
                                                        </div>
                                                 </div>


                                             


                                             <div class="col-lg-12 uk-margin"> </div>   

                                             <div class="col-lg-4 col-lg-push-2">
                                                  <button id=""  type="submit" class="btn btn-default">Run Preprocess</button>  
                                            </div>
                                            <div class="col-lg-8 col-lg-push-2">
                                                   <button id="" type="reset" class="btn btn-default">Clear</button>
                                            </div>

                                            <div class="col-lg-12 uk-margin"> </div>


                                        </div><!-- close row form -->
                                    </li>

                                   <!-- End Analysis -->






                                   <!-- Result && Graph -->
                                    <li >
                                       <div class="row">
                                            <div class="col-lg-6">
                                                <b>Ven diagram</b>
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/sharedsobs.svg">
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
</div>

<script >


</script>
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
        $(document).ready(function () {
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

