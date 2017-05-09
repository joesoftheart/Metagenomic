<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Run_owncloud extends CI_Controller {


      public function __construct(){

        parent::__construct();
        $this->load->helper(array('url','path'));
       
        include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");



  
    }


        
        public function index()
        {
             
             $jobname = "test_mo_own1";
             $project = "data_mothur";
             $user = "admin";
             
             echo "Run Owncloud"."<br/>";
    
             // echo exec("qsub -N 'date_test' -cwd -b y /bin/date");
           
             $this->check_file($user,$project);

         
             #make.contigs
               #$make = "make.contigs(file=stability.files, processors=8,inputdir=./owncloud/data/$user/files/data_mothur/data/input/,outputdir=./owncloud/data/$user/files/data_mothur/output/)";
             
               // file_put_contents('owncloud/data/admin/files/data_mothur/data/input/run.batch', $make);
               // echo "Run ".$make."<br/>";


             # Test run qsub mothur batch file
             // $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur run.batch ";

             //   shell_exec($cmd);

             //   $check_qstat = "qstat  -j '$jobname' ";

             //   exec($check_qstat,$output);
            
              
             //   $id_job = "" ;

             //  foreach ($output as $key_var => $value ) {
              
             //        if($key_var == "1"){

             //            $data = explode(":", $value);
             //            $id_job = $data[1];
             //        }
                    
                    
             //  }

             //  echo "Job number => ".$id_job."<br/>";

             //  $check_jobs = exec("qstat -u apache  '$id_job' ");

             //  echo $check_jobs;

        } 

        public function read_log_sungrid(){
            
            $file = file_get_contents(FCPATH.'admin_align_summary.o76');
            $search_for = 'Start';
            $pattern = preg_quote($search_for,'/');

            $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum).*\$/m";
           
                   if(preg_match_all($pattern, $file, $matches)){
                       echo implode("\n", $matches[0]);
                   }
               
            

        } 



        public function remove_logfile_mothur(){
          
           $project = "data_mothur";
           $user = "admin";

            $path_dir = FCPATH."owncloud/data/$user/files/$project/output/";
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
        }


        public function check_file($user,$project){
           
            $path = "owncloud/data/$user/files/$project/data/input/stability.files";

            //$file_list = "fileList.paired.file"; 
            //$stability = "stability.files";

            $path_file = FCPATH."$path";  
           
            # stability.files ==> check oligos
            if(file_exists($path_file)) {

                  $this->check_oligos($user,$project);
            }
            # create stability.files
            else {
               
                   $out_var = $this->run_makefile($user,$project);

                   if($out_var == "0"){

                     //rename($path.$file_list,$path.$stability);
                     echo  "Run makefile complete"."<br/>";
                     $this->check_file($user,$project);

                   }
                       
            }

        }
 


        public function check_oligos($user,$project){
            
           $total_oligo = 0;
         
           $path_dir = FCPATH."owncloud/data/$user/files/$project/data/input/";
            if (is_dir($path_dir)) {
                if ($read = opendir($path_dir)){
                      while (($file_oligo = readdir($read)) !== false) {
                        
                        $allowed =  array('oligo');
                        $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);

                        if(in_array($ext,$allowed)) {

                            $total_oligo +=1;  
                            echo "have ==> filename: ".$file_oligo." is type oligos"."<br/>";
                            $this->makecontigs_oligos_summary($file_oligo,$user,$project);
                        }

                      }
                      
                   closedir($read);
                }
            }

            if($total_oligo == 0){

               $this->makecontig_summary($user,$project);
  
            }

        }


        # make.file  stability.files
        public function run_makefile($user,$project){

          $jobname = $user."_makefile";
      
           #make.file
               $make = "make.file(inputdir=owncloud/data/$user/files/$project/data/input)";

               file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);


               $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      return "0";
                   }
              }  

        }


         # make.contigs oligos remove primer && summary.seqs
         public function makecontigs_oligos_summary($file_oligo,$user,$project){

            $jobname = $user."_oligo";

            $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=4 ,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
           
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);



             $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run makecontigs_oligos_summary complete";
                      
                   }
              }  
   
        }


        # make.contigs && summary.seqs
        public function makecontig_summary($user,$project){
          
           $jobname = $user."_makesummary";

           $cmd ="make.contigs(file=stability.files,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
             summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

           $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run makecontigs_summary complete"."<br/>";
                      
                   }
              }  
   
         
          
        }

       


       # screen.seqs && summary.seqs
         # input maximum ambiguous  , minimum reads length , maximum reads length  

        public function screen_summary($user,$project){

            $jobname = $user."_screen_summary";

            $cmd = "screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
            
            file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
            $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run screen_summary complete"."<br/>";
                      
                   }
              }  
        }

       #  unique.seqs && count.seqs && summary.seqs

        public function unique_count_summary($user,$project){

             $jobname = $user."_unique_count_summary"; 

             $cmd =" unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     summary.seqs(count=stability.trim.contigs.good.count_table ,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
             
              file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run unique_count_summary complete"."<br/>";
                      
                   }
              }  
        }

    

        # align.seqs && summary.seqs
          # select alignment step
        public function align_summary($user,$project){
          $jobname = $user."_align_summary"; 
          
          $cmd = "align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
       
          file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run align_summary complete"."<br/>";
                      
                   }
              }  


        }
        
        

        # screen.seqs = stat , end && summary.seqs
          #input maximum ambiguous , maximum homopolymer , maximum reads length
        public function screen_summary_2($user,$project){
          $jobname = $user."_screen_summary_2";

          $cmd = "screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=8, end=9582, maxambig=8, maxhomop=8, maxlength=260, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  summary.seqs(fasta=current, count=current,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
       
          file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run screen_summary_2 complete"."<br/>";
                      
                   }
              }  

        }

       

        # filter.seqs && unique.seqs && pre.cluster && chimera.vsearch && remove.seqs && summary.seqs
           # input diffs => pre.cluster
        public function filter_unique_cluster_vsearch_remove_summary($user,$project){
            $jobname = $user."_filter_unique_cluster_vsearch_remove_summary";

            $cmd = "filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.seqs(fasta=current, count=current,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
       
           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run filter_unique_cluster_vsearch_remove_summary complete"."<br/>";
                      
                   }
              }  



        }

       

        # Prepare in taxonmy   
        
        # classifly.seqs && remove.lineage && summary.seqs
          # input reference , taxonomy , cutoff
        public function classifly_removelineage_summary($user,$project){

           $jobname = $user."_classifly_removelineage_summary";
           $cmd = "classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  summary.seqs(fasta=current, count=current,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
        
           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run classifly_removelineage_summary complete"."<br/>";
                      
                   }
              }  

        }


        #  && summary.tax
          # input taxon 
        public function summary_tax($user,$project){

          $jobname = $user."_summary_tax";
          $cmd ="summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
                  
        
           file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run summary_tax complete"."<br/>";
                      
                   }
              }  



        }

        

        # system_cp 

            // path file false stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta final.fasta
        public function system_cp($user,$project){

            $jobname = $user."_system_cp";
            $cmd = "system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta owncloud/data/$user/files/$project/output/final.fasta)
                    system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table owncloud/data/$user/files/$project/output/final.count_table)
                    system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy owncloud/data/$user/files/$project/output/final.taxonomy)"; 
        
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run system_cp complete"."<br/>";
                      
                   }
              }  


        }

        /////////////////////////////////////////////////////////////////////////////
        

        # Prepare phylotype analysis 
         
         # phylotype && make.shared && classify.out 

         public function phylotype_makeshared_classifyout($user,$project){
             
             $jobname = $user."_phylotype_makeshared_classifyout";

             $cmd = "phylotype(taxonomy=final.taxonomy,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     make.shared(list=final.tx.list, count=final.count_table, label=1-2-3-4-5-6,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=1-2-3-4-5-6,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
         
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run phylotype_makeshared_classifyout complete"."<br/>";
                      
                   }
              }  
         } 
          
        


        #classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=2) #get taxon



         # count.groups 
         public function count_gruops_shared($user,$project){
            
            $jobname = $user."_count_gruops_shared"; 

            $cmd ="count.groups(shared=final.tx.shared,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
            
            file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run count_gruops_shared complete"."<br/>";
                      
                   }
              }   
         }

         # sub.sample 
          #input size
         public function sub_smple($user,$project){

             $jobname = $user."_sub_smple";
             $cmd = "sub.sample(shared=final.tx.shared, size=5000,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
        
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run sub_smple complete"."<br/>";
                      
                   }
              }   

         }

        # Alpha and beta analysis based phylotype analysis
        
         # collect.single  && rarefaction.single && summary.single 

         public function collect_rarefaction_summary($user,$project){

           $jobname = $user."_collect_rarefaction_summary";

            $cmd = "collect.single(shared=final.tx.shared, calc=chao, freq=100,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    rarefaction.single(shared=final.tx.shared, calc=sobs, freq=100, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.single(shared=final.tx.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon, subsample=5000,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
        
             file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run collect_rarefaction_summary complete"."<br/>";
                      
                   }
              }   

         }

         

          # dist.shared && summary.shared
          public function dist_shared($user,$project){

              $jobname = $user."_dist_shared";

              $cmd = "dist.shared(shared=final.tx.shared, calc=thetayc-jclass-lennon-morisitahorn-braycurtis, subsample=5000,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                      summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=soils1_1-soils2_1-soils3_1-soils4_1, all=T,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
          
              file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run dist_shared complete"."<br/>";
                      
                   }
              }   

          }

         

          # venn 
           public function venn($user,$project){

              $jobname = $user."_venn";

              $cmd = "venn(shared=final.tx.2.subsample.shared, groups=soils1_1-soils2_1-soils3_1-soils4_1,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

              file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
              $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      echo "Run venn complete"."<br/>";
                      
                   }
              }   


           }


      



       




     

}




?>