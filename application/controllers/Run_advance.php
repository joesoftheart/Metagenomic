<?php  

  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Run_advance extends CI_Controller{


    public function __construct(){
      parent::__construct();
      $this->load->helper(array('url','path'));
      $this->load->helper('form');
      $this->load->library('form_validation');

      //$this->load->controller('Run_owncloud');
      include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");

        
    }


    public function get_json(){
      $value = $_REQUEST['data_array'];

       $user = $value[0];
       $id_project = $value[1];
       $maximum_ambiguous = $value[2];
       $maximum_homopolymer = $value[3];
       $minimum_reads_length = $value[4];
       $maximum_reads_length = $value[5];
       $alignment = $value[6];
       $customer = $value[7];
       $diffs = $value[8];
       $classify = $value[9];
       $cutoff  = $value[10];
       $optionsRadios = $value[11];
       $taxon = $value[12];

         
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new \MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
         }


        #aligment
        if($customer != null){
           $alignment = $customer;

        }else if($alignment == "silva"){
            $alignment = "silva.v4.fasta";
        }else if ($alignment == "gg") {
            $alignment = "";
        }else if ($alignment == "rpd") {
            $alignment = "";
        }
    
        

        #$classify
         if($classify == "silva"){
             $classify = 'silva';
         }else if ($classify == "gg") {
             $classify = 'Greengenes';
         }else if ($classify == "rdp") {
             $classify = 'RDP';
         }


        #taxon
        // if($optionsRadios == '1'){
        //    $taxon;
        // }else if($optionsRadios == '0'){
        //    $taxon = "Chloroplast-Mitochondria-Eukaryota-unknown";
        // }

      $user = "admin";
      $project = "data_mothur";
      $taxon = "taxon";

      $path_input = "owncloud/data/admin/files/data_mothur/data/input/";
      $path_out = "owncloud/data/admin/files/data_mothur/data/output/";

      $path_qadvance = "owncloud/data/admin/files/data_mothur/log/";
      
        $jobname = "q_advance";

        # Check type Project 
           
           #Phylotype
           $cmd = "qsub -N '$jobname' -o $path_qadvance -e $path_qadvance -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype.php $user $project $maximum_ambiguous $maximum_homopolymer $minimum_reads_length $maximum_reads_length $alignment $diffs $classify $cutoff $taxon $path_input $path_out";
           #OTU
           //$cmd = "qsub -N '$jobname' -o $path_qadvance -e $path_qadvance -cwd -b y /usr/bin/php -f Scripts/advance_run_otu.php $user $project $maximum_ambiguous $maximum_homopolymer $minimum_reads_length $maximum_reads_length $alignment $diffs $classify $cutoff $taxon $path_input $path_out";
         
         shell_exec($cmd);

        $check_qstat = "qstat  -j '$jobname' ";
        exec($check_qstat,$output);
              $id_job = "" ;
              foreach ($output as $key_var => $value ) {

                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }
              }

         $id_job = trim($id_job);
         $data_job = array($id_job,$jobname,$path_qadvance,$user,$project);
         echo json_encode($data_job);
     


    }
   

    public function check_run(){

                   //$da_job = json_decode($_REQUEST['data_job'],true);
                   $da_job = $_REQUEST['data_job'];
                   $id_job = $da_job[0];
                   $name_job = $da_job[1];
                   $path_job = $da_job[2];
                   $user = $da_job[3];
                   $project = $da_job[4];
                   $check_run = exec("qstat -j $id_job ");

                   if($check_run == false){
                      $up = array(0,$user,$project);
                      echo json_encode($up);

                   }else{
                       
                       $file = FCPATH."$path_job$name_job.o$id_job";
                       $count = 0 ;
                       $myfile = fopen($file,'r') or die ("Unable to open file");
                         while(($lines = fgets($myfile)) !== false){
                            if($lines != "\n"){
                                 $count++;
                            } 
                        }
                       fclose($myfile);

                       $line = file($file);
                       $message = $line[$count-1];
                       if($message == ""){
                          $message = "run queue ".$name_job." ".$id_job;
                       }
                      $up = array($message);
                      echo json_encode($up);
                   }
             

     }

      public function read_count(){
           $da_count = $_REQUEST['data_count'];
           $user = $da_count[1];
           $project = $da_count[2];
          
           # Check type Project 

           #Phylotype
             $file = FCPATH."owncloud/data/$user/files/$project/data/output/final.tx.count.summary";

           #OTU
             //$file = FCPATH."owncloud/data/$user/files/$project/data/output/final.opti_mcc.count.summary";
           
           $data_read_count = array();
           $count = array();

            $myfile = fopen($file,'r') or die ("Unable to open file");
               while(($lines = fgets($myfile)) !== false){
                 
                 $var =  explode("\t", $lines);
                 array_push($data_read_count, $var[0]." : ".$var[1]);
                 array_push($count, $var[1]);   

              }
           fclose($myfile);
           $count_less = min($count);
           array_push($data_read_count, $count_less);

           echo json_encode($data_read_count);
      }





      public function run_sub_sample(){

        

        $data = $_REQUEST['data_sample'];

        $user = $data[0];
        $project = $data[1];
        $size = $data[2];

        $path_input = "owncloud/data/admin/files/data_mothur/data/input/";
        $path_out = "owncloud/data/admin/files/data_mothur/data/output/";

        $user = "admin";
        $project = "data_mothur";

        $path_qadvance = "owncloud/data/admin/files/data_mothur/log/";
      
        $jobname = "q_advance_2";
        # Check type Project 
           
           #Phylotype
             $cmd = "qsub -N '$jobname' -o $path_qadvance -e $path_qadvance -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype2.php $user $project $path_input $path_out $size";
       
           #OTU
            //$cmd = "qsub -N '$jobname' -o $path_qadvance -e $path_qadvance -cwd -b y /usr/bin/php -f Scripts/advance_run_otu2.php $user $project $path_input $path_out $size";
       
        shell_exec($cmd);

        $check_qstat = "qstat  -j '$jobname' ";
        exec($check_qstat,$output);
              $id_job = "" ;
              foreach ($output as $key_var => $value ) {

                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }
              }

         $id_job = trim($id_job);
         echo json_encode($id_job);

      }



      public function check_subsample(){

        $id_job = $_REQUEST['job_sample'];
        $check_run = exec("qstat -j $id_job ");

            if($check_run == false){
                 $up = 0;
                 echo json_encode($up);

            }else{

               $up = 1;
               echo json_encode($up);
            }
             

      }
  
  }

?>