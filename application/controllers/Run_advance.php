<?php  

  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Run_advance extends CI_Controller{


    public function __construct(){
      parent::__construct();
      $this->load->helper(array('url','path','file','date'));
      $this->load->helper('form');
      $this->load->library('form_validation');
      $this->load->library('zip');
      $this->load->library('excel');


      //$this->load->controller('Run_owncloud');
      include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");

        
    }


    public function recheck(){

      $id_project = $_REQUEST['data_status'];
       $status   = "";
       $step_run = "";
       $id_job   = "";

       $user = "";
       $project = "";
       $project_analysis = "";
       $level = "";
       
       $tg_body = "0";
       $ts_body ="0";

      #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
           foreach ($array_status as $r) {
             
             $status   = $r['status'];
             $step_run = $r['step_run'];              
             $id_job = $r['job_id'];
            
              $user  = $r['user'];
              $project  = $r['project'];
              $project_analysis = $r['project_analysis'];
              $level = $r['level'];

     
           }

        if($level != "0" ){

            $tg_body = $this->read_file_groups_ave_std_summary($user,$project,$project_analysis,$level);
            $ts_body = $this->read_file_summary($user,$project,$project_analysis,$level);

        }
        

        echo json_encode(array($status,$step_run,$id_job,$tg_body,$ts_body));

    }


    public function create_file_design(){

      $id_project = $_REQUEST['current'];
      $data['sample_name'] ="";
      #Query data Sample_Name
        $array_samples = $this->mongo_db->get_where('sample_name',array('project_id' => $id_project));
           foreach ($array_samples as $r) {
             
             $data['sample_name'] = $r['name_sample'];
     
           }


      $this->load->view('excel_design',$data);
    }

    public function create_file_metadata(){

      $id_project = $_REQUEST['current'];
      $data['sample_name'] = "";
      #Query data Sample_Name
        $array_samples = $this->mongo_db->get_where('sample_name',array('project_id' => $id_project));
           foreach ($array_samples as $r) {
             
             $data['sample_name'] = $r['name_sample'];
           }

      $this->load->view('excel_metadata',$data);
    }

    public function write_design(){
       
       $user = $_REQUEST['user'];
       $id_project = $_REQUEST['project_id'];
       $project = "";

       # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                #$project = $r['project_name']; 
                $project = basename($r['project_path']);         
         }

       $design_json = $_REQUEST['data_excel'];

       $path_input = "owncloud/data/$user/files/$project/input/file.design";
      
       $file = FCPATH."$path_input";

       file_put_contents($file, $design_json); 

       echo json_encode($user);

    }

    public function check_file_design(){

       $user = $_REQUEST['user'];
       $id_project = $_REQUEST['project_id'];
       $project = "";

       # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                #$project = $r['project_name']; 
                $project = basename($r['project_path']);        
         }
      $path_input = "owncloud/data/$user/files/$project/input/file.design";
      $path_file = FCPATH."$path_input";

            if(file_exists($path_file)) {
              echo json_encode("file.design");
            }
            else {
              echo json_encode("No File");
            }
    }

    

   
     public function write_metadata(){

       $user = $_REQUEST['user'];
       $id_project = $_REQUEST['project_id'];
       $project = "";

       # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                #$project = $r['project_name']; 
                $project = basename($r['project_path']);    
               
         }

       $metadata_json = $_REQUEST['data_excel'];
       $path_input = "owncloud/data/$user/files/$project/input/file.metadata";
      
       $file = FCPATH."$path_input";


       file_put_contents($file, $metadata_json); 

       echo json_encode($user);


    }


     public function check_file_metadata(){

       $user = $_REQUEST['user'];
       $id_project = $_REQUEST['project_id'];
       $project = "";

       # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                #$project = $r['project_name']; 
                $project = basename($r['project_path']);    
         }
      
      $path_input = "owncloud/data/$user/files/$project/input/file.metadata";
      $path_file = FCPATH."$path_input";

            if(file_exists($path_file)) {
              echo json_encode("file.metadata");
            }
            else {
              echo json_encode("No File");
            }
    }

  

    public function check_fasta(){
      

      $config['upload_path'] = 'Mothur/';
      $config['allowed_types'] = '*';
      $config['max_filename'] = '255';
      $config['remove_spaces'] = TRUE;
      //$config['encrypt_name'] = TRUE;
      //$config['overwrite'] = FALSE; # overwrite file
      

      if(isset($_FILES['file'])){

         if(file_exists('Mothur/'.$_FILES['file']['name'])){

             echo 'File already exists : '. $_FILES['file']['name'];

         }else{
            #upload file to upload_path
            $this->load->library('upload',$config);

               if (!$this->upload->do_upload('file')) {
                   echo $this->upload->display_errors();
               } 
               else {

                    $check_fasta = $this->fasta_read($_FILES['file']['name']);
                    if($check_fasta){
                         echo 'File successfully uploaded ';
                    }else{
                         echo '0';
                    }
                  
               }
         } 
      }
   
    
    }
    public function fasta_read($file){
           $file = FCPATH."Mothur/$file"; 
           $check = "";
           $myfile = fopen($file,'r') or die ("Unable to open file");
           while(($lines = fgets($myfile)) !== false){
                 
                 $check = substr($lines,0,1);
                 break;
            }
           fclose($myfile);

           if($check == '>'){
               return true;
           }else{ 
             unlink($file);
             return false;
          }

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
       $classifly = $value[9];
       $cutoff  = $value[10];
       $optionsRadios = $value[11];
       $taxon = $value[12];

         
       $project = "";
       $project_analysis = "";
       $project_data = "";

      # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_analysis = $r['project_analysis'];
                $project_data = basename($r['project_path']);
         }


        # Check variable aligment
            if($customer != null){
                $alignment = $customer;
            }
            else if($alignment == "silva"){
                $alignment = "silva.v4.fasta";
            }
            else if ($alignment == "gg") {
                $alignment = "gg_13_8_99.fasta";
            }
            else if ($alignment == "rdp") {
                $alignment = "trainset16_022016.rdp.fasta";
            }
    
        

         $reference = '';
         $taxonomy ='';
        # Check variable classifly

            if($classifly == "silva"){
              $reference = 'silva.nr_v128.align';
              $taxonomy ='silva.nr_v128.tax';
            }
            else if($classifly == "gg") {
              $reference = 'gg_13_8_99.fasta';
              $taxonomy ='gg_13_8_99.gg.tax';
            }
             else if($classifly == "rdp") {
              $reference = 'trainset16_022016.rdp.fasta';
              $taxonomy = 'trainset16_022016.rdp.tax';
            }


        # Check variable taxon
            if($optionsRadios == '0'){
               $taxon = "Chloroplast-Mitochondria-Eukaryota-unknown";
            }

      
      
        # Set Path => input ,output ,log
            $path_input = "owncloud/data/$user/files/$project_data/input/";
            $path_out = "owncloud/data/$user/files/$project_data/output/";
            $path_log = "owncloud/data/$user/files/$project_data/log/";

         # create advance.batch
            $file_batch = FCPATH."$path_input"."advance.batch";
            if(!file_exists($file_batch)) {
                file_put_contents($file_batch, "No command !" );

            }
         # create folder log
         $folder_log = FCPATH."$path_log";   
            if (!file_exists($folder_log)) {
                   mkdir($folder_log, 0777, true);
            }
        


        #Create  jobname  advance
            $jobname = $user."-".$project."-".$project_analysis."-"."advance";

           
        #Check type Project is Phylotype OR OTU
            if($project_analysis == "phylotype"){

                $count = $this->mongo_db->where(array('id_project'=> $id_project))->count('advance_classifly');
                  if($count == 0){
                        $data = array('user' => $user,'project_name' => $project,'id_project' => $id_project,'classifly' => $classifly);
    
                        # insert data project
                          $this->mongo_db->insert('advance_classifly', $data);
                  }else{

                        # update classifly
                        $this->mongo_db->where(array('id_project'=> $id_project))->set('classifly', $classifly)->update('advance_classifly');     
                  }

                   $taxon = str_replace(";", ",", $taxon);
                   $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype.php $user $project_data $maximum_ambiguous $maximum_homopolymer $minimum_reads_length $maximum_reads_length $alignment $diffs $reference $taxonomy $cutoff $taxon $path_input $path_out $path_log";
                  
            }
            elseif ($project_analysis == "otu") {

                $count = $this->mongo_db->where(array('id_project'=> $id_project))->count('advance_classifly');
                  if($count == 0){
                        $data = array('user' => $user,'project_name' => $project,'id_project' => $id_project,'classifly' => $project_analysis);
    
                        # insert data project
                          $this->mongo_db->insert('advance_classifly', $data);
                  }else{

                        # update classifly
                        $this->mongo_db->where(array('id_project'=> $id_project))->set('classifly', $project_analysis)->update('advance_classifly');     
                  }
                  
                  $taxon = str_replace(";", ",", $taxon); 
                  $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_otu.php $user $project_data $maximum_ambiguous $maximum_homopolymer $minimum_reads_length $maximum_reads_length $alignment $diffs $reference $taxonomy $cutoff $taxon $path_input $path_out $path_log";
            }

           
         # Run Qsub Advance 
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

        

     #Check data status-process

       $count = $this->mongo_db->where(array('project_id'=> $id_project))->count('status_process');
       if($count == 0){
           $data = array('status' => '1' ,'step_run' => '1' ,'job_id' => $id_job ,'job_name' => $jobname ,'path_log' => $path_log ,'project_id' => $id_project ,'user' => $user, 'project' => $project , 'project_analysis' => $project_analysis ,'classifly' => $classifly,'f_design' => '0' ,'f_metadata' => '0' ,'project_data' => $project_data ,'level' => '0');
           $this->insert_status($data);
       }else{

           $data = array('status' => '1' ,'step_run' => '1' ,'job_id' => $id_job ,'job_name' => $jobname ,'path_log' => $path_log ,'project_id' => $id_project ,'user' => $user, 'project' => $project , 'project_analysis' => $project_analysis ,'classifly' => $classifly,'f_design' => '0' ,'f_metadata' => '0' ,'project_data' => $project_data ,'level' => '0' );
           $this->update_status($id_project,$data);
       }
       

    }
   

    public function check_run(){

             $da_job = $_REQUEST['data_job'];
               $id_job = $da_job[0];
               $id_project = $da_job[1];

               $name_job = "";
               $path_job = "";
              
               #Query data status-process
                $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
                   foreach ($array_status as $r) {
                           
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
                          $message = "Run Preprocess ";
                       }

                       if($count != 0){
                           $percent = (($count/19)*100);
                           $percent_round = round($percent,0);

                       }

                      $up = array($message,$percent_round);
                      echo json_encode($up);
                   }
             

     }



      public function read_count(){

           $da_count = $_REQUEST['data_count'];
           $id_project = $da_count[1];
           
           $user = "";
           $project = "";
           $project_data = "";
           $project_analysis = ""; 

             # Query data status-process by id_project
                $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
                   foreach ($array_status as $r) {
                     $user = $r['user'];
                     $project = $r['project'];
                     $project_analysis = $r['project_analysis'];
                     $project_data = $r['project_data'];    
               
                    }
 
             # Check type Project Phylotype OTU
             if($project_analysis == "phylotype"){

                $file = FCPATH."owncloud/data/$user/files/$project_data/output/final.tx.count.summary";

             }elseif ($project_analysis == "otu") {

                $file = FCPATH."owncloud/data/$user/files/$project_data/output/final.opti_mcc.count.summary";
             }
   
           
           $data_read_count = array();
           $count = array();

           # Name Sample
           $name_sample = array();

            $myfile = fopen($file,'r') or die ("Unable to open file");
               while(($lines = fgets($myfile)) !== false){
                 
                 $var =  explode("\t", $lines);
                 array_push($data_read_count, $var[0]." : ".$var[1]);
                 array_push($count, $var[1]);   

                 array_push($name_sample, $var[0]);

              }

           fclose($myfile);
           $count_less = min($count);
           array_push($data_read_count, $count_less);

           # return data read file
           echo json_encode($data_read_count);


       #Check count data Sample_name

        $count_sample = $this->mongo_db->where(array('project_id'=> $id_project))->count('sample_name');

          if($count_sample == 0){
             $data = array('project_id' => $id_project ,'user' => $user, 'project' => $project,'project_data' => $project_data,'name_sample' => $name_sample);
             
             $this->insert_name_sample($data);

          }else{

           $data = array('name_sample' => $name_sample);
           $this->update_name_sample($id_project,$data);
         }

      }





      public function run_sub_sample(){

        $data = $_REQUEST['data_sample'];

        $user = $data[0];
        $id_project = $data[1];
        $size = $data[2];

        $classifly = "";

        # Query type classifly
        $array_classifly = $this->mongo_db->get_where('advance_classifly',array('id_project' => $id_project));
         foreach ($array_classifly as $r) {
                           
                $classifly = $r['classifly'];
               
         }
       

       $project = "";
       $project_analysis = "";
       $project_data = "";

        # Query data Project By ID
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_analysis = $r['project_analysis'];
                $project_data = basename($r['project_path']);
         }


       # Set Path input , output , log 
        $path_input = "owncloud/data/$user/files/$project_data/input/";
        $path_out = "owncloud/data/$user/files/$project_data/output/";
        $path_log = "owncloud/data/$user/files/$project_data/log/";
      
        #Create  jobname  advance
            $jobname = $user."-".$project."-".$project_analysis."-"."advance2";

        # Check type Project is Phylotype OR OTU

           if ($project_analysis == "phylotype") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype2.php $user $project_data $path_input $path_out $size $path_log";
           }
           else if($project_analysis == "otu") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_otu2.php $user $project_data $path_input $path_out $size $path_log";
           }
             
 
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
         
         $sample_array = array($id_job,$id_project);

         echo json_encode($sample_array);

         
      # Update data status-process Step 2

         $data = array('status' => '1' ,'step_run' => '2' ,'job_id' => $id_job ,'job_name' => $jobname ,'path_log' => $path_log , 'project_data' => $project_data);
         $this->update_status($id_project,$data);

      }



      public function check_subsample(){

        $sample_job = $_REQUEST['job_sample'];
        $id_job = $sample_job[0];
        $id_project = $sample_job[1];
 
         $name_job ="";
         $path_job ="";
        
         $user = "";
         $project_data = "";
         $project_analysis = "";
         $classifly ="";
       


      #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {
                           
                $name_job = $r['job_name'];
                $path_job = $r['path_log'];
                $user = $r['user']; 
                $project_analysis = $r['project_analysis'];
                $classifly = $r['classifly'];
                $project_data = $r['project_data'];
      
         }
        
        $check_run = exec("qstat -j $id_job ");

            if($check_run == false){
                
                # call function read_min_sample
                $count_min = $this->read_min_sample($id_project);

               # Check type Project Phylotype OTU
                  if($project_analysis == "phylotype"){

                    $file = FCPATH."owncloud/data/$user/files/$project_data/output/final.tx.count.summary";

                  }
                  elseif ($project_analysis == "otu") {

                    $file = FCPATH."owncloud/data/$user/files/$project_data/output/final.opti_mcc.count.summary";
                    
                    #set classifly = otu
                    $classifly = $project_analysis;
                  }

                  $sam_name = array();
                  $myfile = fopen($file,'r') or die ("Unable to open file");
                   while(($lines = fgets($myfile)) !== false){
                       $var =  explode("\t", $lines);
                       array_push($sam_name, $var[0]);
                  }
                  fclose($myfile);

                   echo json_encode(array(0,$classifly,$sam_name,$count_min));
 
            }
             else
                 {

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
                          $message = "Run Prepare";
                       }

                       if($count != 0){
                           $percent = ((0.5/$count)*100);
                           $percent_round = round($percent,0);
                       }

               echo json_encode(array(1,$percent_round));
            }
             

      }


      public function read_min_sample($p_id){

           $id_project = $p_id;    
           $user = "";
           $project_data = "";
           $project_analysis = ""; 

             # Query data status-process by id_project
                $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
                   foreach ($array_status as $r) {
                     $user = $r['user'];
                     $project_analysis = $r['project_analysis'];
                     $project_data = $r['project_data']; 
               
                    }
 
             # Check type Project Phylotype OTU
             if($project_analysis == "phylotype"){

                $file = FCPATH."owncloud/data/$user/files/$project_data/output/final.tx.count.summary";

             }elseif ($project_analysis == "otu") {

                $file = FCPATH."owncloud/data/$user/files/$project_data/output/final.opti_mcc.count.summary";
             }

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

           # return data read file
           return $data_read_count;

      }





      public function ven_val($venn1,$venn2,$venn3,$venn4){
         
         # Replace venn
         $group_venn = "" ;
         $index = "";
         $check_venn = array($venn1,$venn2,$venn3,$venn4);
         foreach ($check_venn as $val_venn) {

             if($val_venn != "0"){
                $index .= $val_venn." ";
             }
          }
             $index = trim($index);
             $group_venn =  str_replace(" ", "-", $index);
             return $group_venn;
   
      }

      public function read_name_sample($project_analysis,$user,$project){


        # Check type Project Phylotype OTU
               if($project_analysis == "phylotype"){

                  $file = FCPATH."owncloud/data/$user/files/$project/output/final.tx.count.summary";

                 }
                 elseif ($project_analysis == "otu") {

                  $file = FCPATH."owncloud/data/$user/files/$project/output/final.opti_mcc.count.summary";
                 }

              $sam_name = array();
              $myfile = fopen($file,'r') or die ("Unable to open file");
                while(($lines = fgets($myfile)) !== false){
                       $var =  explode("\t", $lines);
                       array_push($sam_name, $var[0]);
                }

                fclose($myfile);

         # Replace sample
         $group_sample = "" ;
         $index = "";
         
         foreach ($sam_name as  $val) {

             if($val != "0"){
                $index .= $val." ";
             }
          }

         $index = trim($index);
         $group_sample =  str_replace(" ", "-", $index);
         return $group_sample;

      }

     
      public function run_analysis(){


         $data = $_REQUEST['data_analysis'];
         $user = $data[0]; # username
         $id_project = $data[1]; # id_project

         $level = $data[2]; #level database

         $ch_alpha = $data[3];   #size alpah default
         $size_alpha = $data[4]; #size alpah insert

         if($ch_alpha != "1"){
            $size_alpha = trim($ch_alpha);
         }

         $ch_beta = $data[5];  #size beta default
         $size_beta = $data[6];  #size beta insert

         if($ch_beta != "1"){
            $size_beta = trim($ch_beta);
         }


         $venn1 = $data[7];
         $venn2 = $data[8];
         $venn3 = $data[9];
         $venn4 = $data[10];

       #Venn Diagram
         $group_ven = $this->ven_val($venn1,$venn2,$venn3,$venn4);
      
          
        #UPGMA tree with calculator 
          $d_upgma_st = $data[11];
          $d_upgma_me = $data[12];

            if($d_upgma_st == null){
                $d_upgma_st = "0";
            }
            if($d_upgma_me == null){
                $d_upgma_me = "0";
            }


        #PCOA 
          $d_pcoa_st = $data[13];
          $d_pcoa_me = $data[14];

            if($d_pcoa_st == null){
               $d_pcoa_st = "0";
            }
            if($d_pcoa_me == null){
              $d_pcoa_me = "0";
            }

        #NMDS 
          $nmds = $data[15]; 

         #NMDS Calculator
           $d_nmds_st = $data[16];
           $d_nmds_me = $data[17];
             
             if($d_nmds_st == null){
                  $d_nmds_st = "0";
             }
             if($d_nmds_me == null){
                  $d_nmds_me = "0";
             }

        # Options 

        #file design & metadata 
           $file_design = $data[18];
           $file_metadata = $data[19];

        #Amova & Homova
           $ah_mova = $data[20];

        #correlation with Metadata 
           $correlation_meta = $data[21];
           
        #Method & Number of axes Metadata 
           $method_meta = $data[22];
           $axes_meta  = $data[23];


        # correlation of each OTU 
           $correlation_otu = $data[24];
           
        #Method & Number of axes OTU 
           $method_otu = $data[25];
           $axes_otu  = $data[26];


     
        #check variable
          if($file_design == "nodesign"){

                $file_design = "0";
          }
          if($file_metadata =="nometadata"){

              $file_metadata = "0";
          }
          if($ah_mova == ""){

              $ah_mova = "0";
          }

          if($correlation_meta == null){

              $correlation_meta = "0";
               $method_meta = "0";
               $axes_meta  = "0";
          }

          if($correlation_otu == null){

               $correlation_otu = "0";
               $method_otu = "0";
               $axes_otu  = "0";
          }


        $project = "";  # projectname
        $project_analysis = ""; # type project
        $project_data = "";

        # Query data Project By ID
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_analysis = $r['project_analysis'];
                $project_data = basename($r['project_path']);
         }

         # return name sample 
         $group_sam = $this->read_name_sample($project_analysis,$user,$project_data);


       # Set Path input , output , log 
        $path_input = "owncloud/data/$user/files/$project_data/input/";
        $path_out = "owncloud/data/$user/files/$project_data/output/";
        $path_log = "owncloud/data/$user/files/$project_data/log/";


       
       # Query data status-process by id_project
         $array_label = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
                   foreach ($array_label as $r) {
                    
                     $classifly = $r['classifly']; 
               
                    }

           if($classifly == "silva" || $classifly == "rdp"){
              $label_num = "1";
            }
            else if($classifly == "gg") {
              $label_num = "2";
            }
            

        # Create  jobname  advance
            $jobname = $user."-".$project."-".$project_analysis."-"."advance3";

        # Check type Project is Phylotype OR OTU

           if ($project_analysis == "phylotype") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype3.php $user $project_data $path_input $path_out $path_log $level $size_alpha $size_beta $group_sam $group_ven $d_upgma_st $d_upgma_me $d_pcoa_st $d_pcoa_me $nmds $d_nmds_st $d_nmds_me $file_design $file_metadata $ah_mova $correlation_meta $method_meta $axes_meta $correlation_otu $method_otu $axes_otu $label_num";
               
           }
           else if($project_analysis == "otu") {
                
                $label_num = "0.03";

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_otu3.php $user $project_data $path_input $path_out $path_log $level $size_alpha $size_beta $group_sam $group_ven $d_upgma_st $d_upgma_me $d_pcoa_st $d_pcoa_me $nmds $d_nmds_st $d_nmds_me $file_design $file_metadata $ah_mova $correlation_meta $method_meta $axes_meta $correlation_otu $method_otu $axes_otu $label_num";
           }
             
 
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

     
       # Update data status-process Step 3

         $data = array('status' => '1' ,'step_run' => '3' ,'job_id' => $id_job ,'job_name' => $jobname ,'path_log' => $path_log , 'f_design' => $file_design ,'f_metadata' => $file_metadata , 'project_data' => $project_data ,'level' => $level );
         $this->update_status($id_project,$data);



      }


      public function check_analysis(){


        $analysis_job = $_REQUEST['job_analysis'];
        $id_job = $analysis_job[0];
        $id_project = $analysis_job[1];

          $name_job ="";
          $path_job ="";

          $user = "";
          $project = "" ;
          $project_analysis = "";

          $file_design = "";
          $file_metadata = "";

          $project_data = "";
          
          $level = "";


      #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {
                           
                $name_job = $r['job_name'];
                $path_job = $r['path_log'];
                $user = $r['user'];
                $project = $r['project'];
                $project_analysis = $r['project_analysis'];
                $file_design = $r['f_design'];
                $file_metadata = $r['f_metadata'];
                $project_data = $r['project_data'];
                $level = $r['level'];
         }
        
      #Check number command 
        $divisor = 0;
        
        if($file_design == "0" && $file_metadata == "0"){
             $divisor = 25 ;
        }else if($file_design != "0" || $file_metadata != "0"){
             $divisor = 32 ;
        }

         

        $check_run = exec("qstat -j $id_job ");

            if($check_run == false){
              
                 $tg_body = $this->read_file_groups_ave_std_summary($user,$project,$project_analysis,$level);
                 $ts_body = $this->read_file_summary($user,$project,$project_analysis,$level);
                 
                 $this->on_move($user,$project,$project_data,$tg_body,$ts_body);

                 # Update data status-process Step 4
                     $data = array('status' => '0' ,'step_run' => '4' ,'job_id' => $id_job , 'project' => $project , 'project_data' => $project_data);
                     $this->update_status($id_project,$data);

                 $up = 0;
                 echo json_encode(array($up,$divisor,$tg_body,$ts_body));
                 

            }
            else{

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
                          $message = "Run Analysis";
                       }

                       if($count != 0){
                           $percent = (($count/$divisor)*100);
                           $percent_round = round($percent,0);
                       }

               $up = 1;
               echo json_encode(array($up,$percent_round,$message));
            }
             

      }


      public function on_move($user,$project,$project_data){

         # check & create folder user
         $path_img = FCPATH."img_user/$user/$project/";   
         if (!file_exists($path_img)) {
                mkdir($path_img, 0777, true);
         }
         
         $path_dir = FCPATH."owncloud/data/$user/files/$project_data/output/";
            if (is_dir($path_dir)) {
                if ($read = opendir($path_dir)){
                      while (($img = readdir($read)) !== false) {
                        
                        $allowed =  array('png','svg');
                        $ext = pathinfo($img, PATHINFO_EXTENSION);

                        if(in_array($ext,$allowed)) {
                           
                          copy($path_dir.$img,$path_img.$img);
                        }
                      }
     
                   closedir($read);
                }
            }

          #create file excel_table_groups_ave_std.xlsx
          $this->create_file_excel_g($user,$project,$tg_body);

         #create file excel_table_summary.xlsx
          $this->create_file_excel_s($user,$project,$ts_body); 

      }



   public function read_file_groups_ave_std_summary($user,$project,$project_analysis,$level){
   
         $path = FCPATH."owncloud/data/$user/files/$project/output/";

        if($project_analysis == "otu"){

               $file_groups_ave_std_summary = "final.opti_mcc.groups.ave-std.summary";
               $path_file_original_g = $path.$file_groups_ave_std_summary;
                    
        }else{
               $file_groups_ave_std_summary = "final.tx.groups.ave-std.summary";
               $path_file_original_g = $path.$file_groups_ave_std_summary;
        }

        
        $tbody = array();

           if(file_exists($path_file_original_g)){

                $file_g = $path_file_original_g;
                $count = 1;
                $myfile = fopen($file_g,'r') or die ("Unable to open file");
                    while(($lines = fgets($myfile)) !== false){
                        $line0 = explode("\t", $lines);
                        
                        if($count == 1){

                             $new_data = array(
                                       $line0[1],
                                       $line0[2],
                                       $line0[3], 
                                       $line0[4],
                                       $line0[5], 
                                       $line0[9],
                                       $line0[10], 
                                       $line0[11], 
                                       $line0[12], 
                                       $line0[13], 
                                       $line0[14] );

                            array_push($tbody,$new_data);  

                        }else{

                            if($line0[0] == $level ){ 

                                    $new_data = array(
                                       $line0[1],
                                       $line0[2],
                                       $line0[3], 
                                       $line0[4],
                                       $line0[5], 
                                       $line0[9],
                                       $line0[10], 
                                       $line0[11], 
                                       $line0[12], 
                                       $line0[13], 
                                       $line0[14] );

                            array_push($tbody,$new_data); 
                              

                           }
                        }
                
                    $count++;
                    }
                fclose($myfile);  
          
           } 

           return $tbody; 
           

      }

  public function read_file_summary($user,$project,$project_analysis,$level){
          
         $path = FCPATH."owncloud/data/$user/files/$project/output/";

         if($project_analysis == "otu"){

                     $file_summary = "final.opti_mcc.summary";
                     $path_file_original_s = $path.$file_summary;

                }else{
                     
                     $file_summary = "final.tx.summary";
                     $path_file_original_s = $path.$file_summary; 
         }


          $tbody = array();

           if(file_exists($path_file_original_s)){

                $file_s = $path_file_original_s;
                $count = 1;
                $myfile = fopen($file_s,'r') or die ("Unable to open file");
                    while(($lines = fgets($myfile)) !== false){
                        $line0 = explode("\t", $lines);
                        
                        if($count == 1){

                             $new_data = array(
                                       $line0[1],
                                       $line0[3], 
                                       $line0[4],
                                       $line0[5], 
                                       $line0[6],
                                       $line0[7], 
                                       $line0[8], 
                                       $line0[9], 
                                       $line0[10], 
                                       $line0[11] );

                            array_push($tbody,$new_data);  

                        }else{

                            if($line0[0] == $level ){ 

                                    $new_data = array(
                                       $line0[1],
                                       $line0[2],
                                       $line0[4],
                                       $line0[5], 
                                       $line0[6],
                                       $line0[7], 
                                       $line0[8], 
                                       $line0[9], 
                                       $line0[10], 
                                       $line0[11],
                                       $line0[12] );

                            array_push($tbody,$new_data); 
                              

                           }
                        }
                
                    $count++;
                    }
                fclose($myfile);  
          
           } 

           return $tbody; 
            
  
  }

   public function create_file_excel_g($user,$project,$tg_body){

     $objExcel = new PHPExcel();

     $objExcel->getProperties()->setCreator("Metagenomic")
                               ->setLastModifiedBy("Metagenomic")
                               ->setTitle("Metagenomic Document")
                               ->setSubject("Metagenomic Document")
                               ->setDescription("metagenomic generated excel")
                               ->setKeywords("office PHPExcel php")
                              ->setCategory("excel file");

      $objExcel->getActiveSheet()->setTitle("table report");
      $objExcel->setActiveSheetIndex(0);

      $objExcel->getDefaultStyle()->getAlignment()
                                  ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
                                  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


      $objExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);


     #body
     for ($i=0; $i < sizeof($tg_body) ; $i++) { 

       $objExcel->setActiveSheetIndex(0)
               ->setCellValue('A'.($i+1),$tg_body[$i][0])
               ->setCellValue('B'.($i+1),$tg_body[$i][1])
               ->setCellValue('C'.($i+1),$tg_body[$i][2])
               ->setCellValue('D'.($i+1),$tg_body[$i][3])
               ->setCellValue('E'.($i+1),$tg_body[$i][4])
               ->setCellValue('F'.($i+1),$tg_body[$i][5])
               ->setCellValue('G'.($i+1),$tg_body[$i][6])
               ->setCellValue('H'.($i+1),$tg_body[$i][7])
               ->setCellValue('I'.($i+1),$tg_body[$i][8])
               ->setCellValue('J'.($i+1),$tg_body[$i][9])
               ->setCellValue('K'.($i+1),$tg_body[$i][10]);
       
     }
     


      $objWriter = PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
      $filename = "excel_table_groups_ave_std.xlsx";
      $objWriter->save("img_user/".$user."/".$project."/".$filename);
      exit;


  }


  public function create_file_excel_s($user,$project,$ts_body){

     $objExcel = new PHPExcel();

     $objExcel->getProperties()->setCreator("Metagenomic")
                               ->setLastModifiedBy("Metagenomic")
                               ->setTitle("Metagenomic Document")
                               ->setSubject("Metagenomic Document")
                               ->setDescription("metagenomic generated excel")
                               ->setKeywords("office PHPExcel php")
                              ->setCategory("excel file");

      $objExcel->getActiveSheet()->setTitle("table report");
      $objExcel->setActiveSheetIndex(0);

      $objExcel->getDefaultStyle()->getAlignment()
                                  ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
                                  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


      $objExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
      $objExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);


      #header
      $objExcel->setActiveSheetIndex(0)->mergeCells('A1:B1')
               ->setCellValue('A1','comparison')
               ->setCellValue('B1',' ')
               ->setCellValue('C1','lennon')
               ->setCellValue('D1','jclass')
               ->setCellValue('E1','morisitahorn')
               ->setCellValue('F1','sorabund')
               ->setCellValue('G1','thetan')
               ->setCellValue('H1','thetayc')
               ->setCellValue('I1','thetayc_lci')
               ->setCellValue('J1','thetayc_hci')
               ->setCellValue('K1','braycurtis');

     #body
     for ($i=0; $i < sizeof($ts_body) ; $i++) { 

       if($i > 0 ){
              
          $objExcel->setActiveSheetIndex(0)
               ->setCellValue('A'.($i+1),$ts_body[$i][0])
               ->setCellValue('B'.($i+1),$ts_body[$i][1])
               ->setCellValue('C'.($i+1),$ts_body[$i][2])
               ->setCellValue('D'.($i+1),$ts_body[$i][3])
               ->setCellValue('E'.($i+1),$ts_body[$i][4])
               ->setCellValue('F'.($i+1),$ts_body[$i][5])
               ->setCellValue('G'.($i+1),$ts_body[$i][6])
               ->setCellValue('H'.($i+1),$ts_body[$i][7])
               ->setCellValue('I'.($i+1),$ts_body[$i][8])
               ->setCellValue('J'.($i+1),$ts_body[$i][9])
               ->setCellValue('K'.($i+1),$ts_body[$i][10]);
       }
       
       
     }
     
      $objWriter = PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
      $filename = "excel_table_summary.xlsx";
      $objWriter->save("img_user/".$user."/".$project."/".$filename);
      exit;

  }

  public function insert_status($data){
      
            # insert data status-process
            $this->mongo_db->insert('status_process', $data);


     }

  public function update_status($id_project,$data){
          
           # update data status-process
            $this->mongo_db->where(array('project_id'=> $id_project))->set($data)->update('status_process'); 
           
          

     }


    public function insert_name_sample($data){
      
            # insert data sample_name
            $this->mongo_db->insert('sample_name', $data);


     }

     public function update_name_sample($id_project,$data){
          
           # update data sample_name
            $this->mongo_db->where(array('project_id'=> $id_project))->set($data)->update('sample_name'); 
           
          

     }

     public function check_dirzip(){

      $id_project = $_REQUEST['current'];

       $user = "NULL";
       $folder = "NULL";
       $step_run = "NULL";

        #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {             
                
                $step_run = $r['step_run'];
                $user = $r['user'];
                $folder = $r['project'];

         }

         $path_img = FCPATH."img_user/$user/$folder/";  

           if($step_run == "4"){

             if(file_exists($path_img)){

                 echo json_encode("TRUE");  
             }
          
           }else{
             echo json_encode("Null");
         }
     }

    public function down_zip(){


        $id_project = $_REQUEST['current'];

        #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {             
              
                $user = $r['user'];
                $folder = $r['project'];
    
         }

           $this->zip->read_dir("img_user/".$user."/".$folder."/",FALSE);
           $this->zip->download('visualization.zip');


     }

     public function getCanvas1(){

       $img_data = $_REQUEST['data'];
       $id_project = $_REQUEST['current'];
        
        $user = "NULL";
        $folder = "NULL";

        #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {             
              
                $user = $r['user'];
                $folder = $r['project'];
    
         }

         $path_img = FCPATH."img_user/$user/$folder/table_groups_ave_std_summary.png";  

         if(!file_exists($path_img)){
    
              $upload_dir = "img_user/".$user."/".$folder."/";
              $img = str_replace('data:image/png;base64','', $img_data);
              $img = str_replace(' ', '+', $img);
               $data = base64_decode($img);

               $file = $upload_dir."table_groups_ave_std_summary.png";
               file_put_contents($file, $data);
         }
       
      
     }

     public function getCanvas2(){

       $img_data = $_REQUEST['data'];
       $id_project = $_REQUEST['current'];
        
        $user = "NULL";
        $folder = "NULL";

        #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {             
              
                $user = $r['user'];
                $folder = $r['project'];
    
         }

         $path_img = FCPATH."img_user/$user/$folder/table_summary.png";  

          if(!file_exists($path_img)){

              $upload_dir = "img_user/".$user."/".$folder."/";
              $img = str_replace('data:image/png;base64','', $img_data);
              $img = str_replace(' ', '+', $img);
              $data = base64_decode($img);

               $file = $upload_dir."table_summary.png";
               file_put_contents($file, $data);
          }
      
     }



  
  }

?>