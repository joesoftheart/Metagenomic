
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
                <div class="col-lg-6">
                    <div class="uk-card-default">

                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="noti_process change" type="checkbox" data-toggle="toggle">
                            Noti when run project success
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="noti_reboot change" type="checkbox" data-toggle="toggle">
                            Server reboot
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="noti_max_storage change" type="checkbox" checked data-toggle="toggle">
                            Max storage
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="auto_logout change" type="checkbox" data-toggle="toggle">
                            Logout automatic when not use 1 hour
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="noti_email changee" type="checkbox" checked data-toggle="toggle">
                            Noti when run project success
                        </label>
                    </div>



                </div>
                <div id="show"></div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

       // alert("kkk");
    });
</script>