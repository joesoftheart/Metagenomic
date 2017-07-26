<?php  

  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Run_advance extends CI_Controller{


    public function __construct(){
      parent::__construct();
      $this->load->helper(array('url','path','file'));
      $this->load->helper('form');
      $this->load->library('form_validation');

      //$this->load->controller('Run_owncloud');
      include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");

        
    }


    public function create_file_design(){

      $this->load->view('excel_design');
    }

    public function create_file_metadata(){
      $this->load->view('excel_metadata');
    }

    public function write_design(){
       
       $user = $_REQUEST['user'];
       $id_project = $_REQUEST['project_id'];
       $project = "";

       # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];     
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
          
                $project = $r['project_name'];     
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
          
                $project = $r['project_name'];
               
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
          
                $project = $r['project_name'];     
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

      # Query data Project By ID
       $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_analysis = $r['project_analysis'];
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
            $path_input = "owncloud/data/$user/files/$project/input/";
            $path_out = "owncloud/data/$user/files/$project/output/";
            $path_log = "owncloud/data/$user/files/$project/log/";
      
            

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
                   $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype.php $user $project $maximum_ambiguous $maximum_homopolymer $minimum_reads_length $maximum_reads_length $alignment $diffs $reference $taxonomy $cutoff $taxon $path_input $path_out $path_log";
                  
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
                  $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_otu.php $user $project $maximum_ambiguous $maximum_homopolymer $minimum_reads_length $maximum_reads_length $alignment $diffs $reference $taxonomy $cutoff $taxon $path_input $path_out $path_log";
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
         $data_job = array($id_job,$jobname,$path_log,$user,$project);
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
           
           $project_analysis = ""; 

             # Query data Project By project_name
              $array_project = $this->mongo_db->get_where('projects',array('project_name' => $project));
              foreach ($array_project as $r) {
                    $project_analysis = $r['project_analysis'];
              }

             # Check type Project Phylotype OTU
             if($project_analysis == "phylotype"){

                $file = FCPATH."owncloud/data/$user/files/$project/output/final.tx.count.summary";

             }elseif ($project_analysis == "otu") {

                $file = FCPATH."owncloud/data/$user/files/$project/output/final.opti_mcc.count.summary";
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
           echo json_encode($data_read_count);

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

        # Query data Project By ID
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_analysis = $r['project_analysis'];
         }


       # Set Path input , output , log 
        $path_input = "owncloud/data/$user/files/$project/input/";
        $path_out = "owncloud/data/$user/files/$project/output/";
        $path_log = "owncloud/data/$user/files/$project/log/";
      
        #Create  jobname  advance
            $jobname = $user."-".$project."-".$project_analysis."-"."advance2";

        # Check type Project is Phylotype OR OTU

           if ($project_analysis == "phylotype") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype2.php $user $project $path_input $path_out $size $path_log";
           }
           else if($project_analysis == "otu") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_otu2.php $user $project $path_input $path_out $size $path_log";
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
         
         $sample_array = array($id_job,$classifly,$project_analysis,$user,$project);

         echo json_encode($sample_array);

      }



      public function check_subsample(){

        $sample_job = $_REQUEST['job_sample'];
        $id_job = $sample_job[0];
        $classifly = $sample_job[1];
        $project_analysis = $sample_job[2];
        $user = $sample_job[3];
        $project = $sample_job[4];
        
        $check_run = exec("qstat -j $id_job ");

            if($check_run == false){

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

                $up = array(0,$classifly,$sam_name);
                echo json_encode($up);
 
            }else{

               $up = array(1,$classifly);
               echo json_encode($up);
            }
             

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

      public function read_name_sameple($project_analysis,$user,$project){


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
            $size_alpha = $ch_alpha;
         }

         $ch_beta = $data[5];  #size beta default
         $size_beta = $data[6];  #size beta insert

         if($ch_beta != "1"){
            $size_beta = $ch_beta;
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

        #correlation with metadata & correlation of each OTU 
           $correlation = $data[21];
           
        #Method & Number of axes
           $method = $data[22];
           $axes  = $data[23];
     
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
          if($correlation ==""){

              $correlation = "0";
          }


        $project = "";  # projectname
        $project_analysis = ""; # type project

        # Query data Project By ID
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
                $project_analysis = $r['project_analysis'];
         }

         # return name sample 
         $group_sam = $this->read_name_sameple($project_analysis,$user,$project);


       # Set Path input , output , log 
        $path_input = "owncloud/data/$user/files/$project/input/";
        $path_out = "owncloud/data/$user/files/$project/output/";
        $path_log = "owncloud/data/$user/files/$project/log/";
      
        # Create  jobname  advance
            $jobname = $user."-".$project."-".$project_analysis."-"."advance3";

        # Check type Project is Phylotype OR OTU

           if ($project_analysis == "phylotype") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_phylotype3.php $user $project $path_input $path_out $path_log $level $size_alpha $size_beta $group_sam $group_ven $d_upgma_st $d_upgma_me $d_pcoa_st $d_pcoa_me $nmds $d_nmds_st $d_nmds_me $file_design $file_metadata $ah_mova $correlation $method $axes";
               
           }
           else if($project_analysis == "otu") {

                $cmd = "qsub -N '$jobname' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/advance_run_otu3.php $user $project $path_input $path_out $path_log $level $size_alpha $size_beta $group_sam $group_ven $d_upgma_st $d_upgma_me $d_pcoa_st $d_pcoa_me $nmds $d_nmds_st $d_nmds_me $file_design $file_metadata $ah_mova $correlation $method $axes";
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

         echo json_encode(array($id_job,$user,$project));


      }


      public function check_analysis(){


        $analysis_job = $_REQUEST['job_analysis'];
        $id_job = $analysis_job[0];
        $user = $analysis_job[1];
        $project = $analysis_job[2];
     
        
        $check_run = exec("qstat -j $id_job ");

            if($check_run == false){
              
                 $this->on_move($user,$project);
                 $up = 0;
                 echo json_encode($up);
                 

            }else{

               $up = 1;
               echo json_encode($up);
            }
             

      }


      public function on_move($user,$project){

         # check & create folder user
         $path_img = FCPATH."img_user/$user/$project/";   
         if (!file_exists($path_img)) {
                mkdir($path_img, 0777, true);
         }
         
         $path_dir = FCPATH."owncloud/data/$user/files/$project/output/";
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

      }


  
  }

?>