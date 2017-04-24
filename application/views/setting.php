<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {display:none;}

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12"><br>
            <?php $controller_name = $this->uri->segment(1); ?>

            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li class="active">Setting</li>

            </ol>

            <div class="row">
                <table>
                    <?php foreach ($rs_st as $st) ?>
                    <tbody>
                    <tr><td><p>Noti when run project success </p></td>
                    <td><label class="switch">
                            <input type="checkbox" name="noti_process" id="toggle-one"  <?php if ($st['noti_process'] == "on"){ echo "checked";} else{ } ?>>
                            <div class="slider round"></div>

                        </label></td>

                    </tr>
                    <tr>
                        <td> Server reboot</td>
                        <td>
                    <label class="switch">
                        <input type="checkbox" name="noti_reboot" id="toggle-two"  value="<?php echo $st['noti_reboot'];?>" <?php if ($st['noti_reboot'] == "on"){ echo "checked";} else{ } ?>>
                        <div class="slider round"></div>
                    </label>
                        </td>
                    </tr>
                    <tr>
                        <td> Max storage</td>
                        <td><label class="switch">
                                <input type="checkbox" name="noti_max_storage"  value="<?php echo $st['noti_max_storage'];?>" id="toggle-three"<?php if ($st['noti_max_storage'] == "on"){ echo "checked";} else{ } ?>>
                                <div class="slider round"></div>
                            </label></td>

                    </tr>
                    <tr>
                        <td>Logout automatic when not use 1 hour</td>
                        <td>
                    <label class="switch">
                        <input type="checkbox" name="auto_logout" id="toggle-four"  value="<?php echo $st['auto_logout'];?>" <?php if ($st['auto_logout'] == "on"){ echo "checked";} else{ } ?>>
                        <div class="slider round"></div>
                    </label>
                        </td>
                    </tr>
                    <tr>
                        <td>Send to E-mail</td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" name="noti_email" id="toggle-five"  value="<?php echo $st['noti_email'];?>"<?php if ($st['noti_email'] == "on"){ echo "checked";} else{ } ?>>
                                <div class="slider round"></div>
                            </label>
                        </td>
                    </tr>
                    </tbody>

                </table>
                <div id="show"></div>
            </div>

        </div>
    </div>
</div>


<script>
    $(function() {
        $('#toggle-one').change(function() {

            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>setting/update_setting",
                data: {noti_process: $("#toggle-one").val(),noti_reboot: $("#toggle-two").val(),noti_max_storage: $("#toggle-three").val(),auto_logout: $("#toggle-four").val(),noti_email: $("#toggle-five").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show").html(data);
                    }
            });// you have missed this bracket
            return false;
        });
        $('#toggle-two').change(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>setting/update_setting",
                data: {noti_process: $("#toggle-one").val(),noti_reboot: $("#toggle-two").val(),noti_max_storage: $("#toggle-three").val(),auto_logout: $("#toggle-four").val(),noti_email: $("#toggle-five").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show").html(data);
                    }
            });// you have missed this bracket
            return false;
        });
        $('#toggle-three').change(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>setting/update_setting",
                data: {noti_process: $("#toggle-one").val(),noti_reboot: $("#toggle-two").val(),noti_max_storage: $("#toggle-three").val(),auto_logout: $("#toggle-four").val(),noti_email: $("#toggle-five").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show").html(data);
                    }
            });// you have missed this bracket
            return false;
        });
        $('#toggle-four').change(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>setting/update_setting",
                data: {noti_process: $("#toggle-one").val(),noti_reboot: $("#toggle-two").val(),noti_max_storage: $("#toggle-three").val(),auto_logout: $("#toggle-four").val(),noti_email: $("#toggle-five").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show").html(data);
                    }
            });// you have missed this bracket
            return false;
        });
        $('#toggle-five').change(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>setting/update_setting",
                data: {noti_process: $("#toggle-one").val(),noti_reboot: $("#toggle-two").val(),noti_max_storage: $("#toggle-three").val(),auto_logout: $("#toggle-four").val(),noti_email: $("#toggle-five").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show").html(data);
                    }
            });// you have missed this bracket
            return false;
        });
    })
</script>