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
            <h1 class="page-header">New Projects</h1>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo form_open('new_projects/insert_project') ?>
                            <div class="form-group">
                                <label>Project Name :</label>
                                <input class="form-control" name="name_project" type="text" />
                                <label>Title Name :</label>
                                <input class="form-control" name="title_project" type="text" />
                                <label>Detail samples :</label>
                                <textarea class="form-control" name="detail_project" ></textarea>
                                <div class="form-group">
                                <label>Permission :</label>
                                <label class="radio-inline">
                                    <input type="radio" name="radioname" id="" value="joesoftheart">private
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radioname" id="" value="joesoftheart">public
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="radioname" id="" value="joesoftheart">share people
                                </label>
                                </div>
                            </div>
                            <button type="submit" name="save" value="submit" class="btn btn-default">Submit</button>
                            <button type="reset" name="reset"  class="btn btn-default">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>