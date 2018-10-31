<?php
   
   defined('BASEPATH') OR exit('No direct script access allowed');
   /**
   * 
   */
  class Run_qiime2 extends CI_Controller{
   	
   	public function __construct(){

   		parent::__construct();
   		$this->load->helper(array('url','path','file','date'));
      	$this->load->helper('form');
      	$this->load->library('form_validation');

   	}


    
 # upload files 
  public function metadata_upload(){

      $user = $this->uri->segment(3);
      $id_project = $this->uri->segment(4);
      $project = "";
      # Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);              
      }

      $path_input = "owncloud/data/$user/files/$project/input/";
      $path_file = FCPATH.$path_input;

      $config['upload_path'] = $path_file;
      $config['allowed_types'] = '*';
      $config['max_filename'] = '255';
      $config['remove_spaces'] = TRUE;
      $config['file_name'] = "sample-metadata.tsv";
      $config['overwrite'] = TRUE; # overwrite file
      
      if(isset($_FILES['file'])){

         if(file_exists($path_file.$_FILES['file']['name'])){

             echo 'File already exists : '. $_FILES['file']['name'];
         }else{
            #upload file to upload_path
            $this->load->library('upload',$config);
            if(!$this->upload->do_upload('file')) {
                   echo $this->upload->display_errors();
            }else{
                  echo 'File successfully uploaded';
            }
         } 
      }  
  }


   # Generate file sample-metadata.tsv
  public function genmap(){

      $user = $this->uri->segment(3);
      $id_project = $this->uri->segment(4);
      $project = "";
      //$project_platform_type = "";

      # Query data Project By ID
          $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
          foreach ($array_project as $r) {
                $project = basename($r['project_path']);
                //$project_platform_type = ($r['project_platform_type']);               
          }

      # create map_json.txt  
        $map_json = $_REQUEST['data_excel'];
        $path_map_json = "owncloud/data/$user/files/$project/input/sample-metadata.tsv";
        $file_map_json = FCPATH.$path_map_json; 
        file_put_contents($file_map_json, $map_json);
       
        echo json_encode($user);
  }



    # Run qiime Preprocess
    public function run_qiime2_preprocess(){

        include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
         
        $value = $_REQUEST['data_array'];
       
        $user = $value[0];
        $id_project = $value[1];
        $chkmap =   $value[2];
        $pick_otus = $value[3];
        $option_gg =  $value[4];
        $precent_identity =  $value[5];

        
        $reference_sequences = "";
        $taxonomic_classifier = "";


        if($pick_otus == "Denovoclustering" && $option_gg == "denovo"){
           $reference_sequences  = "none";
           $taxonomic_classifier = "classifierNaiveBayFullLength.qza";
        }

        if($option_gg == "v_full"){

          $reference_sequences = "gg_13_8_99_otus.qza";
          $taxonomic_classifier = "classifierNaiveBayFullLength.qza";

        }else if($option_gg == "v1-v3"){

          $reference_sequences = "v13_27F_534R.qz";
          $taxonomic_classifier = "classifierNaiveBayV13.qza";

        }else if($option_gg == "v3-v4"){

          $reference_sequences = "v34_341F_802R.qza";
          $taxonomic_classifier = "classifierNaiveBayV34.qza";
          
        }else if($option_gg == "v4"){

          $reference_sequences ="v4_515F_806R.qza";
          $taxonomic_classifier = "classifierNaiveBayV4.qza";
          
        }else if($option_gg == "v3-v5"){

          $reference_sequences = "v35_341F_909R.qza";
          $taxonomic_classifier = "classifierNaiveBayV35.qza";
          
        }else if($option_gg == "v4-v5"){

          $reference_sequences = "v4_515F_806R.qza";
          $taxonomic_classifier = "classifierNaiveBayV45.qza";
          
        }else if($option_gg == "v5-v6"){

          $reference_sequences = "v56_785F_1081R.qza";
          $taxonomic_classifier = "classifierNaiveBayV56.qza";
        }


        

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
            $path_fileprocess = "owncloud/data/$user/files/$project/fileprocess/";

           # create folder log
            $folder_log = FCPATH."$path_log";   
            if (!file_exists($folder_log)) {
                   mkdir($folder_log, 0777, true);
            }

            # create folder out
            $folder_out = FCPATH."$path_out";   
            if (!file_exists($folder_out)) {
                   mkdir($folder_out, 0777, true);
            }

             # create folder fileprocess
            $folder_fileprocess = FCPATH."$path_fileprocess";   
            if (!file_exists($folder_fileprocess)) {
                   mkdir($folder_fileprocess, 0777, true);
            }

            #create directory folder report
            $this->create_folder_report($user,$project);

            #create directory folder Download
            $this->create_folder_download($user,$project);


           $jobname = $user.'-'.$project.'-'.'qiime2';

           $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f ScriptQiime2018/qiime2018_run1.php $user $project $path_input $path_out $path_log $project_platform_type $pick_otus $option_gg $precent_identity $reference_sequences $taxonomic_classifier $path_fileprocess";

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
                          'project' => $project,
                          'pick_otus' => $pick_otus,
                          'precent_identity' => $precent_identity,
                          'reference_sequences' => $reference_sequences ,
                          'taxonomic_classifier' => $taxonomic_classifier );
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
                          'project' => $project,
                          'pick_otus' => $pick_otus,
                          'precent_identity' => $precent_identity,
                          'reference_sequences' => $reference_sequences ,
                          'taxonomic_classifier' => $taxonomic_classifier );
            $this->update_status($id_project,$data);
        }  



    }



    public function create_folder_report($user,$project){

        $taxonomy_classification = FCPATH."data_report_qiime2/$user/$project/taxonomy_classification/";   
        if (!file_exists($taxonomy_classification)) {mkdir($taxonomy_classification, 0777, true);}

        $alpha_diversity_analysis = FCPATH."data_report_qiime2/$user/$project/alpha_diversity_analysis/";   
        if (!file_exists($alpha_diversity_analysis)) {mkdir($alpha_diversity_analysis, 0777, true);}

        $beta_diversity_analysis = FCPATH."data_report_qiime2/$user/$project/beta_diversity_analysis/";   
        if (!file_exists($beta_diversity_analysis)){ mkdir($beta_diversity_analysis, 0777, true);}

        $optional_output = FCPATH."data_report_qiime2/$user/$project/optional_output/";
        if (!file_exists($optional_output)) {mkdir($optional_output, 0777, true);}

        $file_report = FCPATH."data_report_qiime2/$user/$project/file_report/";
        if (!file_exists($file_report)) {mkdir($file_report, 0777, true);}
  }

   public function create_folder_download($user,$project){

        $taxonomy_classification = FCPATH."data_report_qiime2/$user/$project/Download/taxonomy_classification/";   
        if (!file_exists($taxonomy_classification)) {mkdir($taxonomy_classification, 0777, true);}

        $alpha_diversity_analysis = FCPATH."data_report_qiime2/$user/$project/Download/alpha_diversity_analysis/";   
        if (!file_exists($alpha_diversity_analysis)) {mkdir($alpha_diversity_analysis, 0777, true);}

        $beta_diversity_analysis = FCPATH."data_report_qiime2/$user/$project/Download/beta_diversity_analysis/";   
        if (!file_exists($beta_diversity_analysis)){ mkdir($beta_diversity_analysis, 0777, true);}

        $optional_output = FCPATH."data_report_qiime2/$user/$project/Download/optional_output/";
        if (!file_exists($optional_output)) {mkdir($optional_output, 0777, true);}
  }



    #Check Run qiime2 Preprocess
    public function check_run_qiime2_preprocess(){

          include(APPPATH.'../setting_sge.php');
          putenv("SGE_ROOT=$SGE_ROOT");
          putenv("PATH=$PATH");

          $da_job = $_REQUEST['data_job'];
          $id_job = $da_job[0];
          $id_project = $da_job[1];

          $name_job = "";
          $path_job = "";
          $user = "";
          $project = "";
          #Query data status-process
          $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
          foreach($array_status as $r) {
                $name_job = $r['job_name'];
                $path_job = $r['path_log'];
                $user = $r['user'];
                $project = $r['project'];
          }

          $check_run = exec("qstat -j $id_job ");
          if($check_run == false){

               $data_show = $this->sample_detail($user,$project);

               echo json_encode(array(0,$data_show));

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
                    $message = "Run Qiime2";
                }
                if($count != 0){
                    $percent = (($count/5)*100);
                    $percent_round = round($percent,0);
                }
            $up = array($message,$percent_round);
            echo json_encode($up);
         }
    }



    public function run_qiime2_analysis(){

        include(APPPATH.'../setting_sge.php');
          putenv("SGE_ROOT=$SGE_ROOT");
          putenv("PATH=$PATH");


        $value = $_REQUEST['data_array'];
        $check_options = $_REQUEST['data_opt'];

        $user = $value[0];
        $id_project = $value[1];
        $subsample = $value[2];
        $core_group = $value[3];
        $permanova = $value[4];
        $opt_permanova = $value[5];
        $kegg = $value[6];
        $sample_comparison = $value[7];
        $statistical_test = $value[8];
        $ci_method = $value[9];
        $p_value = $value[10];

         

        #Query data status-process
         $reference_sequences = "";
         $taxonomic_classifier = "";
         $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
          foreach($array_status as $r) {
               
                $reference_sequences = $r['reference_sequences'];
                $taxonomic_classifier = $r['taxonomic_classifier'];    
          }


        #Query data Project By ID
         $project = "";
         $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
            foreach ($array_project as $r) {
                $project = basename($r['project_path']);
            }

        $path_input = "owncloud/data/$user/files/$project/input/";
        $path_out = "owncloud/data/$user/files/$project/output/";
        $path_log = "owncloud/data/$user/files/$project/log/";

        $jobname = $user.'-'.$project.'-'.'qiime2_analysis';

        $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f ScriptQiime2018/qiime2018_run2.php $user $project $path_input $path_out $path_log $check_options $subsample $core_group $permanova $opt_permanova $kegg $sample_comparison $statistical_test $ci_method $p_value $reference_sequences $taxonomic_classifier";

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
      $data = array( 'status' => '1' ,'step_run' => '2','job_id' => $id_job ,'job_name' => $jobname ,'path_log' => $path_log , 'project_data' => $project,'group_core' =>$core_group,'permanova' => $permanova,'opt_permanova' => $opt_permanova,'check_picrust_stamp' => $check_options,"subsample" => $subsample);
      $this->update_status($id_project,$data);
         
   }


    #Check Run qiime2 analysis
    public function check_run_qiime2_analysis(){

          include(APPPATH.'../setting_sge.php');
          putenv("SGE_ROOT=$SGE_ROOT");
          putenv("PATH=$PATH");

          $da_job = $_REQUEST['data_job'];
          $id_job = $da_job[0];
          $id_project = $da_job[1];

          $name_job = "";
          $path_job = "";
          $user = "";
          $project = "";
          #Query data status-process
          $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
          foreach($array_status as $r) {
                $name_job = $r['job_name'];
                $path_job = $r['path_log'];
                $user = $r['user'];
                $project = $r['project'];
          }

          $check_run = exec("qstat -j $id_job ");
          if($check_run == false){

               $data = array('status' => '0' ,'step_run' => '3');
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
                    $message = "Run Qiime2";
                }
                if($count != 0){
                    $percent = (($count/14)*100);
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


    
    #sample-frequency-detail.csv 
    public function sample_detail($user,$project){

        $path = "owncloud/data/$user/files/$project/output/resultchecknumsubsampling/sample-frequency-detail.csv";
        
        $array_value = array();
        $array_name = array();
        $array_min = array();
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
              list($sample_name,$sample_val) = explode(",", $line);

              #add data
              array_push($array_value,$sample_name." : ".$sample_val);
              #add name sample
              array_push($array_name, $sample_name);
              #add number value
              array_push($array_min,$sample_val);

         }
         fclose($read);

         $number_min =  min($array_min);
         # find sample name is value min
         //$index_number_min = array_search($number_min,$array_min);
         //echo $array_name[$index_number_min].":".$number_min;

         return array($array_value,$number_min);

    }



     # read map_json.txt
     public function getgroup(){

        $Data_request = $_REQUEST['data'];
        $user = $Data_request[0];
        $id_project = $Data_request[1];
        $project = "";
         #Query data Project By ID
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
                $project = basename($r['project_path']);              
        }

        $samplename = $this->samset($user,$project); 

        $count = 0;
        $name_group = array();
        $path_input = "owncloud/data/aumza/files/Qiime2fasta/input/sample-metadata.tsv";
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
        //print_r($name_group);

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
      $path = FCPATH. "owncloud/data/$user/files/$project/input/sample-metadata.tsv";;
      $read = fopen($path,"r") or die ("Unable to open file");
      $count = 0;
         while(($line = fgets($read)) !== false){

              $data = explode("\t", $line);
              array_splice($data,0,1);
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
                //print_r(array_count_values($group));
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
          //print_r($name_group);
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