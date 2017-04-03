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
        <div class="col-lg-12">
            <?php foreach ($rs_message as $rs_m) { ?>
            <dl>
                <dt>
                    <?php echo $rs_m['message_title']; ?>
                </dt>
                <dd>
                    <?php echo $rs_m['message_detail']; ?>
                    <p class="pull-right"> <?php echo $rs_m['date']; ?></p>
                </dd>
            </dl>

            <hr>
            <?php } ?>

        </div>
    </div>
</div>