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
                            <div>
                                <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade">
                                    <li class="pre"><a href="#">Preprocess & Prepare in taxonomy </a></li>
                                    <li class="pre2"><a href="#">Prepare <?= $project_analysis ?> </a></li>
                                    <li class="pre3"><a href="#">Analysis</a></li>
                                    <li><a href="#">Result & graph</a></li>
                                </ul>
                                <ul class="uk-switcher uk-margin">


                                    <!--Preprocess && Prepare in taxonomy -->

                                    <li>
                                        <div class="Pre-test">
                                            <form name="Pre-form" method="post" action="#"
                                                  enctype="multipart/form-data">

                                                <input type="hidden" name="username" value="<?= $username ?>">
                                                <input type="hidden" name="project" value="<?= $current_project ?>">
                                                <div class="col-lg-12 ">
                                                    <label>1. Preprocess & Prepare in taxonomy </label><br><br>
                                                    <div class="row">
                                                        <div class="col-lg-1"></div>
                                                        <div class="col-lg-5"><label> Screen reads </label></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3"></div>
                                                        <div class="form-inline col-lg-7">
                                                            <table border="0" class="uk-table uk-table-middle">
                                                                <tr>
                                                                    <td>maximum ambiguous :</td>
                                                                    <td><input id="mbig" class="form-control"
                                                                               type="number" name="maximum_ambiguous"
                                                                               min="0" placeholder="maximum ambiguous"
                                                                               onblur="checkvalue()"
                                                                               onkeypress='return validateNumber(event)'>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>maximum homopolymer :</td>
                                                                    <td><input id="mhomo" class="form-control"
                                                                               type="number" name="maximum_homopolymer"
                                                                               min="0" placeholder="maximum homopolymer"
                                                                               onblur="checkvalue2()"
                                                                               onkeypress='return validateNumber(event)'>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>minimum reads length :</td>
                                                                    <td><input id="miniread" class="form-control"
                                                                               type="number" name="minimum_reads_length"
                                                                               min="0"
                                                                               placeholder="minimum reads length"
                                                                               onblur="checkvalue3()"
                                                                               onkeypress='return validateNumber(event)'>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> maximum reads length :</td>
                                                                    <td><input id="maxread" class="form-control"
                                                                               type="number" name="maximum_reads_length"
                                                                               min="0"
                                                                               placeholder="maximum reads length"
                                                                               onblur="checkvalue4()"
                                                                               onkeypress='return validateNumber(event)'>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-1"></div>
                                                        <div class="col-lg-2 "><label> Alignment step </label></div>
                                                        <div class="col-lg-4">
                                                            <select class="uk-select" name="alignment">
                                                                <option value="silva" selected> Silva</option>
                                                                <option value="gg"> Greengenes</option>
                                                                <option value="rdp"> RDP</option>
                                                            </select>
                                                        </div>
                                                        <label class="col-lg-1"> OR </label>
                                                        <div class="col-lg-4">
                                                            Limit fasta size file 800 MB
                                                            <input type="file" name="customer" id="custo_mer">
                                                            <div class="progress progress-striped active">
                                                                <div id="bar" class="progress-bar progress-bar-success"
                                                                     role="progressbar" aria-valuemin="0"
                                                                     aria-valuemax="100" style="width:0%;">
                                                                    <div class="percent">0%</div>
                                                                </div>
                                                            </div>
                                                            <div id="status"></div>
                                                        </div>
                                                    </div>


                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="row">
                                                        <div class="col-lg-1"></div>
                                                        <div class="col-lg-2"><label> Pre-cluster step </label></div>
                                                        <div class="col-lg-5">
                                                            <select class="uk-select" name="diffs">
                                                                <option value="0">diffs = 0</option>
                                                                <option value="1">diffs = 1</option>
                                                                <option value="2" selected>diffs = 2</option>
                                                                <option value="3">diffs = 3</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row uk-margin">
                                                        <div class="col-lg-1 "></div>
                                                        <div class="col-lg-3"><label>Prepare the taxonomy
                                                                classification</label></div>
                                                        <div class="col-lg-3">
                                                            <select class="uk-select" name="classify">
                                                                <option value="silva"> Silva</option>
                                                                <option value="gg" selected> Greengenes</option>
                                                                <option value="rdp"> RDP</option>
                                                            </select></div>
                                                        <div class="col-lg-2"><label> with cutoff</label></div>
                                                        <div class="col-lg-2 ">
                                                            <input class="uk-input" type="number" name="cutoff" min="50"
                                                                   value="80">
                                                        </div>
                                                        <label class="col-lg-1">(>=50)</label>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-1"></div>
                                                        <label class="col-lg-3 "> taxon elimination</label>
                                                        <div class="col-lg-8">

                                                            <div class="radio">
                                                                <label>
                                                                    <input name="optionsRadios" value="0" type="radio"
                                                                           checked> default
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
                                                                        <option value="Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified"
                                                                                selected>
                                                                            Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified
                                                                        </option>

                                                                    </select>
                                                                </label>
                                                            </div>
                                                        </div>


                                                    </div>


                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="row">
                                                        <div class="col-lg-1"></div>
                                                        <div class="col-lg-4">
                                                            <input id="sub-test" class="btn btn-default"
                                                                   value="Run Preprocess ">
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <input type="reset" class="btn btn-default" value="Clear">
                                                        </div>

                                                    </div>


                                                    <div class="col-lg-12 uk-margin"></div>
                                                </div><!-- close row form -->

                                            </form>
                                        </div> <!-- Pre-test -->

                                        <div class="Pre-show" style="display:none"> Process Queue
                                            <div id="time">30</div>

                                            <div class="progress progress-striped active">
                                                <div id="bar_pre" class="progress-bar progress-bar-success"
                                                     role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                     style="width:0%;">
                                                    <div class="percent_pre">0%</div>
                                                </div>
                                            </div>
                                            <div id="test_run">run queue</div>
                                            <br/>

                                            <!-- <button id="back-test" class="btn btn-default">back</button> -->
                                        </div>

                                    </li>

                                    <!--End Preprocess && Prepare in taxonomy -->


                                    <!--Prepare phylotype -->
                                    <li>

                                        <div class="Pre-test2">
                                            <div class="col-lg-8 col-lg-offset-2">

                                                <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>The number of
                                                        total reads/group after the preprocess</label></div>
                                                <div class="col-lg-10 col-lg-pull-1"><label> show data in count group
                                                        :</label></div>
                                                <div class="row uk-margin">
                                                    <div class="col-lg-9">
                                                        <textarea class="form-control" rows="5"
                                                                  id="show_group"></textarea>
                                                    </div>
                                                    <div class="col-lg-8 col-lg-push-9">
                                                        <button class="btn btn-default" data-toggle="modal"
                                                                data-target="#myModal">
                                                            Back
                                                        </button>
                                                    </div>
                                                </div>


                                                <!-- Phylotype-form -->
                                                <form name="Phylotype-form" method="post">

                                                    <input type="hidden" name="username" value="<?= $username ?>">
                                                    <input type="hidden" name="project" value="<?= $current_project ?>">
                                                    <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Please put the
                                                            number to subsampled file </label></div>

                                                    <div class="row uk-margin">
                                                        <div class="col-lg-8">
                                                            <label>sub sample :</label>
                                                            <input id="sub_sample" class="uk-input" type="number"
                                                                   min="0" name="subsample"
                                                                   onkeypress='return validateNumber(event)'>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="col-lg-4">
                                                        <input id="sub-test2" class="btn btn-default"
                                                               value="Run Preprocess">
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <input type="reset" class="btn btn-default" value="Clear">
                                                    </div>

                                                </form><!-- close  form -->

                                                <div class="col-lg-12 uk-margin"></div>
                                                <!-- Modal -->
                                                <div class="panel-body">
                                                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                                                         aria-labelledby="myModalLabel" aria-hidden="true"
                                                         style="display: none;">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal" aria-hidden="true">×
                                                                    </button>
                                                                    <h4 class="modal-title" id="myModalLabel">
                                                                        Preprocess</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Do you want to re-run preprocess again ?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button id="back_preprocess" class="btn btn-primary"
                                                                            data-dismiss="modal">Yes
                                                                    </button>
                                                                    <button class="btn btn-default"
                                                                            data-dismiss="modal">No
                                                                    </button>
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

                                            <div class="progress progress-striped active">
                                                <div id="bar_pre2" class="progress-bar progress-bar-success"
                                                     role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                     style="width:0%;">
                                                    <div class="percent_pre2">0%</div>
                                                </div>
                                            </div>

                                            <div id="test_run2">run sub sample</div>
                                            <br/>

                                            <!-- <button id="back-test2" class="btn btn-default">back subsample</button> -->

                                        </div>

                                    </li>

                                    <!--End Prepare phylotype analysis-->


                                    <!-- Analysis -->
                                    <li>

                                        <div class="Pre-test3">

                                            <!-- Analysis-form -->
                                            <form name="Analysis-form" method="post" enctype="multipart/form-data">

                                                <input type="hidden" name="username" value="<?= $username ?>">
                                                <input type="hidden" name="project" value="<?= $current_project ?>">

                                                <div class="col-lg-8 col-lg-offset-2">

                                                    <div class="col-lg-10 col-lg-pull-2 uk-margin"><label>Please select
                                                            level that you want to analyse :</label></div>
                                                    <div class="Greengene" style="display:none">
                                                        <div class="col-lg-5 col-lg-pull-1">
                                                            <label> Greengenes : </label>
                                                        </div>
                                                        <div class="col-lg-5 col-lg-pull-4">
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
                                                        <div class="col-lg-5 col-lg-pull-1">
                                                            <label> Silva/RDP : </label>
                                                        </div>
                                                        <div class="col-lg-5 col-lg-pull-4">
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
                                                        <div class="col-lg-5 col-lg-pull-1">
                                                            <label> OTU : </label>
                                                        </div>
                                                        <div class="col-lg-5 col-lg-pull-4">
                                                            <select class="uk-select" id="o_level">
                                                                <option value="0.03" selected> 0.03</option>
                                                                <option value="0.05"> 0.05</option>
                                                                <option value="0.10"> 0.10</option>
                                                                <option value="0.20"> 0.20</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-10 col-lg-pull-1 uk-margin"><label> 3.1 Alpha
                                                            diversity analysis : </label></div>
                                                    <div class="col-lg-10 col-lg-push-1 "><label> Summary alpha
                                                            statistical analysis </label></div>
                                                    <div class="col-lg-8 col-lg-push-2 ">

                                                        <div class="radio">
                                                            <label>
                                                                <input name="optionsRadios" value="1" type="radio"> set
                                                                the size of your smallest group :
                                                                <input class="uk-input" id="alpha" name="size_alpha"
                                                                       type="number" min="0"
                                                                       onkeypress='return validateNumber(event)'>
                                                            </label>
                                                        </div>
                                                        <div class="radio">
                                                            <label>
                                                                <input name="optionsRadios" id="myradio" type="radio"
                                                                       checked> No need set the size
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-10 col-lg-pull-1 uk-margin"><label> 3.2 Beta
                                                            diversity analysis : </label></div>
                                                    <div class="col-lg-10 col-lg-push-1 "><label> Summary beta
                                                            statistical analysis </label></div>
                                                    <div class="col-lg-8 col-lg-push-2 ">

                                                        <div class="radio">
                                                            <label>
                                                                <input name="optionsRadios1" value="1" type="radio"> set
                                                                the size of your smallest group :
                                                                <input class="uk-input" id="beta" name="size_beta"
                                                                       type="number" min="0"
                                                                       onkeypress='return validateNumber(event)'>
                                                            </label>
                                                        </div>
                                                        <div class="radio">
                                                            <label>
                                                                <input name="optionsRadios1" id="myradio1" type="radio"
                                                                       checked> No need set the size
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-11 col-lg-push-1 uk-margin"><label> Venn
                                                            Diagram</label></div>
                                                    <label class="col-lg-11 col-lg-push-2 ">Please put the sample name
                                                        : </label>

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

                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="col-lg-10 col-lg-push-1"><label> UPGMA tree with
                                                            calculator : </label></div>
                                                    <div class="col-lg-7 col-lg-push-2 ">
                                                        <div>Community structure</div>
                                                        <div class="col-lg-7 col-lg-push-1 ">
                                                            <input type='checkbox' name='upgma_st[]' value='braycurtis'>
                                                            braycurtis <br/>
                                                            <input type='checkbox' name='upgma_st[]' value='thetan'>
                                                            thetan <br/>
                                                            <input type='checkbox' name='upgma_st[]' value='thetayc'>
                                                            thetayc <br/>
                                                            <input type='checkbox' name='upgma_st[]'
                                                                   value='morisitahorn'> morisitahorn <br/>
                                                            <input type='checkbox' name='upgma_st[]' value='sorabund'>
                                                            sorabund
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-7 col-lg-push-2">
                                                        Community membership
                                                        <div class="col-lg-7 col-lg-push-1 ">
                                                            <input type='checkbox' name='upgma_me[]' value='jclass'>
                                                            jclass <br/>
                                                            <input type='checkbox' name='upgma_me[]' value='lennon '>
                                                            lennon
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="col-lg-10 col-lg-push-1"><label> PCoA : <input
                                                                    name="func" type="radio" id="radio_pcoa"> Use
                                                            PCoA</label></div>
                                                    <div class="col-lg-7 col-lg-push-2 ">
                                                        <div> Community structure</div>
                                                        <div class="col-lg-7 col-lg-push-1 ">
                                                            <input type='checkbox' name='pcoa_st[]' value='braycurtis'
                                                                   class="pcoa" disabled> braycurtis <br/>
                                                            <input type='checkbox' name='pcoa_st[]' value='thetan'
                                                                   class="pcoa" disabled> thetan <br/>
                                                            <input type='checkbox' name='pcoa_st[]' value='thetayc'
                                                                   class="pcoa" disabled> thetayc <br/>
                                                            <input type='checkbox' name='pcoa_st[]' value='morisitahorn'
                                                                   class="pcoa" disabled> morisitahorn <br/>
                                                            <input type='checkbox' name='pcoa_st[]' value='sorabund'
                                                                   class="pcoa" disabled> sorabund
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-7 col-lg-push-2 ">
                                                        Community membership
                                                        <div class="col-lg-7 col-lg-push-1 ">
                                                            <input type='checkbox' name='pcoa_me[]' value='jclass'
                                                                   class="pcoa" disabled> jclass <br/>
                                                            <input type='checkbox' name='pcoa_me[]' value='lennon '
                                                                   class="pcoa" disabled> lennon
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="col-lg-10 col-lg-push-1"><label> NMDS : <input
                                                                    name="func" type="radio" id="radio_nmds"> Use
                                                            NMDS</label></div>
                                                    <div class="col-lg-4 col-lg-push-2 ">
                                                        <select class="uk-select" name="nmds">
                                                            <option value="2"> 2D</option>
                                                            <option value="3"> 3D</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-9 col-lg-push-2 ">
                                                        Community structure
                                                        <div class="col-lg-9 col-lg-push-1 ">
                                                            <input type='checkbox' name='nmds_st[]' value='braycurtis'
                                                                   class="nmds" disabled> braycurtis <br/>
                                                            <input type='checkbox' name='nmds_st[]' value='thetan'
                                                                   class="nmds" disabled> thetan <br/>
                                                            <input type='checkbox' name='nmds_st[]' value='thetayc'
                                                                   class="nmds" disabled> thetayc <br/>
                                                            <input type='checkbox' name='nmds_st[]' value='morisitahorn'
                                                                   class="nmds" disabled> morisitahorn <br/>
                                                            <input type='checkbox' name='nmds_st[]' value='sorabund'
                                                                   class="nmds" disabled> sorabund
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-9 col-lg-push-2 ">
                                                        Community membership
                                                        <div class="col-lg-9 col-lg-push-1 ">
                                                            <input type='checkbox' name='nmds_me[]' value='jclass'
                                                                   class="nmds" disabled> jclass <br/>
                                                            <input type='checkbox' name='nmds_me[]' value='lennon '
                                                                   class="nmds" disabled> lennon
                                                        </div>
                                                    </div>

                                                      <div class="col-lg-9 col-lg-push-2 ">
                                                           Community membership
                                                               <div class="col-lg-9 col-lg-push-1 ">
                                                                 <input type='checkbox' name='nmds_me[]' value='jclass' class="nmds" disabled> jclass <br/>
                                                                 <input type='checkbox' name='nmds_me[]' value='lennon 'class="nmds" disabled> lennon 
                                                                </div>
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
                                                          <a href="<?php echo site_url('Run_advance/create_file_design');?>?current=<?=$current_project?>" target="_blank">
                                                               <input type="button" value="create design" id="check_design">  
                                                          </a>
                                                         </label>
                                                         <div>
                                                         <p id="pass_design" class="fa fa-file-text-o" > No file design </p> 
                                                         <input type="hidden" id="p_design" name="f_design" value="nodesign">
                                                         <img id="img_design" src="">
                                                         </div>

                                                     </div>
                                                     <div class="col-lg-8 col-lg-push-2 ">                
                                                     <div class="radio">
                                                         <label >
                                                         <input name="optionsRadios2" value="amova" type="radio"> Amova
                                                         </label>
                                                     </div>
                                                     <div class="radio">
                                                         <label>
                                                         <input name="optionsRadios2"  value="homova" type="radio" > Homova
                                                         </label>
                                                     </div>
                                                     </div>
                                                     <div class="col-lg-10 col-lg-push-2 uk-margin"> 
                                                         <label> Create file metadata  
                                                             <a href="<?php echo base_url('Run_advance/create_file_metadata');?>?current=<?=$current_project?>"  target="_blank">
                                                               <input type="button" value="create metadata" id="check_metadata">  
                                                          </a>
                                                         </label>
                                                         <div>
                                                           <p id="pass_metadata" class="fa fa-file-text-o"> No file metadata </p>
                                                           <input type="hidden" id="p_metadata" name="f_metadata" value="nometadata">
                                                           <img id="img_metadata" src="">
                                                         </div>
                                                     </div>

                                                     <div class="col-lg-12 col-lg-push-2"> 
                                                    
                                                         <label class="col-lg-6">
                                                             <input type="checkbox" id="correlation_meta"  value="meta" > correlation with metadata 
                                                         </label>

                                                         <div class="col-lg-3 col-lg-pull-1">
                                                             <select class="uk-select" name="method_meta">
                                                             <option value="spearman"> spearman </option>
                                                             <option value="pearson"> pearson </option>     
                                                             </select>
                                                         </div>
                                                         <div class="col-lg-2 col-lg-pull-1">
                                                            <select class="uk-select" name="axes_meta">
                                                                  <option value="2"> 2 </option>
                                                                  <option value="3"> 3 </option>
                                                             </select>
                                                         </div>

                                                      
                                                     </div> 

                                                     <div class="col-lg-12 col-lg-push-2"> 
                                                    
                                                         <label class="col-lg-6">
                                                            <input type="checkbox" id="correlation_otu"  value="otu" > correlation of each OTU
                                                         </label>

                                                         <div class="col-lg-3 col-lg-pull-1">
                                                            <select class="uk-select" name="method_otu">
                                                             <option value="spearman"> spearman </option>
                                                             <option value="pearson"> pearson </option>     
                                                             </select>
                                                         </div>
                                                         <div class="col-lg-2 col-lg-pull-1">
                                                            <select class="uk-select" name="axes_otu">
                                                                  <option value="2"> 2 </option>
                                                                  <option value="3"> 3 </option>
                                                             </select>
                                                         </div>
                                                      </div>

                                                    <div class="col-lg-12 uk-margin"></div>


                                                    <div class="col-lg-10 col-lg-push-1" id="plus">
                                                        <label> Optional <span class="glyphicon glyphicon-plus-sign"
                                                                               id="plus_option"> </span> </label>

                                                    </div>
                                                    <div class="col-lg-10 col-lg-push-1" id="move" style="display:none">
                                                        <label> Optional <span class="glyphicon glyphicon-minus-sign"
                                                                               id="move_option"></span> </label>

                                                    </div>

                                                    <div class="optional" style="display:none">
                                                        <div class="col-lg-8 col-lg-push-2 ">
                                                            <label> Create file design
                                                                <a href="<?php echo site_url('Run_advance/create_file_design'); ?>"
                                                                   target="_blank">
                                                                    <input type="button" value="create design"
                                                                           id="check_design">
                                                                </a>
                                                            </label>
                                                            <div>
                                                                <p id="pass_design" class="fa fa-file-text-o"> No file
                                                                    design </p>
                                                                <input type="hidden" id="p_design" name="f_design"
                                                                       value="nodesign">
                                                            </div>

                                                        </div>
                                                        <div class="col-lg-8 col-lg-push-2 ">
                                                            <div class="radio">
                                                                <label>
                                                                    <input name="optionsRadios2" value="amova"
                                                                           type="radio"> Amova
                                                                </label>
                                                            </div>
                                                            <div class="radio">
                                                                <label>
                                                                    <input name="optionsRadios2" value="homova"
                                                                           type="radio"> Homova
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-10 col-lg-push-2 uk-margin">
                                                            <label> Create file metadata
                                                                <a href="<?php echo base_url('Run_advance/create_file_metadata'); ?>"
                                                                   target="_blank">
                                                                    <input type="button" value="create metadata"
                                                                           id="check_metadata">
                                                                </a>
                                                            </label>
                                                            <div>
                                                                <p id="pass_metadata" class="fa fa-file-text-o"> No file
                                                                    metadata </p>
                                                                <input type="hidden" id="p_metadata" name="f_metadata"
                                                                       value="nometadata">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12 col-lg-push-2">

                                                            <label class="col-lg-6">
                                                                <input type="checkbox" id="correlation_meta"
                                                                       value="meta"> correlation with metadata
                                                            </label>

                                                            <div class="col-lg-3 col-lg-pull-1">
                                                                <select class="uk-select" name="method_meta">
                                                                    <option value="spearman"> spearman</option>
                                                                    <option value="pearson"> pearson</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-2 col-lg-pull-1">
                                                                <select class="uk-select" name="axes_meta">
                                                                    <option value="2"> 2</option>
                                                                    <option value="3"> 3</option>
                                                                </select>
                                                            </div>


                                                        </div>

                                                        <div class="col-lg-12 col-lg-push-2">

                                                            <label class="col-lg-6">
                                                                <input type="checkbox" id="correlation_otu" value="otu">
                                                                correlation of each OTU
                                                            </label>

                                                            <div class="col-lg-3 col-lg-pull-1">
                                                                <select class="uk-select" name="method_otu">
                                                                    <option value="spearman"> spearman</option>
                                                                    <option value="pearson"> pearson</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-2 col-lg-pull-1">
                                                                <select class="uk-select" name="axes_otu">
                                                                    <option value="2"> 2</option>
                                                                    <option value="3"> 3</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div> <!-- class="optional" style="display:none" -->

                                                    <div class="col-lg-12 uk-margin"></div>
                                                    <div class="col-lg-4 col-lg-push-2">
                                                        <input id="sub-test3" class="btn btn-default"
                                                               value="Run Preprocess">
                                                    </div>
                                                    <div class="col-lg-8 col-lg-push-2">
                                                        <input type="reset" class="btn btn-default" value="Clear">
                                                    </div>
                                                    <div class="col-lg-12 uk-margin"></div>
                                                </div>

                                            </form>  <!-- end Analysis form-->
                                        </div> <!-- Pre-test3 -->

                                        <div class="Pre-show3" style="display:none"> Process Queue Analysis
                                            <div id="time3">30</div>

                                            <div class="progress progress-striped active">
                                                <div id="bar_pre3" class="progress-bar progress-bar-success"
                                                     role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                     style="width:0%;">
                                                    <div class="percent_pre3">0%</div>
                                                </div>
                                            </div>

                                            <div id="test_run3">run analysis</div>
                                            <br/>

                                            <!-- <button id="back-test3" class="btn btn-default">back analysis</button> -->
                                        </div>
                                    </li>

                                    <!-- End Analysis -->


                                    <!-- Result && Graph -->
                                    <li>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>Ven diagram</b>

                                                 <div id="sharedsobs_img">
                                                   <img id="sharedsobs_img_pass" src="#"/>
                                                 </div>
                                                
                                            </div>
                                            <div class="col-lg-6">
                                                <b>Heatmap</b>
                                                 <div id="heartmap_img">
                                                     <img id="heartmap_img_pass" src="#" />
                                                 </div>


                                            </div>
                                        </div>

                                        <hr class="uk-divider-icon">

                                        <div class="row">

                                            <div class="col-lg-6" >
                                                <b>Bioplot</b><br>
                                                 <div id="bioplot_otu_img">
                                                     <img id="bioplot_otu_img_pass"  src="#" /> 
                                                 </div>
                                               
                                            </div>
                                            <div class="col-lg-6" >
                                                <b>Bioplot</b><br>
                                                <div id="bioplot_meta_img">
                                                     <img id="bioplot_meta_img_pass"  src=""/>

                                                </div>

                                            </div>
                                        </div>
                                        <hr class="uk-divider-icon">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <b>Rarefaction</b>
                                                <div id="rare_img">

                                                  <img id="rare_img_pass"  src="#" /> 

                                                </div>


                                            </div>
                                            <div class="col-lg-6">
                                                <b>RelativePhylum</b>
                                                <div id="abun_img">

                                                   <img id="abun_img_pass"  src="#" />
                              
                                                </div>  

                                            </div>
                                        </div>
                                        <hr class="uk-divider-icon">
                                        <b>NMDS</b>
                                        <div class="row">

                                            <div class="col-lg-6 col-lg-offset-3" >
                                               <div id="nmd_img">
                                                   <img id="nmd_img_pass" src="#" />;  
                                               </div>

                                            </div>

                                        </div>
                                        <hr class="uk-divider-icon">
                                        <b>Alpha</b>
                                        <div class="row">
                                            <div class="col-lg-6 col-lg-offset-3">
                                                <div id="alpha_img">

                                                   <img id="alpha_img_pass" src="#" />
                                                       
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


    <input type="hidden" id="advance_num" value="0">

    <!--  Advance Script -->
    <script type="text/javascript">

        function advance_mode(obj) {
            var pid = "<?php echo $current_project ?>";
            var user = "<?php echo $username ?>";
            var project = "<?php echo $project ?>";

            var va_num = 0;
            va_num += Number(obj.getAttribute("href"));
            var num = $('#advance_num').val();
            var check = va_num - num;
            if (check == 1) {
                document.getElementById('advance_num').value = va_num;
            }

            $.ajax({

                   type:"post",
                   datatype:"json",
                   url:"<?php echo base_url('Run_advance/recheck'); ?>",
                   data:{data_status: pid},
                   success:function(data){
                        var status = $.parseJSON(data);
                        if(status[0] == "0" && status[1] == "4"){
                           alert('No Run Queue');
    
                             $('#sharedsobs_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/sharedsobs.svg')?>");
                             $('#heartmap_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/heartmap.png')?>");
                             
                             $('#bioplot_otu_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/NewNMDS_withBiplotwithOTU.png')?>");
                             $('#bioplot_meta_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/NewNMDS_withBiplotwithMetadata.png')?>");
                             
                             $('#rare_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/Rare.png')?>");
                             $('#abun_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/Abun.png')?>");

                             $('#nmd_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/NMD.png')?>");
                             $('#alpha_img_pass').attr("src","<?php echo base_url('img_user/"+user+"/"+project+"/Alpha.png')?>");
                        
                        }else if(status[0] != "0" && status[1] == "1" && check == 1){
                           alert('Run step '+status[1] );
                                 $(".Pre-test").hide();
                                 $(".Pre-show").show();
                                 var data = new Array(status[2],pid);
                                 checkrun(data);
                                 $('#test_run').html('Ckecking Process Queue');

                        }else if(status[0] != "0" && status[1] == "2" && check == 1){
                            alert('Run step '+status[1] );
                            $('.uk-child-width-expand > .pre').next('li').find('a').trigger('click');
                                 $(".Pre-test2").hide();
                                 $(".Pre-show2").show();
                                 var data = new Array(status[2],pid);
                                 check_subsample(data);
                                 $('#test_run2').html('Ckecking Process Queue');


                        }else if(status[0] != "0" && status[1] == "3" && check == 1){
                            alert('Run step '+status[1] );
                            $('.uk-child-width-expand > .pre2').next('li').find('a').trigger('click');
                               $(".Pre-test3").hide();
                               $(".Pre-show3").show();
                               var data = new Array(status[2],pid);
                               ckeck_analysis(data);
                               $('#test_run3').html('Ckecking Process Queue');
                        } 
                      
                   },
                   error:function(e){
                     console.log(e.message);
                   }
           });
            
        }


        $(document).ready(function () {

            $("#sub-test").click(function () {

                var username = document.forms["Pre-form"]["username"].value;
                var project = document.forms["Pre-form"]["project"].value;
                var maximum_ambiguous = document.forms["Pre-form"]["maximum_ambiguous"].value;
                var maximum_homopolymer = document.forms["Pre-form"]["maximum_homopolymer"].value;
                var minimum_reads_length = document.forms["Pre-form"]["minimum_reads_length"].value;
                var maximum_reads_length = document.forms["Pre-form"]["maximum_reads_length"].value;
                var alignment = document.forms["Pre-form"]["alignment"].value;
                var customer = document.forms["Pre-form"]["customer"].value;
                var diffs = document.forms["Pre-form"]["diffs"].value;
                var classify = document.forms["Pre-form"]["classify"].value;
                var cutoff = document.forms["Pre-form"]["cutoff"].value;
                var optionsRadios = document.forms["Pre-form"]["optionsRadios"].value;
                var taxon = document.forms["Pre-form"]["taxon"].value;

                var array_data = new Array(username, project, maximum_ambiguous, maximum_homopolymer, minimum_reads_length, maximum_reads_length, alignment, customer, diffs, classify, cutoff, optionsRadios, taxon);

                if (maximum_ambiguous != "" && maximum_homopolymer != "" && minimum_reads_length != "" && maximum_reads_length != "") {
                    $(".Pre-test").hide();
                    $(".Pre-show").show();
                    getvalue(array_data);
                }

            });

            $("#plus_option").click(function () {
                $(".optional").show();
                $("#move").show();
                $("#plus").hide();
            });

            $("#move_option").click(function () {
                $(".optional").hide();
                $("#move").hide();
                $("#plus").show();
            });


          
           $("#back_preprocess").click(function(){


                $('.uk-child-width-expand > .pre2').prev('li').find('a').trigger('click');

            });

            $("#sub-test2").click(function () {

                var username = document.forms["Phylotype-form"]["username"].value;
                var project = document.forms["Phylotype-form"]["project"].value;
                var sample = document.forms["Phylotype-form"]["subsample"].value;
                var array_data = new Array(username, project, sample);

                if (sample != "") {
                    $(".Pre-test2").hide();
                    $(".Pre-show2").show();
                    get_subsample(array_data);
                }

            });

            var correlation_otu  = null;

           $('#correlation_meta').change(function(){
                     if($(this).is(':checked')){
                        correlation_meta = $('#correlation_meta').val();
                        //console.log(correlation_meta);
                     }else{
                        correlation_meta = null;
                        //console.log(correlation_meta)
                     }
            });

           $('#correlation_otu').change(function(){
                     if($(this).is(':checked')){
                        correlation_otu = $('#correlation_otu').val();
                        //console.log(correlation_otu);
                     }else{
                        correlation_otu = null;
                        //console.log(correlation_otu);
                     }

            });


             var ven1,ven2,ven3,ven4;
             var check_ven_all = null;
   
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

            

              
            

            var design_stop = "";
            var metadata_stop = "";

            $("#sub-test3").click(function () {
                var username = document.forms["Analysis-form"]["username"].value;
                var project = document.forms["Analysis-form"]["project"].value;
                var level = document.forms["Analysis-form"]["level"].value;

                var ch_alpha = document.forms["Analysis-form"]["optionsRadios"].value;
                var size_alpha = document.forms["Analysis-form"]["size_alpha"].value;

                var ch_beta = document.forms["Analysis-form"]["optionsRadios1"].value;
                var size_beta = document.forms["Analysis-form"]["size_beta"].value;

                var venn1 = document.forms["Analysis-form"]["venn1"].value;
                var venn2 = document.forms["Analysis-form"]["venn2"].value;
                var venn3 = document.forms["Analysis-form"]["venn3"].value;
                var venn4 = document.forms["Analysis-form"]["venn4"].value;

                var nmds = document.forms["Analysis-form"]["nmds"].value;

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
                         //alert('Get Analysis');
                     }
                      
                 }

            });


            $("#check_design").click(function () {
                 design_stop = "start";
                 var user = "<?php echo $username ?>";
                 var project = "<?php echo $current_project ?>";
                 var time = 10;
                 var interval = null;
                 interval = setInterval(function(){   
                 time--;
                   $('#img_design').attr("src","<?php echo base_url('images/ajax-loader.gif')?>");
                    
                    if(time === 0){
                     $.ajax({ 
                       type:"post",
                       datatype:"json",
                       url:"<?php echo base_url('Run_advance/check_file_design');?>?user="+user+"&project_id="+project,
                         success:function(data){
                            var design = JSON.parse(data);
                             if(design != "No File" || design_stop == "stop"){
                                   clearInterval(interval);
                                   $('#pass_design').text(" "+design);
                                   document.getElementById('p_design').value = design;
                                   $('#img_design').attr("src","<?php echo base_url('images/check.png')?>");
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
                 metadata_stop = "start";
                 var user = "<?php echo $username ?>";
                 var project = "<?php echo $current_project ?>";
                 var time = 10;
                 var interval = null;
                 interval = setInterval(function(){   
                 time--;
                  $('#img_metadata').attr("src","<?php echo base_url('images/ajax-loader.gif')?>");
                    
                    if(time === 0){
                     $.ajax({ 
                       type:"post",
                       datatype:"json",
                       url:"<?php echo base_url('Run_advance/check_file_metadata');?>?user="+user+"&project_id="+project,
                         success:function(data){
                             var metadata = JSON.parse(data);
                             if(metadata  != "No File" || metadata_stop == "stop"){
                                   clearInterval(interval);
                                   $('#pass_metadata').text(" "+metadata);
                                   document.getElementById('p_metadata').value = metadata;
                                   $('#img_metadata').attr("src","<?php echo base_url('images/check.png')?>");
                             }
                             else{  
                                  time = 5;  
                             } 
                         }
                     });
                   }

                 },1000);      
                  
            });


            $('#radio_pcoa').on('change', function() {
                $(".nmds").attr("disabled", true);
                $(".nmds").prop('checked', false);
                $(".pcoa").removeAttr("disabled");

            });

             $('#radio_nmds').on('change', function() {
                $(".pcoa").attr("disabled", true);
                $(".pcoa").prop('checked', false);
                $(".nmds").removeAttr("disabled");
                
              });


       });


            });
        });


        function create_var(checkboxes) {
            var vals = "";
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    vals += checkboxes[i].value + " ";
                }
            }
            var str = vals.trim();
            var data_var = str.replace(/ /g, ",");
            return data_var;
        }

        $(document).on('change', '#custo_mer', function () {

            var file_data = $('#custo_mer').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            var file_name = file_data.name;
            var file_size = file_data.size;
            var file_mb = (file_data.size / 1024 / 1024).toFixed(0); // MB

            var type = file_name.substring(file_name.lastIndexOf('.') + 1);
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            var i = parseInt(Math.floor(Math.log(file_size) / Math.log(1024)));
            var f_size = Math.round(file_size / Math.pow(1024, i), 2) + ' ' + sizes[i];

            if (type == 'fasta' || type == 'align') {
                if (file_size == 0) {
                    alert('Size : ' + file_size + ' Bytes');
                    document.getElementById('custo_mer').value = "";
                }
                else if (file_mb <= 800) {
                    //alert(file_name+' '+f_size+' '+type);
                    get_fasta(form_data);
                } else {
                    alert('file is too large : ' + f_size);
                    document.getElementById('custo_mer').value = "";
                }
            }
            else {
                alert('file is not fasta or align');
                document.getElementById('custo_mer').value = "";
            }

        });

        function get_fasta(file_data) {

            var bar = $('#bar');
            var percent = $('.percent');
            var status = $('#status');

            $.ajax({
                type: "post",
                dataType: 'text',
                url: "<?php echo base_url('Run_advance/check_fasta'); ?>",
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

                    if (xhr.responseText == '0') {
                        alert("File is not fasta");
                        status.html("File is not fasta");
                        document.getElementById('custo_mer').value = "";
                        bar.width(Math.round(0) + "%");
                        percent.html(Math.round(0) + "%");
                    } else {
                        alert(xhr.responseText);
                        status.html(xhr.responseText);
                        bar.width(Math.round(0) + "%");
                        percent.html(Math.round(0) + "%");
                    }


                },
                error: function (e) {
                    console.log(e.message);
                }
            });
        }

        function get_analysis(array_data) {
            var data_value = array_data;

            $.ajax({
                type: "post",
                datatype: "json",
                url: "<?php echo base_url('Run_advance/run_analysis'); ?>",
                data: {data_analysis: data_value},
                success: function (data) {
                    var job_analysis = $.parseJSON(data);
                    //var job_analysis = JSON.parse(data);
                    ckeck_analysis(job_analysis);
                    //console.log(job_analysis);

                },
                error: function (e) {
                    console.log(e.message);
                }
            });

        }


        function ckeck_analysis(job_analy){
             var user = "<?php echo $username ?>";
             var project = "<?php echo $project ?>";
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
                      //var analysis = JSON.parse(data);
                      var analysis = $.parseJSON(data);
                      if( analysis[0] == "0"){
                         clearInterval(interval);

                             $('#sharedsobs_img').html('<img id="sharedsobs_img_pass" src="<?php echo base_url("img_user/'+user+'/'+project+'/sharedsobs.svg");?>">');
                             $('#heartmap_img').html('<img id="heartmap_img_pass" src="<?php echo base_url("img_user/'+user+'/'+project+'/heartmap.png");?>">');
                             
                             $('#bioplot_otu_img').html('<img id="bioplot_otu_img_pass"  src="<?php echo base_url("img_user/'+user+'/'+project+'/NewNMDS_withBiplotwithOTU.png"); ?> ">'); 
                             $('#bioplot_meta_img').html('<img id="bioplot_meta_img_pass"  src="<?php echo base_url("img_user/'+user+'/'+project+'/NewNMDS_withBiplotwithMetadata.png"); ?> ">'); 

                             $('#rare_img').html('<img id="rare_img_pass"  src="<?php echo base_url("img_user/'+user+'/'+project+'/Rare.png");?>">');
                             $('#abun_img').html('<img id="abun_img_pass"  src="<?php echo base_url("img_user/'+user+'/'+project+'/Abun.png");?>">');

                            
                             $('#nmd_img').html('<img id="nmd_img_pass" src="<?php echo base_url("img_user/'+user+'/'+project+'/NMD.png");?>">');
                             $('#alpha_img').html('<img id="alpha_img_pass" src="<?php echo base_url("img_user/'+user+'/'+project+'/Alpha.png");?>">');
                           
                             $('.uk-child-width-expand > .pre3').next('li').find('a').trigger('click'); 
                             $(".Pre-show3").hide();
                             $(".Pre-test3").show();

                             $('#bar_pre3').width(0+"%");
                             $('.percent_pre3').html(0+"%");

                      }else{

                         var num = analysis[1];
                         $('#bar_pre3').width(num+"%");
                         $('.percent_pre3').html(num+"%");
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

                            /*start div value vene*/
                             var group = "";
                                 group += "<option value=0> </option>";
                                 for (var i=0; i < sample_data[2].length; i++) {
                                   group += "<option value="+sample_data[2][i]+">"+sample_data[2][i]+"</option>";    
                                 }

                                 $('#venn1').html(group);
                                 $('#venn2').html(group);
                                 $('#venn3').html(group);
                                 $('#venn4').html(group);

                             /*end div value vene*/

   
                             var sam_group  = "";  
                             for(var i=0 ;i < sample_data[3].length; i++){
                                
                                //console.log(i+": "+sample_data[3][i]);
                                if(i == sample_data[3].length-1){

                                    //console.log(sample_data[3][i]);
                                  document.getElementById('sub_sample').value = Number(sample_data[3][i]);
                                  document.getElementById('show_group').value = sam_group; 
                                   document.getElementById('alpha').value = Number(sample_data[3][i]);
                                   document.getElementById('beta').value = Number(sample_data[3][i]);
                                   document.getElementById('myradio').value = sample_data[3][i];
                                   document.getElementById('myradio1').value = sample_data[3][i];
                                   $('#sub_sample').attr({'max': Number(sample_data[3][i])});
                                   $('#alpha').attr({'max': Number(sample_data[3][i])});
                                   $('#beta').attr({'max': Number(sample_data[3][i])});
                                   
                                }else{
                                    sam_group += sample_data[3][i];
                                   }
                              }

                             $('#test_run2').html('Run queue sub sample complete');
                             clearInterval(interval);
                             $('.uk-child-width-expand > .pre2').next('li').find('a').trigger('click'); 
                             $(".Pre-show2").hide();
                             $(".Pre-test2").show();

                               /* set processbar 0   */
                               $('#bar_pre2').width(0+"%");
                               $('.percent_pre2').html(0+"%"); 
                            

                      }else{

                            var num = sample_data[1];
                            $('#bar_pre2').width(num+"%");
                            $('.percent_pre2').html(num+"%");
                            time = 20;  
  
                      }  
                    },
                    error:function(e){
                      console.log(e.message);
                    }
                });
              }
          },1000);
