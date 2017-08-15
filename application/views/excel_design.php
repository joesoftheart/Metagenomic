
<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);

} 
else {
    header("location: main/login");
} 

?>


<h2>Create file design</h2>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!--<form>-->



<!-- <button type="button" id="btnAdd">Add new Rows </button>
<button type="button" id="btnRemoveRow">Remove Rows</button> -->

<!-- <button type="button" id="btnAddCol">Add new Column</button>
<button type="button" id="btnRemoveCol">Remove Column</button> -->

<button onclick="getExcel()">create file</button>
   
<br/><br/>


 <form name="myform" id="myform" method="post" >
    <table id="blacklistgrid">
        <!-- <tr id="Row1">
            <td><input type="text" name="head[]" placeholder="Header 1"  /></td>
            <td><input type="text" name="head[]" placeholder="Header 2" /></td>
          
        </tr> -->
       <?php foreach ($sample_name as $key => $value) { ?>
        <tr id="Row2">
            <td>
                <input type="text" name="col[]"  value="<?=$value?>" />
            </td>
            <td>
                <input type="text" name="col[]" />
            </td>
        </tr>
       <?php  } ?>

    </table>
    
    
</form>


  
<!--</form>-->



<script>

$(document).ready(function () {

     $('#btnAdd').click(function () {
         var count = 1,
             first_row = $('#Row2');
         while (count-- > 0) first_row.clone().appendTo('#blacklistgrid');
     });

     
     // var myform = $('#myform'),
     //     col_num = 4;

     // $('#btnAddCol').click(function () {
     //     myform.find('tr').each(function(){
     //       var trow = $(this);
     //         if(trow.index() === 0){
     //             trow.append('<td><input type="text" name="head[]" placeholder=Header'+col_num+'></td>');
     //         }else{
     //            trow.append('<td><input type="text" name="col[]"/></td>');
     //         }
            
     //     });
     //     col_num += 1;
     // });
     
     $('#btnRemoveRow').click(function () {
       var row_count = $('#blacklistgrid  #Row2').length;
       if(row_count > 1){
           $('#Row2').remove();
       }

     });

     
     // $('#btnRemoveCol').click(function () {

     //  	  var column_count = $('#blacklistgrid  #Row1 td').length;
     //  	  if (column_count > 2){
     //          $('table tr').find('td:eq(-1),th:eq(-1)').remove();

     //          col_num -= 1;
     //  	  }
     // });
     
 
 });



function getExcel(){
    var user = "<?php echo $username ?>";
    var project = "<?php echo $current_project ?>";
    var excel = "",
        check_val = "";
    $("#blacklistgrid").find("tr").each(function () {
        var sep = "";
        $(this).find("input").each(function () {
            excel += sep + $(this).val();
            check_val += $(this).val();
            sep = "\t";
        });
        excel += "\n";
    });
   
   if(check_val != ""){
     $.ajax({
            type:"post",
            datatype:"json",
            url:"<?php echo base_url('Run_advance/write_design');?>?user="+user+"&project_id="+project,
            data:{data_excel: excel},
            success:function(data){
                        var ex_u = $.parseJSON(data);
                        alert("Create design "+ex_u+" success");
                        
             },error:function(e){
                     console.log(e.message);
                   }
   
    });

   }else{

     alert("Please insert value");

   }
    

}


</script>