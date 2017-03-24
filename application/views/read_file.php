<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12"><br>
            <ul class="breadcrumb">
                <li><a href="#">Home</a><span class="divider">/</span></li>
            </ul>

            <div class="row">
                <div class="col-lg-12">
                    <?php echo form_open_multipart('read_file/upload_file/');?>
                    <table class="table">
                        <thead>
                        <tr>
                            <td>
                                Name
                            </td>
                            <td>
                                Show file
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_input('title'); ?>
                            </td>
                            <td>
                                <?php echo form_upload('pictures'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><?php echo form_submit('submit', 'save', 'class="btn btn-default"');?></td>
                        </tr>
                        </tbody>
                    </table>
                    <?php form_close(); ?>
                </div>

            </div>
        </div>
    </div>
</div>