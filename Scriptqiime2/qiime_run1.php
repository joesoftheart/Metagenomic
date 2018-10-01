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
  
  $GLOBALS['check_options'] = $argv[21];
  

  
  #reNamePairedend($user,$project,$path_in,$path_out);
  runPerl($user,$project,$path_in,$path_out);
  
  


  function reNamePairedend($user,$project,$path_in,$path_out){

        $file_fastq = glob($path_in."*.fastq"); 
        foreach ($file_fastq as $key => $file) {
            if(stripos(basename($file),'_R1') !== false){
                  $data  = explode("_R1", basename($file));
                  $file_new = $path_in.$data[0]."_R1.fastq";
                  rename($file, $file_new);
            }else{

                 $data  = explode("_R2", basename($file));
                 $file_new = $path_in.$data[0]."_R2.fastq";
                 rename($file, $file_new);
            }
        } 

      runPerl($user,$project,$path_in,$path_out);
   }

 # 1
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

  # 2
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

    # 3

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

                           split_map($user,$project,$path_in,$path_out);
                      }
                     
                   }
         }   
    }

    # 4
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

    # 5
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

        split_map($user,$project,$path_in,$path_out); 

    }

    # 6
    function read_filter_log($user,$project,$path_in,$path_out){

      $array_sample_out = array();
        $read_filter = "owncloud/data/$user/files/$project/output/fasta_files";
        $file_fasta = glob($read_filter."/*.fasta");

        foreach ($file_fasta as $key => $file) {
              $sample = "out".basename($file);
              array_push($array_sample_out,$sample); 
        } 
       
        return $array_sample_out;
    }

    # 7
     function split_map($user,$project,$path_in,$path_out){

        $data_map = array();
        $out_sample = read_filter_log($user,$project,$path_in,$path_out);

        $file_num_sample = "owncloud/data/$user/files/$project/input/map.txt";
        $myfile =  fopen( $file_num_sample, 'r') or die ("Unable to open file");
      
        $count = 0;
        $line = 0 ;
        while (($value = fgets($myfile)) !== false) {

                $group = explode("\t", $value);
                if($line > 0){
                    $group[4] = $out_sample[$count];
                    $count++;
                    $group_files = implode("\t", $group);
                    array_push($data_map, $group_files);

                }else if($line == 0){

                    $line++;
                    $group_files = implode("\t", $group);
                    array_push($data_map, $group_files);
                }
        }
        fclose($myfile);
        file_put_contents($path_in."map.txt",$data_map);

        # function add_labels()
        add_labels($user,$project,$path_in,$path_out);
     }






    # select option (miseq_without_barcodes)  => folder fasta_files
    # select option (miseq_contain_primer) => folder newfasta_files
  
    # 8
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

   # 9
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
    
  # 10
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

    # 11
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

    # 12
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

    # 13
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

    # 14
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
    
    # 15
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
                      biom_summarize_table2($user,$project,$path_in,$path_out);
                   }
         }   
    }

    # 16
    function biom_summarize_table2($user,$project,$path_in,$path_out){

        echo "biom_summarize_table2"."\n";
    
        $option_i = "";
        
        $option_o = "";

         # select option (miseq_contain_primer) => folder newfasta_files
         if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
            
              $option_i = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
        
              $option_o = $path_out."newfasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_summary.txt";
         
         }
         # select option (miseq_without_barcodes)  => folder fasta_files
         else{ 

            $option_i = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras.biom";
        
              $option_o = $path_out."fasta_files/Processeddata/otus/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_summary.txt";
         }

       
        $jobname = $user."_biom_summarize_table2";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y Scriptqiime2/runqiime8_2 $option_i $option_o";


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


    # 17
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


    # 18
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

     # 19
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

     # 20
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


    # 21
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

   
    # 22
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

    # 23
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

    # 24
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

   # 25
   function anosim($user,$project,$path_in,$path_out){

        if ($GLOBALS['anosim'] !== "none" && $GLOBALS['opt_anosim'] != "none") {

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

    # 26
    function adonis($user,$project,$path_in,$path_out){

        if ($GLOBALS['adonis'] != "none" && $GLOBALS['opt_adonis'] != "none" ) {

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

    # 27
    function permanova($user,$project,$path_in,$path_out){

        if($GLOBALS['permanova'] != "none" && $GLOBALS['opt_permanova'] != "none"){

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

            $options_picrust_stamp =  $GLOBALS['check_options']; 
            if($options_picrust_stamp == "true"){

                qiime_To_picrust_1($user,$project,$path_in,$path_out);
              
             }else{
                 
                keep_file_qiime($user,$project,$path_in,$path_out);
             }
           
            
        }
         
    }

    # 28
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

    # 29
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


    # 30
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


    # 31
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

    # 32
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

    # 33
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

    # 34
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


    # 35
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

    #36
    function plot_STAMP($user,$project,$path_in,$path_out,$sample1,$sample2){

            echo "plot_STAMP"."\n";

            # $L = Please select level of KEGG pathway  level 1,2 or 3
            $L = $GLOBALS['kegg'];
            $myResultsPathway = "";
            $path_to_save = "";

            # Folder Qiime SVG optional_output
            $download_optional_output = "data_report_qiime/$user/$project/Download/optional_output/";

            # select option (miseq_contain_primer) => folder newfasta_files
            if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){

                 $myResultsPathway = $path_out."newfasta_files/Processeddata/otus/final_otus_tables/myResultsPathway".$L.".tsv";

                 $path_to_save = $download_optional_output."bar_plot_STAMP.svg";
            }
            # select option (miseq_without_barcodes)  => folder fasta_files
            else{ 
  
                 $myResultsPathway = $path_out."fasta_files/Processeddata/otus/final_otus_tables/myResultsPathway".$L.".tsv";

                 $path_to_save = $download_optional_output."bar_plot_STAMP.svg";
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
                   $loop = false;

                    # Folder PNG Qiime optional_output
                      $image_png = "data_report_qiime/$user/$project/optional_output/bar_plot_STAMP.png";

                      $cmd_png = '/usr/bin/inkscape -z  '. $path_to_save.' -e '.$image_png;
                      exec($cmd_png); 

                    keep_file_qiime($user,$project,$path_in,$path_out);
                }
            }
    }


    # 37
    function keep_file_qiime($user,$project,$path_in,$path_out){

        echo "keep_file_qiime"."\n";
        
        $path_all_file = null;
        $min = $GLOBALS['min'];
      
        # Folder Qiime Report
        $file_report = "data_report_qiime/$user/$project/file_report/";
      

        # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
              $path_all_file = $path_out."newfasta_files/Processeddata/otus/";
        }

        # select option (miseq_without_barcodes)  => folder fasta_files
        else{ 
              $path_all_file = $path_out."fasta_files/Processeddata/otus/";
        }

      
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

        #4 copy otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt
            $path_otu_table = $path_all_file."otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
            $copy_otu_table = $path_otu_table;
            $past_otu_table = $file_report."otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";
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
             $path_adonis = $path_all_file."cdotu/bdiv_even".$min."/adonis".$GLOBALS['core_group'].$GLOBALS['opt_adonis']."ed/adonis_results.txt";
             $copy_adonis = $path_adonis;
             $past_adonis = $file_report."adonis_results.txt";

             if(file_exists($path_adonis)){
                copy($copy_adonis,$past_adonis);
             }  


        #11 copy anosim_results.txt
             $path_anosim = $path_all_file."cdotu/bdiv_even".$min. "/anosim".$GLOBALS['core_group'].$GLOBALS['opt_anosim']."ed/anosim_results.txt";
             $copy_anosim = $path_anosim;
             $past_anosim = $file_report."anosim_results.txt";

             if(file_exists($path_anosim)){
                copy($copy_anosim,$past_anosim);
             }

        #12 copy permanova_results.txt
             $path_permanova = $path_all_file."cdotu/bdiv_even".$min. "/permanova".$GLOBALS['core_group'].$GLOBALS['opt_permanova']."ed/permanova_results.txt";
             $copy_permanova = $path_permanova;
             $past_permanova = $file_report."permanova_results.txt";
             
             if(file_exists($path_permanova)){
                 copy($copy_permanova,$past_permanova);
             }

        #13 copy myResultsPathwayL2.tsv
            $path_tsv = $path_all_file."final_otus_tables/myResultsPathway".$GLOBALS['kegg'].".tsv";
            $copy_tsv = $path_tsv;
            $past_tsv = $file_report."myResultsPathway".$GLOBALS['kegg'].".tsv";
            
             if(file_exists($path_tsv)){
                copy($copy_tsv,$past_tsv);
             }


        #14 copy otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_summary.txt
            $path_chimeras_summary = $path_all_file."otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_summary.txt";   
            $copy_chimeras_summary = $path_chimeras_summary;
            $past_chimeras_summary = $file_report."otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_summary.txt";
            copy($copy_chimeras_summary,$past_chimeras_summary);


        #15 copy read_filter.log
            $path_read_filter = $path_out."stitched_reads_filter/read_filter.log";   
            $copy_read_filter = $path_read_filter;
            $past_read_filter = $file_report."read_filter.log";
            copy($copy_read_filter,$past_read_filter);


        #16 copy sampleName.txt
            $path_sampleName = $path_in."sampleName.txt";   
            $copy_sampleName = $path_sampleName;
            $past_sampleName = $file_report."sampleName.txt";
            copy($copy_sampleName,$past_sampleName);


        # create file min.txt
           $L = $GLOBALS['kegg'];
           $file_min_txt = "data_report_qiime/$user/$project/file_report/min.txt";
           $data_min_txt = $min."\t".$L;

           file_put_contents($file_min_txt,$data_min_txt);

        plot_heatmap($user,$project,$path_in,$path_out);


    }


    function plot_heatmap($user,$project,$path_in,$path_out){

         #echo "plot_heatmap"."\n";

       # Folder Qiime Report
         $file_report = "data_report_qiime/$user/$project/file_report/";

      # Folder Qiime taxonomy_classification
         $download_taxonomy_classification = "data_report_qiime/$user/$project/Download/taxonomy_classification/";

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


         $image_png = "data_report_qiime/$user/$project/taxonomy_classification/heatmap.png";
         $cmd_png = '/usr/bin/inkscape -z  '.$option_o_2.' -e '.$image_png;
         exec($cmd_png); 

         plot_bar($user,$project,$path_in,$path_out,$count_sample);
    }





    function plot_bar($user,$project,$path_in,$path_out,$count_sample){

         #echo "plot_bar"."\n";

         # Folder Qiime Report
         $file_report = "data_report_qiime/$user/$project/file_report/";

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
        $taxonomy_classification = "data_report_qiime/$user/$project/taxonomy_classification/";
   
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
        $download_taxonomy_classification = "data_report_qiime/$user/$project/Download/taxonomy_classification/";
   
        $path_svg = "data_report_qiime/$user/$project/file_report/taxaonlyphylum/charts/*.svg";
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

        #echo "plot_rarefaction"."\n";

         # Folder Qiime Report
         $file_report = "data_report_qiime/$user/$project/file_report/";

         # Folder SVG Qiime alpha_diversity_analysis
         $dowload_alpha_diversity = "data_report_qiime/$user/$project/Download/alpha_diversity_analysis/";


         $option_i = $file_report."observed_species.txt";
         $option_o = $file_report."outaverage.txt";
          
         $cmd = "Scriptqiime2/Rscript/getaveragerarefaction.sh $option_i $option_o";
         exec($cmd);

         $txt_1 = $file_report."outaverage.txt";
         $svg_2 = $dowload_alpha_diversity."Rarefactionqiime.svg";

         $cmd2 = "/usr/bin/Rscript Scriptqiime2/Rscript/plot_Rarefaction_qiime.R $txt_1 $svg_2";
         exec($cmd2);


         # Folder PNG Qiime alpha_diversity_analysis
         $image_png = "data_report_qiime/$user/$project/alpha_diversity_analysis/Rarefactionqiime.png";

         $cmd_png = '/usr/bin/inkscape -z  '.$svg_2.' -e '.$image_png;
         exec($cmd_png); 


         copy_graph_chao_shannon($user,$project,$path_in,$path_out);

    }


    function copy_graph_chao_shannon($user,$project,$path_in,$path_out){

        #echo "copy_graph_chao_shannon"."\n";
        
        $path_all_file = null;
        $min = $GLOBALS['min'];
      
        # Folder Qiime Report
        $file_report = "data_report_qiime/$user/$project/file_report/";
      
        # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
              $path_all_file = $path_out."newfasta_files/Processeddata/otus/";
        }
        # select option (miseq_without_barcodes)  => folder fasta_files
        else{ 
              $path_all_file = $path_out."fasta_files/Processeddata/otus/";
        }


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

        #echo "convert_img_chao_shannon"."\n";

        # Folder Qiime Report
        $file_report = "data_report_qiime/$user/$project/file_report/";

        #boxplots_chao.pdf
          $pdf_chao = $file_report."boxplots_chao.pdf";

          # svg
          $path_svg_chao = "data_report_qiime/$user/$project/Download/alpha_diversity_analysis/boxplots_chao.svg";
          $cmd_svg_chao ="/usr/bin/inkscape -z -f ".$pdf_chao." -l ".$path_svg_chao;
          exec($cmd_svg_chao);

          # png
          $path_png_chao = "data_report_qiime/$user/$project/alpha_diversity_analysis/boxplots_chao.png";
          $cmd_png_chao = '/usr/bin/inkscape -z  '.$pdf_chao.' -e '.$path_png_chao;
          exec($cmd_png_chao);


        #boxplots_shannon.pdf
          $pdf_shannon = $file_report."boxplots_shannon.pdf";
          $path_svg_shannon = "data_report_qiime/$user/$project/Download/alpha_diversity_analysis/boxplots_shannon.svg";
         
          # svg
          $cmd_svg_shannon ="/usr/bin/inkscape -z -f ".$pdf_chao." -l ".$path_svg_shannon;
          exec($cmd_svg_shannon);

          # png
          $path_png_shannon = "data_report_qiime/$user/$project/alpha_diversity_analysis/boxplots_shannon.png";
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

         #echo "copy_pcoa"."\n";

         $path_all_file = null;

         $min = $GLOBALS['min'];
         
        # Folder Qiime PNG beta_diversity_analysis
        $beta_diversity_analysis = "data_report_qiime/$user/$project/beta_diversity_analysis/";

        # Folder Qiime SVG beta_diversity_analysis
        $download_beta_diversity_analysis = "data_report_qiime/$user/$project/Download/beta_diversity_analysis/";

      
        # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
              $path_all_file = $path_out."newfasta_files/Processeddata/otus/";
        }
        # select option (miseq_without_barcodes)  => folder fasta_files
        else{ 
              $path_all_file = $path_out."fasta_files/Processeddata/otus/";
        }


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

         #echo "copy_folder_cdotu"."\n";

        $path_all_file = null;
        # Folder cdotu
        $folder_cdotu = "data_report_qiime/$user/$project/cdotu/";

        # select option (miseq_contain_primer) => folder newfasta_files
        if($GLOBALS['project_platform_type'] == "miseq_contain_primer"){
            $path_all_file = $path_out."newfasta_files/Processeddata/otus/cdotu/";
        }
        # select option (miseq_without_barcodes)  => folder fasta_files
        else{ 
            $path_all_file = $path_out."fasta_files/Processeddata/otus/cdotu/";
        }

        # copy folder cdotu 
        
        $cmd = "/bin/cp -r ".$path_all_file." ".$folder_cdotu;
        exec($cmd);

     }




?>