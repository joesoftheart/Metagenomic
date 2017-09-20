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
                //   echo "Name project :" . $r['project_name'];
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
                        <li class="uk-active "><a class="uk-text-capitalize uk-text-bold" href="#">Standard Mode <i class="fa fa-question-circle-o" aria-hidden="true" title="“Standard Mode” was designed in such a way that will optimize and generate the meaningful information/result/output for most of the samples submitted. In many cases, however, data requires a fine adjustment to the pipeline parameter for meaningful output/result. Thus the “Advance Mode” could be more appreciated." uk-tooltip></i></a>

                        </li>

                        <li><a class="uk-text-capitalize uk-text-bold" href="1" onclick="advance_mode(this);">Advance Mode <i class="fa fa-question-circle-o" aria-hidden="true" title="“Advance Mode”, is designed for the optimum use of the software.  Users can make changes to the pipeline parameter which in turns increase the flexibility of the software. This mode allows the pipeline to be adjusted so that it will be able to handle with different types of data. Hence, it will be more applicable to different type of experiments. Introductions and recommendations to steps including quality control, align sequences & clean alignment, pre-cluster sequences & chimera detection, classify sequences, remove bacterial sequences, OTU preparation, which are provided in the section below. " uk-tooltip></i></a></li>


                    </ul>
                    <ul class="uk-switcher" >

                        <li >
                            <div >

                                <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade">
                                    <li><a href="#">Run</a></li>
                                    <li><a href="#">Result & Graph</a></li>
                                </ul>
                                <ul class="uk-switcher uk-margin">
                                    <li >
                                        <!-- Standard run -->
                                        <?php echo form_open_multipart('projects/standard_run/' . $current_project); ?>



                                        <div class="panel panel-info ">
                                            <div class="panel-heading">Run Standard</div>
                                            <div class="panel-body">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">1.Quality control  #1 <i class="fa fa-question-circle-o" aria-hidden="true" title="In “Quality Control” section, every setting is made to control the overall result’s reliability and resolution. The term ‘ambiguous’ refers to n-base, an unknown/unidentified base, hence it is of our best interest to keep the value of ‘maximum ambiguous’ as low as possible. The same trend was applied to the ‘maximum homopolymer’ since homopolymer refers to the repeated bases (<8 is recommended). The minimum and maximum read length recommended for this software is between 100 – 250 base pairs. Although, depending on different dataset, the parameter setting of the software may varies." uk-tooltip></i></a>
                                                </h4>
                                            </div>
                                            <div id="collapseOne" class="panel-collapse collapse">
                                                <div class="panel-body">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">2.Align sequence & Clean alignment #2 <i class="fa fa-question-circle-o" aria-hidden="true" title="There are 3 options for the “Alignment Step” in total which are SILVA, Greengenes and RDP. The default was set to be SILVA due to the fact that this database has a better performance at doing alignment than other databases. (REFERENCE) " uk-tooltip></i></a>
                                                </h4>
                                            </div>
                                            <div id="collapseTwo" class="panel-collapse collapse">
                                                <div class="panel-body">

                                                    <div class="row">
                                                        <div class="col-lg-2">
                                                            <label>Screen reads </label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <table border="0" class="uk-table uk-table-middle">
                                                                <tr>
                                                                    <td>maximum ambiguous :</td>
                                                                    <td><input class="uk-input" type="text" name="cmd" value=""
                                                                               placeholder="8" disabled></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>maximum homopolymer :</td>
                                                                    <td><input class="uk-input" type="text" name="cmd" value=""
                                                                               placeholder="8" disabled></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>minimum reads length :</td>
                                                                    <td><input class="uk-input" type="text" name="cmd" value=""
                                                                               placeholder="260" disabled></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>maximum reads length :</td>
                                                                    <td><input class="uk-input" type="text" name="cmd" value=""
                                                                               placeholder="260" disabled></td>
                                                                </tr>
                                                            </table>

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-2">
                                                            <label>Alignment step </label>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <select class="uk-select uk-margin" disabled>
                                                                <option>silva.v4.fasta</option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">3.Pre-cluster sequence & Chimera detection #3 <i class="fa fa-question-circle-o" aria-hidden="true" title="This step is performed to check for the strand similarities, in other words, to check for a possible error in some strands. The main objective of this step is to de-noise the sequences, thus providing a better resolution. To check for errors the recommended number of different bases between strand are <2. [Pre-Cluster Sequences]
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
                                                            <select class="uk-select" disabled>
                                                                <option>diffs=2</option>

                                                            </select>
                                                        </div>


                                                    </div>
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">4.Classify sequences & Remove non-bacterial sequence #4  <i class="fa fa-question-circle-o" aria-hidden="true" title="To prepare for a taxonomy classification, the a recommended database is Greengenes (set as default). Similar to the reason being the AM software, this consists of a software known as PICRUst, which was designed to predict the function of samples submitted. Furthermore, Greengenes database works at the best performance together with this software. Optionally, if the users are not interested in predicting functions and would like to change the database, SILVA and RDP are also available.
	Greengenes database is the only database that provide information down to species level, while SILVA and RDP can provide only information at the level of genus. In contrast to Greengenes, RDP database was cleaner and SILVA was mostly use for alignment (SILVA > RDP, in terms of taxa). “Taxon Elimination” is designed to remove the unwanted data (i.e., helping to clean the result). The default was set to eliminate the taxon of chloroplast, mitochondria, eukaryote, and unknown. Optionally, users can eliminate other information, which include an option to remove archaea-unknown and archaea-unknown & bacteria-unknown" uk-tooltip></i></a>
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
                                                                    <select class="uk-select" disabled>
                                                                        <option>gg_13_8_99.fasta</option>
                                                                    </select>
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td>cutoff</td>
                                                                <td><input class="uk-input" type="text" name="cutoff"
                                                                           value="" placeholder="80" disabled></td>
                                                            </tr>
                                                            <tr>
                                                                <td>taxon elimination</td>
                                                                <td><textarea class="uk-textarea" type="textarea"
                                                                              name="texonomy" value=""
                                                                              placeholder="Chloroplast-Mitochondria-Eukaryota-unknown"
                                                                              disabled></textarea>
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
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">5.Alpha/Beta diversity analysis #5  <i class="fa fa-question-circle-o" aria-hidden="true" title="Alpha – Diversity:
	“Alpha – Diversity” is set as a default setting. This analysis will calculate the statistic for the community of bacteria within the group. A total of 5 statistical analysis will be provided in a table such as nseq, cover, sobs, Chao and Shannon.
