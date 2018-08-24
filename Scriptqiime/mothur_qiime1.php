<?php 
     
    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

   
    $user = $argv[1];
    $project = $argv[2];
    $path_in = $argv[3];
    $path_out = $argv[4];
    $GLOBALS['path_log'] = $argv[5];
    $GLOBALS['file_map']  = $argv[6];
    $GLOBALS['permanova']  = $argv[7];
    $GLOBALS['anosim']  = $argv[8]; 
    $GLOBALS['adonis'] = $argv[9];
    $GLOBALS['opt_permanova'] =  $argv[10];
    $GLOBALS['opt_anosim'] =  $argv[11];
    $GLOBALS['opt_adonis'] =  $argv[12];

    $GLOBALS['min'] = null;

    add_qiime_labels($user,$project,$path_in,$path_out);
   

    # use file map.txt
    function add_qiime_labels($user,$project,$path_in,$path_out){

     	echo "add_qiime_labels"."\n";

     	$map = $path_in."map.txt";

     	$jobname = $user."_add_qiime_labels";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/runqiime191_add $path_out $map $path_out";


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
                      pick_open_reference_otus($user,$project,$path_in,$path_out);
                   }
         }   
     } 

     # use file uc_fast_params.txt
     # create folder clustering
     function pick_open_reference_otus($user,$project,$path_in,$path_out){

     	echo "pick_open_reference_otus"."\n";
        
        $combined = $path_out."combined_seqs.fna";
        $clustering = $path_out."clustering";
        $uc_fast_params = $path_in."uc_fast_params.txt";


     	$jobname = $user."_pick_open_reference_otus";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/runqiime191_pick $combined $clustering $uc_fast_params";

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
                      remove_low_confidence_otus($user,$project,$path_in,$path_out);
                   }
         }   
     }


     function remove_low_confidence_otus($user,$project,$path_in,$path_out){


     	echo "remove_low_confidence_otus"."\n";
        
        $cluster_input = $path_out."clustering/otu_table_mc1_w_tax_no_pynast_failures.biom";

        $cluster_out = $path_out."clustering/otu_table_high_conf.biom";


     	$jobname = $user."_remove_low_confidence_otus";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/runqiime191_remove $cluster_input $cluster_out";

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
                      biom_otus_pick($user,$project,$path_in,$path_out);
                   }
         }   
     }


     function biom_otus_pick($user,$project,$path_in,$path_out){
      
      	echo "biom_otus_pick"."\n";
        
        $input_biom = $path_out."clustering/otu_table_mc1_w_tax_no_pynast_failures.biom";

        $output_txt = $path_out."clustering/otu_table_mc1_w_tax_no_pynast_failures.txt";


     	$jobname = $user."_biom_otus_pick";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/biom_otus_pick $input_biom $output_txt";

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
                      biom_core($user,$project,$path_in,$path_out);
                   }
         }   
     }

     function biom_core($user,$project,$path_in,$path_out){

         echo "biom_core"."\n";
        
        $input_biom = $path_out."clustering/otu_table_high_conf.biom";

        $output_txt = $path_out."clustering/otu_table_high_conf_summary.txt";
      
     	$jobname = $user."_biom_core";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/biom_core $input_biom $output_txt";

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
                      minotu_table($user,$project,$path_in,$path_out);
                   }
         }   

     }

     
     function minotu_table($user,$project,$path_in,$path_out){


         $cmd = $path_out.'final_otu_tables';
         if(!file_exists($cmd)){

             mkdir($cmd);
         }
          
         $otu_talbe = $path_out."clustering/otu_table_high_conf_summary.txt";
         $file = file_get_contents($otu_talbe);
         $search_for = 'Min';
         $pattern = preg_quote($search_for, '/');

            $pattern = "/^.*(Min).*\$/m";

                if (preg_match_all($pattern, $file, $matches)) {
                  
                     $value = $matches[0][0];
                     list($mane_min,$val_min) = explode(':', $value);
                     list($int,$double) = explode(".", $val_min);
                     $val_int = str_replace(",","",$int);
                     $val_int = trim($val_int);
                     $GLOBALS['min'] = $val_int;

                     echo $val_int."\n";
                     single_rarefaction($user,$project,$path_in,$path_out,$val_int);

                }
    }

    
    #create folder final_otu_tables
    function single_rarefaction($user,$project,$path_in,$path_out,$val_int){
     
         echo "single_rarefaction"."\n";
         
        $val_min = $val_int; 
        $inputbiom = $path_out."clustering/otu_table_high_conf.biom";
        $outbiom = $path_out."final_otu_tables/otu_table.biom";

     	$jobname = $user."_single_rarefaction";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/single_rarefaction $inputbiom $outbiom $val_min";

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
                      summarize_taxa_through_plots($user,$project,$path_in,$path_out);
                   }
         }   
    }

    
    # use file map.txt 
    # create folder taxa_summary 
    function summarize_taxa_through_plots($user,$project,$path_in,$path_out){

    	echo "summarize_taxa_through_plots"."\n";
        

        $map = $path_in."map.txt";
        $inputbiom = $path_out."final_otu_tables/otu_table.biom";
        $out = $path_out."taxa_summary";

    	$jobname = $user."_summarize_taxa_through_plots";
    	$log = $GLOBALS['path_log'];

    	$cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/summarize_plots $out $inputbiom $map";

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
                      core_diversity_analyses($user,$project,$path_in,$path_out);
                   }
         }   
    }

    # use file map.txt
    # use file alpha_params.txt
    # value min
    function core_diversity_analyses($user,$project,$path_in,$path_out){

    	echo "core_diversity_analyses"."\n";

    	
        $map  = $path_in."map.txt";
    	$cdotu = $path_out."cdotu";
    	$inputbiom = $path_out."final_otu_tables/otu_table.biom";
    	$rep_set = $path_out."clustering/rep_set.tre";
    	$min = $GLOBALS['min'];
        $alpha_params = $path_in."alpha_params.txt";



    	$jobname = $user."_core_diversity_analyses";
    	$log = $GLOBALS['path_log'];

         $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/core_analyses $cdotu $inputbiom $map $rep_set $min $alpha_params";

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
                      alpha_diversity($user,$project,$path_in,$path_out);
                   }
         }   
    }


    # Alpha diversity
    function alpha_diversity($user,$project,$path_in,$path_out){
       
        echo "alpha_diversity"."\n";

        $jobname = $user."_alpha_diversity";
        $log = $GLOBALS['path_log'];

        $file_gun = $path_out."cdotu/table_even".$GLOBALS['min'].".biom.gz";
        shell_exec('/usr/bin/gunzip '.$file_gun);

        $table_even = $path_out."cdotu/table_even".$GLOBALS['min'].".biom";
        $out = $path_out."cdotu/alpha_diversity_from_table_even".$GLOBALS['min'].".txt";
        $rep_set = $path_out."clustering/rep_set.tre";

        $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/alpha_diversity $table_even $out $rep_set";

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
                      make_2d_plots($user,$project,$path_in,$path_out);
                   }
         }   
    }

    # use map.txt
    # Beta diversity
    function make_2d_plots($user,$project,$path_in,$path_out){

    	echo "make_2d_plots"."\n";


    	$unweighted = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_pc.txt";
    	$map = $path_in."map.txt";
    	$out =  $path_out."cdotu/bdiv_even".$GLOBALS['min']."/2d_plots_coordinate/";


    	$jobname = $user."_make_2d_plots";
    	$log = $GLOBALS['path_log'];

    	$cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/make_2d_plots $unweighted $map $out";

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
                      permanova($user,$project,$path_in,$path_out);
                   }
         }   
    }


    #option permanova
    # input weight , group
    function permanova($user,$project,$path_in,$path_out){

        if($GLOBALS['permanova'] != "none"){

            echo "permanova"."\n";
            $weight = $GLOBALS['opt_permanova'];
            $input = null;
            $map = $path_in."map.txt";
            $group = $GLOBALS['permanova'];
            $out = $path_out."permanova".$group;

            # weight , unweight
            if($weight == "weight"){
                $input = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/weighted_unifrac_dm.txt";

            }elseif ($weight == "unweight") {
                $input = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_dm.txt";
            }


            $jobname = $user."_permanova";
            $log = $GLOBALS['path_log'];
            $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/compare_permanova $input $map $group $out";

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
                      adonis($user,$project,$path_in,$path_out);

                   }
            }   
   
        }else{

            adonis($user,$project,$path_in,$path_out);
        }

       
    }



    #option adonis
    # input weight , group
    function adonis($user,$project,$path_in,$path_out){

        if($GLOBALS['adonis'] != "none"){

            echo "adonis"."\n";
            $weight = $GLOBALS['opt_adonis'];
            $input = null;
            $map = $path_in."map.txt";
            $group = $GLOBALS['adonis'];
            $out = $path_out."adonis".$group;

             # weight , unweight
            if($weight == "weight"){
                $input = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/weighted_unifrac_dm.txt";

            }elseif ($weight == "unweight") {
                $input = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_dm.txt";
            }

            $jobname = $user."_adonis";
            $log = $GLOBALS['path_log'];
            $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/compare_adonis $input $map $group $out";

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
                      anosim($user,$project,$path_in,$path_out);
                   }
            }   

        }else{
            
             anosim($user,$project,$path_in,$path_out);
        }
    }

     #option anosim 
     # input weight , group
    function anosim($user,$project,$path_in,$path_out){

        if($GLOBALS['anosim'] != "none"){

            echo "anosim"."\n";

            $weight = $GLOBALS['opt_anosim'];
            $input = null;
            $map = $path_in."map.txt";
            $group = $GLOBALS['anosim'];
            $out = $path_out."anosim".$group;

             # weight , unweight
            if($weight == "weight"){
                $input = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/weighted_unifrac_dm.txt";

            }elseif ($weight == "unweight") {
                $input = $path_out."cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_dm.txt";
            }


             $jobname = $user."_anosim";
             $log = $GLOBALS['path_log'];
             $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/compare_anosim $input $map $group $out";

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
        }else{
            
            break; 
        }
    }



?>