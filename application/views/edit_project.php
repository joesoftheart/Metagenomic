<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
} else {
    header("location: main/login");
} ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <br>
            <ul class="breadcrumb">
                <li><a href="#">Home</a> <span class="divider">/</span></li>
                <li class="active">Edit project<span class="divider">/</span></li>

            </ul>
            <h4 class="page-header">Edit Projects</h4>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo form_open('edit_project/edit_project/' . $this->uri->segment(3)) ?>
                                <div class="form-group">
                                    <?php foreach ($rs as $r) { ?>
                                    <label>Project Name :</label>
                                    <input class="form-control" name="project_name" type="text"
                                           value="<?php echo $r['project_name'] ?>"/>
                                    <label>Title Name :</label>
                                    <input class="form-control" name="project_title" type="text"
                                           value="<?php echo $r['project_title'] ?>"/>
                                    <label>Detail samples :</label>
                                    <textarea class="form-control"
                                              name="project_detail"><?php if ($r['project_detail'] != null) {
                                            echo $r['project_detail'];
                                        } ?></textarea>
                                    <div class="form-group">
                                        <label>Permission :</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id=""
                                                   value="private" <?php if ($r['project_permission'] == "private") {
                                                echo "checked";
                                            } ?>>private
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id=""
                                                   value="public" <?php if ($r['project_permission'] == "public") {
                                                echo "checked";
                                            } ?>>public
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id=""
                                                   value="share" <?php if ($r['project_permission'] == "share") {
                                                echo "checked";
                                            } ?>>share people
                                        </label>
                                    </div>
                                    <label>Raw sequencing data from :</label>
                                    <select class="uk-select uk-width-1-4" name="project_sequencing">
                                        <option <?php if ($r['project_sequencing'] == "illumina") {
                                            echo "selected";
                                        } ?>>illumina
                                        </option>
                                        <option <?php if ($r['project_sequencing'] == "other") {
                                            echo "selected";
                                        } ?>>other
                                        </option>

                                    </select><br><br>
                                    <label>Project type :</label>
                                    <select class="uk-select uk-width-1-4" name="project_type">
                                        <option <?php if ($r['project_type'] == "18s") {
                                            echo "selected";
                                        } ?>>18s
                                        </option>
                                        <option <?php if ($r['project_type'] == "16s") {
                                            echo "selected";
                                        } ?>>16s
                                        </option>
                                        <option <?php if ($r['project_type'] == "its") {
                                            echo "selected";
                                        } ?>>its
                                        </option>
                                    </select><br>

                                    <label>Select program :</label>
                                    <div class="form-group">
                                        <label class="radio-inline">
                                            <input type="radio" name="project_program" id="mothur"
                                                   value="mothur" <?php if ($r['project_program'] == "mothur") {
                                                echo "checked";
                                            } ?>>mothur

                                        </label>

                                        <!--                                    <label>Select analysis :</label> -->
                                        <select class="uk-select  uk-width-1-4 <?php if ($r['project_analysis'] == "") {
                                            echo "hide";
                                        } ?>" id="program" name="project_analysis">
                                            <option <?php if ($r['project_analysis'] == "") {
                                                echo "selected";
                                            } ?>>
                                            </option>
                                            <option <?php if ($r['project_analysis'] == "OTUs") {
                                                echo "selected";
                                            } ?>>OTUs
                                            </option>
                                            <option <?php if ($r['project_analysis'] == "phylotype") {
                                                echo "selected";
                                            } ?>>phylotype
                                            </option>
                                        </select>
                                        <br>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_program" id="qiime"
                                                   value="qiime" <?php if ($r['project_program'] == "qiime") {
                                                echo "checked";
                                            } ?>>Qiime
                                        </label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_program" id="uparse"
                                                   value="uparse" <?php if ($r['project_program'] == "uparse") {
                                                echo "checked";
                                            } ?>>UPARSE
                                        </label>
                                    </div>
                                    <div>
                                        <?php
                                        $path_owncloud = "../owncloud/data/" . $username . "/files/";


                                        $result_folder = array();
                                        $result_file = array();
                                        $cdir = scandir($path_owncloud);
                                        foreach ($cdir as $key => $value) {
                                            if (!in_array($value, array(".", ".."))) {
                                                if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                                                    $result_folder[$value] = $value;
                                                } else {
                                                    $result_file[$value] = $value;
                                                }
                                            }
                                        }
                                        ?>

                                        <?php // print_r($result_folder) ?>


                                        <label>Select sample from owncloud :</label>
                                        <select class="uk-select" name="project_path">
                                            <?php foreach ($result_folder as $r_folder) { ?>
                                                <option value="<?php echo $path_owncloud . $r_folder; ?>" <?php if ($path_owncloud . $r_folder == $r['project_path']) {
                                                    echo "selected";
                                                } ?>><?php echo $r_folder; ?></option>
                                            <?php } ?>

                                        </select>





                                        <!--                                    <br>-->
                                        <!--                                <input class="form-control" name="txtFileName" type="text" id="txtFileName">-->
                                        <!--                                    <br>-->
                                        <!--                                <input class="form-control" name="btnBrowse" type="button" id="btnBrowse" value="Browse" onClick="filName.click();">-->
                                        <!--                                <input class="form-control" type="file" name="filName" STYLE="display:none" onChange="txtFileName.value = this.value;">-->


                                    </div>

                                </div>
                                <label>Plaase check your reads sequence which you receive NGS platform:  </label>
                                <div class="form-group" >
                                    <label class="radio-inline">
                                        <input type="radio" name="project_platform" id="platform_mi" value="miseq" <?php if ($r['project_platform_sam'] == "miseq") {
                                            echo "checked";
                                        } ?>>Miseq lilumina

                                    </label><br>
                                    <div class="form-group " id="miseq" >
                                        <label class="radio-inline">
                                            <label class="radio-inline">
                                                <input type="radio" name="project_platform_type" id="miseq_without_barcodes"  value="miseq_without_barcodes"   <?php if ($r['project_platform_type'] == "miseq_without_barcodes") {
                                                    echo "checked";
                                                } ?>>Paired-end fastq file without barcode
                                            </label><br>
                                            <label class="radio-inline">
                                                <input type="radio" name="project_platform_type" id="miseq_barcodes_primers" value="miseq_barcodes_primers" <?php if ($r['project_platform_type'] == "miseq_barcodes_primers") {
                                                    echo "checked";
                                                } ?>>Paired-end file contrain barcodes and primers
                                            </label>
                                        </label>
                                    </div>
                                    <label class="radio-inline">
                                        <input type="radio" name="project_platform" id="platform_pro" value="proton" <?php if ($r['project_platform_sam'] == "proton") {
                                            echo "checked";
                                        } ?>>Ion Proton

                                    </label>
                                    <div class="form-group " id="proton">
                                        <label class="radio-inline">
                                            <label class="radio-inline">
                                                <input type="radio" name="project_platform_type" id="proton_barcodes_primers" value="proton_barcodes_primers" <?php if ($r['project_platform_type'] == "proton_barcodes_primers") {
                                                    echo "checked";
                                                } ?>>Fastq file contrain barcodes and primers
                                            </label><br>
                                            <label class="radio-inline">
                                                <input type="radio" name="project_platform_type" id="proton_barcodes_fasta"  value="proton_barcodes_fasta" <?php if ($r['project_platform_type'] == "proton_barcodes_fasta") {
                                                    echo "checked";
                                                } ?>>Only fasta file contrain barcodes and primers (no quality file
                                                )
                                            </label>
                                        </label>
                                    </div>

                                </div>


                            </div>
                            <?php } ?>
                                <button type="submit" name="save" value="submit" class="btn btn-default">Submit</button>
                                <button type="reset" name="reset" class="btn btn-default">Clear</button>
                            </div>
                            <?php echo form_close() ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>

<script>
    $('#mothur').on('change', function () {
        $('#program').removeClass("hide");
    });
    $('#qiime').on('change', function () {
        $('#program').addClass("hide");
    });
    $('#uparse').on('change', function () {
        $('#program').addClass("hide");
    });
    $('#platform_mi').on('change', function () {
        $('#miseq').removeClass("hide");
        $('#proton').addClass("hide");
        $('#proton_barcodes_primers').prop('checked',false);
        $('#proton_barcodes_fasta').prop('checked',false);
        $('')
    });
    $('#platform_pro').on('change', function () {
        $('#miseq_without_barcodes').prop('checked',false);
        $('#miseq_barcodes_primers').prop('checked',false);
        $('#proton').removeClass("hide");
        $('#miseq').addClass("hide");
    });
</script>