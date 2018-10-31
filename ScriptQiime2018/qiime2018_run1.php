<?php 

    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");
 
    $user = $argv[1];
    $project = $argv[2];
    $path_in = $argv[3];
    $path_out = $argv[4];
    $GLOBALS['path_log'] = $argv[5];
    $GLOBALS['project_platform_type'] = $argv[6];
    $GLOBALS['pick_otus'] = $argv[7];
    $GLOBALS['option_gg'] = $argv[8];
    $GLOBALS['precent_identity'] = $argv[9];
    $GLOBALS['reference_sequences'] = $argv[10];
    $GLOBALS['taxonomic_classifier'] = $argv[11];
    $path_fileprocess = $argv[12];


     
     
     copyTofileprocess($user,$project,$path_in,$path_out,$path_fileprocess);

     function copyTofileprocess($user,$project,$path_in,$path_out,$path_fileprocess){

        $search_fastq = glob($path_in."*.fasta");
        foreach ($search_fastq as $key => $value){
          $var_name =  basename($value);
          copy($value, $path_fileprocess.$var_name); 
       } 

       mergeFastaMakeInputQiime($user,$project,$path_in,$path_out,$path_fileprocess);

     }
    

    #1
    function mergeFastaMakeInputQiime($user,$project,$path_in,$path_out,$path_fileprocess){

      echo "mergeFasta"."\n";

      $jobname = $user."_mergeFasta";
      $log = $GLOBALS['path_log'];

      $outputQiime_fasta = $path_out."inputQiime.fasta";

      $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/1mergefasta $path_fileprocess $outputQiime_fasta";

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

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      $loop = false;
                      import_merged($user,$project,$path_in,$path_out);
                   }
         }   
    }

    #2
    function import_merged($user,$project,$path_in,$path_out){

       echo "import_merged"."\n";

       $jobname = $user."_import_merged";
       $log = $GLOBALS['path_log'];

       $inputfile = $path_out."inputQiime.fasta";
       $outputfile = $path_out."1_input_seq.qza";

    
       $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/2import_merged $inputfile $outputfile";

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
              $check_run = exec("qstat -j $id_job");
              if($check_run == false){
                      $loop = false;
                      remove_replication($user,$project,$path_in,$path_out);
                       
              }
         }   
    }

    #3
    function remove_replication($user,$project,$path_in,$path_out){

       echo "remove_replication"."\n";

       $jobname = $user."_remove_replication";
       $log = $GLOBALS['path_log'];

       $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/3remove_replication $path_in $path_out";

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
              $check_run = exec("qstat -j $id_job");
              if($check_run == false){
                      $loop = false;
                      clustering($user,$project,$path_in,$path_out);
              }
         }   
    }

    #4
    function clustering($user,$project,$path_in,$path_out){

       echo "clustering"."\n";


        $db_reference = $path_in.$GLOBALS['reference_sequences'];

        $precent_string =  $GLOBALS['precent_identity'];
        $precent_int  =  (int)$precent_string;
        $precent_identity = $precent_int/100;


        $jobname = $user."_clustering";
        $log = $GLOBALS['path_log'];
        $cmd = "";

       //Denovoclustering
       if($GLOBALS['pick_otus'] == "Denovoclustering"){
           $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/4_1clustering_denovo $path_in $path_out $precent_identity";

       }
       //CloseReference
       elseif ($GLOBALS['pick_otus'] == "CloseReference") {

           $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/4_2clustering_close $path_in $db_reference $path_out $precent_identity";

        }
       //OpenReference
       elseif ($GLOBALS['pick_otus'] == "OpenReference") {

           $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/4_3clustering_open $path_in $db_reference $path_out $precent_identity";

       }

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
              $check_run = exec("qstat -j $id_job");
              if($check_run == false){
                      $loop = false;
                      chimera_checking($user,$project,$path_in,$path_out);
                       
              }
         }   
    }


    #5
    function chimera_checking($user,$project,$path_in,$path_out){

       echo "chimera_checking"."\n";


        $jobname = $user."_chimera_checking";
        $log = $GLOBALS['path_log'];
       

         $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/5chimera_checking $path_out";

      

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
              $check_run = exec("qstat -j $id_job");
              if($check_run == false){
                      $loop = false;
                       
              }
         }   
    }
  
  

?>