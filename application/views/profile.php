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
            <ul class="breadcrumb">
                <li><a href="#">Home</a><span class="divider">/</span></li>
                <li><a href="#">Library</a><span class="divider">/</span></li>
                <li><a href="#">data</a><span class="divider">/</span></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3" >
            <div class="uk-card uk-card-default uk-card-body">
                <img src="http://placehold.it/350x250">
            </div>
        </div>
        <div class="col-lg-9">
            <?php  if ($rs_user != null) { ?>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">User info </h3>
                <?php echo form_open('edit_profile/edit_profile/'.$id); ?>
                <?php foreach ($rs_user as $r) {?>
                <p>User Name : <?php echo  $username ?></p>
                <p>First Name :<?php echo $r['first_name']; ?></p>
                <p>Last Name :<?php echo $r['last_name']; ?></p>
                <p>Addess :<?php echo $r['address']; ?></p>
                <p>Tel : <?php echo $r['tel']; ?></p>
                <p>Gender : <?php echo $r['gender']; ?></p>
                <?php  } ?>
                <button class="btn btn-default right" name="edit">Edit profile</button>
                <?php form_close() ?>
            </div>
            <?php } else { ?>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">User info </h3>
                <?php echo form_open('profile/update_profile/'.$id); ?>
                    <p>User Name : <?php echo  $username ?></p>
                    <p>First Name :<input class="uk-input" type="text" name="first_name" value=""></p>
                    <p>Last Name :<input class="uk-input" type="text" name="last_name" value=""></p>
                    <p>Addess :<input class="uk-input" type="text" name="address" value=""></p>
                    <p>Tel : <input class="uk-input" type="text" name="tel" value=""></p>
                    <p>Gender : <input class="uk-input" type="text" name="gender" value=""></p>
                <button class="btn btn-default right" name="update">Update profile</button>
                <?php form_close() ?>
            </div>
            <?php } ?>

        </div>
    </div>
</div>