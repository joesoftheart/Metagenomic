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

        //    echo json_encode(array($id_job,$id_project));

        // # Insert data run project to status_process
        // #Check data status-process
        // $count = $this->mongo_db->where(array('project_id'=> $id_project))->count('status_process');
        //  if($count == 0){
        //     $data = array(
        //                   'status' => '1' ,
        //                   'step_run' => '1' ,
        //                   'job_id' => $id_job ,
        //                   'job_name' => $jobname ,
        //                   'path_log' => $path_log ,
        //                   'project_id' => $id_project ,
        //                   'user' => $user,
        //                   'project' => $project ,
        //                   );
        //     $this->insert_status($data);
        //  }else{
        //      $data = array(
        //                   'status' => '1' ,
        //                   'step_run' => '1' ,
        //                   'job_id' => $id_job ,
        //                   'job_name' => $jobname ,
        //                   'path_log' => $path_log ,
        //                   'project_id' => $id_project ,
        //                   'user' => $user, 
        //                   'project' => $project , 
        //                  );
        //     $this->update_status($id_project,$data);
        // }
         
    }

    # Check Run mothur Preprocess
    public function check_run_mothur(){

          $da_job = $_REQUEST['data_job'];
          $id_job = $da_job[0];
          $id_project = $da_job[1];

          $name_job = "";
          $path_job = "";
          #Query data status-process
          $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
          foreach($array_status as $r) {
                $name_job = $r['job_name'];
                $path_job = $r['path_log'];
          }

          $check_run = exec("qstat -j $id_job ");
          if($check_run == false){
              echo json_encode(array(0,$id_project));

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
                $message = $line[$count];
                if($message == ""){
                    $message = "Run Qiime";
                }
                if($count != 0){
                    $percent = (($count/16)*100);
                    $percent_round = round($percent,0);
                }
            $up = array($message,$percent_round);
            echo json_encode($up);
         }
    }


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



 # Run Qiime
  public function mothur_qiime1(){


       $value = $_REQUEST['data_array'];

        $user = $value[0];
        $id_project = $value[1];
        $file_map =   $value[2];
        $permanova = $value[3];
        $anosim = $value[4];
        $adonis = $value[5];
        $opt_permanova = $value[6];
        $opt_anosim = $value[7];
        $opt_adonis = $value[8];

        $project = "";
        # Query data Project By ID
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
            foreach ($array_project as $r) {
                $project = basename($r['project_path']);
            }

        $path_input = "owncloud/data/$user/files/$project/input/";
        $path_out = "owncloud/data/$user/files/$project/output/";
   		  $path_log = "owncloud/data/$user/files/$project/log/";

   		  $jobname = $user.'-'.$project.'-'.'mothur_qiime1';

   		  $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scriptqiime/mothur_qiime1.php $user $project $path_input $path_out $path_log $file_map $permanova $anosim $adonis $opt_permanova $opt_anosim $opt_adonis";

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
         echo json_encode(array($id_job,$id_project));

      # Update data status-process Step 2

      $data = array('status' => '1' ,'step_run' => '2' ,'job_id' => $id_job ,'job_name' => $jobname ,'path_log' => $path_log , 'project_data' => $project);
      $this->update_status($id_project,$data);
         
   }

 # check Run Qiime
  public function check_run_mothur_qiime1(){

          $da_job = $_REQUEST['data_job'];
          $id_job = $da_job[0];
          $id_project = $da_job[1];

          $name_job = "";
          $path_job = "";
          #Query data status-process
          $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
          foreach($array_status as $r) {
                $name_job = $r['job_name'];
                $path_job = $r['path_log'];
          }

          $check_run = exec("qstat -j $id_job ");
          if($check_run == false){
              echo json_encode(array(0,$id_project));

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
                    $message = "Run Qiime ";
                }
                if($count != 0){
                    $percent = (($count/14)*100);
                    $percent_round = round($percent,0);
                }
            $up = array($message,$percent_round);
            echo json_encode($up);
         }
    }


    // public function view_mothur_qiime(){

    //     $img_source = 'images/check.png';
    //     $img_code = base64_encode(file_get_contents($img_source));
    //     $data['src'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

    //     $img_source = 'images/ajax-loader.gif';
    //     $img_code = base64_encode(file_get_contents($img_source));
    //     $data['srcload'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

    //     $this->load->view('header');
    //     $this->load->view('run_mothur_qiime',$data);
    //     $this->load->view('footer');
    // }

    
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

   # Generate files map.txt
    public function excel_map(){

      $user = $this->session->userdata['logged_in']['username'];
      $id_project = $this->session->userdata['current_project'];
      $project = "";
      # Query data Project By ID
      $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);              
      }

      $data['num_sample'] = $this->getSamId($user,$project);
      $this->load->view('excel_map',$data);

    }
  
    # read Test_Map.txt
    public function getSamId($user,$project){

        $sample_id = array();
        $count = 0 ;
        $path_input = "owncloud/data/$user/files/$project/input/Test_Map.txt";
        $file_num_sample = FCPATH.$path_input;

        if(file_exists($file_num_sample)){
           $myfile =  fopen( $file_num_sample, 'r') or die ("Unable to open file");
           while (($value = fgets($myfile)) !== false) {
               if($count > 0){
                  $name_sample = explode("\t",$value);
                  array_push($sample_id, $name_sample[0]);
               }
               $count++;
            }
            fclose($myfile);   
        }
        else {
               $sample_id = null; 
        }
        
        return $sample_id;
        
     }

    # read map_json.txt
    public function map_json(){

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
                 array_splice($name_group,0,1);
                 $count++;
              }   
        }
        fclose($myfile);
        echo json_encode(array($count,$name_group));
     }


     # Add Group into files map.txt
     public function addGroup(){

      $user = $this->uri->segment(3);
      $id_project = $this->uri->segment(4);
      $project = "";
      # Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);              
          }

      # Read map_json insert to array
        $map_json = $_REQUEST['data_excel'];
        $path_map_json = "owncloud/data/$user/files/$project/input/map_json.txt";
        $file_map_json = FCPATH.$path_map_json; 
        file_put_contents($file_map_json, $map_json);

        $data_map = $this->split_map_json($user,$project); 
     
      # Read file map and insert group to file map
        $mapcreate = array();
        $count = 0;
        $path_Test_Map = "owncloud/data/$user/files/$project/input/Test_Map.txt";
        $Test_Map = FCPATH.$path_Test_Map;
        $myfile2 =  fopen($Test_Map, 'r') or die ("Unable to open file");
        while (($value = fgets($myfile2)) !== false) {
             
              $val = explode("\t",$value);
                $data0 = $val[0];
                $data1 = $val[1];
                $data2 = $val[2];
                $data3 = $val[3];
                $data4 = $val[4];
                $data5 = trim($val[5]);
                
               # array index $data_map
               $group_map = trim($data_map[$count]); 

               $b_head = $data0."\t".$data1."\t".$data2."\t".$data3."\t".$data4."\t".$group_map."\t".$data5."\n";

                array_push($mapcreate, $b_head);
                $count++;           
        }

        fclose($myfile2);
        $path_map = "owncloud/data/$user/files/$project/input/map.txt";
        $file_map_addGroup = FCPATH.$path_map;
        file_put_contents($file_map_addGroup,$mapcreate); 
        echo json_encode($user);

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
                array_splice($group,0,1);
                $sam = implode("\t",$group);
                $group_sam = trim($sam);
                array_push($data_map,$group_sam);

        }
        fclose($myfile);
        return $data_map;

     }

   

    






   }


?>