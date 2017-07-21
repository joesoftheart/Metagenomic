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
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li <?php if ($controller_name == 'process'){
                    echo "class=active";} ?>><?php if ($controller_name == 'process'){?>process project<?php } else {?><a href="<?php echo site_url('projects/index/'.$current_project)?>">Current project</a><?php } ?></li>
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
            print_r($keywords_split_line);
            $num = count($keywords_split_line);
        }
        echo $num;
        if(file_exists($progress) and $num < 18){
            ?>
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                     aria-valuenow="<?php echo $num/18*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $num/18*100; ?>%">
                    <?php echo $num/18*100; ?>%
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
        }else {
           // redirect("/projects/index/" . $current_project, 'refresh');
        }?>



    </div>
</div>