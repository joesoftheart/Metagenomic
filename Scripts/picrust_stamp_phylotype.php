<?php

   include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

# Run separately 

function make_biom($user,$project,$path_in,$path_out){
   

   #  silva , rdp => $label = 1
   #  greengene   => $label = 2


   $label = '2';

   echo "make_biom"."\n";
   $jobname = $user."_make_biom";
   
   $make = "make.biom(shared=final.tx.shared, label=".$label.",constaxonomy=final.tx.".$label.".cons.taxonomy, reftaxonomy=gg_13_8_99.gg.tax, picrust=99_otu_map.txt,inputdir=$path_in,outputdir=$path_out)";

   file_put_contents($path_in.'/advance.batch', $make);

      $log = $GLOBALS['path_log'];
      $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";
              
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

                      convert_biom($user,$project,$path_in,$path_out);
                      break;   
                   }
              }   
   
   

}

function convert_biom($user,$project,$path_in,$path_out){
   

     #  silva , rdp => $label = 1
     #  greengene   => $label = 2
    
    $label = '2';

    echo "convert_biom"."\n";

    $jobname = $user."_convert_biom";
    $log = $GLOBALS['path_log'];

    $path_input_biom  = $path_out."final.tx.".$label.".biom";
    $path_output_biom = $path_out."normalized_otus.".$label.".biom";
    $path_output_txt  = $path_out."final.tx.".$label.".txt";
   
    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y picrust-1.1.1/scripts/convert_biom $path_input_biom $path_output_biom $path_output_txt";


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

                      phylotype_picrust($user,$project,$path_in,$path_out);
                      break;  
                   }
          }   


}

function phylotype_picrust($user,$project,$path_in,$path_out){

     #  silva , rdp => $label = 1
     #  greengene   => $label = 2

    $label = '2';

    echo "phylotype_picrust"."\n";

    $jobname = $user."_phylotype_picrust";
    $log = $GLOBALS['path_log'];

    $path_input_biom = $path_out."final.tx.".$label.".biom";
    $path_output_biom = $path_out."final.biom";


    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust_norm $path_input_biom $path_output_biom ";

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

                      remove_logfile_mothur2($path_out);
                      remove_file_tree_sum($path_in);

                      //change_name($user,$project,$path_in,$path_out);
                      phylotype_picrust2($user,$project,$path_in,$path_out);
                      break;  
                   }
          }   

}




function phylotype_picrust2($user,$project,$path_in,$path_out){
    


     #  silva , rdp => $label = 1
     #  greengene   => $label = 2
    
   $label = '2';

    echo "phylotype_picrust2"."\n";

    $jobname = $user."_phylotype_picrust2";
    $log = $GLOBALS['path_log'];

  $normalized_otus = $path_out."normalized_otus.".$label.".biom";
  $metagenome_predictions = $path_out."metagenome_predictions.".$label.".biom";


    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust $normalized_otus $metagenome_predictions";

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
                      phylotype_picrust3($user,$project,$path_in,$path_out);
                      break;  
                   }
          }   


}

# Run separately  


function phylotype_picrust3($user,$project,$path_in,$path_out){

    
     #  silva , rdp => $label = 1
     #  greengene   => $label = 2


    $label = '2';

    echo "phylotype_picrust3"."\n";

    $jobname = $user."_phylotype_picrust3";
    $log = $GLOBALS['path_log'];

  

  $metagenome_predictions = $path_out."metagenome_predictions.".$label.".biom";

   # $L = Please select level of KEGG pathway  level 1,2 or 3
   $L = "L2";
  
  $predicted_metagenomes = $path_out."predicted_metagenomes.".$label.".".$L.".biom";
    

    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust1 $metagenome_predictions $label $predicted_metagenomes ";

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
                      biom_to_stamp($user,$project,$path_in,$path_out);
                      break;  
                   }
          }   

}

function biom_to_stamp($user,$project,$path_in,$path_out){

     #  silva , rdp => $label = 1
     #  greengene   => $label = 2
    
   $label = '2';

    echo "biom_to_stamp"."\n";

    $jobname = $user."_biom_to_stamp";
    $log = $GLOBALS['path_log'];

   # $L = Please select level of KEGG pathway  level 1,2 or 3
   $L = "L2";

   $predicted_metagenomes = $path_out."predicted_metagenomes.".$label.".".$L.".biom";

   $pathways = $path_out."pathways".$label.$L.".spf";
    

    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubBiomtoStamp $predicted_metagenomes $pathways";

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
                      remove_float($user,$project,$path_in,$path_out);
                      break;  
                   }
          }   

}



function remove_float($user,$project,$path_in,$path_out){
    
    echo "remove_float"."\n";

    $jobname = $user."_remove_float";
    $log = $GLOBALS['path_log'];

    
     #  silva , rdp => $label = 1
     #  greengene   => $label = 2

     $label = '2';

     # $L = Please select level of KEGG pathway  level 1,2 or 3
     $L = "L2";

    $pathways = $path_out."pathways".$label.$L.".spf";

    $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y  /usr/bin/php -f R_Script/replace_string.php $pathways";
   
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            stamp($user,$project,$path_in,$path_out);
            break;
        }
    }
}

function stamp($user,$project,$path_in,$path_out){

   
     #  silva , rdp => $label = 1
     #  greengene   => $label = 2

     $label = '2';

     # $L = Please select level of KEGG pathway  level 1,2 or 3
     $L = "L2";

    echo "stamp"."\n";

    $jobname = $user."_stamp";
    $log = $GLOBALS['path_log'];

    $pathways = "../".$path_out."pathways".$label.$L.".spf";
    $myResultsPathway = "../".$path_out."myResultsPathway".$L.".tsv";
    $sample1 = "S1_1_16s_S1";
    $sample2 = "S2_1_16s_S3";
    $statistical_test = "G-test (w/ Yates' correction)";
    $ci_method = "DP: Newcombe-Wilson";
    $p_value = "0.05";
    
    

    $function = 'python  STAMP-1.8/commandLine.py --file '.$pathways.' --sample1 '.$sample1.' --sample2 '.$sample2.' --statTest "'.$statistical_test.'" --CI "'.$ci_method.'" -p '.$p_value.' --coverage 0.95 --outputTable '.$myResultsPathway.'';

     file_put_contents($path_in.'qsubStamp.sh', $function);
     chmod($path_in.'qsubStamp.sh',0775);
     $getPath = "../".$path_in."qsubStamp.sh";

     $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y STAMP-1.8/$getPath";

    //$cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y STAMP-1.8/qsubStamp.sh $pathways $sample1 $sample2 $myResultsPathway  $p_value";
    
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

                      break;  
                   }
          }   


}


?>