Beta – Diversity:
	“Beta – Diversity” will analyze the community of bacteria between different groups. During this step choices to view diagrams for different statistical calculator can be made. A total 7 statistical analysis will be provided in a table, i.e. lennon, jclass, moristahorn, sorabund, thetan, thetayc and braycurtis.  " uk-tooltip></i></a>
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
                                                                $path_owncloud = "../owncloud/data/" . $username . "/files/" . $project . "/data/input/";
                                                                $file_files = array('design');
                                                                $file_metadata = array('metadata');
                                                                $check_file = '0';
                                                                $check_metadata = '0';
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
                                                                            }
                                                                        }
                                                                    }
                                                                }


                                                                ?>

                                                                <?php if ($check_file == '0') { ?>
                                                                    <li>Please upload
                                                                        file.design? <?php echo form_upload('design'); ?></li>
                                                                <?php } ?>

                                                                <?php if ($check_metadata == '0') { ?>
                                                                    <li>Please upload
                                                                        file.metadata? <?php echo form_upload('metadata'); ?></li>
                                                                <?php } ?>
                                                            </ul>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                <button id="btn_prepro" name="submit" class="btn btn-default pull-right">
                                                    Submit
                                                </button>
                                            </div>

                                        </div>
                                        </form>
                                    </li>

                                    <li id="print">

                                        <?php

                                        foreach ($rs as $r) {

                                            $sample_folder = $r['project_path'];
                                        }
                                        $project = basename($sample_folder);
                                        $user = $this->session->userdata['logged_in']['username'];

                                        $path = "../owncloud/data/$user/files/$project/output/";


                                        ?>

                                        <div class="panel panel-info " >
                                            <div class="panel-heading">Test</div>
                                            <div class="panel-body">


                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <b>Ven diagram</b>
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>sharedsobs.svg">
                                                </div>
                                                <div class="col-lg-6">
                                                    <b>Heatmap</b>
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>heatmap.png">
                                                </div>
                                            </div>

                                            <hr class="uk-divider-icon" >

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <b>*Biplot</b><br>
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>NewNMDS_withBiplotwithOTU.png">
                                                </div>
                                                <div class="col-lg-6">
                                                    <b>*Biplot</b><br>
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>NewNMDS_withBiplotwithMetadata.png">
                                                </div>
                                            </div>
                                            <hr class="uk-divider-icon">

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <b>Rarefaction</b>
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>Rare.png">
                                                </div>
                                                <div class="col-lg-6">
                                                    <b>RelativePhylum</b>
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>Abun.png">
                                                </div>
                                            </div>
                                            <hr class="uk-divider-icon">
                                            <b>NMDS</b>
                                            <div class="row">
                                                <div class="col-lg-6 col-lg-offset-3">
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>NMD.png">
                                                </div>

                                            </div>
                                            <hr class="uk-divider-icon">
                                            <b>Alpha</b>
                                            <div class="row">
                                                <div class="col-lg-6 col-lg-offset-3">
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>Alpha.png">
                                                </div>

                                            </div>
                                            <hr class="uk-divider-icon">
                                            <b>Tree</b>
                                            <div class="row" >
                                                <div class="col-lg-6 col-lg-offset-3">
                                                    <img class="img-thumbnail"
                                                         src="<?php echo base_url(); ?><?php echo $path ?>Tree.png">
                                                </div>

                                            </div>

                                            <hr class="uk-divider-icon">
                                            <?php
                                            $path_file_original_phylotype = $path . "final.tx.groups.ave-std.summary";
                                            $path_file_original_otu = $path . "final.opti_mcc.groups.ave-std.summary";
                                            if (file_exists($path_file_original_phylotype)){
                                                $path_file_original = $path_file_original_phylotype;
                                            }
                                            if (file_exists($path_file_original_otu)){
                                                $path_file_original = $path_file_original_otu;
                                            }

                                            $name_file = basename($path_file_original);



                                            ?>


                                            <b><?php echo $name_file ?></b>
                                            <div class="row" >
                                            <div class="col-lg-6">
                                                <?php


                                                if ($file_original = fopen($path_file_original, "r")) {
                                                    $keywords_split_line = preg_split("/[\n]/", fread($file_original, filesize($path_file_original)));
                                                    $num_line = count($keywords_split_line);
                                                    ?>
                                                    <table class="table table-striped table-bordered dataTable">
                                                    <thead>
                                                    <tr>
                                                        <td>groups</td>
                                                        <td>method</td>
                                                        <td>nseq</td>
                                                        <td>cover</td>
                                                        <td>sobs</td>
                                                        <td>chao</td>
                                                        <td>chao_lci</td>
                                                        <td>chao_hci</td>
                                                        <td>shannon</td>
                                                        <td>shannon_lci</td>
                                                        <td>shannon_hci</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    for ($i = 0; $i <= $num_line - 1; $i++) {
                                                        $line_split_tab_i = preg_split("/[\t]/", $keywords_split_line[$i]);
                                                        if ($line_split_tab_i[0] == "2" or $line_split_tab_i[0] == "0.03") { ?>
                                                                <tr>
                                                                    <td><?php echo $line_split_tab_i[1];?></td>
                                                                    <td><?php echo $line_split_tab_i[2];?></td>
                                                                    <td><?php echo $line_split_tab_i[3];?></td>
                                                                    <td><?php echo $line_split_tab_i[4];?></td>
                                                                    <td><?php echo $line_split_tab_i[5];?></td>
                                                                    <td><?php echo $line_split_tab_i[9];?></td>
                                                                    <td><?php echo $line_split_tab_i[10];?></td>
                                                                    <td><?php echo $line_split_tab_i[11];?></td>
                                                                    <td><?php echo $line_split_tab_i[12];?></td>
                                                                    <td><?php echo $line_split_tab_i[13];?></td>
                                                                    <td><?php echo $line_split_tab_i[14];?></td>
                                                                </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    </tbody>
                                                    </table>
                                                    <?php
                                                }
                                                ?>
                                            </div>


                                        </div>
                                        <hr class="uk-divider-icon">
                                        <?php
                                        $path_file_original_phylotype = $path . "final.tx.summary";
                                        $path_file_original_otu = $path . "final.opti_mcc.summary";
                                        if (file_exists($path_file_original_phylotype)){
                                            $path_file_original = $path_file_original_phylotype;
                                        }
                                        if (file_exists($path_file_original_otu)){
                                            $path_file_original = $path_file_original_otu;
                                        }
                                        $name_file_summary = basename($path_file_original);



                                        ?>


                                        <b><?php echo $name_file_summary ?></b>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <?php
                                                $path_file_original_phylotype = $path . "final.tx.summary";
                                                $path_file_original_otu = $path . "final.opti_mcc.summary";
                                                if (file_exists($path_file_original_phylotype)){
                                                    $path_file_original = $path_file_original_phylotype;
                                                }
                                                if (file_exists($path_file_original_otu)){
                                                    $path_file_original = $path_file_original_otu;
                                                }
                                                if ($file_original = fopen($path_file_original, "r")) {
                                                    $keywords_split_line = preg_split("/[\n]/", fread($file_original, filesize($path_file_original)));
                                                    $num_line = count($keywords_split_line);
                                                    ?>
                                                    <table class="table table-striped table-bordered dataTable" style="text-align: center">
                                                        <thead>
                                                        <tr>
                                                            <td colspan="2">comparison</td>
                                                            <td>lennon</td>
                                                            <td>jclass</td>
                                                            <td>morisitahorn</td>
                                                            <td>sorabund</td>
                                                            <td>thetan</td>
                                                            <td>thetayc</td>
                                                            <td>thetayc_lci</td>
                                                            <td>thetayc_hci</td>
                                                            <td>braycurtis</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        for ($i = 0; $i <= $num_line - 1; $i++) {
                                                            $line_split_tab_i = preg_split("/[\t]/", $keywords_split_line[$i]);
                                                            if ($line_split_tab_i[0] == "2" or $line_split_tab_i[0] == "0.03") { ?>
                                                                <tr>
                                                                    <td><?php echo $line_split_tab_i[1];?></td>
                                                                    <td><?php echo $line_split_tab_i[2];?></td>
                                                                    <td><?php echo $line_split_tab_i[4];?></td>
                                                                    <td><?php echo $line_split_tab_i[5];?></td>
                                                                    <td><?php echo $line_split_tab_i[6];?></td>
                                                                    <td><?php echo $line_split_tab_i[7];?></td>
                                                                    <td><?php echo $line_split_tab_i[8];?></td>
                                                                    <td><?php echo $line_split_tab_i[9];?></td>
                                                                    <td><?php echo $line_split_tab_i[10];?></td>
                                                                    <td><?php echo $line_split_tab_i[11];?></td>
                                                                    <td><?php echo $line_split_tab_i[12];?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>



                                                    <?php

                                                }
                                                ?>
                                            </div>

                                        </div>
                                            </div>
                                        </div>
                                        <button onclick="printContent('print')">Print</button>
                                    </li>
                                </ul>

                            </div>

                        </li>

                        <?php echo form_close(); ?>
                        <!-- End Standard run -->

     <!-- ADVANCE  -->
     <li>
         <link href="<?php echo base_url();?>tooltip/smart_wizard_theme_arrows.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/loading.css" rel="stylesheet" />
         <link href="<?php echo base_url();?>tooltip/tooltip.css" rel="stylesheet" />
         <script src="<?php echo base_url();?>tooltip/tooltip.js" type="text/javascript"></script>
         <script src="<?php echo base_url();?>tooltip/html2canvas.js" type="text/javascript"></script>

         <div class="sw-theme-arrows">
             <ul class="nav-tabs step-anchor" uk-switcher="animation: uk-animation-fade">
                 <li class="pre"><a href="#">Step 1<br />Preprocess & Prepare in taxonomy</a></li>
                 <li class="pre2"><a href="#">Step 2<br />Prepare <?= $project_analysis ?> </a></li>
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
                <div class="col-lg-11">

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
                                    <select class="uk-select" name="alignment">
                                        <option value="silva" selected> Silva</option>
                                         <option value="gg"> Greengenes</option>
                                         <option value="rdp"> RDP</option>
                                     </select>
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
                                              <button class="btn btn-primary" data-toggle="modal" data-target="#myModal"> Back Preprocess</button>
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
                     <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;"> 
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
                                 <i class="fa fa-question-circle-o"></i>         
                                
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
                                     <label class="col-lg-6"> Silva/RDP : </label>
                                             <div class="col-lg-6 col-lg-pull-3">
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
                                     <label class="col-lg-6"> OTU : </label>
                                             <div class="col-lg-6 col-lg-pull-3">
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

                                <label class="col-lg-10"> PCoA : <input name="func" type="radio" id="radio_pcoa"> Use PCoA</label>
                                <div class="col-lg-7 col-lg-push-2 ">
                                     <label class="col-lg-7"> Community structure</label>
                                     <div class="col-lg-7 col-lg-push-1 ">
                                         <input type='checkbox' name='pcoa_st[]' value='braycurtis' class="pcoa" disabled> braycurtis <br/>
                                         <input type='checkbox' name='pcoa_st[]' value='thetan' class="pcoa" disabled> thetan <br/>
                                         <input type='checkbox' name='pcoa_st[]' value='thetayc' class="pcoa" disabled> thetayc <br/>     
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
                                 <label class="col-lg-10 "> NMDS : <input  name="func" type="radio" id="radio_nmds"> Use NMDS</label>
                                <div class="col-lg-4 col-lg-push-2 ">
                                     <select class="uk-select" name="nmds">
                                         <option value="2"> 2D</option>
                                         <option value="3"> 3D</option>
                                     </select>
                                </div>
                                <div class="col-lg-9 col-lg-push-2 ">
                                     <label class="col-lg-7">Community structure</label>
                                     <div class="col-lg-9 col-lg-push-1 ">
                                         <input type='checkbox' name='nmds_st[]' value='braycurtis'class="nmds" disabled> braycurtis <br/>       
                                         <input type='checkbox' name='nmds_st[]' value='thetan' class="nmds" disabled> thetan <br/>
                                         <input type='checkbox' name='nmds_st[]' value='thetayc'class="nmds" disabled> thetayc <br/>                          
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
                        
                         <div class="panel panel-info">
                         <div class="panel-heading">
                             <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse11">7. Optional
                                <i class="fa fa-question-circle-o" onmouseover="tooltip.ajax(this, '<?php echo base_url();?>tooltip/tooltip-ajax.html#div12');"></i>  
                                 
                                 </a> 
                             </h4>
                         </div>
                         <div id="collapse11" class="panel-collapse collapse">
                         <div class="panel-body">


                             <div class="col-lg-8 "> 
                                 <label> Create file design  <a href="<?php echo site_url('Run_advance/create_file_design');?>?current=<?=$current_project?>" target="_blank"><input type="button" class="btn btn-outline btn-info" value="create design" id="check_design"></a> </label>         
                                 <div>
                                     <p id="pass_design" class="fa fa-file-text-o" > No file design </p>                    
                                     <input type="hidden" id="p_design" name="f_design" value="nodesign">
                                     <img id="img_design" src="">
                                </div>
                             </div>
                             <div class="col-lg-8 col-lg-push-1 ">                
                                 <div class="radio"><label><input name="optionsRadios2" value="amova" type="radio"> Amova </label></div>
                                 <div class="radio"><label> <input name="optionsRadios2"  value="homova" type="radio" > Homova </label></div>
                             </div>
                             <div class="col-lg-10 uk-margin"> 
                                 <label> Create file metadata <a href="<?php echo base_url('Run_advance/create_file_metadata');?>?current=<?=$current_project?>"  target="_blank"><input type="button" class="btn btn-outline btn-info" value="create metadata" id="check_metadata"></a></label>   
                             <div>
                                 <p id="pass_metadata" class="fa fa-file-text-o"> No file metadata </p>
                                 <input type="hidden" id="p_metadata" name="f_metadata" value="nometadata">
                                 <img id="img_metadata" src="">
                             </div>
                             </div>
                            
                                 <label class="col-lg-6"><input type="checkbox" id="correlation_meta"  value="meta" > correlation with metadata </label>
                                 <div class="col-lg-4 col-lg-pull-2">
                                     <select class="uk-select" name="method_meta">
                                         <option value="spearman"> spearman </option>
                                         <option value="pearson"> pearson </option>     
                                     </select>
                                </div>
                             <div class="col-lg-2 col-lg-pull-2">
                                     <select class="uk-select" name="axes_meta">
                                         <option value="2"> 2 </option>
                                         <option value="3"> 3 </option>
                                     </select>
                             </div>
                              
                            
                                 <label class="col-lg-6"><input type="checkbox" id="correlation_otu"  value="otu" > correlation of each OTU </label>
                                 <div class="col-lg-4 col-lg-pull-2">
                                     <select class="uk-select" name="method_otu">
                                         <option value="spearman"> spearman </option>
                                         <option value="pearson"> pearson </option>     
                                     </select>
                                 </div>
                                 <div class="col-lg-2 col-lg-pull-2">
                                     <select class="uk-select" name="axes_otu">
                                         <option value="2"> 2 </option>
                                         <option value="3"> 3 </option>
                                     </select>
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
         <div class="row">
            <div class="col-lg-6">
                 <div class="panel-body">
                 <label>Ven diagram</label>
                 <div id="sharedsobs_img">
                    <img id="sharedsobs_img_pass" src="#"/>
                  
                 </div>
                 </div>
             </div>
             <div class="col-lg-6">
                 <div class="panel-body">
                 <label>Heatmap</label>
                 <div id="heartmap_img">
                     <img id="heartmap_img_pass" src="#" />
                 </div>
                 </div>
            </div>
         </div>

         <hr class="uk-divider-icon">
         <div class="row">
             <div class="col-lg-6" >
                 <div class="panel-body">
                 <label>Bioplot</label>
                 <div id="bioplot_otu_img">
                     <img id="bioplot_otu_img_pass"  src="#" /> 
                 </div>
                 </div>
             </div>
             <div class="col-lg-6" >
                 <div class="panel-body">
                 <b>Bioplot</b><br>
                 <div id="bioplot_meta_img">
                    <img id="bioplot_meta_img_pass"  src=""/>
                 </div>
                 </div>
             </div>
        </div>

        <hr class="uk-divider-icon">
             <div class="row">
                 <div class="col-lg-6">
                 <div class="panel-body">
                    <label>Rarefaction</label>
                    <div id="rare_img">
                         <img id="rare_img_pass"  src="#" /> 
                    </div>
                    </div>
                 </div>
                <div class="col-lg-6">
                <div class="panel-body">
                <label>RelativePhylum</label>
                    <div id="abun_img">
                       <img id="abun_img_pass"  src="#" />
                    </div> 
                 </div> 
                 </div>
             </div>

             <hr class="uk-divider-icon">
                 <div class="panel-body">
                 <label>NMDS</label>
                 <div class="row">
                     <div class="col-lg-6 col-lg-offset-3" >
                     
                     <div id="nmd_img">
                         <img id="nmd_img_pass" src="#" /> 
                     </div>
                     </div>
                     </div>
                 </div>
             <hr class="uk-divider-icon">
                 <div class="panel-body">
                 <label>Alpha</label>
                 <div class="row">
                     <div class="col-lg-6 col-lg-offset-3">
                      <div id="alpha_img">
                         <img id="alpha_img_pass" src="#" />
                     </div> 
                     </div>
                     </div>
            </div>

            <!-- Table  -->           
            <?php if($project_analysis == "otu"){

                     $file_groups_ave_std_summary = "final.opti_mcc.groups.ave-std.summary";
                     $file_summary = "final.opti_mcc.summary";
                     $path_file_original_g = $path.$file_groups_ave_std_summary;
                     $path_file_original_s = $path.$file_summary;

                }else{
                     
                     $file_groups_ave_std_summary = "final.tx.groups.ave-std.summary";
                     $file_summary = "final.tx.summary";
                     $path_file_original_g = $path.$file_groups_ave_std_summary;
                     $path_file_original_s = $path.$file_summary; 

                    }

            ?>
           
            <div class="panel-body">
             <!-- Table groups.ave-std.summary -->
              <hr class="uk-divider-icon">
              <label><?php echo $file_groups_ave_std_summary; ?></label>
              <div class="row">
                     <div class="col-lg-12">
                     <div class="table-responsive" id="table_g" style="display:none">
                    <?php 

                     if(file_exists($path_file_original_g)){
                     $file_g = $path_file_original_g;

                     $count = 1;
                     $myfile = fopen($file_g,'r') or die ("Unable to open file");
                         while(($lines = fgets($myfile)) !== false){
                            $line0 = explode("\t", $lines);
                          if($count == 1){ ?>
                            <div id="html-content-1">
                             <table class="table table-striped table-bordered table-hover" style="text-align: center">
                                 <thead>
                                 <tr>
                                     <td><?php echo $line0[1] ?></td>
                                     <td><?php echo $line0[2] ?></td>
                                     <td><?php echo $line0[3] ?></td>
                                     <td><?php echo $line0[4] ?></td>
                                     <td><?php echo $line0[5] ?></td>
                                     <td><?php echo $line0[9] ?></td>
                                     <td><?php echo $line0[10] ?></td>
                                     <td><?php echo $line0[11] ?></td>
                                     <td><?php echo $line0[12] ?></td>
                                     <td><?php echo $line0[13] ?></td>
                                     <td><?php echo $line0[14] ?></td>
                                     
                                 </tr>
                                </thead>
                                <tbody>

                        <?php  }else{  ?>

                                <tr>
                                     <td><?php echo $line0[1] ?></td>
                                     <td><?php echo $line0[2] ?></td>
                                     <td><?php echo $line0[3] ?></td>
                                     <td><?php echo $line0[4] ?></td>
                                     <td><?php echo $line0[5] ?></td>
                                     <td><?php echo $line0[9] ?></td>
                                     <td><?php echo $line0[10] ?></td>
                                     <td><?php echo $line0[11] ?></td>
                                     <td><?php echo $line0[12] ?></td>
                                     <td><?php echo $line0[13] ?></td>
                                     <td><?php echo $line0[14] ?></td>
                                </tr> 

                      <?php } 
                          $count++;
                         }
                      fclose($myfile);  

                      }    ?>
                
                              </tbody>
                            </table>
                            </div><!-- #html-content-1-->

                     </div>
                     </div>
                     </div>    <!-- End Table groups.ave-std.summary -->


             <!--  Table file_summary -->
             <hr class="uk-divider-icon">

             <label><?php echo $file_summary; ?></label>

             <div class="row">
                <div class="col-lg-12">
                 <div class="table-responsive" id="table_s" style="display:none">  
                   <?php 

                     if(file_exists($path_file_original_s)){
                     $file_s = $path_file_original_s;

                     $count = 1;
                     $myfile = fopen($file_s,'r') or die ("Unable to open file");
                         while(($lines = fgets($myfile)) !== false){
                            $line0 = explode("\t", $lines);
                          if($count == 1){ ?>
                            <div id="html-content-2">
                             <table class="table table-striped table-bordered dataTable" style="text-align: center">
                                 <thead>
                                 <tr>
                                    <td colspan="2"><?php echo $line0[1] ?></td>
                                     
                                     <td><?php echo $line0[3] ?></td>
                                     <td><?php echo $line0[4] ?></td>
                                     <td><?php echo $line0[5] ?></td>
                                     <td><?php echo $line0[6] ?></td>
                                     <td><?php echo $line0[7] ?></td>
                                     <td><?php echo $line0[8] ?></td>
                                     <td><?php echo $line0[9] ?></td>
                                     <td><?php echo $line0[10] ?></td>
                                     <td><?php echo $line0[11] ?></td>
                                 </tr>
                                </thead>
                                <tbody>

                        <?php  }else{  ?>
                                   <tr>
                                     <td><?php echo $line0[1] ?></td>
                                     <td><?php echo $line0[2] ?></td>
                                     <td><?php echo $line0[4] ?></td>
                                     <td><?php echo $line0[5] ?></td>
                                     <td><?php echo $line0[6] ?></td>
                                     <td><?php echo $line0[7] ?></td>
                                     <td><?php echo $line0[8] ?></td>
                                     <td><?php echo $line0[9] ?></td>
                                     <td><?php echo $line0[10] ?></td> 
                                     <td><?php echo $line0[11] ?></td>
                                     <td><?php echo $line0[12] ?></td> 
                                </tr>

                      <?php } 
                          $count++;
                         }
                      fclose($myfile);  
                      
                       }   ?>
                
                              </tbody>
                            </table>
                            </div><!-- #html-content-2-->

                     </div>
                     </div>
                     <div class="col-lg-12 uk-margin"></div>
                        <center>
                               <input  class="btn btn-outline btn-info" value="Download all zip" id="zipall"> 
                        </center> 
                     </div><!-- End Table file_summary -->
             
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
<style>
    #html-content-1{
        display:inline-block;
        background-color:#FAFAFA;
        padding-left:10px;
        padding-top: 10px;
        padding-right: 10px;
        padding-bottom: 10px; 
    }
    #html-content-2{
        display:inline-block;
        background-color:#FAFAFA;
        padding-left: 15px;
        padding-top: 10px;
        padding-right: 15px;
        padding-bottom: 10px; 
    }

</style>
<script type="text/javascript">  

document.getElementById("zipall").onclick = function(){

    $.ajax({ 
          type:"post",
          datatype:"json",
          url:"<?php echo base_url('Run_advance/check_dirzip'); ?>",
          data:{current:"<?=$current_project?>"},
             success:function(data){
                var dir = JSON.parse(data); 
                if(dir == "TRUE"){
                   location.href="<?php echo site_url('Run_advance/down_zip');?>?current=<?=$current_project?>";           
                }else{
                    alert("FALSE");
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

            $("#sub-test2").click(function () {

                  var username = document.forms["Phylotype-form"]["username"].value;
                  var project  = document.forms["Phylotype-form"]["project"].value;
                  var sample = document.forms["Phylotype-form"]["subsample"].value;
                  var array_data = new Array(username,project,sample);
                  
                    if(sample != ""){
                         $(".Pre-test2").hide();
                         $(".Pre-show2").show();
                         get_subsample(array_data);
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
            var correlation_meta = null;
            var correlation_otu  = null;

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

                var ah_mova = document.forms["Analysis-form"]["optionsRadios2"].value;

                var method_meta = document.forms["Analysis-form"]["method_meta"].value;
                var axes_meta = document.forms["Analysis-form"]["axes_meta"].value;

                var method_otu = document.forms["Analysis-form"]["method_otu"].value;
                var axes_otu = document.forms["Analysis-form"]["axes_otu"].value;


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
                   
                 
                  design_stop = "stop";
                  metadata_stop = "stop";
                  

                 if(username != "" && project != "" &&  level != "" &&  venn1 != "0" && venn2 != "0" && check_ven_all == "start" ){

                     if((d_upgma_st != 0 || d_upgma_me != 0 ) && (d_pcoa_st != 0 || d_pcoa_me != 0 || d_nmds_st != 0 || d_nmds_me != 0 )){

                         var array_data = new Array(username,project,level,ch_alpha,size_alpha,ch_beta,size_beta,venn1,venn2,venn3,venn4,d_upgma_st,d_upgma_me,d_pcoa_st,d_pcoa_me,nmds,d_nmds_st,d_nmds_me,file_design,file_metadata,ah_mova,correlation_meta,method_meta,axes_meta,correlation_otu,method_otu,axes_otu);
                        
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
                     }else{
                         check_ven_all = "start";
                     }
                 }
                
                 
             });
              $('#venn2').change(function(){
                 ven2 = $('#venn2').val();
                  if(ven2 != '0'){
                     if(ven2 === ven1 || ven2 === ven3 ||ven2 === ven4){
                         alert('Duplicate value');
                         check_ven_all = "stop";
                    }else{
                         check_ven_all = "start";
                    }
                    
                 }
                
             });
             $('#venn3').change(function(){
                 ven3 = $('#venn3').val();
                  if(ven3 != '0'){
                     if(ven3 === ven1 || ven3 === ven2 || ven3 === ven4 ){
                          alert('Duplicate value');
                         check_ven_all = "stop";
                    }else{
                         check_ven_all = "start";
                    } 
                 }
                 
             });
              $('#venn4').change(function(){
                 ven4 = $('#venn4').val();
                  if(ven4 != '0'){
                    if(ven4 === ven1 || ven4 === ven2 || ven4 === ven3){
                          alert('Duplicate value');
                         check_ven_all = "stop";
                     }else{
                         check_ven_all = "start";
                     }
                 }
                
             });



           $('#correlation_meta').change(function(){
                if($(this).is(':checked')){
                    correlation_meta = $('#correlation_meta').val();
                }else{
                    correlation_meta = null;
                }
            });

           $('#correlation_otu').change(function(){
                if($(this).is(':checked')){
                    correlation_otu = $('#correlation_otu').val();
                }else{
                    correlation_otu = null;
                }
            });

 });


function getCanvas1(){
      var element = $("#html-content-1");
      var getCanvas; 
      var cur = "<?php echo $current_project?>";
     setTimeout(function(){
        html2canvas( element, {
             onrendered: function (canvas) {
                 getCanvas = canvas;
                 var imgageData = getCanvas.toDataURL("image/png");
                 $.post("<?php echo base_url('Run_advance/getCanvas1');?>",{data:imgageData,current:cur});
              
             }
        });

     },5000);
        
}

function getCanvas2(){
      var element = $("#html-content-2");
      var getCanvas; 
      var cur = "<?php echo $current_project?>";
     setTimeout(function(){
        html2canvas( element, {
             onrendered: function (canvas) {
                 getCanvas = canvas;
                 var imgageData = getCanvas.toDataURL("image/png");
                 $.post("<?php echo base_url('Run_advance/getCanvas2');?>",{data:imgageData,current:cur});
              
             }
        });

     },5000);
        
}


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
    </script>


