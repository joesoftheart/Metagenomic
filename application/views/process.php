<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>
<script language="JavaScript">
    var counter = 0;
    window.setInterval("refreshDiv()", 5000);
    function refreshDiv() {
        counter = counter + 1;
        $("#progress").load(document.URL + ' #progress');
        $("#pipeline").load(document.URL + ' #pipeline');
    }



</script>
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

            ?>
            <br/>
            <div class="row" >
                <div class="col-lg-12" >
                    <div class="progress" id="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                             aria-valuenow="<?php echo $num / 18 * 100; ?>" aria-valuemin="0" aria-valuemax="100"
                             style="width:<?php echo $num / 18 * 100; ?>%">
                            <?php echo round($num / 18 * 100); ?>%
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default" >
                <div class="panel-body" id="pipeline">

                    <div class="row">
                        <div class="col-lg-2">
                            <button class="btn  btn-warning" style="width:100%">
                                Quality Control
                                <?php if (in_array("quality-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i><?php }else if(in_array("quality",$keywords_split_line)){
                                ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?>

                            </button>
                        </div>
                        <div class="col-xs-1"><center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn  btn-warning" style="width:100%">
                                Align Sequence
                                <?php if (in_array("align-sequence-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("align-sequence",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>
                        <div class="col-xs-1"><center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Pre-custer-sequence
                                <?php if (in_array("pre-cluster-chimera-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("pre-cluster-chimera",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>
                        <div class="col-xs-1">
                            <center>
                                <i class="fa fa-long-arrow-right fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Classify Sequence
                                <?php if (in_array("classify-sequence-remove-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("classify-sequence-remove",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
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
                                Plot graph
                                <?php if (in_array("plot-graph-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("plot-graph",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                            <center><i class="fa fa-long-arrow-up fa-rotate-180 fa-2x" style="margin-top: 5px"></i></center>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right fa-rotate-180 fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Alpha/Beta diversity
                                <?php if (in_array("alpha-beta-diversity-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("alpha-beta-diversity",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>
                        <div class="col-xs-1">
                            <center>
                                <i class="fa fa-long-arrow-right fa-rotate-180 fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2 ">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Classify OTU
                                <?php if (in_array("classify-otu-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("classify-otu",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right fa-rotate-180 fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                <font size="1.5">Remove non-bacterial sequence</font>
                                <?php if (in_array("classify-sequence-remove-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("classify-sequence-remove",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>



                    </div>
                    <br>
                    <div class="row">

                        <div class="col-lg-2 ">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Make biom
                                <?php if (in_array("make-biom-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php }else if(in_array("make-biom",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>
                        <div class="col-xs-1 ">
                            <center>
                                <i class="fa fa-long-arrow-right  fa-2x"></i>
                            </center>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline btn-warning" style="width:100%">
                                Picrust
                                <?php if (in_array("picrust-finish", $keywords_split_line)) { ?> <i class="fa fa-check" aria-hidden="true"></i>
                                <?php redirect('complete_run/index/'.$current_project) ?>

                                <?php }else if(in_array("finish",$keywords_split_line)){
                                    ?>
                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw" aria-hidden="true"></i>
                                <?php } ?></button>
                        </div>



                    </div>
                </div>
            </div>
            <?php
        ?>


    </div>
</div>