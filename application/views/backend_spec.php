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
        <div class="col-lg-12">
            <br>
            <?php $controller_name = $this->uri->segment(1); ?>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li>Statistics</li>
                <li class="active">Backend spec</li>

            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <!--            <table class="table">-->
            <!--                <thead>-->
            <!--                <tr>-->
            <!--                    <td>CPU Using</td>-->
            <!--                    <td>RAM Using</td>-->
            <!--                    <td>TOTAL ACCESS</td>-->
            <!--                    <td>Project Name</td>-->
            <!--                </tr>-->
            <!--                </thead>-->
            <!--                <tbody>-->
            <!--                    <tr>-->
            <!--                        <td>10 Core</td>-->
            <!--                        <td>1G</td>-->
            <!--                        <td>3 Person</td>-->
            <!--                        <td>Thalassemia</td>-->
            <!--                    </tr>-->
            <!---->
            <!--                </tbody>-->
            <!--                <tfoot>-->
            <!---->
            <!--                </tfoot>-->
            <!---->
            <!--            </table>-->
            <?php echo "<pre>" . $rs_cpu . "</pre>" ?>


        </div>
    </div>
</div>