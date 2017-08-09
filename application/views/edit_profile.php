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
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">User info </h3>
                <?php echo form_open('profile/update_profile/'.$id) ?>
                <?php foreach ($rs_user as $r) {?>
                    <p>User Name : <?php echo  $username ?></p>
                    <p>First Name :<input class="uk-input" type="text" name="first_name" value="<?php echo $r['first_name']; ?>"></p>
                    <p>Last Name :<input class="uk-input" type="text" name="last_name" value="<?php echo $r['last_name']; ?>"></p>
                    <p>Addess :<input class="uk-input" type="text" name="address" value="<?php echo $r['address']; ?>"></p>
                    <p>Tel : <input class="uk-input" type="text" name="tel" value="<?php echo $r['tel']; ?>"></p>
                    <div class="form-group">
                        Gender :
                        <label class="radio-inline">
                            <input type="radio" name="gender" id="" value="Male" <?php if ($r['gender'] == "Male"){
                                echo "checked";} ?>>Male
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="gender" id="" value="Female" <?php if ($r['gender'] == "Female"){
                                echo "checked";} ?>>Female
                        </label>
                    </div>
                <?php  } ?>
                <button class="btn btn-default right">Update profile</button>
                <?php form_close() ?>

            </div>



        </div>
    </div>
</div>