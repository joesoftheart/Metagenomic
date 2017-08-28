<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
} else {
    header("location: main/login");
} ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages: ["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Language', 'Speakers (in millions)'], ['Other', 13], ['Other user', <?php echo $rs_do; ?>], ['Company', 1.4], ['Dogri', 2.3], ['Your storages', <?php echo $rs_dm;?>], ['Free storagse', 300]]);

        var options = {
            title: 'Storages',
            legend: 'none',
            pieSliceText: 'label',
            slices: {
                4: {offset: 0.2},
                12: {offset: 0.3},
                14: {offset: 0.4},
                15: {offset: 0.5},
            },
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
</script>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php echo "User :" . $username . "   Email :" . $email . "   ID :" . $id; ?>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">Used resource</li>

            </ol>


        </div>
    </div>
    <div class="row">

        <div class="col-lg-6">
            <!-- /.panel-heading -->
            <h3 class="page-header">Pie chart</h3>
            <div class="panel-body">
                <div id="piechart" style="width: 700px; height: 700px;"></div>
            </div>
            <!-- /.panel-body -->
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Progress bar
                </div>
                <div class="panel-body">

                    <p>Server CPU Usage :<span class="description" id="show_cpu"></span>%</p>
                    <p>Server Memory Usage :<span class="description" id="show_ram"></span>%</p>
                    <p>
                        <strong>Task 2</strong>
                        <span class="pull-right text-muted">20% Complete</span>
                    </p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20"
                             aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                            <span class="sr-only">20% Complete</span>
                        </div>
                    </div>
                    <p>
                        <strong>Task 2</strong>
                        <span class="pull-right text-muted">20% Complete</span>
                    </p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="20"
                             aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                            <span class="sr-only">20% Complete</span>
                        </div>
                    </div>
                    <p>
                        <strong>Task 2</strong>
                        <span class="pull-right text-muted">20% Complete</span>
                    </p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="20"
                             aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                            <span class="sr-only">20% Complete</span>
                        </div>
                    </div>
                    <p>
                        <strong>Task 2</strong>
                        <span class="pull-right text-muted">20% Complete</span>
                    </p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="20"
                             aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                            <span class="sr-only">20% Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    window.setInterval(function () {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url();?>used_resource/get_server_cpu_usage",
            cache: false,
            success: function (data) {
                $("#show_cpu").html(data);
            }
        });// you have missed this bracket
        return false;
    }, 3000);
</script>
<script>
    window.setInterval(function () {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url();?>used_resource/get_server_memory_usage",
            cache: false,
            success: function (data) {
                $("#show_ram").html(data);
            }
        });// you have missed this bracket
        return false;
    }, 3000);
</script>