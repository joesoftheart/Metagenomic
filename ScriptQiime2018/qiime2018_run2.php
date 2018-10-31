<?php

    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");
 
    $user = $argv[1];
    $project = $argv[2];
    $path_in = $argv[3];
    $path_out = $argv[4];
    $GLOBALS['path_log'] = $argv[5];

    $GLOBALS['check_options'] = $argv[6];
    $GLOBALS['subsample'] = $argv[7];
    $GLOBALS['core_group'] = $argv[8];
    $GLOBALS['permanova'] = $argv[9];
    $GLOBALS['opt_permanova'] = $argv[10];
    $GLOBALS['kegg'] = $argv[11];
    $GLOBALS['sample_comparison'] = $argv[12];
    $GLOBALS['statistical_test'] = $argv[13];
    $GLOBALS['ci_method'] = $argv[14];
    $GLOBALS['p_value'] = $argv[15];
    $GLOBALS['reference_sequences'] = $argv[16];
    $GLOBALS['taxonomic_classifier'] = $argv[17];



    
    copyCheckingChimera4($user,$project,$path_in,$path_out);

    #cp checkingChimera/4_* ./
    function copyCheckingChimera4($user,$project,$path_in,$path_out){

     	$path_folder = "owncloud/data/$user/files/$project/output/checkingChimera/";
     	$file_4 = glob($path_folder."4_*");
        
        foreach ($file_4 as  $file) {
            $base_name = basename($file);
            $paste_file = $path_out.$base_name;
            copy($file,$paste_file);   
        }
        generate_tree_phylogenetic($user,$project,$path_in,$path_out); 
    }


    function generate_tree_phylogenetic($user,$project,$path_in,$path_out){

    	echo "generate_tree_phylogenetic"."\n";

        $jobname = $user."_generate_tree_phylogenetic";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/6generate_tree_phylogenetic $path_out";

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
                      alpha_beta_diversity($user,$project,$path_in,$path_out);
                   }
         }   
    }


   

    function alpha_beta_diversity($user,$project,$path_in,$path_out){

    	echo "alpha_beta_diversity"."\n";

    	$num_minimun = $GLOBALS['subsample'];
    	$group = $GLOBALS['core_group'];

        $jobname = $user."_alpha_beta_diversity";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/7alpha_beta_diversity $path_in $path_out $num_minimun $group";

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

				    	if($GLOBALS['permanova'] != "none"){

				    		permanova($user,$project,$path_in,$path_out);

				    	}else{

				    		taxonomic_analysis($user,$project,$path_in,$path_out);	
				    	}  
                   }
         }   
    }



    function permanova($user,$project,$path_in,$path_out){

    	echo "permanova"."\n";

        $type = "";
        $group = $GLOBALS['core_group'];
    	$opt_permanova = $GLOBALS['opt_permanova'];

    	if($opt_permanova == "weight"){
    		$type = "weighted";
    	}else if($opt_permanova == "unweight"){
    		$type = "unweighted";
    	}

        $jobname = $user."_permanova";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/8permanova $path_in $path_out $group $type";

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
                      taxonomic_analysis($user,$project,$path_in,$path_out);
                   }
         }   
    }


    function taxonomic_analysis($user,$project,$path_in,$path_out){

    	echo "taxonomic_analysis"."\n";

        $taxonomic_classifier = $path_in.$GLOBALS['taxonomic_classifier'];

        $jobname = $user."_taxonomic_analysis";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/9taxonomic_analysis $path_in $path_out $taxonomic_classifier";

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
                     }  
                }
         }   
    }


    function qiime_To_picrust_1($user,$project,$path_in,$path_out){

        echo "qiime_To_picrust_1"."\n";

        $option_i = $path_out."exported-feature-table/feature-table.biom";
        $option_o = $path_out."exported-feature-table/otu_table_json.biom";

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

        $option_i = $path_out."exported-feature-table/otu_table_json.biom";
        $option_o = $path_out."exported-feature-table/closed_otus.biom";

        $option_e ="/home/aum/anaconda/lib/python2.7/site-packages/qiime_default_reference/gg_13_8_otus/rep_set/97_otus.fasta";

       
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

        $path_input_biom = $path_out."exported-feature-table/closed_otus.biom";
        $path_output_biom = $path_out."exported-feature-table/normalized_otus.biom";

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

        $normalized_otus = $path_out."exported-feature-table/normalized_otus.biom";
        $metagenome_predictions =  $path_out."exported-feature-table/metagenome_predictions.biom";

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

        $metagenome_predictions = $path_out."exported-feature-table/metagenome_predictions.biom";

        $predicted_metagenomes = $path_out."exported-feature-table/predicted_metagenomes.".$L.".biom";

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

        $predicted_metagenomes = $path_out."exported-feature-table/predicted_metagenomes.".$L.".biom";

        $pathways = $path_out."exported-feature-table/pathways".$L.".spf";

      
        
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
             $pathways = $path_out."exported-feature-table/pathways".$L.".spf";

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
            $pathways = "../".$path_out."exported-feature-table/pathways".$L.".spf";

            $myResultsPathway = "../".$path_out."exported-feature-table/myResultsPathway".$L.".tsv";


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


             # Folder Qiime SVG optional_output
            $download_optional_output = "data_report_qiime2/$user/$project/Download/optional_output/";


            # $L = Please select level of KEGG pathway  level 1,2 or 3
            $L = $GLOBALS['kegg'];

            $myResultsPathway = $path_out."exported-feature-table/myResultsPathway".$L.".tsv";

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
                      $image_png = "data_report_qiime2/$user/$project/optional_output/bar_plot_STAMP.png";

                      $cmd_png = '/usr/bin/inkscape -z  '. $path_to_save.' -e '.$image_png;
                      exec($cmd_png); 

                }
            }
    }



    function extractQiime2Html($user,$project,$path_in,$path_out){


    	echo "extractQiime2Html"."\n";

        $jobname = $user."_extractQiime2Html";
        $log = $GLOBALS['path_log'];

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/extractQiime2 $path_out";

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

                    $path_folder_outHTML = $path_out."outHTML";
    				$path_past = "data_report_qiime2/$user/$project/";
    				$cmd2 = "/bin/cp -r ".$path_folder_outHTML." ".$path_past;
        			exec($cmd2);    
                }
         }   
    }



    function export_shannon($user,$project,$path_in,$path_out){

        echo "export_shannon"."\n";

        $jobname = $user."_export_shannon";
        $log = $GLOBALS['path_log'];

        $inputfile = $path_out."diversityAnalysisResults/shannon_vector.qza";

        $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y ScriptQiime2018/10export_shannon $inputfile $path_out";

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