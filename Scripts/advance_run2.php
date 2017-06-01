<?php  

     include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");


         $user = $argv[1];
         $project = $argv[2];

         $path_in = $argv[3];
         $path_out = $argv[4];

         $size = $argv[5];



  function sub_sample(){
  	   $cmd = "sub.sample(shared=final.opti_mcc.shared, size=10000,inputdir=$path_in,outputdir=$path_in)";
  }

   
?>