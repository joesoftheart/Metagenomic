<div id="page-wrapper">


        <?php

        foreach ($rs as $r) {

            $sample_folder = $r['project_path'];
        }
        $project = basename($sample_folder);
        $user = $this->session->userdata['logged_in']['username'];

        $path = "../owncloud/data/$user/files/$project/output/";


        ?>


                <div class="row">
                    <div class="col-lg-3">
                        <i class="fa fa-grav fa-5x" aria-hidden="true"></i>

                    </div>

                </div>


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
                            <table class="table table-striped table-bordered dataTable" width="100%">
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
                            <table class="table table-striped table-bordered dataTable" style="text-align: center" width="100%">
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