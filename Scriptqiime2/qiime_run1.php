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
  $GLOBALS['permanova'] = $argv[7];
  $GLOBALS['opt_permanova'] = $argv[8];
  $GLOBALS['anosim'] = $argv[9];
  $GLOBALS['opt_anosim'] = $argv[10];
  $GLOBALS['adonis'] = $argv[11];
  $GLOBALS['opt_adonis'] = $argv[12];
  $GLOBALS['core_group'] = $argv[13];

  $GLOBALS['kegg'] = $argv[14];
  $GLOBALS['sample_comparison'] = $argv[15];
  $GLOBALS['statistical_test'] = $argv[16];
  $GLOBALS['ci_method'] = $argv[17];
  $GLOBALS['p_value'] = $argv[18];

  $GLOBALS['beta_diversity_index1']= $argv[19];
  $GLOBALS['beta_diversity_index2']= $argv[20];

  $GLOBALS['min'] = null;

  
  stamp($user,$project,$path_in,$path_out);
  #runPerl($user,$project,$path_in,$path_out);

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

                      if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
                           
                           cutadapt1($user,$project,$path_in,$path_out);

                      }else{

                           add_labels($user,$project,$path_in,$path_out);
                      }
                     
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

        cutadapt2($user,$project,$path_in,$path_out);  
        
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

        add_labels($user,$project,$path_in,$path_out); 

    }

    # select option (miseq_without_barcodes)  => folder fasta_files
    # select option (miseq_contain_primer) => folder newfasta_files
  
    function add_labels($user,$project,$path_in,$path_out){

        echo "add_labels"."\n";

        $option_i = "";
        $option_m = "";
        $option_o = "";

        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

            $option_i = $path_out."newfasta_files/";
            $option_m = $path_in."map.txt";
            $option_o = $path_out."newfasta_files/Processeddata/";

        }else{ 

            $option_i = $path_out."fasta_files/";
            $option_m = $path_in."map.txt";
            $option_o = $path_out."fasta_files/Processeddata/";

            $path = $option_i;
            $file_fasta = glob($path."/*.fasta"); 
            foreach ($file_fasta as $key => $filein) {
                $old_name =  explode(".", basename($filein));
                $new_name = $old_name[0];
                rename($filein, $path."/out".$new_name.".assembled_filtered.fasta");
            } 
        }

        
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
                      pick_open($user,$project,$path_in,$path_out);
                   }
         }   
    }


    

    function pick_open($user,$project,$path_in,$path_out){

        echo "pick_open"."\n";

        $option_i = "";
        $option_o = "";
        $option_p = "";
          
         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/combined_seqs.fna";
             $option_o = $path_out."newfasta_files/Processeddata/otus/";
             $option_p = $path_in."uc_fast_paramsmodi.txt";
         }

         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/combined_seqs.fna";
             $option_o = $path_out."fasta_files/Processeddata/otus/";
             $option_p = $path_in."uc_fast_paramsmodi.txt";
         }

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
                      vsearch($user,$project,$path_in,$path_out);
                   }
         }   

    }

    function vsearch($user,$project,$path_in,$path_out){

        echo "vsearch"."\n";

        $option_uchime_ref = "";
        $option_chimeras = "";

         # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_uchime_ref = $path_out."newfasta_files/Processeddata/otus/rep_set.fna";
             $option_chimeras = $path_out."newfasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
        else{ 

             $option_uchime_ref = $path_out."fasta_files/Processeddata/otus/rep_set.fna";
             $option_chimeras = $path_out."fasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";
         }


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
                      filter_otus($user,$project,$path_in,$path_out);
                   }
         }   
    }

     function filter_otus($user,$project,$path_in,$path_out){

        echo "filter_otus"."\n";
    
        $option_i = "";
        $option_o = "";
        $option_e = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                 $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures.biom";
                 $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
                 $option_e = $path_out."newfasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";    
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 
                 $option_i = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures.biom";
                 $option_o = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
                 $option_e = $path_out."fasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";
         }

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
                      filter_fasta($user,$project,$path_in,$path_out);
                   }
         }   
    }


    function filter_fasta($user,$project,$path_in,$path_out){

        echo "filter_fasta"."\n";
    
        $option_f = "";
        $option_o = "";
        $option_a = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                $option_f = $path_out."newfasta_files/Processeddata/otus/pynast_aligned_seqs/rep_set_aligned_pfiltered.fasta";
                $option_o = $path_out."newfasta_files/Processeddata/otus/rep_set_aligned_pfiltered_no_chimeras.fasta";
                $option_a = $path_out."newfasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta";

         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_f = $path_out."fasta_files/Processeddata/otus/pynast_aligned_seqs/rep_set_aligned_pfiltered.fasta";
             $option_o = $path_out."fasta_files/Processeddata/otus/rep_set_aligned_pfiltered_no_chimeras.fasta";
             $option_a = $path_out."fasta_files/Processeddata/otus/mc2_w_tax_no_pynast_failures_chimeras.fasta"; 
         }

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
                      make_phylogeny($user,$project,$path_in,$path_out);
                   }
         }   
    }

     function make_phylogeny($user,$project,$path_in,$path_out){

        echo "make_phylogeny"."\n";
    
        $option_i = "";
        $option_o = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/rep_set_aligned_pfiltered_no_chimeras.fasta";
             $option_o = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

              $option_i = $path_out."fasta_files/Processeddata/otus/rep_set_aligned_pfiltered_no_chimeras.fasta";
              $option_o = $path_out."fasta_files/Processeddata/otus/rep_set_no_chimeras.tre";
  
         }

       
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
                      filter_otus_from_otu_table($user,$project,$path_in,$path_out);
                   }
         }   
    }


    function filter_otus_from_otu_table($user,$project,$path_in,$path_out){

        echo "filter_otus_from_otu_table"."\n";
    
        $option_i = "";
        $option_o = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
             $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
         
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
             $option_o = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";

         }

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
                      biom_summarize_table($user,$project,$path_in,$path_out);
                   }
         }   
    }


     function biom_summarize_table($user,$project,$path_in,$path_out){

        echo "biom_summarize_table"."\n";
    
        $option_i = "";
        
        $option_o = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
            
              $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
        
              $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
         
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

            $option_i = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
        
              $option_o = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
         }

       
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
                      minotu_table($user,$project,$path_in,$path_out);
                   }
         }   
    }




    function minotu_table($user,$project,$path_in,$path_out){


         $otu_talbe = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

               $otu_talbe = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

               $otu_talbe = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
         }

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

                     #echo $val_int."\n";
                     single_rarefaction($user,$project,$path_in,$path_out);

                }
    }



    # Get value min && Create folder final_otus_tables
     function single_rarefaction($user,$project,$path_in,$path_out){


        echo "single_rarefaction"."\n";

        $option_i = "";
        $option_o = "";
        $folder_final_otus_tables = "";   
       

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
             $option_o = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";
             $folder_final_otus_tables = $path_out."newfasta_files/Processeddata/otus/final_otus_tables";   

         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
             $option_o = $path_out."fasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";
             $folder_final_otus_tables = $path_out."fasta_files/Processeddata/otus/final_otus_tables";   
         }

        if (!file_exists($folder_final_otus_tables)) {
             mkdir($folder_final_otus_tables, 0777, true);
        }


        # read value min from otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt
        $option_d = $GLOBALS['min'];
       
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
                      summarize_taxa_through_plots($user,$project,$path_in,$path_out);
                   }
         }   
    }

     function summarize_taxa_through_plots($user,$project,$path_in,$path_out){

       
        echo "summarize_taxa_through_plots"."\n";
    
        $option_o = "";
        $option_i = "";
        $option_m = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

            $option_o = $path_out."newfasta_files/Processeddata/otus/taxa_summary";
            $option_i = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";
            $option_m = $path_in."map.txt";
          
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

            $option_o = $path_out."fasta_files/Processeddata/otus/taxa_summary";
            $option_i = $path_out."fasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";
            $option_m = $path_in."map.txt";
         }

       
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
                      core_diversity_analyses($user,$project,$path_in,$path_out);
                   }
         }   
    }


     function core_diversity_analyses($user,$project,$path_in,$path_out){

       
        echo "core_diversity_analyses"."\n";
    
        $option_o = "";
        $option_i = "";
        $option_m = "";
        $option_t ="";


         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu";
             $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
             $option_m = $path_in."map.txt";
             $option_t = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";

         
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_o = $path_out."fasta_files/Processeddata/otus/cdotu";     
             $option_i = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered.biom";
             $option_m = $path_in."map.txt";
             $option_t = $path_out."fasta_files/Processeddata/otus/rep_set_no_chimeras.tre";

         }

        #read value min from otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt
        $option_e = $GLOBALS['min'];
        $option_c = $GLOBALS['core_group'];
        $option_p =  $path_in."alpha_params.txt";

       
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
                      gunzipfile($user,$project,$path_in,$path_out);
                   }
         }   
    }

    function gunzipfile($user,$project,$path_in,$path_out){

        echo "gunzipfile"."\n";
        $file_gz = ""; 

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

            $file_gz  = $path_out."newfasta_files/Processeddata/otus/cdotu/*.gz";  
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $file_gz  = $path_out."fasta_files/Processeddata/otus/cdotu/*.gz";  
         }

        $file_all = glob($file_gz ); 
        foreach ($file_all as $key => $filein){
            #echo $filein."\n";
            $cmd = "/usr/bin/gunzip  $filein";
            shell_exec($cmd);
        }

        alpha_diversity_mc($user,$project,$path_in,$path_out);  
    }



    # Use min value 
    function alpha_diversity_mc($user,$project,$path_in,$path_out){

       
        echo "alpha_diversity_mc"."\n";
         
        $min = $GLOBALS['min'];
        $option_i = "";
        $option_o = "";
        $option_t = "";


         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/table_mc".$min.".biom";
             $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/alpha_diversity_from_table_mc".$min.".txt";
             $option_t = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";

         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/cdotu/table_mc".$min.".biom";      
             $option_o = $path_out."fasta_files/Processeddata/otus/cdotu/alpha_diversity_from_table_mc".$min.".txt";
             $option_t = $path_out."fasta_files/Processeddata/otus/rep_set_no_chimeras.tre";
         }

       
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
                      alpha_diversity_even($user,$project,$path_in,$path_out);
                   }
         }   
    }


    # Use min value 
    function alpha_diversity_even($user,$project,$path_in,$path_out){

       
        echo "alpha_diversity_even"."\n";
         
        $min = $GLOBALS['min'];
        $option_i = "";
        $option_o = "";
        $option_t = "";

         # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/table_even".$min.".biom";
             $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/alpha_diversity_from_table_even".$min.".txt";
             $option_t = $path_out."newfasta_files/Processeddata/otus/rep_set_no_chimeras.tre";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/cdotu/table_even".$min.".biom";
             $option_o = $path_out."fasta_files/Processeddata/otus/cdotu/alpha_diversity_from_table_even".$min.".txt";
             $option_t = $path_out."fasta_files/Processeddata/otus/rep_set_no_chimeras.tre";
         }

       
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
                      make_2d_plots($user,$project,$path_in,$path_out);
                   }
         }   
    }


    function make_2d_plots($user,$project,$path_in,$path_out){

       
        echo "make_2d_plots"."\n";
         
        $min = $GLOBALS['min'];

        $option_i = "";
        $option_m = "";
        $option_o = "";

        $option_b = $GLOBALS['core_group'];

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){


             $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/unweighted_unifrac_pc.txt";
             $option_m = $path_in."map.txt";
             $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/2d_plots_coordinate/";

         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/unweighted_unifrac_pc.txt";
             $option_m = $path_in."map.txt";
             $option_o = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/2d_plots_coordinate/";
         }

       
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
                      anosim($user,$project,$path_in,$path_out);
                   }
         }   
    }


   function anosim($user,$project,$path_in,$path_out){

        if ($GLOBALS['anosim'] !== "none") {

             echo "anosim"."\n";
         
             $min = $GLOBALS['min'];
             $weight = $GLOBALS['opt_anosim'];


             $option_i = "";      
             $option_m = "";
             $option_c = $GLOBALS['core_group'];
             $option_o = "";

             # select option (miseq_contain_primer) => folder newfasta_files
             if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                 $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/".$weight."ed_unifrac_dm.txt";
                 $option_m = $path_in."map.txt";
                 $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/anosim".$GLOBALS['core_group'].$weight."ed";


             }
            # select option (miseq_without_barcodes)  => folder fasta_files
            else{ 

                $option_i = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/".$weight."ed_unifrac_dm.txt";
                $option_m = $path_in."map.txt";
                $option_o = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/anosim".$GLOBALS['core_group'].$weight."ed";
            }

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
        }else{

             adonis($user,$project,$path_in,$path_out);
        }
       
    }


    function adonis($user,$project,$path_in,$path_out){

        if ($GLOBALS['adonis'] != "none") {

             echo "adonis"."\n";
         
             $min = $GLOBALS['min'];
             $weight = $GLOBALS['opt_adonis'];

             $option_i = "";
             $option_m = "";
             $option_c = $GLOBALS['core_group'];
             $option_o = "";


             # select option (miseq_contain_primer) => folder newfasta_files
             if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/".$weight."ed_unifrac_dm.txt";
             $option_m = $path_in."map.txt";
             $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/adonis".$GLOBALS['opt_adonis'].$weight."ed";

             }
             # select option (miseq_without_barcodes)  => folder fasta_files
             else{ 

              $option_i = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/".$weight."ed_unifrac_dm.txt";
              $option_m = $path_in."map.txt";
              $option_o = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/adonis".$GLOBALS['core_group'].$weight."ed";
             }

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
        }else{

            permanova($user,$project,$path_in,$path_out);
        }
       
    }


    function permanova($user,$project,$path_in,$path_out){

        if($GLOBALS['permanova'] != "none"){

            echo "permanova"."\n";
         
            $min = $GLOBALS['min'];
            $weight = $GLOBALS['opt_permanova'];

            $option_i = "";
            $option_m = "";
            $option_c = $GLOBALS['core_group'];
            $option_o = "";

            # select option (miseq_contain_primer) => folder newfasta_files
            if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/".$weight."ed_unifrac_dm.txt";
             $option_m = $path_in."map.txt";
             $option_o = $path_out."newfasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/permanova".$GLOBALS['core_group'].$weight."ed";
            }
            # select option (miseq_without_barcodes)  => folder fasta_files
            else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/".$weight."ed_unifrac_dm.txt";
             $option_m = $path_in."map.txt";
             $option_o = $path_out."fasta_files/Processeddata/otus/cdotu/bdiv_even".$min."/permanova".$GLOBALS['core_group'].$weight."ed";
            }

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
                      qiime_To_picrust_1($user,$project,$path_in,$path_out);
                   }
            }   

        }else{
           
           qiime_To_picrust_1($user,$project,$path_in,$path_out);
        }
         
    }


    function qiime_To_picrust_1($user,$project,$path_in,$path_out){

        echo "qiime_To_picrust_1"."\n";

        $option_i = "";
        $option_o = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";

             $option_o = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table_json.biom";
         }

         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/final_otus_tables/otu_table.biom";
            
             $option_o = $path_out."fasta_files/Processeddata/otus/final_otus_tables/otu_table_json.biom";
         }

        $jobname = $user."_qiime_To_picrust_1";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/qiimeTopicrust1 $option_i $option_o";

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


    function qiime_To_picrust_2($user,$project,$path_in,$path_out){

        echo "qiime_To_picrust_2"."\n";

        $option_i = "";
        $option_o = "";

        $option_e ="/home/aum/anaconda/lib/python2.7/site-packages/qiime_default_reference/gg_13_8_otus/rep_set/97_otus.fasta";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $option_i = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/otu_table_json.biom";

             $option_o = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/closed_otus.biom";
         }

         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $option_i = $path_out."fasta_files/Processeddata/otus/final_otus_tables/otu_table_json.biom";
            
             $option_o = $path_out."fasta_files/Processeddata/otus/final_otus_tables/closed_otus.biom";
         }

        $jobname = $user."_qiime_To_picrust_2";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/qiimeTopicrust2 $option_i $option_o $option_e";

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



    function phylotype_picrust($user,$project,$path_in,$path_out){


        echo "phylotype_picrust"."\n";

        $path_input_biom = "";
        $path_output_biom = "";

        # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $path_input_biom = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/closed_otus.biom";

             $path_output_biom = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/normalized_otus.biom";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $path_input_biom = $path_out."fasta_files/Processeddata/otus/final_otus_tables/closed_otus.biom";
            
             $path_output_biom = $path_out."fasta_files/Processeddata/otus/final_otus_tables/normalized_otus.biom";
         }

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

    function phylotype_picrust2($user,$project,$path_in,$path_out){
   
        echo "phylotype_picrust2"."\n";

        $normalized_otus = "";
        $metagenome_predictions = "";


         # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $normalized_otus = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/normalized_otus.biom";

             $metagenome_predictions = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/metagenome_predictions.biom";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $normalized_otus = $path_out."fasta_files/Processeddata/otus/final_otus_tables/normalized_otus.biom";
            
             $metagenome_predictions = $path_out."fasta_files/Processeddata/otus/final_otus_tables/metagenome_predictions.biom";
         }

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


    function phylotype_picrust3($user,$project,$path_in,$path_out){

    
        echo "phylotype_picrust3"."\n";

        # $L = Please select level of KEGG pathway  level 1,2 
        $L = $GLOBALS['kegg'];
        $label = "2";

        $metagenome_predictions = "";
        $predicted_metagenomes = "";


        # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $metagenome_predictions = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/metagenome_predictions.biom";

             $predicted_metagenomes = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/predicted_metagenomes.".$L.".biom";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $metagenome_predictions = $path_out."fasta_files/Processeddata/otus/final_otus_tables/metagenome_predictions.biom";
            
             $predicted_metagenomes = $path_out."fasta_files/Processeddata/otus/final_otus_tables/predicted_metagenomes.".$L.".biom";
         }


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


    function biom_to_stamp($user,$project,$path_in,$path_out){


        echo "biom_to_stamp"."\n";

        # $L = Please select level of KEGG pathway  level 1,2 or 3
        $L = $GLOBALS['kegg'];

        $predicted_metagenomes = "";
        $pathways = "";

         # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

             $predicted_metagenomes = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/predicted_metagenomes.".$L.".biom";

             $pathways = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/pathways".$L.".spf";
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

             $predicted_metagenomes = $path_out."fasta_files/Processeddata/otus/final_otus_tables/predicted_metagenomes.".$L.".biom";
            
             $pathways = $path_out."fasta_files/Processeddata/otus/final_otus_tables/pathways".$L.".spf";
         }

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


    function remove_float($user,$project,$path_in,$path_out){
    
            echo "remove_float"."\n";
          
            # $L = Please select level of KEGG pathway  level 1,2 or 3
            $L = $GLOBALS['kegg'];
            $pathways ="";


            # select option (miseq_contain_primer) => folder newfasta_files
            if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                 $pathways = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/pathways".$L.".spf";
            }
            # select option (miseq_without_barcodes)  => folder fasta_files
            else{ 
            
                $pathways = $path_out."fasta_files/Processeddata/otus/final_otus_tables/pathways".$L.".spf";
            }


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



    function stamp($user,$project,$path_in,$path_out){

             echo "stamp"."\n";

             # $L = Please select level of KEGG pathway  level 1,2 or 3
             $L = $GLOBALS['kegg'];
             $pathways = "";
             $myResultsPathway = "";

            # select option (miseq_contain_primer) => folder newfasta_files
            if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                 $pathways = "../".$path_out."newfasta_files/Processeddata/otus/final_otus_tables/pathways".$L.".spf";

                 $myResultsPathway = "../".$path_out."newfasta_files/Processeddata/otus/final_otus_tables/myResultsPathway".$L.".tsv";
            }
            # select option (miseq_without_barcodes)  => folder fasta_files
            else{ 
            
                 $pathways = "../".$path_out."fasta_files/Processeddata/otus/final_otus_tables/pathways".$L.".spf";

                 $myResultsPathway = "../".$path_out."fasta_files/Processeddata/otus/final_otus_tables/myResultsPathway".$L.".tsv";
            }


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


    function plot_STAMP($user,$project,$path_in,$path_out,$sample1,$sample2){

            echo "plot_STAMP"."\n";

            # $L = Please select level of KEGG pathway  level 1,2 or 3
            $L = $GLOBALS['kegg'];
            $myResultsPathway = "";
            $path_to_save = "";

            # select option (miseq_contain_primer) => folder newfasta_files
            if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                 $myResultsPathway = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/myResultsPathway".$L.".tsv";

                 $path_to_save = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/bar_plot_STAMP.png";
            }
            # select option (miseq_without_barcodes)  => folder fasta_files
            else{ 
  
                 $myResultsPathway = $path_out."fasta_files/Processeddata/otus/final_otus_tables/myResultsPathway".$L.".tsv";

                 $path_to_save = $path_out."fasta_files/Processeddata/otus/final_otus_tables/bar_plot_STAMP.png";
            }

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
                  //$loop = false;
                  break;
                }
            }
    }


?>