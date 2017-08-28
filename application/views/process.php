<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>
<meta http-equiv="refresh" content="15">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li <?php if ($controller_name == 'process') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'process') { ?>processing<?php } else { ?><a
                        href="<?php echo site_url('projects/index/' . $current_project) ?>">Current
                            project</a><?php } ?></li>
            </ol>
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
        $num = null;
        $keywords_split_line = array();
        $progress = "owncloud/data/$user/files/$project/output/progress.txt";
        if (file_exists($progress)) {
            $file_progress = fopen($progress, "r");
            $keywords_split_line = preg_split("/[\n]/", fread($file_progress, filesize($progress)));
            //print_r($keywords_split_line);
            $num = count($keywords_split_line);
        }
        // echo $num;
        if (file_exists($progress) and $num < 18) {
            ?>
            <br/>
            <div class="row">
                <div class="col-lg-12">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                             aria-valuenow="<?php echo $num / 18 * 100; ?>" aria-valuemin="0" aria-valuemax="100"
                             style="width:<?php echo $num / 18 * 100; ?>%">
                            <?php echo round($num / 18 * 100); ?>%
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-2">
                            <button class="btn  btn-warning" style="width:100%">
                                Quality Control<i class="fa fa-check" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="col-xs-1"><center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn  btn-warning" style="width:100%">
                                Align Sequence
                                <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></button>
                        </div>
                        <div class="col-xs-1"><center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Clean Alignment</button>
                        </div>
                        <div class="col-xs-1">
                            <center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Pre-custer-sequence </button>
                            <center><i class="fa fa-long-arrow-up fa-rotate-180 fa-2x" style="margin-top: 5px"></i></center>
                        </div>
                        <div class="col-xs-1">
                            <center>

                            </center>
                        </div>


                    </div>
                    <br>
                    <div class="row">

                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Classify OTU</button>
                            <center><i class="fa fa-long-arrow-up fa-rotate-180 fa-2x" style="margin-top: 5px"></i></center>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right fa-rotate-180 fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                <font size="1.5"> Remove non-bacterial sequence</font></button>
                        </div>
                        <div class="col-xs-1">
                            <center>
                                <i class="fa fa-long-arrow-right fa-rotate-180 fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2 ">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Classify Sequence</button>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right fa-rotate-180 fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Chimera detection</button>
                        </div>



                    </div>
                    <br>
                    <div class="row">

                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Alpha/Beta diversity</button>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right  fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                <font size="1.5"> Make biom</font></button>
                        </div>
                        <div class="col-xs-1">
                            <center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2 ">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Plot graph</button>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right  fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Chimera detection</button>
                        </div>



                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table" width="100%" border="0">

                        <tr>
                            <td><?php if (in_array("check_file", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>check_file</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("check_oligos", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>check_oligos</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("make_contigs_oligos", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>make_contigs_oligos</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("make_contigs_summary", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>make_contigs_summary</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("screen_seqs", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>screen_seqs</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("classify_system", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>classify_system</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("phylotype_count", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>phylotype_count</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("sub_sample_summary", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>sub_sample_summary</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("plot_graph", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>plot_graph</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("plot_graph_r_heatmap", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>plot_graph_r_heartmap</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("plot_graph_r_NMD", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>plot_graph_r_NMD</td>
                        </tr>
                        <tr>
                            <td><?php if (in_array("plot_graph_r_Rare", $keywords_split_line)) { ?><i
                                        class="fa fa-check"><?php } ?></td>
                            <td>plot_graph_r_Rare</td>
                        </tr>


                    </table>
                </div>


            </div>



            <?php
        } else {
           // redirect("/projects/index/" . $current_project, 'refresh');
        } ?>


    </div>
</div>