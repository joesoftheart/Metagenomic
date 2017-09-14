<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Signup</title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/sb-admin-2-custom.css">

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/bootstrap/css/bootstrap.min.css">

    <!-- MetisMenu CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/metisMenu/metisMenu.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/sb-admin-2.css">

    <!-- Morris Charts CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/morrisjs/morris.css">

    <!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/font-awesome/css/font-awesome.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>vendor/datatables-plugins/dataTables.bootstrap.css">

    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>vendor/datatables-responsive/dataTables.responsive.css">


    <!-- Uikit Design -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/uikit.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <style>
        .modal-dialog,
        .modal-content {
            /* 80% of window height */
            height: 80%;
        }

        .modal-body {
            /* 100% = dialog height, 120px = header + footer */
            max-height: calc(100% - 120px);
            overflow-y: scroll;
        }
    </style>
</head>

<?php
if (isset($this->session->userdata['logged_in'])) {
    header("location: main/user_login_process");
}
?>
<body>


<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class='error_msg'><?php echo validation_errors(); ?></div>
                        <?php
                        echo form_open('signup/new_user_registration'); ?>
                        <label>Create Username : </label>
                        <br/>
                        <input type="text" name="username" class="form-control"/>
                        <div class='error_msg'>
                            <?php if (isset($message_display)) {
                                echo $message_display;
                            } ?>
                        </div>
                        <br/>
                        <label>Email :</label>
                        <br/>
                        <input type="email" name="email_value" class="form-control"/>
                        <br/>
                        <br/>
                        <label>Password :</label>
                        <br/>
                        <input type="password" name="password" class="form-control">
                        <br/>
                        <br/>
                        <button class="btn btn-default" type="submit" name="submit">Signup</button>
                        <a href="<?php echo base_url() ?>main/login">For Login Click Here</a>
                        <?php
                        echo form_close();
                        ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <!-- Button trigger modal -->

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">

                        <div id="Translation">
                            <h3>ท่อนหนึ่งของ Lorem Ipsum ที่ใช้กันเป็นมาตรฐานมาตั้งแต่ศตวรรษที่ 16</h3>

                            <p>&quot;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&quot;</p>

                            <h3>ตอนที่ 1.10.32 จาก &quot;de Finibus Bonorum et Malorum&quot; เขียนโดย ซิเซโร เมื่อ 45 ปี ก่อนคริตศตวรรษ</h3>

                            <p>&quot;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&quot;</p>

                            <h3>ฉบับแปลเมื่อปี ค.ศ. 1914 โดย เอช แร็คแคม</h3>

                            <p>&quot;But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?&quot;</p>

                            <h3>ตอนที่ 1.10.33 จาก &quot;de Finibus Bonorum et Malorum&quot; เขียนโดย ซิเซโร เมื่อ 45 ปี ก่อนคริตศตวรรษ</h3>

                            <p>&quot;At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.&quot;</p>

                            <h3>ฉบับแปลเมื่อปี ค.ศ. 1914 โดย เอช แร็คแคม</h3>

                            <p>&quot;On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.&quot;</p>
                            <form action="">
                                <input type="radio" name="accept" value="accept">Accept &nbsp; <input type="radio" name="accept" value="decline">Decline<br><br><br>

                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Next</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

    </div>
</div>

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>-->
<!-- jQuery -->
<script type="text/javascript" src="<?php echo base_url(); ?>vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap C1ore JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/metisMenu/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/raphael/raphael.min.js"></script>


<!-- Custom Theme JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>js/sb-admin-2.js"></script>


<!--Table javascript -->
<script type="text/javascript" src="<?php echo base_url(); ?>vendor/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript"
        src="<?php echo base_url(); ?>vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script type="text/javascript"
        src="<?php echo base_url(); ?>vendor/datatables-responsive/dataTables.responsive.js"></script>

<script type="text/javascript">
    $(window).on('load',function () {
        $('#myModal').modal('show');
    });

</script>

</body>

</html>
