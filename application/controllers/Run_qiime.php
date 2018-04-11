<?php
   
   defined('BASEPATH') OR exit('No direct script access allowed');
   /**
   * 
   */
   class Run_qiime extends CI_Controller{
   	
   	public function __construct(){

   		parent::__construct();
   		$this->load->helper(array('url','path','file','date'));
      	$this->load->helper('form');
      	$this->load->library('form_validation');
  

      	include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
   	}



    # Run mothur Preprocess
    public function run_qiime1($user,$project){

        
          # Query data Project By ID
        //   $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        //     foreach ($array_project as $r) {
          
        //         $project = $r['project_name'];
        //         $project_analysis = $r['project_analysis'];
        //         $project_data = basename($r['project_path']);
        //         $project_platform_sam = ($r['project_platform_sam']);
        //         $project_platform_type = ($r['project_platform_type']); 
        //    }

         

           # Set Path => input ,output ,log
            $path_input = "owncloud/data/$user/files/$project/input/";
            $path_out = "owncloud/data/$user/files/$project/output/";
            $path_log = "owncloud/data/$user/files/$project/log/";

        
           # create advance.batch
            // $file_batch = FCPATH."$path_input"."advance.batch";
            // if(!file_exists($file_batch)) {
            //     file_put_contents($file_batch, "No command !" );

            // }
           # create folder log
            $folder_log = FCPATH."$path_log";   
            if (!file_exists($folder_log)) {
                   mkdir($folder_log, 0777, true);
            }

           $jobname = $user.'-'.$project.'-'.'qiime1';

           $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scriptqiime2/qiime_run1.php $user $project $path_input $path_out $path_log";

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
           echo "Job ID : ".$id_job;

      
         
    }

    # Check Run mothur Preprocess
    // public function check_run_mothur(){

    //       $da_job = $_REQUEST['data_job'];
    //       $id_job = $da_job[0];
    //       $id_project = $da_job[1];

    //       $name_job = "";
    //       $path_job = "";
    //       #Query data status-process
    //       $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
    //       foreach($array_status as $r) {
    //             $name_job = $r['job_name'];
    //             $path_job = $r['path_log'];
    //       }

    //       $check_run = exec("qstat -j $id_job ");
    //       if($check_run == false){
    //           echo json_encode(array(0,$id_project));

    //       }else{
    //            $file = FCPATH."$path_job$name_job.o$id_job";
    //            $count = 0 ;
    //            $myfile = fopen($file,'r') or die ("Unable to open file");
    //             while(($lines = fgets($myfile)) !== false){
    //                 if($lines != "\n"){
    //                     $count++;
    //                 } 
    //             }
    //            fclose($myfile);

    //             $line = file($file);
    //             $message = $line[$count];
    //             if($message == ""){
    //                 $message = "Run Qiime";
    //             }
    //             if($count != 0){
    //                 $percent = (($count/16)*100);
    //                 $percent_round = round($percent,0);
    //             }
    //         $up = array($message,$percent_round);
    //         echo json_encode($up);
    //      }
    // }


   # insert value mongo
    public function insert_status($data){
        # insert data status-process
        $this->mongo_db->insert('status_process', $data);
    }
  
   # update value mongo
    public function update_status($id_project,$data){
         # update data status-process
         $this->mongo_db->where(array('project_id'=> $id_project))->set($data)->update('status_process');    
    }



    
    # check file map.txt in directory Projects
    public function check_map_file(){

       $user = $this->uri->segment(3);
       $id_project = $this->uri->segment(4);

       $project = "";

       # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                #$project = $r['project_name']; 
                $project = basename($r['project_path']);        
         }
      $path_input = "owncloud/data/$user/files/$project/input/map.txt";
      $path_file = FCPATH."$path_input";

            if(file_exists($path_file)) {
              $output = $this->validate_mapping_file($user,$project);
              echo json_encode($output);
            }
            else {
              echo json_encode("nofile");
            }

    }

   # Run Script check file map.txt 
    public function validate_mapping_file($user,$project){

        $path_input = "owncloud/data/$user/files/$project/input/map.txt";
        $folder_test = "owncloud/data/$user/files/$project/test";
        $file = FCPATH.$path_input;

        $cmd = "/usr/bin/python Scriptqiime/validate_mapping_file.py -m $file -o $folder_test";
        $output = shell_exec($cmd);
        $chk = trim($output);
        # No errors or warnings were found in mapping file.
        if($chk == "No errors or warnings were found in mapping file."){
           return('Noerror');
        }else{
           return('Error');
        }
     }


     public function random_sequence($num){
      
         $arr_sequence = array();
         # arvg[1] is number-sequence
         # arvg[2] is length min sequence
         # arvg[3] is length max sequence
         # arvg[4] is number-set-sequence
         $cmd = "/usr/bin/python Scriptqiime/random_sequence.py $num 8 8 1";
         $output = shell_exec($cmd);
      
          $arr = explode(',', $output);
            foreach ($arr as $key => $value) {
                array_push($arr_sequence, $value);
            }

         return $arr_sequence;
    }



     # Generate file map.txt
     public function genmap(){

      $user = $this->uri->segment(3);
      $id_project = $this->uri->segment(4);
      $project = "";
      # Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);              
          }

      # create map_json.txt  
        $map_json = $_REQUEST['data_excel'];
        $path_map_json = "owncloud/data/$user/files/$project/input/map_json.txt";
        $file_map_json = FCPATH.$path_map_json; 
        file_put_contents($file_map_json, $map_json);
         
          $this->genFullMap($user,$project);
          echo json_encode($user);
     }

  

     public function genFullMap($user,$project){

         list($val_sample,$barcode) = $this->split_map_json($user,$project);

         $mapcreate = array();
         foreach ($val_sample as $key => $value){
             
             #splice group 
             $val_group = explode("\t",$value);
             array_splice($val_group,0,3);
             $data_group = implode("\t",$val_group);
             $group = trim($data_group);
            
             #splice SampleID PrimerSequence ReversePrimer
              $val = explode("\t", $value);
              array_splice($val,3);

              if($key == 0){

                $head = $val[0]."\t"."BarcodeSequence"."\t"."Linker".$val[1]."\t".$val[2]."\t"."InputFileName"."\t".$group."\t"."Description"."\n";
                array_push($mapcreate, $head);


              }else{

                $body =  $val[0]."\t".trim($barcode[$key])."\t".$val[1]."\t".$val[2]."\t"."out.".$val[0].".assembled_filtered.fasta"."\t".$group."\t"."oricode_".$val[0]."\n";

                array_push($mapcreate, $body);
              }
         }

          $path_map = "owncloud/data/$user/files/$project/input/map.txt";
          $file_map = FCPATH.$path_map;
          file_put_contents($file_map,$mapcreate); 

     }


     #split SampleID go out map_json.txt
     public function split_map_json($user,$project){

        $data_map = array();
        $group_sam = "";
        $path_input = "owncloud/data/$user/files/$project/input/map_json.txt";
        $file_num_sample = FCPATH.$path_input;
        $myfile =  fopen( $file_num_sample, 'r') or die ("Unable to open file");
        while (($value = fgets($myfile)) !== false) {
             
                $group = explode("\t", $value);
                $sam = implode("\t",$group);
                $group_sam = trim($sam);
                array_push($data_map,$group_sam);

        }
        fclose($myfile);
         $num = count($data_map);
         $barcode = $this->random_sequence($num);

         return  array($data_map,$barcode);
 
     }


     # read map_json.txt
     public function getgroup(){

        $Data_request = $_REQUEST['data'];
        $user = $Data_request[0];
        $id_project = $Data_request[1];
        $project = "";
      # Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);              
          }

        $count = 0;
        $name_group = array();
        $path_input = "owncloud/data/$user/files/$project/input/map_json.txt";
        $file_num_sample = FCPATH.$path_input;
        $myfile =  fopen( $file_num_sample, 'r') or die ("Unable to open file");
        while (($value = fgets($myfile)) !== false) {
              if($count == 0){
                 $name_group = explode("\t", $value);
                 array_splice($name_group,0,3);
                 $count++;
              }   
        }
        fclose($myfile);
        echo json_encode(array($count,$name_group));

     }

   

    






   }


?>