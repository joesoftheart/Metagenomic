<?php 

 include('setting_sge.php');
 putenv("SGE_ROOT=$SGE_ROOT");
 putenv("PATH=$PATH");

  $user = $argv[1];
  $project = $argv[2];
  $path_in = $argv[3];
  $path_out = $argv[4];
  $GLOBALS['path_log'] = $argv[5];
  
  
  anosim($user,$project,$path_in,$path_out);

  function runPerl($user,$project,$path_in,$path_out){

 		  echo "runPerl"."\n";

     	$jobname = $user."_runPerl";
      $log = $GLOBALS['path_log'];


      $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/qsubworkqiime $path_out $path_in";


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
                      runfilter($user,$project,$path_in,$path_out);  
                   }
         }   
    }


    function runfilter($user,$project,$path_in,$path_out){

        echo "runfilter"."\n";

        $jobname = $user."_filter";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/qsubworkqiime1 $path_out";


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
                      run_fastq_to_fasta($user,$project,$path_in,$path_out);
                   }
         }   
    }

     function run_fastq_to_fasta($user,$project,$path_in,$path_out){

        echo "run_fastq_to_fasta"."\n";

        $jobname = $user."_run_fastq_to_fasta";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/qsubworkqiime2 $path_out";


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


    function cutadapt1($user,$project,$path_in,$path_out){

        $folder_cut1 = $path_out."fasta_files/cutadapt1";   
        if (!file_exists($folder_cut1)) {
            mkdir($folder_cut1, 0777, true);
        }
      
        $path = $path_out."fasta_files";
        $file_fasta = glob($path."/*.fasta"); 
        
        foreach ($file_fasta as $key => $filein) {
            
            $fileout = $path_out."fasta_files/cutadapt1/".basename($filein);
            $cmd = "/usr/bin/cutadapt -g CCTACGGGNGGCWGCAG -g GACTACHVGGGTATCTAATCC -e 0.15 -o $fileout $filein";

            shell_exec($cmd);

          }  

    }

    function cutadapt2($user,$project,$path_in,$path_out){

       
        $folder_newfasta_files = $path_out."newfasta_files";   
        if (!file_exists($folder_newfasta_files)) {
             mkdir($folder_newfasta_files, 0777, true);
        }

        $path = $path_out."fasta_files/cutadapt1";
        $file_fasta = glob($path."/*.fasta"); 
        
        foreach ($file_fasta as $key => $filein) {
            
            $fileout = $path_out."newfasta_files/"."out".basename($filein);
            $cmd = "/usr/bin/cutadapt -a GGATTAGATACCCBDGTAGTC -a CTGCWGCCNCCCGTAGG -e 0.15 -o $fileout $filein";

            shell_exec($cmd);

        }  

    }

    function add_labels($user,$project,$path_in,$path_out){

        echo "add_labels"."\n";

        $option_i = $path_out."newfasta_files/";
        $option_m = $path_in."mappor.txt";
        $option_o = $path_out."newfasta_files/Processeddata/";

        $jobname = $user."_add_labels";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime1 $option_i $option_m $option_o";


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

    function pick_open($user,$project,$path_in,$path_out){

        echo "pick_open"."\n";
    
        $option_i = $path_out."newfasta_files/Processeddata/combined_seqs.fna";
        $option_o = $path_out."newfasta_files/Processeddata/otus/";
        $option_p = $path_in."uc_fast_paramsmodi.txt";


        $jobname = $user."_pick_open";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime2 $option_i $option_o $option_p";


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

    function vsearch($user,$project,$path_in,$path_out){

        echo "vsearch"."\n";
    
        $option_uchime_ref = $path_out."newfasta_files/Processeddata/otus/rep_set.fna";
        $option_chimeras = $path_out."newfasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";


        $jobname = $user."_vsearch";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime3 $option_uchime_ref $option_chimeras";


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

     function filter_otus($user,$project,$path_in,$path_out){

        echo "filter_otus"."\n";
    
        $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures.biom";
        $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
        $option_e = $path_out."newfasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";

        $jobname = $user."_filter_otus";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime4 $option_i $option_o $option_e";


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


    function filter_fasta($user,$project,$path_in,$path_out){

        echo "filter_fasta"."\n";
    
        $option_f = $path_out."newfasta_files/Processeddata/otus/pynast_aligned_seqs/rep_set_aligned_pfiltered.fasta";
        $option_o = $path_out."newfasta_files/Processeddata/otus/rep_set_aligned_pfiltered_no_chimeras.fasta";
        $option_a = $path_out."newfasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";

        $jobname = $user."_filter_fasta";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime5 $option_f $option_o $option_a";


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

     function make_phylogeny($user,$project,$path_in,$path_out){

        echo "make_phylogeny"."\n";
    
        $option_i = $path_out."newfasta_files/Processeddata/otus/rep_set_aligned_pfiltered_no_chimeras.fasta";
        $option_o = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";
       
        $jobname = $user."_make_phylogeny";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime6 $option_i $option_o";


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


    function filter_otus_from_otu_table($user,$project,$path_in,$path_out){

        echo "filter_otus_from_otu_table"."\n";
    
        $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
        $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
       
        $jobname = $user."_filter_otus_from_otu_table";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime7 $option_i $option_o";


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


     function biom_summarize_table($user,$project,$path_in,$path_out){

        echo "biom_summarize_table"."\n";
    
        $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
        
        $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
       
        $jobname = $user."_biom_summarize_table";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime8 $option_i $option_o";


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


    # Get value min && Create folder final_otus_tables
     function single_rarefaction($user,$project,$path_in,$path_out){


        $folder_final_otus_tables = $path_out."newfasta_files/Processeddata/otus/final_otus_tables";   
        if (!file_exists($folder_final_otus_tables)) {
             mkdir($folder_final_otus_tables, 0777, true);
        }

        echo "single_rarefaction"."\n";
    
        $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
        
        $option_o = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";

        # read value min from otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt
        $option_d = "93831";
       
        $jobname = $user."_single_rarefaction";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime9 $option_i $option_o $option_d";


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

     function summarize_taxa_through_plots($user,$project,$path_in,$path_out){

       
        echo "summarize_taxa_through_plots"."\n";
    
        $option_o = $path_out."newfasta_files/Processeddata/otus/taxa_summary";
        
        $option_i = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";

        $option_m = $path_in."mappor.txt";

       
        $jobname = $user."_summarize_taxa_through_plots";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime10 $option_o $option_i $option_m";


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


     function core_diversity_analyses($user,$project,$path_in,$path_out){

       
        echo "core_diversity_analyses"."\n";
    
        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu";
        
        $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";

        $option_m = $path_in."mappor.txt";

        $option_t = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";

        #read value min from otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt
        $option_e = "93831";

        $option_c = "groupA";

        $option_p =  $path_in."alpha_params1.txt";

       
        $jobname = $user."_core_diversity_analyses";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime11 $option_o $option_i $option_m $option_t $option_e $option_c $option_p";


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

    function gunzipfile($user,$project,$path_in,$path_out){

        echo "gunzipfile"."\n";
        $file_gz  = $path_out."newfasta_files/Processeddata/otus/cdotu/*.gz";     
        $file_all = glob($file_gz ); 
        foreach ($file_all as $key => $filein){
            #echo $filein."\n";
            $cmd = "/usr/bin/gunzip  $filein";
            shell_exec($cmd);
        }  
    }



    # Use min value 
    function alpha_diversity_mc($user,$project,$path_in,$path_out){

       
        echo "alpha_diversity_mc"."\n";
         
        $min = "93831";
        $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/table_mc".$min.".biom";
      
        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/alpha_diversity_from_table_mc".$min.".txt";

        $option_t = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";

       
        $jobname = $user."_alpha_diversity_mc";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime12 $option_i $option_o $option_t";


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


    # Use min value 
    function alpha_diversity_even($user,$project,$path_in,$path_out){

       
        echo "alpha_diversity_even"."\n";
         
        $min = "93831";
        $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/table_even".$min.".biom";
      
        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/alpha_diversity_from_table_even".$min.".txt";

        $option_t = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";

       
        $jobname = $user."_alpha_diversity_even";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime13 $option_i $option_o $option_t";


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


    function make_2d_plots($user,$project,$path_in,$path_out){

       
        echo "make_2d_plots"."\n";
         
        $min = "93831";

        $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even93831/unweighted_unifrac_pc.txt";
      
        $option_m = $path_in."mappor.txt";

        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/2d_plots_coordinate/";

        $option_b = "'groupA'";

       
        $jobname = $user."_make_2d_plots";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime14 $option_i $option_m $option_o $option_b";


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


   function anosim($user,$project,$path_in,$path_out){

       
        echo "anosim"."\n";
         
        $min = "93831";

        $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/weighted_unifrac_dm.txt";
      
        $option_m = $path_in."mappor.txt";

         $option_c = "groupA";

        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/anosimGroupAonlyoneinput";

        $jobname = $user."_anosim";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime_anosim15 $option_i $option_m $option_c $option_o";


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
    }


    function adonis($user,$project,$path_in,$path_out){

       
        echo "adonis"."\n";
         
        $min = "93831";

        $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/weighted_unifrac_dm.txt";
      
        $option_m = $path_in."mappor.txt";

         $option_c = "groupA";

        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/adonisGroupAweighted";

        $jobname = $user."_adonis";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime_adonis16 $option_i $option_m $option_c $option_o";


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


    function permanova($user,$project,$path_in,$path_out){

       
        echo "permanova"."\n";
         
        $min = "93831";

        $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/weighted_unifrac_dm.txt";
      
        $option_m = $path_in."mappor.txt";

         $option_c = "groupA";

        $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/permanovaGroupAweighted";

        $jobname = $user."_permanova";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime_permanova17 $option_i $option_m $option_c $option_o";


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