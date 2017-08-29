 <h3> Example file metadata</h3>


 <?php 

  $file = FCPATH."example_file/file.metadata";

  ?>

  <table border="1" width="25%" > 
    

  <?php 

  $myfile = fopen($file,'r') or die ("Unable to open file");
               while(($lines = fgets($myfile)) !== false){
                   $var =  explode("\t", $lines);
                  
  ?>
                  <tr>
                  	<td><?=$var[0]?></td>
                  	<td><?=$var[1]?></td>
                    <td><?=$var[2]?></td>
                  </tr>

 <?php
                 
              }

   fclose($myfile);

 ?>

</table>
