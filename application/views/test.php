<div id="page-wrapper">


            <script>
                $(document).ready(function(){
                    $("#search-box").keyup(function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url(); ?>meta_test/search",
                            data:'search='+$(this).val(),
                            beforeSend: function(){
                            },
                            success: function(data){
                                $("#suggesstion-box").show();
                                $("#suggesstion-box").html(data);
                                $("#search-box").css("background","#FFF");
                            }
                        });
                    });
                });


            </script>

    <div class="frmSearch">
        <input type="text" id="search-box" placeholder="Country Name" />
        <div id="suggesstion-box"></div>
    </div>

</div>