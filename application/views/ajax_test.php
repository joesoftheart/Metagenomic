
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-6">
            <input type="text" name="text" value="text" id="text">
        <button id="bt" class="bt" name="bt">Get External Content</button>
        </div>

        <div class="col-lg-6"  >
            <textarea id="show" style="height: 300px; width: 300px;"></textarea>

        </div>
    </div>
</div>







<script>
    $(document).ready(function(){

        $("#bt").click(function()
        {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>ajax_test/call_data",
                data: {text: $("#text").val()},
                dataType: "text",
                cache:false,
                success:
                    function(data){
                        $("#show").html(data);
                    }
            });// you have missed this bracket
            return false;
        });
    });



</script>