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
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li class="active">New project</li>

            </ol>
            <h1 class="page-header">New Projects</h1>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo form_open('new_projects/insert_project') ?>
                            <div class="form-group">
                                <label>Project Name :</label>
                                <input class="form-control" name="project_name" type="text" />
                                <label>Title Name :</label>
                                <input class="form-control" name="project_title" type="text" />
                                <label>Detail samples :</label>
                                <textarea class="form-control" name="project_detail" ></textarea>
                                <div class="form-group">
                                <label>Permission :</label>
                                <label class="radio-inline">
                                    <input type="radio" name="project_permission" id="" value="private">private
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="project_permission" id="" value="public">public
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="project_permission" id="" value="share">share people
                                </label>
                                </div>

                                <label>Project type :</label>
                                <select class="uk-select" name="project_type">
                                    <option>18s</option>
                                    <option>16s</option>
                                    <option>its</option>
                                </select><br>
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

                                    <?php // print_r($result_file) ?>



                                 <label>Select sample from owncloud :</label>
                                    <select class="uk-select" name="project_path">
                                        <?php foreach ($result_folder as $r) { ?>
                                        <option  value="<?php echo $path_owncloud.$r;?>"><?php echo $r;?></option>
                                        <?php  } ?>

                                    </select>





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