<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class Run_picrust_stamp extends CI_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','path','file','date'));
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library("session");

        include(APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
		
	}
    
    public function index(){

         $id = $this->uri->segment(3);
         $data['samname'] = $this->samset($id);
    	   $this->session->set_userdata('current_project',$id);
         $this->load->view('header');
         $this->load->view('run_picrust_stamp',$data);
         $this->load->view('footer');

    }

    public function samset($id_project){

      $samplename = array();
     
      $sample_name = null ;
       
      #Query data Sample_Name
      $array_samples = $this->mongo_db->get_where('sample_name',array('project_id' => $id_project));
           foreach ($array_samples as $r) {
             $sample_name = $r['name_sample'];
           }

         for ($i=0; $i < sizeof($sample_name); $i++) {  
             for ($j= 1; $j < sizeof($sample_name)-$i; $j++) { 
             	array_push($samplename ,$sample_name[$i]." --- ".$sample_name[$j+$i]);
             }	  
         }
      return $samplename;

    }

    public function runqueue($id){

    $username = $this->session->userdata['logged_in']['username'];
    $project_name = null;
    $project_analysis = null;
    $project_path = null;
         
 	$read = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id)));
 	foreach ($read as $r) {
          	  $project_name = $r['project_name'];
          	  $project_analysis = $r['project_analysis'];
              $project_path = $r['project_path'];
     }

       
    	$kegg = $this->input->post('kegg');
    	$sample_comparison = $this->input->post('sample_comparison');
    	$statistical_test = $this->input->post('statistical_test');
    	$ci_method = $this->input->post('ci_method');
    	$p_value = $this->input->post('p_value');

        echo $username."<br/>";
        echo $project_name."<br/>";
        echo $kegg."<br/>";
        echo $sample_comparison."<br/>";
        echo $statistical_test."<br/>";
        echo $ci_method."<br/>";
        echo $p_value."<br/>";

      if($project_analysis == "phylotype"){

      }else{

      }







    }




}

?>