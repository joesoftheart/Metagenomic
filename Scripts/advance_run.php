<?php 
        include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

    

         $user = $argv[1];
         $project = $argv[2];
       
         
         $GLOBALS['maximum_ambiguous'] = $argv[3];
         $GLOBALS['maximum_homopolymer'] = $argv[4];
         $GLOBALS['minimum_reads_length'] = $argv[5];
         $GLOBALS['maximum_reads_length'] = $argv[6];
         $GLOBALS['alignment'] = $argv[7];
         $GLOBALS['diffs'] = $argv[8];
         $GLOBALS['classify'] = $argv[9];
         $GLOBALS['cutoff'] = $argv[10];
         $GLOBALS['taxon'] = $argv[11]; 
         
         $path_in = $argv[12];
         $path_out = $argv[13];

        


         if($user != "" && $project != "" && $argv[3] != "" && $argv[4] != "" && $argv[5] != "" && $argv[6] != "" && $argv[7] != "" && $argv[8] != "" && $argv[9] != "" && $argv[10] != "" && $argv[11] != "" && $argv[12] != "" && $argv[13] != ""){
            echo "check_parameter "."\n";
            check_file($user,$project,$path_in,$path_out);
          
         }else{

          echo "user : ".$user."\n";
          echo "project : ".$project."\n";
          echo "ambiguous : ".$GLOBALS['maximum_ambiguous']."\n";
          echo "homopolymer : ".$GLOBALS['maximum_homopolymer']."\n";
          echo "minimum_reads : ".$GLOBALS['minimum_reads_length']."\n";
          echo "maximum_reads : ".$GLOBALS['maximum_reads_length']."\n";
          echo "alignment : ".$GLOBALS['alignment']."\n";
          echo "diffs : ".$GLOBALS['diffs']."\n";
          echo "classify : ".$GLOBALS['classify']."\n";
          echo "cutoff : ".$GLOBALS['cutoff']."\n";
          echo "taxon : ".$GLOBALS['taxon']."\n";
          echo "path_in : ".$path_in."\n";
          echo "path_out : ".$path_out."\n";

         }
         
         
 

         function check_file($user,$project,$path_in,$path_out){

            echo "check_file "."\n";
           
            $path_file = $path_in."stability.files";

            # stability.files ==> check oligos
            if(file_exists($path_file)) {
                  
                  check_oligos($user,$project,$path_in,$path_out);

            }
            # create stability.files
            else {

                   run_makefile($user,$project,$path_in,$path_out);
          
            }
        }




         function check_oligos($user,$project,$path_in,$path_out){
            
           echo "check_file_oligos "."\n";
           $total_oligo = 0;
         
           $path_dir = $path_in;
            if (is_dir($path_dir)) {
                if ($read = opendir($path_dir)){
                      while (($file_oligo = readdir($read)) !== false) {
                        
                        $allowed =  array('oligo');
                        $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);

                        if(in_array($ext,$allowed)) {
                          
                            $total_oligo +=1;  
                            makecontigs_oligos_summary($file_oligo,$user,$project,$path_in,$path_out);
                        }
                      }
                      
                   closedir($read);
                }
            }

            if($total_oligo == 0){
              makecontig_summary($user,$project,$path_in,$path_out);
            }

        }


        # make.file  stability.files
        function run_makefile($user,$project,$path_in,$path_out){
          
           echo "Run_makefile "."\n";
          $jobname = $user."_makefile";

           #make.file
               $make = "make.file(inputdir=$path_in,outputdir=$path_in)";

               file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);


               $cmd = "qsub  -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch";

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

                      remove_logfile_mothur($path_in);
                      check_file($user,$project,$path_in,$path_out);

                      break;
                   }
              }
       }


         # make.contigs oligos remove primer && summary.seqs
         function makecontigs_oligos_summary($file_oligo,$user,$project,$path_in,$path_out){

            echo "Run makecontigs_oligos_summary  "."\n";
            $jobname = $user."_oligo";

            $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=4 ,inputdir=$path_in,outputdir=$path_out)
                    summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path_in,outputdir=$path_out)";
           
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);



             $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -j '$id_job' ");

                   if($check_run == false){
                      
                      screen_summary($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }    
        }


        # make.contigs && summary.seqs
        function makecontig_summary($user,$project,$path_in,$path_out){
          
           echo "Run makecontigs_summary "."\n";
           $jobname = $user."_makesummary";

           $cmd ="make.contigs(file=stability.files,processors=8,inputdir=$path_in,outputdir=$path_out)
                 summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path_in,outputdir=$path_out)";

           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

           $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                      
                      screen_summary($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }      
        }


       //////////////////////////////////////////////////
        # screen.seqs && summary.seqs
         #  $maximum_ambiguous  
         #  $minimum_reads_length 
         #  $maximum_reads_length 

         function screen_summary($user,$project,$path_in,$path_out){

            echo "Run screen_summary "."\n";
            $jobname = $user."_screen_summary";

            $cmd = "screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=".$GLOBALS['maximum_ambiguous'].", minlength=".$GLOBALS['minimum_reads_length']." , maxlength=".$GLOBALS['maximum_reads_length'].", processors=8,inputdir=$path_in,outputdir=$path_out)
                    summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path_in,outputdir=$path_out)";
            
            file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
            $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                   
                      unique_count_summary($user,$project,$path_in,$path_out);

                      break;
                      
                   }
              }  
        }



      #  unique.seqs && count.seqs && summary.seqs

         function unique_count_summary($user,$project,$path_in,$path_out){

             echo "Run unique_count_summary "."\n";
             $jobname = $user."_unique_count_summary"; 

             $cmd =" unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path_in,outputdir=$path_out)
                     count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path_in,outputdir=$path_out)
                     summary.seqs(count=stability.trim.contigs.good.count_table ,inputdir=$path_in,outputdir=$path_out)";
             
              file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

               shell_exec($cmd);
               $check_qstat = "qstat -j $jobname ";
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
                      
                      
                      align_summary($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }  
        }




         //////////////////////////////////////////
          # align.seqs && summary.seqs
            # input select alignment step

         function align_summary($user,$project,$path_in,$path_out){
          $jobname = $user."_align_summary"; 
          
          $cmd = "align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=".$GLOBALS['alignment'].", processors=8,inputdir=$path_in,outputdir=$path_out)
                  summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=$path_in,outputdir=$path_out)";
       
          file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
              $id_j = trim($id_job);
              $log = "owncloud/data/$user/files/$project/log/".$jobname.".o".$id_j;
              $loop = true;
              while ($loop) {

                   $check_run = exec("qstat -j  $id_job ");

                   if($check_run == false){
                      
                      echo "Run align_summary "."\n";
                      //echo $log."\n";
                      read_log_sungrid($user,$project,$path_in,$path_out,$log);
                      break;
                      
                   }
              }  


        }





        function read_log_sungrid($user,$project,$path_in,$path_out,$log){
            
            $file = file_get_contents($log);
            $search_for = 'Start';
            $pattern = preg_quote($search_for,'/');
            
            $start_array = array();
            $end_array   = array();
             
            $start = 0;
            $end =0; 

            $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum).*\$/m";
           
                   if(preg_match_all($pattern, $file, $matches)){
                       $val = implode("\n", $matches[0]);
                       $sum = explode("\n", $val);

                       foreach ($sum as $key => $value) {
                           //echo  $value ."<br/>";
                            if($key >= "1"){
                                 $va_ex = explode(":", $value);
                                 $va_ex2 = explode("\t", trim($va_ex[1]));
                                  array_push($start_array,$va_ex2[0]);
                                  array_push($end_array,$va_ex2[1]);
                            }
                        }   
                    }

                    #start
                    $count_start = array_count_values($start_array);
                    $start_max = max($count_start);
                    $start_min = min($count_start);

                    #end
                    $count_end = array_count_values($end_array);
                    $end_max = max($count_end);
                    $end_min = min($count_end);


                     if(($start_min == $start_max) || ($end_min == $end_max)){

                        foreach ($sum as $key => $value) {
                           echo  $value ."\n";     
                        }   

                           
                     }elseif (($start_min != $start_max) && ($end_min != $end_max) ) {
                          #start
                          foreach ($count_start as $key_start => $value_start) {
                            if($start_max == $value_start){
                                $start = $key_start;
                              }
                          }
                         #end
                         foreach ($count_end as $key_end => $value_end) {
                             if($end_max == $value_end){
                                 $end = $key_end;
                               }
                         } 
                       echo "Read_log_sungrid"."\n";
                       screen_summary_2($user,$project,$path_in,$path_out,$start,$end);    
                    }

        } 




         ///////////////////////////////////////
         # Start  End 

        # screen.seqs = stat , end && summary.seqs
          # input maximum ambiguous , maximum homopolymer , maximum reads length

         function screen_summary_2($user,$project,$path_in,$path_out,$start,$end){

          echo "Run screen_summary_2  "."\n";
          $jobname = $user."_screen_summary_2";

          $cmd = "screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=$start, end=$end, maxambig=".$GLOBALS['maximum_ambiguous'].", maxhomop=".$GLOBALS['maximum_homopolymer'].", maxlength=".$GLOBALS['maximum_reads_length'].", processors=8,inputdir=$path_in,outputdir=$path_out)
                  summary.seqs(fasta=current, count=current,inputdir=$path_in,outputdir=$path_out)";
       
          file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                   
                      
                      filter_unique_cluster_vsearch_remove_summary($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }  

        }


        ///////////////////////////////////////
        # filter.seqs && unique.seqs && pre.cluster && chimera.vsearch && remove.seqs && summary.seqs
           
           # input diffs => pre.cluster

         function filter_unique_cluster_vsearch_remove_summary($user,$project,$path_in,$path_out){
            
            echo "Run filter_unique_cluster_vsearch_remove_summary  "."\n";
            $jobname = $user."_filter_unique_cluster_vsearch_remove_summary";

            $cmd = "filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=$path_in,outputdir=$path_out)
                    unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=$path_in,outputdir=$path_out)
                    pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=".$GLOBALS['diffs'].",inputdir=$path_in,outputdir=$path_out)
                    chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=$path_in,outputdir=$path_out)
                    remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=$path_in,outputdir=$path_out)
                    summary.seqs(fasta=current, count=current,inputdir=$path_in,outputdir=$path_out)";
       
           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                     
                      classifly_removelineage_summary($user,$project,$path_in,$path_out);
                      break;
                   }
              }  

        }



        //////////////////////////////////////////////
        # Prepare in taxonmy   
        
        # classifly.seqs && remove.lineage && summary.seqs
          # input reference , taxonomy , cutoff
         function classifly_removelineage_summary($user,$project,$path_in,$path_out){

           echo "Run classifly_removelineage_summary "."\n";
           $jobname = $user."_classifly_removelineage_summary";
           $cmd = "classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=".$GLOBALS['cutoff'].", processors=8,inputdir=$path_in,outputdir=$path_out)
                  remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=&path_in,outputdir=$path_out)
                  summary.seqs(fasta=current, count=current,inputdir=$path_in,outputdir=$path_out)";
        
           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                      
                      summary_tax($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }  

        }


        #  && summary.tax
          
         function summary_tax($user,$project,$path_in,$path_out){

          echo "Run summary_tax "."\n";
          $jobname = $user."_summary_tax";
          $cmd ="summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=$path_in,outputdir=$path_out)";
                  
        
           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                     
                      
                      system_cp($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }  

        }


        # system_cp 

            // path file false stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta final.fasta
        function system_cp($user,$project,$path_in,$path_out){

            echo "Run system_cp "."\n";
            $jobname = $user."_system_cp";
            $cmd = "system(cp ".$path_out."/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta ".$path_out."/final.fasta ,outputdir=$path_out)
                    system(cp ".$path_out."/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table ".$path_out."/final.count_table ,outputdir=$path_out)
                    system(cp ".$path_out."/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy ".$path_out."/final.taxonomy ,outputdir=$path_out)"; 
        
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";

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
                      
                      dist_cluster_shared($user,$project,$path_in,$path_out);
                      break;
                      
                   }
              }  


        }

       # OTUs Analysis

        function dist_cluster_shared($user,$project,$path_in,$path_out){

          echo "Run dist_cluster_shared "."\n";
          $jobname = $user."_dist_cluster_shared";
          $cmd = " dist.seqs(fasta=final.fasta, cutoff=0.21, processors=8,inputdir=$path_in,outputdir=$path_out)
          cluster(column=final.dist, count=final.count_table, method=opti, cutoff=0.03,inputdir=$path_in,outputdir=$path_out)
          make.shared(list=final.opti_mcc.list, count=final.count_table, label=0.03,inputdir=$path_in,outputdir=$path_out) ";
          
          file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
          
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
                      
                      
                      classify_count($user,$project,$path_in,$path_out);

                      break;
                      
                   }
              }  
         
        }


        function classify_count ($user,$project,$path_in,$path_out){

          echo "Run classify_count "."\n";
          $jobname = $user."_classify_count";
          $cmd = "classify.otu(list=final.opti_mcc.list, count=final.count_table, taxonomy=final.taxonomy, label=0.03,inputdir=$path_in,outputdir=$path_out)
          count.groups(shared=final.opti_mcc.shared,inputdir=$path_in,outputdir=$path_out)";
          
          file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
            $cmd = "qsub -N '$jobname' -o owncloud/data/$user/files/$project/log  -cwd -j y -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
          
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
                      
                      remove_logfile_mothur($path_out);

                      break;
                      
                   }
              }  

        }


         function remove_logfile_mothur($path_out){ 
            
            $path_dir = $path_out;
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






