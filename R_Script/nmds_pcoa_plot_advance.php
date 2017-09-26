<?php  

 $data = $argv[1];

 $value_data = explode(",",$data);

 for ($i =0 ; $i < sizeof($value_data);$i++) {

   

      $value_data2 = explode("-",$value_data[$i]);

       $file  =  $value_data2[0];
       $image =  $value_data2[1];

      get_file_image($file,$image);

 }



 function get_file_image($get_file,$get_img) {

      $cmd = "/usr/bin/Rscript  R_Script/NMD_graph.R $get_file $get_img";
      exec($cmd);


 }



 ?>