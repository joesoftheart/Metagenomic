<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12"><br>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li class="active">Setting</li>

            </ol>
        </div>
    </div>
</div>