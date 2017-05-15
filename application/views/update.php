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
            <?php echo form_open('update/update_data/'.$this->uri->segment(3))?>
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
                            <?php echo form_input('name',$r['name'],'name');?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            input text name :
                        </td>
                        <td>
                            <?php echo form_input('input',$r['select'],'input');?>
                        </td>
                    </tr>
                <?php  } ?>
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
</div>