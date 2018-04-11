<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);

} else {
    header("location: main/login");
}

?>

<script src="<?php echo base_url('js/jquery-3.2.1.js'); ?>"></script>

<!-- Bootstrap Core CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/bootstrap/css/bootstrap.css">


<!-- Custom Fonts -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>vendor/font-awesome/css/font-awesome.min.css">

<!-- Uikit Design -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/uikit.css">

<!-- Bootstrap C1ore JavaScript -->
<script type='text/javascript' src="<?php echo base_url(); ?>vendor/bootstrap/js/bootstrap.min.js"></script>


<!-- <button type="button" id="btnAdd">Add new Rows </button>
<button type="button" id="btnRemoveRow">Remove Rows</button> -->

<nav class="navbar navbar-default navbar-static-top " role="navigation" style="margin-bottom: 0">
    <div id="wrapper">

        <!-- Navigation -->

        <div class="navbar-header">

            <label class="navbar-brand"><i class="fa fa-codepen fa-1x"></i> Amplicon Metagenomic</label>
        </div>
        <!-- /.navbar-header -->

    </div>
</nav>
<div class="col-lg-12 uk-margin"></div>



    <div class="col-lg-12">
        <h3>Create file map</h3>
    </div>

    <div class="col-lg-12">
        <button class="btn btn-info" data-toggle="modal" data-target="#myModal"> View Example</button>

    </div>

    <div class="col-lg-12 uk-margin"></div>
    <div class="col-lg-12">
        <button class="btn btn-default" id="btnAddCol">Add new Column</button>
        <button class="btn btn-default" id="btnRemoveCol">Remove Column</button>
    </div>

    <div class="col-lg-12 uk-margin"></div>

    <!--<form>-->
    <div class="col-lg-12">

        <form name="myform" id="myform" method="post">
            <table id="blacklistgrid">
                <tr id="Row1">
                <td><input type="text" value="#SampleID" readonly="readonly"/></td>
                <td><input type="text" value="groupA" readonly="readonly"/></td>
                <td><input type="text" value="groupB" readonly="readonly"/></td>
                <td><input type="text" value="groupC" readonly="readonly"/></td>
                </tr>

               

                    <tr id="Row2">
                        <td>
                            <input type="text" value="test" readonly="readonly"/>
                        </td>
                        <td>
                            <input type="text" value="" />
                        </td>
                        <td>
                            <input type="text" value="" />
                        </td>
                        <td>
                            <input type="text" value="" />
                        </td>

                    </tr>

             

            </table>

        </form>
        <!--</form>-->
        <button class="btn btn-success" onclick="getExcel()">create file</button>

    </div>


<div class="col-lg-12 uk-margin"></div>
<!-- Modal -->
<div class="panel-body">
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel"> Example file map</h4>
                </div>
                <div class="modal-body">

                    <?php $file = FCPATH . "example_file/map.txt"; ?>

                    <table class="table table-bordered">
                        <?php
                        $myfile = fopen($file, 'r') or die ("Unable to open file");
                        while (($lines = fgets($myfile)) !== false) {
                            $var = explode("\t", $lines);
                            ?>
                            <tr>
                                <td><?= $var[0] ?></td>
                                <td><?= $var[5] ?></td>
                                <td><?= $var[6] ?></td>
                                <td><?= $var[7] ?></td>
                            </tr>


                        <?php }
                        fclose($myfile); ?>

                    </table>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-dismiss="modal">OK</button>

                </div>
            </div> <!-- /.modal-content -->
        </div> <!-- /.modal-dialog -->
    </div>
</div><!-- End Modal -->


<script>
    $(document).ready(function () {


        // $('#btnAdd').click(function () {
        //     var count = 1,
        //         first_row = $('#Row2');
        //     while (count-- > 0) first_row.clone().appendTo('#blacklistgrid');
        // });


        // $('#btnRemoveRow').click(function () {
        //   var row_count = $('#blacklistgrid  #Row2').length;
        //   if(row_count > 1){
        //       $('#Row2').remove();
        //   }

        // });


        var myform = $('#myform');
        var col_num = 3;
        var alphas = '';

        $('#btnAddCol').click(function () {

            myform.find('tr').each(function () {
                var trow = $(this);
                alphas = String.fromCharCode(col_num + 65);
                if (trow.index() === 0) {
                    trow.append('<td><input type="text" readonly="readonly" value=group'+alphas+' /></td>');
                } else {
                    trow.append('<td><input type="text" /></td>');
                }

            });
            col_num += 1;
        });


        $('#btnRemoveCol').click(function () {

            var column_count = $('#blacklistgrid #Row1 td').length;
            if (column_count > 1) {
                $('table tr').find('td:eq(-1),th:eq(-1)').remove();

                col_num -= 1;
            }
        });


    });



    function getExcel() {

        var user = "<?php echo $username ?>";
        var project = "<?php echo $current_project ?>";
        var excel = "",
            check_val = "";
       
        $("#blacklistgrid").find("tr").each(function () {
            var sep = "";
            $(this).find("input").each(function () {
                excel += sep + $(this).val();
                check_val += $(this).val() + sep;
                sep = "\t";
            });
            excel += "\n";
        });
        //console.log(excel);
        var count = true;
        var res = check_val.split("\t");
        for (var i = 0; i < res.length - 1; i++) {
            if (res[i] == "") {
                count = false;
                //console.log("data: null");
            } else {
                //console.log("data :"+i+" "+res[i]);
            }
        }
        if (count == false) {
            alert("Please insert value");
        } else {


            alert("ok");
            // $.ajax({
            //     type: "post",
            //     datatype: "json",
            //     url: "<?php echo base_url('Run_mothur_qiime/addGroup');?>/" + user + "/" + project,
            //     data: {data_excel: excel},
            //     success: function (data) {
            //         var user_file = $.parseJSON(data);
            //         alert("Create map " + user_file + " success");
            //         //console.log(data);
            //     }, error: function (e) {
            //         console.log(e.message);
            //     }

            // });
        }
    }
</script>