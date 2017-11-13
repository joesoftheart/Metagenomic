<?php  
 defined('BASEPATH') OR exit('No direct script access allowed');

 /**
 * 
 */
 class Run_sra extends CI_Controller
 {
 	
 	public function __construct()
 	{
 		# code...
 		parent::__construct();
 		$this->load->helper(array('url','path','file','date'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->helper('download');

        # email
        $this->load->library('user_agent');
        $this->load->library('email');

        include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");

 	}


  public function sra_projects(){
       $data['rs']  = $this->mongo_db->get_where('projects', array("user_id" => $this->session->userdata["logged_in"]["_id"]));
        $data['rs_user'] = $this->mongo_db->get('user_login');

        $this->load->view('header',$data);
        $this->load->view('sra_projects',$data);
        $this->load->view('footer');

  }


 	public function index($id){
   
 		  
        $job_id = null;
        $type_script = null;
        $status_job = null; 


        $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id));
        foreach ($array_status as $r){        
           
             $job_id       = $r['job_id'];
             $type_script  = $r['type_script']; 
             $status_job   = $r['status_job'];    
               
         }

          
         if($type_script == "1" && $status_job == "1" ){
         	   # get check check_script1
              $data['id_job'] = $job_id;
              $data['id_project'] = $id;
              $this->load->view('header');
              $this->load->view('running_sra',$data);
              $this->load->view('footer');

         }else if($type_script == "1" && $status_job == "0" ){
             # get read_stability_tsv($id_project)
             $this->read_stability_tsv($id);

         }else if($type_script == "2" && $status_job == "1" ){
             # get check check_script2
             $data['id_job'] = $job_id;
             $data['id_project'] = $id;
             $this->load->view('header');
             $this->load->view('running_sra2',$data);
             $this->load->view('footer');

         }else if($type_script == "2" && $status_job == "0" ){
             # get formMail($id_project)
             $this->formMail($id);
         }else{

                $this->session->set_userdata('current_project',$id);
                $this->load->view('header');
                $this->load->view('run_sra');
                $this->load->view('footer');

         }



 	}

  public function reRun($id){

     $data = array('type_script' => "0" ,'status_job' => "0");
     $this->update_status($id,$data);

    $this->session->set_userdata('current_project',$id);
    $this->load->view('header');
    $this->load->view('run_sra');
    $this->load->view('footer');

    
  }

  public function checkFiles(){
    
    $id = $_REQUEST['getid'];
    $user = null;
    $project = null;

    #Query data status-process
//      $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id));
//      foreach ($array_status as $r) {
//          $user  = $r['user'];
//          $project  = $r['project'];
//      }
      $data = $this->mongo_db->get_where('projects',array('_id' => new MongoId($id)));
      foreach ($data as $r) {
          $project  = $r['project_name'];
      }
      $user = $this->session->userdata['logged_in']['username'];



    $path = FCPATH."owncloud/data/$user/files/$project/input/stability.files";
        if(file_exists($path)){

            echo json_encode("T");
        }else{

            echo json_encode("F");
        }

  }



 	public function create($id){

         
        $username = $this->input->post('username');
        $last     = $this->input->post('last');
        $first    = $this->input->post('first');
        $email    = $this->input->post('email');
        $center   = $this->input->post('center');
        $type     = $this->input->post('type');
        $website  = $this->input->post('website');
        $projectname = $this->input->post('projectname');
        $projecttitle = $this->input->post('projecttitle');
        $description  = $this->input->post('description');
        $grantid  = $this->input->post('grantid');
        $agency   = $this->input->post('agency');
        $title    = $this->input->post('title');
        $optionsRadios = $this->input->post('optionsRadios');
 		

 		if($username != "" &&
 		   $last   != "" &&
 		   $first  != "" &&
 		   $email  != "" &&
 		   $center != "" &&
 		   $type   != "" &&
 		   $website != "" &&
 		   $projectname != "" &&
 		   $projecttitle != "" &&
 		   $description != "" &&
 		   $grantid != "" &&
 		   $agency  != "" &&
 		   $title    != "" &&
 		   $optionsRadios != "" ){

          $project_name = null;
          $project_path = null;
          $package = $optionsRadios;

 		  $read = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id)));	
          foreach ($read as $r) {
          	  $project_name = $r['project_name'];
              $project_path = $r['project_path'];
          }

          

         # Write test.project

        $data = "USERNAME"."\t".$username."\n".
			    "Last"."\t".$last ."\n".
			    "First"."\t".$first ."\n".
			    "EMAIL"."\t".$email."\n".
				  "CENTER"."\t".$center."\n".
				  "TYPE"."\t".$type."\n".
				  "WEBSITE"."\t".$website."\n".
				  "ProjectName"."\t".$projectname."\n".
				  "ProjectTitle"."\t".$projecttitle."\n".
				  "Description"."\t".$description."\n".
				  "Grant id=".$grantid.", agency=".$agency.", title=".$title;


            
          # create test.project
            $file_project = FCPATH.$project_path."/input/test.project";
            file_put_contents($file_project,$data);

         #create sra.batch
            $file_batch = FCPATH.$project_path."/input/sra.batch";
            if(!file_exists($file_batch)) {
                file_put_contents($file_batch, "No SRA command !" );

            }


            $this->run_script_sra($project_name,$package,$id);
             
       
 		}

      


 	}


 	public function run_script_sra($project_name,$package,$id_project){


            $username = $this->session->userdata['logged_in']['username'];

            $path = "owncloud/data/$username/files/$project_name/input/";
            $path_log = "owncloud/data/$username/files/$project_name/log/";

            # input script : path , packages
            $jobName = $username."_SRA_".$project_name;

            $cmd = "qsub -N '$jobName' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/sra_script1.php $path $package $path_log $username";
            shell_exec($cmd);

             $check_qstat = "qstat  -j '$jobName' ";
         		exec($check_qstat,$output);
              	$id_job = "" ;
              	foreach ($output as $key_var => $value ) {

                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }
              	}

                $id_job = trim($id_job);

        #table sra_run 
         $type_script ="1";
         $status_job = "1"; 
         
        # Day
         $day = date("d/m/Y");
      
       #Check data sra_run

       $count = $this->mongo_db->where(array('project_id'=> $id_project))->count('sra_run');
      
       if($count == 0){
           $data = array('project_id' => $id_project ,'project_name' => $project_name ,'user' => $username ,'job_id' => $id_job ,'type_script' => $type_script ,'status_job' => $status_job ,'date_email' => $day, 'quota_send_email' => '5');
           $this->insert_status($data);

       }else{

           $data = array('job_id' => $id_job ,'type_script' => $type_script ,'status_job' => $status_job);
           $this->update_status($id_project,$data);
       }

      
       # load view running_sra

          $data['id_job'] = $id_job;
          $data['id_project'] = $id_project;
          $this->load->view('header');
          $this->load->view('running_sra',$data);
          $this->load->view('footer');


        
 	}


 	public function check_script1(){

 		   $data = $_REQUEST['data'];
 		   $id_job = $data[0];
       $id_project = $data[1];

        $status_job = "0"; 

        $check_run = exec("qstat -j $id_job ");
        if($check_run == false){

             $data = array('status_job' => $status_job);
             $this->update_status($id_project,$data); 
                   
             echo json_encode(array("0","End Process"));
             


        }else{
                       
             echo json_encode(array("Run Queue"));
         }



 	}


 	public function read_stability_tsv($id_project){

 		#Query table sra_run
 		$user = null;
 		$project = null;

        $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
            foreach ($array_status as $r) {
                           
             $project = $r['project_name'];
             $user    = $r['user'];       
               
        }

        $data['path'] = FCPATH."owncloud/data/$user/files/$project/input/stability.tsv";
        $data['id_project'] = $id_project;
        $this->load->view('header');
        $this->load->view('read_stability',$data);
        $this->load->view('footer');


 	}



 	public function insert_status($data){
      
            # insert data table sra_run
            $this->mongo_db->insert('sra_run', $data);


     }

  public function update_status($id_project,$data){
          
           # update data table sra_run
            $this->mongo_db->where(array('project_id'=> $id_project))->set($data)->update('sra_run'); 
           
          

     }


 	public function checkEmail(){

 		if(!empty($_REQUEST["email"])){

 			 if(!filter_var($_REQUEST["email"],FILTER_VALIDATE_EMAIL)){

 			 	echo "<font color='red'>Invalid email format</font>";

 			 }else{
 			 	echo "<font color='green'>Email Available</font>";
 			 }

 		}

 	}


 	public function write_stabilitynew($id_project){

        $in_new = $_REQUEST['innew'];

 		#Query table sra_run
 		$user = null;
 		$project = null;

        $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
            foreach ($array_status as $r) {
                           
             $project = $r['project_name'];
             $user    = $r['user'];       
               
        }
         

     $path = FCPATH."owncloud/data/$user/files/$project/input/stability.tsv";

     $data_new = "";
 		 $count = 0;
         $myfile = fopen($path,'r') or die ("Unable to open file");
               while(($lines = fgets($myfile)) !== false){

                   $count++;
                   if ($count <= 10){ 

                   	   $data_new .= $lines;

                   }
                  
               }
      
         fclose($myfile); 

         $file = FCPATH."owncloud/data/$user/files/$project/input/stability.tsv";
         file_put_contents($file,$data_new);  


         file_put_contents($file,$in_new.PHP_EOL,FILE_APPEND | LOCK_EX);
         echo json_encode("success");

 	}



    public function run_script_sra2($id_project){

           $username = null;
           $project_name = null;

           $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
            foreach ($array_status as $r) {
                           
             $project_name = $r['project_name'];
             $username    = $r['user'];       
               
           }
         

            $path = "owncloud/data/$username/files/$project_name/input/";
            $path_log = "owncloud/data/$username/files/$project_name/log/";

            # input script : path 
            $jobName = $username."_SRA2_".$project_name;

            $cmd = "qsub -N '$jobName' -o $path_log -e $path_log -cwd -b y /usr/bin/php -f Scripts/src_script2.php $path $path_log $username";
            shell_exec($cmd);

             $check_qstat = "qstat  -j '$jobName' ";
         		exec($check_qstat,$output);
              	$id_job = "" ;
              	foreach ($output as $key_var => $value ) {

                    if($key_var == "1"){
                        $data = explode(":", $value);
                        $id_job = $data[1];
                    }
              	}

                $id_job = trim($id_job);


        # Table sra_run 

         $type_script ="2";
         $status_job = "1"; 

      
        # Update data sra_run

         $data = array('job_id' => $id_job ,'type_script' => $type_script ,'status_job' => $status_job);
         $this->update_status($id_project,$data);
     

      
       # load view running_sra2

          $data['id_job'] = $id_job;
          $data['id_project'] = $id_project;
          $this->load->view('header');
          $this->load->view('running_sra2',$data);
          $this->load->view('footer');



    }


    public function check_script2(){

 		    $data = $_REQUEST['data'];
 		    $id_job = $data[0];
        $id_project = $data[1];

        $status_job = "0"; 

        $check_run = exec("qstat -j $id_job ");
        if($check_run == false){

             $data = array('status_job' => $status_job);
             $this->update_status($id_project,$data); 
                   
             echo json_encode(array("0","End Process"));    

        }else{
                       
             echo json_encode(array("Run Queue"));
         }



 	}


 	public function formMail($id_project){

         $day = date("d/m/Y");
         $date_email = null;
         $quota_send_email = null;

          $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
          foreach ($array_status as $r) {
               
                $date_email = $r['date_email'];
                $quota_send_email = $r['quota_send_email'];   
          }
    
        # update is today 
         if($day != $date_email){
       
           $data = array('date_email' => $day, 'quota_send_email' => '5');
           $this->update_status($id_project,$data); 
        }


          $data['id_project'] = $id_project;
          $data['quota_send_email'] = $quota_send_email;
 		      $this->load->view('header');
          $this->load->view('sra_getmail',$data);
          $this->load->view('footer');

 	}

    
    public function inGetMail($id_project){

    	  $username = null;
        $project_name = null;
        $quota_send_email = null;
        $num_quota = null;

        $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
        foreach ($array_status as $r){
                           
             $project_name = $r['project_name'];
             $username     = $r['user'];  
             $quota_send_email = $r['quota_send_email'];     
               
         }
        
        $quota = (int)$quota_send_email;

        $path = FCPATH."owncloud/data/$username/files/$project_name/input/submission.xml"; 
        
        $name    = $this->input->post('name');
        $email   = $this->input->post('email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        if($quota != 0 &&
            $username != ""  && 
            $project_name != "" && 
        	  $name     != ""  && 
            $email != "" &&
        	  $subject  != "" ){

        	   $message = $this->getEmailsra($path,$name,$email,$subject,$message);
             if($message == "send"){
               
               # update quota send email
                $num_quota = $quota-1;
                $data = array('quota_send_email' => $num_quota);
                $this->update_status($id_project,$data); 

               # redirect formmail
                $this->session->set_flashdata('message','Send email success');
                redirect('Run_sra/formMail/'.$id_project,'refresh');

             } else{

                $this->session->set_flashdata('message','Send email failed');
                redirect('Run_sra/formMail/'.$id_project,'refresh');
             }   

        }else{

             $this->session->set_flashdata('message','quota send email failed');
             redirect('Run_sra/formMail/'.$id_project,'refresh');
        }

    }


 	public function getEmailsra($path,$name,$email,$subject,$message){


      $confog['protocol']   = 'smtp';
      $config['charset'] = 'utf-8';
      $config['wordwrap'] = TRUE;
      $confog['smtp_crypto'] = 'tls';
      $config['newline'] = '\r\n';
      $confog['mailtype'] = 'text';

      $this->email->initialize($config);

      $this->email->from($email, $name);
      $this->email->to('aumthai72@gmail.com','Thatsanai');

      // sra@ncbi.nlm.nih.gov

      $this->email->subject($subject);
      $this->email->message($message);
    
      
      $this->email->attach($path);

      $mes = $this->email->send();
      
      # send email success
      if($mes){
           
           return 'send';
          //echo "true :".$mes;
          

      }else{

          return 'no';
         //echo $this->email->print_debugger();
      }

    
     

      
  

 	}


 	public function ChdownXml(){
       
        $id_project = $_REQUEST['current'];
 		$username = null;
        $project_name = null;

        $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
        foreach ($array_status as $r){
                           
             $project_name = $r['project_name'];
             $username    = $r['user'];       
               
         }

        $path = FCPATH."owncloud/data/$username/files/$project_name/input/submission.xml"; 
        
        if(file_exists($path)){

            echo json_encode("true");
        }else{
        	echo json_encode("false");

        }

 	}

 	public function loadXml($id_project){
       
       
 		$username = null;
        $project_name = null;

        $array_status = $this->mongo_db->get_where('sra_run',array('project_id' => $id_project));
        foreach ($array_status as $r){
                           
             $project_name = $r['project_name'];
             $username    = $r['user'];       
               
         }

        $path = FCPATH."owncloud/data/$username/files/$project_name/input/submission.xml"; 
        
        force_download($path,NULL);

 	}








 }

 ?>