<?php  

  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Run_advance extends CI_Controller{


    public function __construct(){
      parent::__construct();
      $this->load->helper(array('url','path'));
      $this->load->helper('form');
      $this->load->library('form_validation');

      $this->load->controller('Run_owncloud');
        
    }

    public function form_value(){

        $user = $this->input->post('username');
        $id_project = $this->input->post('project');
        
        $array_project = $this->mongo_db->get_where('projects',array('_id' => new \MongoId($id_project)));
        foreach ($array_project as $r) {
          
                $project = $r['project_name'];
         }

      $maximum_ambiguous = $this->input->post('maximum_ambiguous');
      $maximum_homopolymer = $this->input->post('maximum_homopolymer');
      $minimum_reads_length = $this->input->post('minimum_reads_length');
      $maximum_reads_length = $this->input->post('maximum_reads_length');

      $alignment = $this->input->post('alignment');
      $customer  = $this->input->post('customer');

      if($customer != null){
           $alignment = $customer;
      }

      if($alignment == "silva"){
          $alignment = "silva.v4.fasta";
     }



      $diffs = $this->input->post('diffs');
      $classify = $this->input->post('classify');
      $cutoff = $this->input->post('cutoff');
      $optionsRadios = $this->input->post('optionsRadios');

      if($optionsRadios == '1'){
        $taxon = $this->input->post('taxon');
      }else{
        $taxon = "default";
      }


        //Path
         $path_in = "owncloud/data/admin/files/data_mothur/data/input/";
         $path_out = "owncloud/data/admin/files/data_mothur/data/output/";

      echo $user ."<br/>";
      echo $project ."<br/>";

      echo $maximum_ambiguous."<br/>";
      echo $maximum_homopolymer."<br/>";
      echo $minimum_reads_length."<br/>";
      echo $maximum_reads_length."<br/>";

      echo $alignment."<br/>";
      echo $diffs."<br/>";
        
        echo $classify."<br/>";
        echo $cutoff."<br/>";

        echo $optionsRadios."<br/>";
        echo $taxon."<br/>";

  
        $this->Run_owncloud->index('admin','data_mothur',$maximum_ambiguous,$maximum_homopolymer,$minimum_reads_length,$maximum_reads_length,$alignment,$diffs,$classify,$cutoff,$optionsRadios,$taxon,$path_in,$path_out);





    }

  }

?>