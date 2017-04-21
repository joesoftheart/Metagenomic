<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php echo form_open('admin_notification/insert_noti')?>
            <label>Subject :</label>
          <input type="text" class="uk-input" name="subject">
            <label>Description :</label>
            <input type="text" class="uk-input" name="description">
            <label>New :</label>
            <select class="uk-select" name="new">
                <option value="no">No</option>
                <option value="new">New</option>
            </select>
            <label>Status :</label>
            <select class="uk-select" name="status">
                <option value="message">Message</option>
                <option value="reboot">Server reboot</option>
                <option value="delay">Server delay</option>
            </select>
            <br>
            <br>
            <button class="btn btn-default pull-right" type="submit" > submit</button>
            <?php form_close() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <table class="table">
                <thead>
                <th>Subject</th>
                <th>Description</th>
                <th>New</th>
                <th>Status</th>
                <th>Manage</th>
                </thead>
                <tbody>
                <?php foreach ($rs_noti as $rs){ ?>
                <tr>
                    <td>
                    <?php echo $rs['subject'] ?>
                    </td>
                    <td>
                        <?php echo $rs['description'] ?>
                    </td>
                    <td>
                        <?php echo $rs['new'] ?>
                    </td>
                    <td>
                        <?php echo $rs['status'] ?>
                    </td>
                    <td>
                        <?php echo anchor('admin_notification/delete_noti/'.$rs['_id'],'Delete',array('class' => 'btn btn-default')) ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>