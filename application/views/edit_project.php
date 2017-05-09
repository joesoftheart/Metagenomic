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
            <?php echo "User :" . $username . "   Email :" . $email . "   ID :" . $id;?>
            <br>
            <ul class="breadcrumb">
                <li><a href="#">Home</a> <span class="divider">/</span></li>
                <li><a href="#">Library</a> <span class="divider">/</span></li>
                <li class="active">Data</li>
            </ul>
            <h1 class="page-header">Edit Projects</h1>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo form_open('edit_project/edit_project/'.$this->uri->segment(3)) ?>
                                <div class="form-group">
                                    <?php foreach ($rs as $r) {?>
                                    <label>Project Name :</label>
                                    <input class="form-control" name="project_name" type="text" value="<?php echo $r['project_name']?>"/>
                                    <label>Title Name :</label>
                                    <input class="form-control" name="project_title" type="text" value="<?php echo $r['project_title'] ?>" />
                                    <label>Detail samples :</label>
                                    <textarea class="form-control" name="project_detail" ><?php if ($r['project_detail'] != null){ echo $r['project_detail'];}?></textarea>
                                    <div class="form-group">
                                        <label>Permission :</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id="" value="private" <?php if ($r['project_permission'] == "private"){
                                                echo "checked";
                                            } ?>>private
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id="" value="public" <?php if ($r['project_permission'] == "public"){
                                                echo "checked";
                                            } ?>>public
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="project_permission" id="" value="share" <?php if ($r['project_permission'] == "share"){
                                                echo "checked";
                                            } ?>>share people
                                        </label>
                                    </div>

                                    <label>Project type :</label>
                                    <select class="uk-select" name="project_type">
                                        <option <?php if ($r['project_type'] == "18s"){
                                            echo "selected";
                                        } ?>>18s</option>
                                        <option <?php if ($r['project_type'] == "16s"){
                                            echo "selected";
                                        } ?>>16s</option>
                                        <option>its</option>
                                    </select <?php if ($r['project_type'] == "its"){
                                        echo "selected";
                                    } ?>><br>
                                    <div>
                                        <?php
                                        $path_owncloud = "../owncloud/data/" . $username . "/files/";


                                        $result_folder = array();
                                        $result_file = array();
                                        $cdir = scandir($path_owncloud);
                                        foreach ($cdir as $key => $value)
                                        {
                                            if (!in_array($value,array(".","..")))
                                            {
                                                if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value))
                                                {
                                                    $result_folder[$value] = $value;
                                                }
                                                else
                                                {
                                                    $result_file[$value] = $value;
                                                }
                                            }
                                        }
                                        ?>

                                        <?php // print_r($result_folder) ?>



                                        <label>Select sample from owncloud :</label>
                                        <select class="uk-select" name="project_path">
                                            <?php foreach ($result_folder as $r_folder) { ?>
                                                <option  value="<?php echo $path_owncloud.$r_folder;?>" <?php  if($path_owncloud.$r_folder == $r['project_path']){ echo "selected";} ?>><?php echo $r_folder;?></option>
                                            <?php  } ?>

                                        </select>


                                           <?php } ?>


                                        <!--                                    <br>-->
                                        <!--                                <input class="form-control" name="txtFileName" type="text" id="txtFileName">-->
                                        <!--                                    <br>-->
                                        <!--                                <input class="form-control" name="btnBrowse" type="button" id="btnBrowse" value="Browse" onClick="filName.click();">-->
                                        <!--                                <input class="form-control" type="file" name="filName" STYLE="display:none" onChange="txtFileName.value = this.value;">-->



                                    </div>
                                </div>
                                <button type="submit" name="save" value="submit" class="btn btn-default">Submit</button>
                                <button type="reset" name="reset"  class="btn btn-default">Clear</button>
                            </div>
                            <?php echo form_close()?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>