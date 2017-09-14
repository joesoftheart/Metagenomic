</div>
<!-- /#wrapper -->

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


<!-- Uikit javascript -->
<script type="text/javascript" src="<?php echo base_url(); ?>js/uikit.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/uikit-icons.min.js"></script>

<!-- material design -->
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--js/ripples.min.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--js/material.min.js"></script>-->
<!--<script type="text/javascript" src="https://fezvrasta.github.io/snackbarjs/dist/snackbar.min.js"></script>-->

</body>

</html>
<script>
    $(document).ready(function () {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#dataTables-example2').DataTable({
            responsive: true
        });
    });
</script>

<script>
    var sessionTimer = setInterval(function(){
        $.ajax({
            url: '/ajax/sessiontimeout',
            beforeSend: function(){},
            success: function(data){
                console.info(data);
            }
        });
    },5000);
</script>
