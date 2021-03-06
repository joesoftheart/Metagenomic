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
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">New project</li>

            </ol>
            <h4 class="page-header">New Projects</h4>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-10 col-lg-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo form_open('new_projects/insert_project') ?>
                                <div class="form-group">
                                    <label>Project Name :</label>
                                    <input class="form-control" name="project_name" type="text" required/>
                                    <label>Title Name :</label>
                                    <input class="form-control" name="project_title" type="text"/>
                                    <label>Project detail:</label>
                                    <textarea class="form-control" name="project_detail" ></textarea>
                                    <div class="form-group" >
                                        <label>Project permission :</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id="" value="private" required>private
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id="" value="public">public
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id="" value="share">share
                                            people
                                        </label>
                                    </div>
                                    <label>Raw sequencing data from :</label>
                                    <select class="uk-select uk-width-1-4" name="project_sequencing" required>
                                        <option value="illumina">illumina</option>
                                        <option value="other">other</option>
                                    </select><br><br>
                                    <label>Project type :</label>
                                    <select class="uk-select uk-width-1-4" name="project_type" required>
                                        <option value="16s">16s</option>
                                        <option value="18s">18s</option>
                                        <option value="its">ITS</option>
                                    </select><br>
                                    <label>Select program :</label>
                                    <div class="form-group">
                                        <label class="radio-inline">
                                            <input type="radio" name="project_program" id="mothur" value="mothur" required>Mothur

                                        </label>
                                        <!--                                    <label>Select analysis :</label> -->
                                        <select class="uk-select  uk-width-1-4 hide" id="program"
                                                name="project_analysis">
                                            <option value="OTUs">OTUs</option>
                                            <option value="phylotype">Phylotype</option>
                                        </select>
                                        <br>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_program" id="qiime" value="qiime">Qiime1
                                        </label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_program" id="mothur_qiime" value="mothur_qiime">Mothur + Qiime1
                                        </label><br>
                                         <label class="radio-inline">
                                            <input type="radio" name="project_program" id="qiime2" value="qiime2">Qiime2
                                        </label>
                                    </div>

                                    <div>
                                        <?php
                                        $path_owncloud = "../owncloud/data/" . $username . "/files/";

                                        $result_folder = array();
                                        $result_file = array();
                                        if (is_dir($path_owncloud)) {
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
                                        }
                                        ?>

                                        <?php // print_r($result_file) ?>


                                        <label>Select sample from owncloud :</label>
                                        <select class="uk-select  uk-width-1-2" name="project_path" required>
                                            <?php if ($result_folder != null) { ?>
                                                <?php foreach ($result_folder as $r) { ?>
                                                    <option value="<?php echo $path_owncloud . $r; ?>"><?php echo $r; ?></option>
                                                <?php } ?>
                                            <?php } else {
                                                echo "<option>You not have sample in owncloud</option>";
                                            } ?>

                                        </select>


                                        <!--                                    <br>-->
                                        <!--                                <input class="form-control" name="txtFileName" type="text" id="txtFileName">-->
                                        <!--                                    <br>-->
                                        <!--                                <input class="form-control" name="btnBrowse" type="button" id="btnBrowse" value="Browse" onClick="filName.click();">-->
                                        <!--                                <input class="form-control" type="file" name="filName" STYLE="display:none" onChange="txtFileName.value = this.value;">-->


                                    </div>
                                    <label>Please check your reads sequence which you receive NGS platform:  </label>
                                    <div class="form-group" >
                                        <label class="radio-inline">
                                            <input type="radio" name="project_platform" id="platform_mi" value="miseq" required>Miseq lilumina

                                        </label><br>
                                                <div class="form-group hide" id="miseq" >
                                                    <label class="radio-inline">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="project_platform_type" id="miseq_without_barcodes"  value="miseq_without_barcodes" required>Paired-end fastq file without barcode and primer
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="project_platform_type" id="miseq_contain_primer"  value="miseq_contain_primer" required>Paired-end fastq file without barcode but contain primer
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="project_platform_type" id="miseq_barcodes_primers" value="miseq_barcodes_primers">Paired-end file contrain barcodes and primers
                                                        </label>
                                                    </label>
                                                </div>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_platform" id="platform_pro" value="proton">Ion Proton, PacBio

                                        </label>
                                        <div class="form-group hide " id="proton">
                                            <label class="radio-inline">
                                                <label class="radio-inline">
                                                    <input type="radio" name="project_platform_type" id="proton_barcodes_primers" value="proton_barcodes_primers" >Fastq file contain barcodes and primers
                                                </label><br>
                                                <label class="radio-inline">
                                                    <input type="radio" name="project_platform_type" id="proton_barcodes_fasta"  value="proton_barcodes_fasta" required>Only fasta file contain barcodes and primers (no quality file
                                                    )
                                                </label><br>

                                                 <label class="radio-inline">
                                                    <input type="radio" name="project_platform_type" id="proton_without"  value="proton_without" required> Fasta file without barcodes and primers (no quality file)
                                                </label>

                                            </label>
                                        </div>

                                    </div>


                                </div>

                                <!--                                <label>Would you like to create new submission for SRA of NCBI :</label>-->
                                <!--                                <div class="form-group">-->
                                <!--                                    <label class="radio-inline">-->
                                <!--                                        <input type="radio" name="project_permission" id="" value="private">Yes-->
                                <!--                                    </label>-->
                                <!--                                    <label class="radio-inline">-->
                                <!--                                        <input type="radio" name="project_permission" id="" value="public">No-->
                                <!--                                    </label>-->
                                <!--                                    <label class="radio-inline">-->
                                <!--                                        <input type="radio" name="project_permission" id="" value="share">Later-->
                                <!--                                    </label>-->
                                <!--                                </div>-->
                                <!--                                <ul uk-accordion>-->
                                <!--                                    <li class="uk-open">-->
                                <!--                                        <h3 class="uk-accordion-title">Create a project file +</h3>-->
                                <!--                                        <div class="uk-accordion-content">-->
                                <!--                                            <table>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>UserName : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>LastName : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>FirstName : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>Email : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>Center : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>Type : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>Website : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>ProjectName : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>ProjectTitle : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>Description : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                                <tr>-->
                                <!--                                                    <td>Grant id : </td>-->
                                <!--                                                    <td><input class="uk-input" type="text"></td>-->
                                <!--                                                </tr>-->
                                <!--                                            </table>-->
                                <!---->
                                <!---->
                                <!--                                           </div>-->
                                <!--                                    </li>-->
                                <!--                                    <li>-->
                                <!--                                        <h3 class="uk-accordion-title">Create detail the experiment data +</h3>-->
                                <!--                                        <div class="uk-accordion-content">-->
                                <!--                                            <label>Environment :</label>-->
                                <!--                                            <select class="uk-select uk-width-1-4" name="">-->
                                <!--                                                <option value="">none</option>-->
                                <!--                                                <option value="">air</option>-->
                                <!--                                                <option value="">host_associated</option>-->
                                <!--                                                <option value="">soil</option>-->
                                <!--                                                <option value="">wastewater</option>-->
                                <!--                                                <option value="">water</option>-->
                                <!--                                                <option value="">sediment</option>-->
                                <!--                                                <option value="">microbial</option>-->
                                <!--                                            </select><br>-->
                                <!--                                            <label>Plant :</label>-->
                                <!--                                            <select class="uk-select uk-width-1-4" name="">-->
                                <!--                                                <option value="">none</option>-->
                                <!--                                                <option value="">plant_associated</option>-->
                                <!---->
                                <!--                                            </select><br>-->
                                <!--                                            <label>Human :</label>-->
                                <!--                                            <select class="uk-select uk-width-1-4" name="">-->
                                <!--                                                <option value="">none</option>-->
                                <!--                                                <option value="">human_associated</option>-->
                                <!--                                                <option value="">human_gut</option>-->
                                <!--                                                <option value="">human_oral</option>-->
                                <!--                                                <option value="">human_skin</option>-->
                                <!--                                                <option value="">human_vaginal</option>-->
                                <!--                                            </select><br>-->
                                <!--                                        </div>-->
                                <!--                                    </li>-->
                                <!--                                    <li>-->
                                <!--                                        <h3 class="uk-accordion-title">Item 3</h3>-->
                                <!--                                        <div class="uk-accordion-content">-->
                                <!--                                            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat proident.</p>-->
                                <!--                                        </div>-->
                                <!--                                    </li>-->
                                <!--                                </ul>-->
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
        $('#miseq_barcodes_primers').attr("disabled",false);
        $('#proton_barcodes_fasta').attr("disabled",false);
        $('#proton_without').attr("disabled",false);
    });

    $('#qiime').on('change', function () {
        $('#program').addClass("hide");
        $('#miseq_barcodes_primers').attr("disabled",true);
        $('#proton_barcodes_fasta').attr("disabled",true);
        $('#proton_without').attr("disabled",false);
    });
    $('#mothur_qiime').on('change', function () {
        $('#program').addClass("hide");
        $('#miseq_barcodes_primers').attr("disabled",false);
        $('#proton_barcodes_fasta').attr("disabled",false);
        $('#proton_without').attr("disabled",false);
    });

    $('#qiime2').on('change', function () {
        $('#program').addClass("hide");
        $('#platform_mi').attr("disabled",true);
        $('#proton_barcodes_primers').attr("disabled",true);
        $('#proton_barcodes_fasta').attr("disabled",true);
    });


    
    $('#platform_mi').on('change', function () {
        $('#miseq').removeClass("hide");
        $('#proton').addClass("hide");
        $('#proton_barcodes_primers').prop('checked',false);
        $('#proton_barcodes_fasta').prop('checked',false);
        $('#proton_without').prop('checked',false);
    });

    $('#platform_pro').on('change', function () {
        $('#miseq_without_barcodes').prop('checked',false);
        $('#miseq_contain_primer').prop('checked',false);
        $('#miseq_barcodes_primers').prop('checked',false);
        $('#proton').removeClass("hide");
        $('#miseq').addClass("hide");
    });
</script>
