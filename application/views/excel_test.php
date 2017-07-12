<h1>Excel Test</h1>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!--<form>-->

<button type="button" id="btnAdd">Add new Rows </button>
<button type="button" id="btnRemoveRow">Remove Rows</button>

<button type="button" id="btnAddCol">Add new Column</button>
<button type="button" id="btnRemoveCol">Remove Column</button>

<br/><br/>
<button onclick="getExcel()">create file</button>
   
<br/><br/>


 <form name="myform" id="myform" method="post" >
    <table id="blacklistgrid">
        <tr id="Row1">
            <td><input type="text" name="head[]" placeholder="Header 1"  /></td>
            <td><input type="text" name="head[]" placeholder="Header 2" /></td>
            <td><input type="text" name="head[]" placeholder="Header 3" /></td>
            
        </tr>

        <tr id="Row2">
            <td>
                <input type="text" name="col[]" />
            </td>
            <td>
                <input type="text" name="col[]" />
            </td>
            <td>
                <input type="text" name="col[]" />
            </td>
          
        </tr>
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

     
     var myform = $('#myform'),
         col_num = 4;

     $('#btnAddCol').click(function () {
         myform.find('tr').each(function(){
           var trow = $(this);
             if(trow.index() === 0){
                 trow.append('<td><input type="text" name="head[]" placeholder=Header'+col_num+'></td>');
             }else{
                trow.append('<td><input type="text" name="col[]"/></td>');
             }
            
         });
         col_num += 1;
     });
     
     $('#btnRemoveRow').click(function () {
       var row_count = $('#blacklistgrid  #Row2').length;
       if(row_count > 1){
           $('#Row2').remove();
       }

     });

     
     $('#btnRemoveCol').click(function () {

      	  var column_count = $('#blacklistgrid  #Row2 td').length;
      	  if (column_count > 1){
              $('table tr').find('td:eq(-1),th:eq(-1)').remove();

              col_num -= 1;
      	  }
     });
     
 
 });



function getExcel(){

    var excel = "";
    $("#blacklistgrid").find("tr").each(function () {
        var sep = "";
        $(this).find("input").each(function () {
            excel += sep + $(this).val();
            sep = "\t";
        });
        excel += "\n";
    });

    $.ajax({
            type:"post",
            datatype:"json",
            url:"<?php echo base_url('Run_advance/write_excel'); ?>",
            data:{data_excel: excel},
            success:function(data){
                        var ex = $.parseJSON(data);
                        //alert(ex);
                        
             },error:function(e){
                     console.log(e.message);
                   }
   
    });

}


</script>