<?php 
     
    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

   
    $user = $argv[1];
    $project = $argv[2];
    $path_in = $argv[3];
    $path_out = $argv[4];
    $GLOBALS['path_log'] = $argv[5];

    $GLOBALS['core_group'] = $argv[6];

    $GLOBALS['beta_diversity_index'] = $argv[7];
    $GLOBALS['beta_diversity_index2'] = $argv[8];
    $GLOBALS['permanova'] = $argv[9];
    $GLOBALS['opt_permanova'] = $argv[10]; 
    $GLOBALS['anosim'] = $argv[11];
    $GLOBALS['opt_anosim'] = $argv[12]; 
    $GLOBALS['adonis'] = $argv[13];
    $GLOBALS['opt_adonis'] = $argv[14];
    $GLOBALS['kegg'] = $argv[15];
    $GLOBALS['sample_comparison'] = $argv[16];
    $GLOBALS['statistical_test']  = $argv[17];
    $GLOBALS['ci_method'] = $argv[18];
    $GLOBALS['p_value'] = $argv[19];
    $GLOBALS['check_options'] = $argv[20];

    $GLOBALS['min'] = null;

    add_qiime_labels($user,$project,$path_in,$path_out);
   
   
    # 1
    # use file map.txt
    function add_qiime_labels($user,$project,$path_in,$path_out){

     	echo "add_qiime_labels"."\n";

     	$map = $path_in."map.txt";

     	$jobname = $user."_add_qiime_labels";
        $log = $GLOBALS['path_log'];

        $option_o = $path_out."Processeddata/";


        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/runqiime191_add $path_out $map $option_o";


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

     # 2
     # use file uc_fast_params.txt
     # create folder clustering
     function pick_open_reference_otus($user,$project,$path_in,$path_out){

     	echo "pick_open_reference_otus"."\n";
        
        $combined = $path_out."Processeddata/combined_seqs.fna";
        $clustering = $path_out."Processeddata/clustering/";
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

     # 3
     function remove_low_confidence_otus($user,$project,$path_in,$path_out){


     	echo "remove_low_confidence_otus"."\n";
        
        $cluster_input = $path_out."Processeddata/clustering/otu_table_mc1_w_tax_no_pynast_failures.biom";

        $cluster_out = $path_out."Processeddata/clustering/otu_table_high_conf.biom";


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


     # 4
     function biom_otus_pick($user,$project,$path_in,$path_out){
      
      	echo "biom_otus_pick"."\n";
        
        $input_biom = $path_out."Processeddata/clustering/otu_table_mc1_w_tax_no_pynast_failures.biom";

        $output_txt = $path_out."Processeddata/clustering/otu_table_mc1_w_tax_no_pynast_failures.txt";


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

     # 5
     function biom_core($user,$project,$path_in,$path_out){

         echo "biom_core"."\n";
        
        $input_biom = $path_out."Processeddata/clustering/otu_table_high_conf.biom";

        $output_txt = $path_out."Processeddata/clustering/otu_table_high_conf_summary.txt";
      
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


         $cmd = $path_out.'Processeddata/final_otu_tables/';
         if(!file_exists($cmd)){

             mkdir($cmd);
         }
          
         $otu_talbe = $path_out."Processeddata/clustering/otu_table_high_conf_summary.txt";
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

    # 6
    #create folder final_otu_tables
    function single_rarefaction($user,$project,$path_in,$path_out,$val_int){
     
         echo "single_rarefaction"."\n";
         
        $val_min = $val_int; 
        $inputbiom = $path_out."Processeddata/clustering/otu_table_high_conf.biom";
        $outbiom = $path_out."Processeddata/final_otu_tables/otu_table.biom";

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

    # 7 
    # use file map.txt 
    # create folder taxa_summary 
    function summarize_taxa_through_plots($user,$project,$path_in,$path_out){

    	echo "summarize_taxa_through_plots"."\n";
        

        $map = $path_in."map.txt";
        $inputbiom = $path_out."Processeddata/final_otu_tables/otu_table.biom";
        $out = $path_out."Processeddata/taxa_summary/";

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
     
    # 8
    # use file map.txt
    # use file alpha_params.txt
    # value min
    function core_diversity_analyses($user,$project,$path_in,$path_out){

    	echo "core_diversity_analyses"."\n";

    	
        $map  = $path_in."map.txt";
    	$cdotu = $path_out."Processeddata/cdotu/";
    	$inputbiom = $path_out."Processeddata/final_otu_tables/otu_table.biom";
    	$rep_set = $path_out."Processeddata/clustering/rep_set.tre";
    	$min = $GLOBALS['min'];
        $alpha_params = $path_in."alpha_params.txt";
        $option_c = $GLOBALS['core_group'];



    	$jobname = $user."_core_diversity_analyses";
    	$log = $GLOBALS['path_log'];

         $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/core_analyses $cdotu $inputbiom $map $rep_set $min $option_c $alpha_params";

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

    # 9
    # Alpha diversity
    function alpha_diversity($user,$project,$path_in,$path_out){
       
        echo "alpha_diversity"."\n";

        $jobname = $user."_alpha_diversity";
        $log = $GLOBALS['path_log'];

        $file_gun = $path_out."Processeddata/cdotu/table_even".$GLOBALS['min'].".biom.gz";
        shell_exec('/usr/bin/gunzip '.$file_gun);

        $table_even = $path_out."Processeddata/cdotu/table_even".$GLOBALS['min'].".biom";
        $out = $path_out."Processeddata/cdotu/alpha_diversity_from_table_even".$GLOBALS['min'].".txt";
        $rep_set = $path_out."Processeddata/clustering/rep_set.tre";

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
    
    # 10
    # use map.txt
    # Beta diversity
    function make_2d_plots($user,$project,$path_in,$path_out){

    	echo "make_2d_plots"."\n";


    	$unweighted = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_pc.txt";
    	$map = $path_in."map.txt";
    	$out =  $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/2d_plots_coordinate/";

        $option_b = $GLOBALS['core_group'];


    	$jobname = $user."_make_2d_plots";
    	$log = $GLOBALS['path_log'];

    	$cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y Scriptqiime/make_2d_plots $unweighted $map $out $option_b";

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



    # 11
    #option permanova
    # input weight , group
    function permanova($user,$project,$path_in,$path_out){

        if($GLOBALS['permanova'] != "none" && $GLOBALS['opt_permanova'] != "none"){

            echo "permanova"."\n";
            $weight = $GLOBALS['opt_permanova'];
            $input = null;
            $map = $path_in."map.txt";
            $group = $GLOBALS['permanova'];
            $out = $path_out."Processeddata/permanova".$group;

            # weight , unweight
            if($weight == "weight"){
                $input = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/weighted_unifrac_dm.txt";

            }elseif ($weight == "unweight") {
                $input = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_dm.txt";
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


    # 12
    #option adonis
    # input weight , group
    function adonis($user,$project,$path_in,$path_out){

        if($GLOBALS['adonis'] != "none" && $GLOBALS['opt_adonis'] != "none"){

            echo "adonis"."\n";
            $weight = $GLOBALS['opt_adonis'];
            $input = null;
            $map = $path_in."map.txt";
            $group = $GLOBALS['adonis'];
            $out = $path_out."Processeddata/adonis".$group;

             # weight , unweight
            if($weight == "weight"){
                $input = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/weighted_unifrac_dm.txt";

            }elseif ($weight == "unweight") {
                $input = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_dm.txt";
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

    # 13
    #option anosim 
    # input weight , group
    function anosim($user,$project,$path_in,$path_out){

        if($GLOBALS['anosim'] != "none" && $GLOBALS['opt_anosim'] != "none"){

            echo "anosim"."\n";

            $weight = $GLOBALS['opt_anosim'];
            $input = null;
            $map = $path_in."map.txt";
            $group = $GLOBALS['anosim'];
            $out = $path_out."Processeddata/anosim".$group;

             # weight , unweight
            if($weight == "weight"){
                $input = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/weighted_unifrac_dm.txt";

            }elseif ($weight == "unweight") {
                $input = $path_out."Processeddata/cdotu/bdiv_even".$GLOBALS['min']."/unweighted_unifrac_dm.txt";
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
                         if($GLOBALS['check_options'] == "true"){

                             qiime_To_picrust_1($user,$project,$path_in,$path_out);
                        }else{

                             keep_file_mothurqiime($user,$project,$path_in,$path_out);

                         }  
                   }
            } 


        }else{
            
             if($GLOBALS['check_options'] == "true"){

                 qiime_To_picrust_1($user,$project,$path_in,$path_out);
             }else{

                keep_file_mothurqiime($user,$project,$path_in,$path_out);

             }      
        }
    }


  
     # 14
     function qiime_To_picrust_1($user,$project,$path_in,$path_out){

        echo "qiime_To_picrust_1"."\n";

     
        $option_i = $path_out."Processeddata/final_otu_tables/otu_table.biom";
            
        $option_o = $path_out."Processeddata/final_otu_tables/otu_table_json.biom";
        

        $jobname = $user."_qiime_To_picrust_1";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/qiimeTopicrust1 $option_i $option_o";

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
                      qiime_To_picrust_2($user,$project,$path_in,$path_out);
                   }
         }   
    }


    # 15
    function qiime_To_picrust_2($user,$project,$path_in,$path_out){

        echo "qiime_To_picrust_2"."\n";


        $option_e ="/home/aum/anaconda/lib/python2.7/site-packages/qiime_default_reference/gg_13_8_otus/rep_set/97_otus.fasta";


        $option_i = $path_out."Processeddata/final_otu_tables/otu_table_json.biom";
            
        $option_o = $path_out."Processeddata/final_otu_tables/closed_otus.biom";
       

        $jobname = $user."_qiime_To_picrust_2";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime/qiimeTopicrust2 $option_i $option_o $option_e";

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
                      phylotype_picrust($user,$project,$path_in,$path_out);
                   }
         }   
    }


     # 16
     function phylotype_picrust($user,$project,$path_in,$path_out){


             echo "phylotype_picrust"."\n";

      

             $path_input_biom = $path_out."Processeddata/final_otu_tables/closed_otus.biom";
            
             $path_output_biom = $path_out."Processeddata/final_otu_tables/normalized_otus.biom";
       

             $jobname = $user."_phylotype_picrust";
             $log = $GLOBALS['path_log'];

             $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust_norm $path_input_biom $path_output_biom";

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
                      phylotype_picrust2($user,$project,$path_in,$path_out);
                   }
            }   
    }



    # 17
    function phylotype_picrust2($user,$project,$path_in,$path_out){
   
             echo "phylotype_picrust2"."\n";

             $normalized_otus = $path_out."Processeddata/final_otu_tables/normalized_otus.biom";
            
             $metagenome_predictions = $path_out."Processeddata/final_otu_tables/metagenome_predictions.biom";
        

             $jobname = $user."_phylotype_picrust2";
             $log = $GLOBALS['path_log'];

       
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
                      $loop = false;
                      phylotype_picrust3($user,$project,$path_in,$path_out);
                   }
            }   
    }

   
    # 18
    function phylotype_picrust3($user,$project,$path_in,$path_out){

    
            echo "phylotype_picrust3"."\n";

            # $L = Please select level of KEGG pathway  level 1,2 
             $L = $GLOBALS['kegg'];
             $label = "2";

          
             $metagenome_predictions = $path_out."Processeddata/final_otu_tables/metagenome_predictions.biom";
            
             $predicted_metagenomes = $path_out."Processeddata/final_otu_tables/predicted_metagenomes.".$L.".biom";
       

             $jobname = $user."_phylotype_picrust3";
             $log = $GLOBALS['path_log'];

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
                      $loop = false;
                      biom_to_stamp($user,$project,$path_in,$path_out); 
                   }
            }   
    }

    # 19
    function biom_to_stamp($user,$project,$path_in,$path_out){


        echo "biom_to_stamp"."\n";

        # $L = Please select level of KEGG pathway  level 1,2 or 3
        $L = $GLOBALS['kegg'];

        $predicted_metagenomes = $path_out."Processeddata/final_otu_tables/predicted_metagenomes.".$L.".biom";
            
        $pathways = $path_out."Processeddata/final_otu_tables/pathways".$L.".spf";
       
        $jobname = $user."_biom_to_stamp";
        $log = $GLOBALS['path_log'];

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
                      $loop = false;
                      remove_float($user,$project,$path_in,$path_out);
                   }
          }   
    }

    # 20
    function remove_float($user,$project,$path_in,$path_out){
    
            echo "remove_float"."\n";
          
            # $L = Please select level of KEGG pathway  level 1,2 or 3
            $L = $GLOBALS['kegg'];
                        
            $pathways = $path_out."Processeddata/final_otu_tables/pathways".$L.".spf";

            $jobname = $user."_remove_float";
            $log = $GLOBALS['path_log'];

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
                    $loop = false;
                    stamp($user,$project,$path_in,$path_out);
                }
            }
    }

     # 21
     function stamp($user,$project,$path_in,$path_out){

             echo "stamp"."\n";

             # $L = Please select level of KEGG pathway  level 1,2 or 3
             $L = $GLOBALS['kegg'];
            
             $pathways = "../".$path_out."Processeddata/final_otu_tables/pathways".$L.".spf";

             $myResultsPathway = "../".$path_out."Processeddata/final_otu_tables/myResultsPathway".$L.".tsv";
          

            $jobname = $user."_stamp";
            $log = $GLOBALS['path_log'];

            list($sample1,$sample2)=explode("--vs--",  $GLOBALS['sample_comparison']);
            $sample1 = $sample1;
            $sample2 = $sample2;

             #Selected statistical test : 
            $statistical_test = $GLOBALS['statistical_test'];

            if($GLOBALS['statistical_test'] == "Chi-square"  ){
                $statistical_test = "Chi-square test";
            }elseif ($GLOBALS['statistical_test'] =="Chi-square2" ) {
                $statistical_test = "Chi-square test (w/ Yates' correction)";
            }elseif ($GLOBALS['statistical_test'] == "Difference") {
                $statistical_test = "Difference between proportions";
            }elseif ($GLOBALS['statistical_test'] == "Fisher") {
                $statistical_test = "Fisher's exact test";
            }elseif ($GLOBALS['statistical_test'] == "G‐test2" ) {
                $statistical_test = "G-test (w/ Yates' correction)";
             }

             # CI method : 
            $ci_method = null;

            if($GLOBALS['ci_method'] == "DP1"){
                $ci_method = "DP: Newcombe-Wilson";
            }elseif ($GLOBALS['ci_method'] == "DP2") {
                $ci_method = "DP: Asymptotic";
            }elseif ($GLOBALS['ci_method'] == "DP3") {
                $ci_method = "DP: Asymptotic-CC";
            }elseif ($GLOBALS['ci_method'] == "OR1") {
                $ci_method = "OR: Haldane adjustment";
            }else{
                $ci_method = "RP: Asymptotic";
            }

            $p_value = $GLOBALS['p_value'];
    

             $function = '/home/aum/anaconda/bin/python  STAMP-1.8/commandLine.py --file '.$pathways.' --sample1 '.$sample1.' --sample2 '.$sample2.' --statTest "'.$statistical_test.'" --CI "'.$ci_method.'" -p '.$p_value.' --coverage 0.95 --outputTable '.$myResultsPathway.'';

            file_put_contents($path_in.'qsubStamp.sh', $function);
            chmod($path_in.'qsubStamp.sh',0775);
            $getPath = "../".$path_in."qsubStamp.sh";

            $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y STAMP-1.8/$getPath";

    
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
                      plot_STAMP($user,$project,$path_in,$path_out,$sample1,$sample2); 
                   }
            }   
    }


    # 22
    function plot_STAMP($user,$project,$path_in,$path_out,$sample1,$sample2){

            echo "plot_STAMP"."\n";

            # $L = Please select level of KEGG pathway  level 1,2 or 3
            $L = $GLOBALS['kegg'];

            
            # Folder Qiime SVG optional_output
            $download_optional_output = "data_report_mothurQiime/$user/$project/Download/optional_output/";
  
            $myResultsPathway = $path_out."Processeddata/final_otu_tables/myResultsPathway".$L.".tsv";

            $path_to_save = $download_optional_output."bar_plot_STAMP.svg";
            

            $jobname = $user ."_plot_STAMP";
            $log = $GLOBALS['path_log'];
            $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $myResultsPathway $path_to_save $sample1 $sample2";
   
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
                     $loop = false;

                      # Folder PNG Qiime optional_output
                      $image_png = "data_report_mothurQiime/$user/$project/optional_output/bar_plot_STAMP.png";

                      $cmd_png = '/usr/bin/inkscape -z  '. $path_to_save.' -e '.$image_png;
                      exec($cmd_png); 

                      keep_file_mothurqiime($user,$project,$path_in,$path_out);
                }
            }
    }

    
    function keep_file_mothurqiime($user,$project,$path_in,$path_out){

        $min = $GLOBALS['min'];
   
        # Folder MothurQiime Report
        $file_report = "data_report_mothurQiime/$user/$project/file_report/";
        $path_all_file = $path_out."Processeddata/";


        #1 copy alpha_diversity_from_table_even.$min.txt
            $path_table_even = $path_all_file."cdotu/alpha_diversity_from_table_even".$min.".txt";   
            $copy_even = $path_table_even;
            $past_even = $file_report."alpha_diversity_from_table_even".$min.".txt";
            copy($copy_even,$past_even); 

        #2 copy abund_jaccard_dm.txt
            $path_jaccard_dm = $path_all_file."cdotu/bdiv_even".$min."/abund_jaccard_dm.txt";   
            $copy_jaccard_dm = $path_jaccard_dm;
            $past_jaccard_dm = $file_report."abund_jaccard_dm.txt";
            copy($copy_jaccard_dm,$past_jaccard_dm); 

        #3 copy morisita_horn_dm.txt
            $path_horn_dm = $path_all_file."cdotu/bdiv_even".$min."/morisita_horn_dm.txt";   
            $copy_horn_dm = $path_horn_dm;
            $past_horn_dm = $file_report."morisita_horn_dm.txt";
            copy($copy_horn_dm,$past_horn_dm);  

        #4 copy otu_table_high_conf_summary.txt
            $path_otu_table = $path_all_file."clustering/otu_table_high_conf_summary.txt";
            $copy_otu_table = $path_otu_table;
            $past_otu_table = $file_report."otu_table_high_conf_summary.txt";
            copy($copy_otu_table,$past_otu_table);


        #5 copy observed.txt
            $path_observed = $path_all_file."cdotu/arare_max".$min."/alpha_div_collated/observed.txt";
            $copy_observed = $path_observed;
            $past_observed = $file_report."observed.txt";
            copy($copy_observed,$past_observed);


        #6 observed_species.txt
            $path_observed_species = $path_all_file."cdotu/arare_max".$min."/alpha_div_collated/observed_species.txt";
            $copy_observed_species = $path_observed_species;
            $past_observed_species = $file_report."observed_species.txt";
            copy($copy_observed_species,$past_observed_species);

        #7 copy table_mc.$min._sorted_L6.txt
            $path_table_L6 = $path_all_file."cdotu/taxa_plots/table_mc".$min."_sorted_L6.txt";                 
            $copy_L6 = $path_table_L6;
            $past_L6 = $file_report."table_mc".$min."_sorted_L6.txt";
            copy($copy_L6,$past_L6);

         #8 copy table_mc.$min._sorted_L2.txt
            $path_table_L2 = $path_all_file."cdotu/taxa_plots/table_mc".$min."_sorted_L2.txt";                 
            $copy_L2 = $path_table_L2;
            $past_L2 = $file_report."table_mc".$min."_sorted_L2.txt";
            copy($copy_L2,$past_L2);

        #9 copy morisita_horn_pc.txt
            $path_horn_pc = $path_all_file."cdotu/bdiv_even".$min."/morisita_horn_pc.txt";   
            $copy_horn_pc = $path_horn_pc;
            $past_horn_pc = $file_report."morisita_horn_pc.txt";
            copy($copy_horn_pc,$past_horn_pc);

        
        #10 copy adonis_results.txt  
             $path_adonis = $path_all_file."adonis".$GLOBALS['core_group']."/adonis_results.txt";
             $copy_adonis = $path_adonis;
             $past_adonis = $file_report."adonis_results.txt";

             if(file_exists($path_adonis)){
                copy($copy_adonis,$past_adonis);
             } 

        #11 copy anosim_results.txt
             $path_anosim = $path_all_file."anosim".$GLOBALS['core_group']."/anosim_results.txt";
             $copy_anosim = $path_anosim;
             $past_anosim = $file_report."anosim_results.txt";

             if(file_exists($path_anosim)){
                copy($copy_anosim,$past_anosim);
             }


        #12 copy permanova_results.txt
             $path_permanova = $path_all_file."permanova".$GLOBALS['core_group']."/permanova_results.txt";
             $copy_permanova = $path_permanova;
             $past_permanova = $file_report."permanova_results.txt";
             
             if(file_exists($path_permanova)){
                 copy($copy_permanova,$past_permanova);
             } 

        #13 copy myResultsPathwayL2.tsv
            $path_tsv = $path_all_file."final_otu_tables/myResultsPathway".$GLOBALS['kegg'].".tsv";
            $copy_tsv = $path_tsv;
            $past_tsv = $file_report."myResultsPathway".$GLOBALS['kegg'].".tsv";
            
             if(file_exists($path_tsv)){
                copy($copy_tsv,$past_tsv);
             }

        #14 copy otu_table_mc1_w_tax_no_pynast_failures.txt 
            $path_pynast = $path_all_file."clustering/otu_table_mc1_w_tax_no_pynast_failures.txt";   
            $copy_pynast = $path_pynast;
            $past_pynast = $file_report."otu_table_mc1_w_tax_no_pynast_failures.txt";
            copy($copy_pynast,$past_pynast);


        #15 copy read_filter.log
            // $path_read_filter = $path_out."stitched_reads_filter/read_filter.log";   
            // $copy_read_filter = $path_read_filter;
            // $past_read_filter = $file_report."read_filter.log";
            // copy($copy_read_filter,$past_read_filter);


        #16 copy sampleName.txt
            $path_sampleName = $path_in."sampleName.txt";   
            $copy_sampleName = $path_sampleName;
            $past_sampleName = $file_report."sampleName.txt";
            copy($copy_sampleName,$past_sampleName);

        # create file min.txt
           $L = $GLOBALS['kegg'];
           $file_min_txt = $file_report."min.txt";
           $data_min_txt = $min."\t".$L;
           file_put_contents($file_min_txt,$data_min_txt);



    }



    function plot_heatmap($user,$project,$path_in,$path_out){

       

        # Folder Qiime Report
        $file_report = "data_report_mothurQiime/$user/$project/file_report/";

        # Folder Qiime taxonomy_classification
         $download_taxonomy_classification = "data_report_mothurQiime/$user/$project/Download/taxonomy_classification/";

         $min = $GLOBALS['min'];
        
         $option_i  = $file_report."table_mc".$min."_sorted_L6.txt"; 
         $option_o = $file_report."table_mc".$min."_sorted_L6ex1.txt";
     
         $path_sample = $path_in."sampleName.txt";
         $file_Name = file_get_contents($path_sample);
         $get_Name = explode("\t", trim($file_Name));
         $count_sample = count($get_Name);
         $cmd = "Scriptqiime2/step1_heatmap.sh $option_i $option_o";
         exec($cmd);


         $option_i_1  = $file_report."table_mc".$min."_sorted_L6ex1.txt"; 
         $option_o_1 = $file_report."table_mc".$min."_sorted_L6ex1.biom";
         $cmd2 = "Scriptqiime2/step2_heatmap $option_i_1 $option_o_1 ";
         exec($cmd2);


         $option_i_2 = $file_report."table_mc".$min."_sorted_L6ex1.biom";
         $option_o_2 = $download_taxonomy_classification."heatmap.svg";

         $width = null;       
         if($count_sample >= 6 && $count_sample <= 10){
            $width = "7";
         }else if($count_sample < 6){
            $width = "5";
         }else if($count_sample > 10){
            $width = "8";
         }

         $cmd3 = "Scriptqiime2/step3_heatmap $option_i_2 $option_o_2 $width";
         exec($cmd3,$output);


         $image_png = "data_report_mothurQiime/$user/$project/taxonomy_classification/heatmap.png";
         $cmd_png = '/usr/bin/inkscape -z  '.$option_o_2.' -e '.$image_png;
         exec($cmd_png); 

         plot_bar($user,$project,$path_in,$path_out,$count_sample);
    }




    function plot_bar($user,$project,$path_in,$path_out,$count_sample){

        
         # Folder Qiime Report
         $file_report = "data_report_mothurQiime/$user/$project/file_report/";

         $min = $GLOBALS['min'];
        
         $option_i = $file_report."table_mc".$min."_sorted_L2.txt";
         $option_o = $file_report."taxaonlyphylum";

         $option_x = null;
         $num_sample = (int)$count_sample;
     
         if($num_sample <= 6){
            $option_x = "6";
         }else{
            $option_x = "8";
         }

         $cmd = "Scriptqiime2/barplot $option_i $option_o $option_x";
         exec($cmd);

         
        # Folder PNG Qiime taxonomy_classification
        $taxonomy_classification = "data_report_mothurQiime/$user/$project/taxonomy_classification/";
   
        $path = $file_report."taxaonlyphylum/charts/*.png";
        $all_png = glob($path);
        $image_bar = "bar_plot.png";
        $image_legend = "bar_plot_legend.png"; 
        foreach ($all_png as $key => $value) {
            $name = basename($value);
            $data = explode("_",$name);
            if(count($data) > 1){
                 copy($value,$taxonomy_classification.$image_legend);          
            }else{
                 copy($value,$taxonomy_classification.$image_bar); 
            }   
        }


         # Folder SVG Qiime taxonomy_classification
        $download_taxonomy_classification = "data_report_mothurQiime/$user/$project/Download/taxonomy_classification/";
   
        $path_svg = "data_report_mothurQiime/$user/$project/file_report/taxaonlyphylum/charts/*.svg";
        $all_svg = glob($path_svg);
        $image_bar_svg = "bar_plot.svg";
        $image_legend_svg = "bar_plot_legend.svg"; 

        foreach ($all_svg as $key_svg => $value_svg) {
            $name_svg = basename($value_svg);
            $data_svg = explode("_",$name_svg);
            if(count($data_svg) > 1){
               
                 # svg
                 copy($value_svg,$download_taxonomy_classification.$image_legend_svg);

            }else{
                
                 # svg
                 copy($value_svg,$download_taxonomy_classification.$image_bar_svg);

            }   
        }

         plot_rarefaction($user,$project,$path_in,$path_out);
    }



    function plot_rarefaction($user,$project,$path_in,$path_out){


         # Folder Qiime Report
         $file_report = "data_report_mothurQiime/$user/$project/file_report/";

         # Folder SVG Qiime alpha_diversity_analysis
         $dowload_alpha_diversity = "data_report_mothurQiime/$user/$project/Download/alpha_diversity_analysis/";


         $option_i = $file_report."observed_species.txt";
         $option_o = $file_report."outaverage.txt";
          
         $cmd = "Scriptqiime2/Rscript/getaveragerarefaction.sh $option_i $option_o";
         exec($cmd);

         $txt_1 = $file_report."outaverage.txt";
         $svg_2 = $dowload_alpha_diversity."Rarefactionqiime.svg";

         $cmd2 = "/usr/bin/Rscript Scriptqiime2/Rscript/plot_Rarefaction_qiime.R $txt_1 $svg_2";
         exec($cmd2);


         # Folder PNG Qiime alpha_diversity_analysis
         $image_png = "data_report_mothurQiime/$user/$project/alpha_diversity_analysis/Rarefactionqiime.png";

         $cmd_png = '/usr/bin/inkscape -z  '.$svg_2.' -e '.$image_png;
         exec($cmd_png); 


         copy_graph_chao_shannon($user,$project,$path_in,$path_out);

    }


     function copy_graph_chao_shannon($user,$project,$path_in,$path_out){

       
        # Folder Qiime Report
        $file_report = "data_report_mothurQiime/$user/$project/file_report/";
        $path_all_file = $path_out."Processeddata/";
        $min = $GLOBALS['min'];
      
        # 1 copy boxplots_chao.pdf 
          $path_chao = $path_all_file."cdotu/arare_max".$min."/compare_chao1/";
          if (is_dir($path_chao)) {
              if ($read = opendir($path_chao)){
                while (($chao= readdir($read)) !== false) {
                    if(stripos($chao,'boxplots.pdf') !== false){
 
                        $copy_chao = $path_chao.$chao;
                        $past_chao = $file_report."boxplots_chao.pdf";
                        if(file_exists($copy_chao)){
                            copy($copy_chao,$past_chao);
                        }
                    }    
                }
                closedir($read);
              }
           }

         # 2 copy boxplots_shannon.pdf 
          $path_shannon = $path_all_file."cdotu/arare_max".$min."/compare_shannon/";
          if (is_dir($path_shannon)) {
              if ($read2 = opendir($path_shannon)){
                while (($shannon = readdir($read2)) !== false) {
                    if(stripos($shannon,'boxplots.pdf') !== false){
 
                        $copy_shannon = $path_shannon.$shannon;
                        $past_shannon = $file_report."boxplots_shannon.pdf";
                        if(file_exists($copy_shannon)){
                            copy($copy_shannon,$past_shannon);
                        }
                    }    
                }
                closedir($read2);
              }
           }
       convert_img_chao_shannon($user,$project,$path_in,$path_out);
    }




    function convert_img_chao_shannon($user,$project,$path_in,$path_out){

       

        # Folder Qiime Report
        $file_report = "data_report_mothurQiime/$user/$project/file_report/";

        #boxplots_chao.pdf
        $pdf_chao = $file_report."boxplots_chao.pdf";

        # svg
         $path_svg_chao = "data_report_mothurQiime/$user/$project/Download/alpha_diversity_analysis/boxplots_chao.svg";
         $cmd_svg_chao ="/usr/bin/inkscape -z -f ".$pdf_chao." -l ".$path_svg_chao;
         exec($cmd_svg_chao);

        # png
         $path_png_chao = "data_report_mothurQiime/$user/$project/alpha_diversity_analysis/boxplots_chao.png";
          $cmd_png_chao = '/usr/bin/inkscape -z  '.$pdf_chao.' -e '.$path_png_chao;
          exec($cmd_png_chao);


        #boxplots_shannon.pdf
          $pdf_shannon = $file_report."boxplots_shannon.pdf";
          $path_svg_shannon = "data_report_mothurQiime/$user/$project/Download/alpha_diversity_analysis/boxplots_shannon.svg";
         
          # svg
          $cmd_svg_shannon ="/usr/bin/inkscape -z -f ".$pdf_chao." -l ".$path_svg_shannon;
          exec($cmd_svg_shannon);

          # png
          $path_png_shannon = "data_report_mothurQiime/$user/$project/alpha_diversity_analysis/boxplots_shannon.png";
          $cmd_png_shannon = '/usr/bin/inkscape -z  '.$pdf_chao.' -e '.$path_png_shannon;
          exec($cmd_png_shannon);


          copy_pcoa($user,$project,$path_in,$path_out);
    }



    function getNameDir($path,$data_path){

      $path_name = null;
        if($read = opendir($path)){
            while (($dir_tmp = readdir($read)) !== false) {
              if(!in_array($dir_tmp,$data_path)){
                if(is_dir($path.$dir_tmp)){
                    $path_name = $dir_tmp;
                }      
              }       
            }
            closedir($read);
        }
      return $path_name;
    }


    function copy_pcoa($user,$project,$path_in,$path_out){


        # Folder Qiime PNG beta_diversity_analysis
        $beta_diversity_analysis = "data_report_mothurQiime/$user/$project/beta_diversity_analysis/";

        # Folder Qiime SVG beta_diversity_analysis
        $download_beta_diversity_analysis = "data_report_mothurQiime/$user/$project/Download/beta_diversity_analysis/";

        $path_all_file = $path_out."Processeddata/";
        $min = $GLOBALS['min'];
        

       $path = $path_all_file."cdotu/bdiv_even".$min."/2d_plots_coordinate/";
       
       $data_path = array(".","..","js");
       $data_path2 = array(".",".."); 

       $dir_1 = getNameDir($path,$data_path);
       $path2 = $path.$dir_1."/";
       $dir_2 = getNameDir($path2,$data_path2);
       $path_full = $path.$dir_1."/". $dir_2."/";

        if($read = opendir($path_full)){
            while (($dir_file = readdir($read)) !== false) {
                if(!in_array($dir_file,$data_path2)){

                    if(stripos($dir_file,'plot.png') !== false){
                      $pcoa_copy = $path_full.$dir_file;
                      $pcoa_past = $beta_diversity_analysis.$dir_file;
                      copy($pcoa_copy,$pcoa_past);
                    }else{

                      $pcoa_copy = $path_full.$dir_file;
                      $pcoa_past = $download_beta_diversity_analysis.$dir_file;
                      copy($pcoa_copy,$pcoa_past);
                    }
                }
            }
          closedir($read);
        }

        # unzip by gunzip
        if (is_dir($download_beta_diversity_analysis)) {
            if ($read2 = opendir($download_beta_diversity_analysis)){
                while (($file_eps = readdir($read2)) !== false) {
                    if(stripos($file_eps,'plot.eps.gz') !== false){

                      $unzip = $download_beta_diversity_analysis.$file_eps;
                      $cmd = "/usr/bin/gunzip ".$unzip;
                      exec($cmd);
                            
                    }      
                }
              closedir($read2);
            }
        }


      # copy folder  2d_plots_coordinate to  folder beta_diversity_analysis
        $path_past = $beta_diversity_analysis."2d_plots_coordinate/";
        $cmd2 = "/bin/cp -r ".$path." ".$path_past;
        exec($cmd2);

        copy_folder_cdotu($user,$project,$path_in,$path_out);

    }



     function copy_folder_cdotu($user,$project,$path_in,$path_out){

        # Folder cdotu
        $folder_cdotu = "data_report_mothurQiime/$user/$project/cdotu/";

        $path_all_file = $path_out."Processeddata/cdotu/";
       
        # copy folder cdotu 
        $cmd = "/bin/cp -r ".$path_all_file." ".$folder_cdotu;
        exec($cmd);

     }




?>