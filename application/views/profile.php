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
        <div class="col-lg-12"><br>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">Profile</li>


            </ol>
        </div>
    </div>
    <div class="row">
        <div class="uk-card uk-card-default uk-card-body">
            <div class="col-lg-2">
                <?php foreach ($rs_user as $s) {
                    $name_img_profile = $s['user_img_profile'];

                } ?>
                <div>
                    <img class="img-circle" id="profile_picture" height="128"
                         data-src="<?php echo base_url() ?>/images/default.jpg" data-holder-rendered="true"
                         style="width: 140px; height: 140px;"
                         src="<?php if (isset($name_img_profile) and $name_img_profile != null) {
                             echo base_url() ?>images/<?php echo $name_img_profile . ".png";
                         } else {
                             echo base_url() ?>images/default.jpg <?php } ?>"/>
                    <br><br>
                    <a type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Change Profile
                        Picture</a>
                </div>
            </div>


            <div id="myModal" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h3>Change Profile Picture</h3>
                        </div>
                        <div class="modal-body">
                            <form action="profile/change_img/<?php echo $id; ?>/<?php echo $username; ?>" method="post"
                                  enctype="multipart/form-data" id="form1">
                                <strong>Upload Image:</strong> <br><br>
                                <input type="file" name="pictures" value="" id="image_name"/>

                                <div id='preview-profile-pic'></div>
                                <div id="thumbs" style="padding:5px; width:600px"></div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" form="form1" value="Submit" class="btn btn-primary">Crop & Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <!--            <form action="profile/change_img" method="post" enctype="multipart/form-data">-->
            <!--                <input type="file" name="pictures" onchange="javascript:this.form.submit();">-->
            <!--            </form>-->

            <div class="col-lg-9">
                <?php if ($rs_user != null) { ?>
                <h3 class="uk-card-title">User info </h3>
                <?php echo form_open('edit_profile/edit_profile/' . $id); ?>
                <?php foreach ($rs_user as $r) { ?>
                    <p>User Name : <?php echo $username ?></p>
                    <p>First Name :<?php echo $r['first_name']; ?></p>
                    <p>Last Name :<?php echo $r['last_name']; ?></p>
                    <p>Addess :<?php echo $r['address']; ?></p>
                    <p>Tel : <?php echo $r['tel']; ?></p>
                    <p>Gender : <?php echo $r['gender']; ?></p>
                <?php } ?>
                <button class="btn btn-default right" name="edit">Edit profile</button>
                <?php form_close() ?>
            </div>
            <?php } else { ?>

                <h3 class="uk-card-title">User info </h3>
                <?php echo form_open('profile/update_profile/' . $id . "/" . $username); ?>
                <p>User Name : <?php echo $username ?></p>
                <p>First Name :<input class="uk-input" type="text" name="first_name" value=""></p>
                <p>Last Name :<input class="uk-input" type="text" name="last_name" value=""></p>
                <p>Addess :<input class="uk-input" type="text" name="address" value=""></p>
                <p>Tel : <input class="uk-input" type="text" name="tel" value=""></p>
                <div class="form-group">
                    Gender :
                    <label class="radio-inline">
                        <input type="radio" name="gender" id="" value="Male">Male
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="gender" id="" value="Female">Female
                    </label>
                </div>
                <button class="btn btn-default right" name="update">Update profile</button>
                <?php form_close() ?>

            <?php } ?>

        </div>
    </div>
</div>
</div>


<script>
    document.getElementById("file").onchange = function () {
        document.getElementById("form").submit();
    };
</script>