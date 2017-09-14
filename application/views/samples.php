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
                <li class="active">Sample</li>

            </ol>
            <h1 class="page-header">New Projects</h1>


            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form role="form">
                                    <div class="form-group">
                                        <label>Sample Name :</label>
                                        <input class="form-control" type="text"/>
                                        <label>Name :</label>
                                        <input class="form-control" type="text"/>
                                        <label>Address :</label>
                                        <textarea class="form-control"></textarea>
                                        <div class="form-group">
                                            <label>Sex :</label>
                                            <label class="radio-inline">
                                                <input type="radio" name="radioname" id="" value="joesoftheart">male
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="radioname" id="" value="joesoftheart">female
                                            </label>

                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-default">Submit</button>
                                    <button type="reset" class="btn btn-default">Clear</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>