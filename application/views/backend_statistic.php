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
            <?php echo "User :" . $username . "   Email :" . $email . "   ID :" . $id;?>
            <br>
            <ul class="breadcrumb">
                <li><a href="#">Home</a><span class="divider">/</span> </li>
                <li><a href="#">Library</a><span class="divider">/</span> </li>
                <li><a href="#">data</a><span class="divider">/</span> </li>
            </ul>
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
            <?php  echo "<pre>" . $rs_cpu . "</pre>"?>


        </div>
    </div>
</div>