<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php foreach ($rs_notifi as $rs) { ?>
                <dl>
                    <dt><?php $name_subject = $rs['subject'] ?>
                        <?php echo anchor('view_notification/view_notification/' . $rs['_id'], $name_subject) ?>
                    </dt>
                    <dd>
                        <?php echo $rs['description']; ?>
                        <p class="pull-right"> <?php echo $rs['new']; ?></p>
                    </dd>
                    <dd>
                        <?php echo $rs['new'] ?>
                    </dd>
                </dl>

                <hr>
            <?php } ?>

        </div>
    </div>
</div>