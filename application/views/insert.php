<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
} else {
    header("location: main/login");
} ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <br>
            <ul class="breadcrumb">
                <li><a href="#">Home</a><span class="divider">/</span> </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header"> Test insert</h2>
            <?php echo form_open('insert/insert_data')?>
            <table class="table">
                <thead>
                <tr>
                    <td>
                        name
                    </td>
                    <td>
                        input
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        input text name :
                    </td>
                    <td>
                        <?php echo form_input('name','name','name');?>
                    </td>
                </tr>
                <tr>
                    <td>
                        input text name :
                    </td>
                    <td>
                        <?php echo form_input('input','input','input');?>
                    </td>
                </tr>
                <tr>
                    <td>

                    </td>
                    <td>
                        <?php echo form_submit('save','submit','class=btn-btn-dufault')?>
                    </td>
                </tr>
                </tbody>


            </table>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Show data</h2>
            <table class="table">
                <thead>
                <tr>
                    <td>
                        name
                    </td>
                    <td>
                        input
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rs as $r) { ?>
                <tr>
                    <td>
                        input text name :
                    </td>
                    <td>
                    <?php echo $r['name']?>
                    </td>
                    <td><?php echo anchor("insert/delete_data/".$r['_id'],"Delete")?></td>
                    <td><?php echo anchor("update/update_data/".$r['_id'],"Edit") ?></td>
                </tr>
                <?php } ?>
                </tbody>


            </table>
        </div>
    </div>
</div>