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

   	}



    # Run qiime Preprocess
    public function run_qiime_process(){

        include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
         
        $value = $_REQUEST['data_array'];
        $data_opt = $_REQUEST['data_opt'];

        $user = $value[0];
        $id_project = $value[1];
        $chkmap =   $value[2];

        $permanova = $value[3];
        $opt_permanova = $value[4];
        $anosim = $value[5];
        $opt_anosim = $value[6];
        $adonis = $value[7];
        $opt_adonis = $value[8];
        $core_group = $value[9];

        $kegg = $value[10];
        $sample_comparison = $value[11];
        $statistical_test = $value[12]; 
        $ci_method = $value[13];
        $p_value = $value[14];

        $beta_diversity_index = $value[15];
        $beta_diversity_index2 = $value[16];


        
        #Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
            foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_platform_type = ($r['project_platform_type']); 
           }

           # Set Path => input ,output ,log
            $path_input = "owncloud/data/$user/files/$project/input/";
            $path_out = "owncloud/data/$user/files/$project/output/";
            $path_log = "owncloud/data/$user/files/$project/log/";

           # create folder log
            $folder_log = FCPATH."$path_log";   
            if (!file_exists($folder_log)) {
                   mkdir($folder_log, 0777, true);
            }


            #create directory folder report
            $this->create_folder_report($user,$project);

            #create directory folder Download
            $this->create_folder_download($user,$project);


           $jobname = $user.'-'.$project.'-'.'qiime1';

           $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scriptqiime2/qiime_run1.php $user $project $path_input $path_out $path_log $project_platform_type $permanova $opt_permanova $anosim $opt_anosim $adonis $opt_adonis $core_group $kegg $sample_comparison $statistical_test $ci_method $p_value $beta_diversity_index $beta_diversity_index2 $data_opt";

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


         # Insert data run project to status_process
        #Check data status-process
        $count = $this->mongo_db->where(array('project_id'=> $id_project))->count('status_process');
         if($count == 0){
            $data = array(
                          'status' => '1' ,
                          'step_run' => '1' ,
                          'job_id' => $id_job ,
                          'job_name' => $jobname ,
                          'path_log' => $path_log ,
                          'project_id' => $id_project ,
                          'user' => $user,
                          'project' => $project);
            $this->insert_status($data);
         }else{
             $data = array(
                          'status' => '1' ,
                          'step_run' => '1' ,
                          'job_id' => $id_job ,
                          'job_name' => $jobname ,
                          'path_log' => $path_log ,
                          'project_id' => $id_project ,
                          'user' => $user, 
                          'project' => $project);
            $this->update_status($id_project,$data);
        }   
    }



    public function create_folder_report($user,$project){

        $taxonomy_classification = FCPATH."data_report_qiime/$user/$project/taxonomy_classification/";   
        if (!file_exists($taxonomy_classification)) {mkdir($taxonomy_classification, 0777, true);}

        $alpha_diversity_analysis = FCPATH."data_report_qiime/$user/$project/alpha_diversity_analysis/";   
        if (!file_exists($alpha_diversity_analysis)) {mkdir($alpha_diversity_analysis, 0777, true);}

        $beta_diversity_analysis = FCPATH."data_report_qiime/$user/$project/beta_diversity_analysis/";   
        if (!file_exists($beta_diversity_analysis)){ mkdir($beta_diversity_analysis, 0777, true);}

        $optional_output = FCPATH."data_report_qiime/$user/$project/optional_output/";
        if (!file_exists($optional_output)) {mkdir($optional_output, 0777, true);}

        $file_report = FCPATH."data_report_qiime/$user/$project/file_report/";
        if (!file_exists($file_report)) {mkdir($file_report, 0777, true);}
  }

   public function create_folder_download($user,$project){

        $taxonomy_classification = FCPATH."data_report_qiime/$user/$project/Download/taxonomy_classification/";   
        if (!file_exists($taxonomy_classification)) {mkdir($taxonomy_classification, 0777, true);}

        $alpha_diversity_analysis = FCPATH."data_report_qiime/$user/$project/Download/alpha_diversity_analysis/";   
        if (!file_exists($alpha_diversity_analysis)) {mkdir($alpha_diversity_analysis, 0777, true);}

        $beta_diversity_analysis = FCPATH."data_report_qiime/$user/$project/Download/beta_diversity_analysis/";   
        if (!file_exists($beta_diversity_analysis)){ mkdir($beta_diversity_analysis, 0777, true);}

        $optional_output = FCPATH."data_report_qiime/$user/$project/Download/optional_output/";
        if (!file_exists($optional_output)) {mkdir($optional_output, 0777, true);}
  }



    #Check Run qiime Preprocess
    public function check_run_qiime(){

          include(APPPATH.'../setting_sge.php');
          putenv("SGE_ROOT=$SGE_ROOT");
          putenv("PATH=$PATH");

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

               $data = array('status' => '0' ,'step_run' => '2');
               $this->update_status($id_project,$data);
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
                    $message = "Run Qiime";
                }
                if($count != 0){
                    $percent = (($count/40)*100);
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
      $project_platform_type = "";

      # Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);
                $project_platform_type = ($r['project_platform_type']);               
          }

      # create map_json.txt  
        $map_json = $_REQUEST['data_excel'];
        $path_map_json = "owncloud/data/$user/files/$project/input/map_json.txt";
        $file_map_json = FCPATH.$path_map_json; 
        file_put_contents($file_map_json, $map_json);
         
         
          if($project_platform_type == "proton_without"){

                $this->genFullMap2($user,$project);
          }else{

                $this->genFullMap($user,$project);
          }

          echo json_encode($user);
     }

  

     public function genFullMap($user,$project){

         #Query data Project By project_name
         // $project_platform_type = "";
         // $array_project = $this->mongo_db->get_where('projects',array('project_name' => $project));
         //    foreach ($array_project as $r) {
          
         //       $project_platform_type = ($r['project_platform_type']); 
         // }

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

                $body =  $val[0]."\t".trim($barcode[$key])."\t".$val[1]."\t".$val[2]."\t"."out".$val[0].".assembled_filtered.fasta"."\t".$group."\t"."oricode_".$val[0]."\n";

                array_push($mapcreate, $body);
              }
            }

         
          $path_map = "owncloud/data/$user/files/$project/input/map.txt";
          $file_map = FCPATH.$path_map;
          file_put_contents($file_map,$mapcreate); 

     }


    public function genFullMap2($user,$project){

         $sample_map = $this->numfile_fasta($user,$project);
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

                $body =  $val[0]."\t".trim($barcode[$key])."\t".$val[1]."\t".$val[2]."\t".$sample_map[$key-1]."\t".$group."\t"."oricode_".$val[0]."\n";

                array_push($mapcreate, $body);
              }
          }

         
          $path_map = "owncloud/data/$user/files/$project/input/map.txt";
          $file_map = FCPATH.$path_map;
          file_put_contents($file_map,$mapcreate); 
    }



    public function numfile_fasta($user,$project){
      
        $sample_map = array();
        $ref_fasta = array("gg_13_8_99.fasta",
                           "silva.v4.fasta",
                           "silva.bacteria.fasta",
                           "silva.v123.fasta",
                           "silva.v34.fasta",
                           "silva.v345.fasta",
                           "silva.v45.fasta",
                           "trainset16_022016.rdp.fasta");

         $path = "owncloud/data/$user/files/$project/input/";
         $findfasta = glob($path."*.{fasta,fst}", GLOB_BRACE); 
         foreach ($findfasta as $key => $file) {
             $name_fasta = basename($file);
             if(!in_array($name_fasta,$ref_fasta)){
                  array_push($sample_map, trim($name_fasta));
             }
        }
        return $sample_map;  
       
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

        $samplename = $this->samset($user,$project); 

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
        echo json_encode(array($count,$name_group,$samplename));

     }


    public function samset($user,$project){

      $path_sampleName = FCPATH."owncloud/data/$user/files/$project/input/sampleName.txt";
      $file_text = file_get_contents($path_sampleName);
      $get_Name = explode("\t", $file_text);
      $result = array_filter($get_Name);

      $sample_name = $result;
      $samplename = array();
      for ($i=0; $i < sizeof($sample_name); $i++) {  
             for ($j= 1; $j < sizeof($sample_name)-$i; $j++) { 
              array_push($samplename ,$sample_name[$i]."--vs--".$sample_name[$j+$i]);
             }    
      }
      return $samplename;
    } 




  public function read_map_json($user,$id_project){

      $project = "";
      # Query data Project By ID
      $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
      foreach ($array_project as $r) {
           $project = basename($r['project_path']);              
      }

      $group_check = $_REQUEST['data_group'];

      $data_set_group = array();
      $path = FCPATH. "owncloud/data/$user/files/$project/input/map_json.txt";;
      $read = fopen($path,"r") or die ("Unable to open file");
      $count = 0;
         while(($line = fgets($read)) !== false){
               
              $data = explode("\t", $line);
              array_splice($data,0,3);
              array_push($data_set_group,$data);
              $count = count($data);
             
         }
      fclose($read);
      //print_r($data_set_group);
          $name_group = array();
          for($i=0;$i < $count;$i++){
               $num_repeat = 1;
               $group = array_column($data_set_group,$i);
               // array_push($name_group, $group[0]);
               // print_r(array_count_values($group));
               $num_value = (array_count_values($group));

               $key_data_repeat = array();
              foreach ($num_value as $key => $value) {
                 $val = (int)$value;
                 //echo $key."<br>";
                 if($val > $num_repeat){
                     array_push($key_data_repeat, $val); 
                 }
              }

              $num = count($key_data_repeat);
              $key = trim($group[0]);
              $name_group[$key] = $num;
               
          }
          #print_r($name_group);
          $check = null;
          $data =  $name_group[$group_check];
          if($data > "1"){
              $check = "on";
          }else{
              $check = "off";
          }
          echo json_encode(array($check,$group_check));

     }

  }


?>