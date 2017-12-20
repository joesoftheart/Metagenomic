<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>


<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">SRA</li>
            </ol>
            <h3 class="page-header">SRA </h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>


    <?php
    $data_line_one = array();
    $data_pull = array();
    $data_line_last = array();

    $count = 0;

    $myfile = fopen($path, 'r') or die ("Unable to open file");
    while (($lines = fgets($myfile)) !== false) {

        $count++;
        if ($count <= 7) {

            array_push($data_line_one, $lines);

        } else if ($count >= 8 && $count <= 10) {

            $d_push = explode("\t", $lines);
            array_push($data_pull, $d_push);

        } else {

            $d_last = explode("\t", $lines);
            array_push($data_line_last, $d_last[0]);

        }
    }

    fclose($myfile);


    $count_data_pull = 0;
    foreach ($data_pull as $num) {

        $count_data_pull += count($num);
    }

    $count_loop = ($count_data_pull / 3);

    ?>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">


            <div class="panel panel-default">
                <div class="panel-heading">
                    Input Data stability.tsv
                </div><!--class="panel-heading"-->

                <div class="panel-body">
                    <div class="table-responsive">

                        <form id="mystability" method="post">

                            <table class="table table-bordered table-hover" id="table_stability">

                                <?php for ($i = 0; $i < sizeof($data_line_one); $i++) { ?>

                                    <tr class="info">
                                        <td colspan=<?= $count_data_pull; ?>>

                                            <?php
                                            if ($i == 2) {
                                                echo "<font color='red'>" . $data_line_one[$i] . "</font>";
                                            } else {

                                                echo $data_line_one[$i];
                                            }
                                            ?>


                                        </td>

                                    </tr>
                                <?php } ?>

                                <tr>
                                    <?php for ($i = 0; $i < $count_loop; $i++) { ?>

                                        <td> <?= $data_pull[0][$i] ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <?php for ($i = 0; $i < $count_loop; $i++) { ?>

                                        <td> <?= $data_pull[1][$i] ?></td>
                                    <?php } ?>
                                </tr>
                                <tr class="danger">
                                    <?php for ($i = 0; $i < $count_loop; $i++) { ?>

                                        <td> <?= $data_pull[2][$i] ?></td>
                                    <?php } ?>
                                </tr>

                                <?php for ($i = 0; $i < sizeof($data_line_last); $i++) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?= $data_line_last[$i]; ?>" readonly>
                                        </td>

                                        <?php for ($k = 0; $k < $count_loop - 1; $k++) { ?>

                                            <td><input type="text"/></td>
                                        <?php } ?>
                                    </tr>


                                <?php } ?>

                            </table>

                        </form>


                    </div><!--class="table-responsive"-->
                </div><!--class="panel-body"-->
            </div><!--panel panel-default-->

            <div class="col-lg-11 uk-margin">
                <button class="btn btn-primary" onclick="getStability();">Submit File</button>
            </div>

        </div><!--class="col-lg-12"-->
    </div><!--class="row" -->

</div><!--id="page-wrapper"-->


<?php echo form_open_multipart('Run_sra/run_script_sra2/' . $id_project, array('id' => 'myform')); ?>

<?php echo form_close(); ?>


<script>


    function getStability() {

        var excel = "",
            res = new Array(),
            miss = "missing";
        var count = true;

        $("#table_stability").find("tr").each(function () {

            var sep = "";
            var inData = "";

            $(this).find("input").each(function () {

                inData = $(this).val();

                if (inData == "") {

                    sep += miss + "\t";
                } else {
                    sep += inData + "\t";
                }

                res.push(inData);
            });


            excel += sep.trim() + "\n";


        });

        console.log(excel.trim());

        for (var i = 0; i < res.length; i++) {
            if (res[i] == "") {
                count = false;

            }
        }

        if (count == false) {

            if (confirm("Do you want insert 'missing' into empty field") == true) {
                getInnew(excel);
            }

        } else {

            getInnew(excel);

        }
    }


    function getInnew(data) {
        var excel = data.trim();
        var project = "<?php echo $current_project ?>";
        $.ajax({
            cache: false,
            type: "post",
            datatype: "json",
            url: "<?php echo site_url('Run_sra/write_stabilitynew/"+project+"');?>",
            data: {innew: excel},
            success: function (data) {
                // alert("Insert value Success");
                document.getElementById('myform').submit();
            }, error: function (e) {
                console.log(e.message);
            }

        });
    }


</script>





















  