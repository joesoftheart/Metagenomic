<?php  

    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");


    $path = $argv[1];
    $package = $argv[2];
    $path_log = $argv[3];
    $user = $argv[4];

    
    if($path != "" && $package != "" && $path_log != "" && $user != "") {
    	get_mimarkspackage($path,$package,$path_log,$user);
    	
    }else{
       echo "Error Command !!!";

    }


    function get_mimarkspackage($path,$package,$path_log,$user){

        echo "get_mimarkspackage"."\n";

        $jobname = $user."_getmimarkspackage";

    	$make = "get.mimarkspackage(file=stability.files, package=$package, requiredonly=t ,inputdir=$path,outputdir=$path)";
        
        file_put_contents($path.'/sra.batch', $make);

        $cmd = "qsub  -N '$jobname' -o $path_log  -cwd -j y -b y Mothur/mothur $path/sra.batch";


        shell_exec($cmd);

               $check_qstat = "qstat  -j '$jobname' ";
               exec($check_qstat,$output);

               $id_job = "" ; # give job id
               foreach ($output as $key_var => $value ) {

                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }
              }

              $loop = true;
              while ($loop) {
                
                   $check_run = exec("qstat -j $id_job ");

                   if($check_run == false){

                      remove_logfile_mothur($path);
                      break;
                   }
              }
    }



    function remove_logfile_mothur($path){ 
            
            $path_dir = $path;
            if (is_dir($path_dir)) {
                if ($read = opendir($path_dir)){
                      while (($logfile = readdir($read)) !== false) {
                        
                        $allowed =  array('logfile');
                        $ext = pathinfo($logfile, PATHINFO_EXTENSION);

                        if(in_array($ext,$allowed)) {
                           
                            unlink($path_dir.$logfile);
                        }
                      }
     
                   closedir($read);
                }
            }
           echo "remove_logfile_mothur"."\n";
          
           
     }

    




 ?>