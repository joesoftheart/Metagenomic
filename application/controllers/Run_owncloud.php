<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Run_owncloud extends CI_Controller {


      public function __construct(){

        parent::__construct();
        $this->load->helper(array('url','path'));
       
        include (APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");



  
    }


        
        public function index()
        {
             
//             $jobname = "test_mo_own1";
//             $project = "data_mothur";
//             $user = "admin";

            $jobname = "test_mo_own1";
            $project = "SAMPLE-WES1053";
            $user = "joesoftheart";
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


        public function check_file($user,$project){
           
            $path = "owncloud/data/$user/files/$project/data/input/";

            $file_list = "fileList.paired.file"; 
            $stability = "stability.files";

            $path_file = FCPATH."$path";  
           
            #stability.files
            if(file_exists($path_file.$stability)) {

                  $this->check_oligos($user,$project);
            }
            #fileList.paired.file
            else {
               
                   $out_var = $this->run_makefile($user,$project);

                   if($out_var == "0"){
                     rename($path.$file_list,$path.$stability);
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
                            $this->make_contigs_oligos($file_oligo,$user,$project);
                        }

                      }
                      
                   closedir($read);
                }
            }

            if($total_oligo == 0){

               $this->makecontig_summary($user,$project);
  
            }

        }


        # make.file
        public function run_makefile($user,$project){

          $jobname = $user."_makefile";

         
           #make.file
               $make = "make.file(inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/data/input)";

               file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);


               $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch";

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
                  echo "in loop";

                   $check_run = exec("qstat -u apache  '$id_job' ");

                   if($check_run == false){
                      $loop = false;
                      return "0";
                   }
              }

        }



         # make.contigs remove primer
         public function make_contigs_oligos($file_oligo,$user,$project){

            $jobname = $user."_oligo";

            $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=4 ,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";
           
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
                      echo "Run make contigs oligos complete";
                      $this->summary_seqs($user,$project);
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
                      echo "Run make.contigs && summary.seqs complete"."<br/>";
                      
                   }
              }  
   
         
          
        }

        # summary.seqs
        public function summary_seqs($user,$project){

           $jobname = $user."_summary";

           $cmd = "summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";


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
                      echo "Run summary.seqs complete"."<br/>";
                      
                   }
              }  


        }






       




     

}




?>