>>>>>>> 78a410303ff1cee517a5c7bd8cb8b384609891c9
        }

        function getvalue(array_data) {
            var data_value = array_data;
            $.ajax({
                type: "post",
                datatype: "json",
                url: "<?php echo base_url('Run_advance/get_json'); ?>",
                data: {data_array: data_value},
                success: function (data) {
                    var data_job = $.parseJSON(data);
                    // console.log("jid :" + data_job[0]);
                    // console.log("pid :" + data_job[1]);
                    checkrun(data_job);
                },
                error: function (e) {
                    console.log(e.message);
                }
            });

        }

        function checkrun(job_val) {

            var time = 30;
            var interval = null;
            interval = setInterval(function () {
                time--;
                $('#time').html(time);
                if (time === 0) {
                    $.ajax({
                        type: "post",
                        datatype: "json",
                        url: "<?php echo base_url('Run_advance/check_run'); ?>",
                        data: {data_job: job_val},
                        success: function (data) {
                            //console.log("data : " + JSON.parse(data));
                            var data_up = $.parseJSON(data);
                            if (data_up[0] == "0") {
                                $('#test_run').html('Run queue complete');
                                clearInterval(interval);
                                get_prepare(data_up);

                            } else {
                                var show_data = data_up[0];
                                var show_num = data_up[1];
                                $('#bar_pre').width(show_num + "%");
                                $('.percent_pre').html(show_num + "%");
                                $('#test_run').html(show_data);
                                time = 30;

                            }
                        },
                        error: function (e) {
                            console.log(e.message);
                        }
                    });
                }
            }, 1000);
        }

        function get_prepare(data) {
            $.ajax({
                type: "post",
                datatype: "json",
                url: "<?php echo base_url('Run_advance/read_count'); ?>",
                data: {data_count: data},
                success: function (data) {
                    var d_group = "";
                    var d_count = $.parseJSON(data);
                    for (var i = 0; i < d_count.length; i++) {

                        if (i == d_count.length - 1) {
                            document.getElementById('sub_sample').value = Number(d_count[i]);
                            document.getElementById('show_group').value = d_group;

                            $('.uk-child-width-expand > .pre').next('li').find('a').trigger('click');
                            $(".Pre-show").hide();
                            $(".Pre-test").show();

                            $('#test_run').html("run queue");
                            $('#bar_pre').width(Math.round(0) + "%");
                            $('.percent_pre').html(Math.round(0) + "%");

                        } else {
                            d_group += d_count[i];
                        }

                    }

                },
                error: function (e) {
                    console.log(e.message);
                }
            });
        }

        function validateNumber(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46) {
                return true;
            } else if (key < 48 || key > 57) {
                return false;
            } else {
                return true;
            }
        }


        function checkvalue() {
            var mbig = document.getElementById('mbig');
            if (mbig.value == "") {
                $('#mbig').css("border", "1px solid #FF0000");
            } else {
                $('#mbig').css("border", "1px solid #e1ede1");
            }
        }

        function checkvalue2() {
            var mhomo = document.getElementById('mhomo');
            if (mhomo.value == "") {
                $('#mhomo').css("border", "1px solid #FF0000");
            } else {
                $('#mhomo').css("border", "1px solid #e1ede1");
            }
        }

        function checkvalue3() {
            var miniread = document.getElementById('miniread');
            if (miniread.value == "") {
                $('#miniread').css("border", "1px solid #FF0000");
            } else {
                $('#miniread').css("border", "1px solid #e1ede1");
            }
        }

        function checkvalue4() {
            var maxread = document.getElementById('maxread');
            if (maxread.value == "") {
                $('#maxread').css("border", "1px solid #FF0000");
            } else {
                $('#maxread').css("border", "1px solid #e1ede1");
            }
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


