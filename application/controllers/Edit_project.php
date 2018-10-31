<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/30/17
 * Time: 6:01 PM
 */
class  Edit_project extends CI_Controller
{

    public function __construct(){
        parent::__construct();

    }


    public function edit_project($id){
        $file_read = array('fastq');

        $project_path = $this->input->post("project_path") . "/input/";
        $project_program = $this->input->post("project_program");
        $project_platform_type = $this->input->post("project_platform_type");


        if ($this->input->post("save")){

             if($project_platform_type == "proton_without"){
                 $count_fasta = $this->numfile_fasta($project_path);
                 $data_project = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_sequencing" => $this->input->post("project_sequencing"),
                "project_permission" => $this->input->post("project_permission"),
                "project_type" => $this->input->post("project_type"),
                "project_program" => $this->input->post("project_program"),
                "project_analysis" => $this->input->post("project_analysis"),
                "project_platform_sam" => $this->input->post("project_platform"),
                "project_platform_type" => $this->input->post("project_platform_type"),
                "project_path" => $this->input->post("project_path"),
                "project_num_sam" => $count_fasta,
                "project_group_sam" => $count_fasta,
                "project_date_time" => date("Y-m-d H:i:s"));

                 //$this->checkfile_fasta($project_path);
                 $this->mongo_db->where(array("_id" => new \MongoId($id)))->set($data_project)->update('projects');
                 redirect("all_projects", "refresh");

             }else{

                 $show = $this->manage_file->num_file($file_read, $project_path);
                 $data_project = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_sequencing" => $this->input->post("project_sequencing"),
                "project_permission" => $this->input->post("project_permission"),
                "project_type" => $this->input->post("project_type"),
                "project_program" => $this->input->post("project_program"),
                "project_analysis" => $this->input->post("project_analysis"),
                "project_platform_sam" => $this->input->post("project_platform"),
                "project_platform_type" => $this->input->post("project_platform_type"),
                "project_path" => $this->input->post("project_path"),
                "project_num_sam" => $show,
                "project_group_sam" => $show / 2,
                "project_date_time" => date("Y-m-d H:i:s"));

                 $this->checkfile_run($project_path);
                 $this->mongo_db->where(array("_id" => new \MongoId($id)))->set($data_project)->update('projects');
                 redirect("all_projects", "refresh");
             }     
        }


      
        $data['rs'] = $this->mongo_db->get_where('projects', array("_id" => new \MongoId($id)));
        $this->load->view("header", $data);
        $this->load->view("edit_project", $data);
        $this->load->view("footer");


    }


     public function checkfile_run($project_path){

       $path = FCPATH."$project_path";
       $sampleName = array();
       $search_fastq = glob($path."*.fastq");
        foreach ($search_fastq as $key => $value) {
            $var_name =  basename($value);
            $re_name = str_replace("-","t", $var_name);
            rename($path.$var_name,$path.$re_name);
            list($n1,$n2) = explode("_",$re_name);
            if($key%2 == 0){
                array_push($sampleName, $n1."\t");
            }    
        }

      

      $path_sampleName = FCPATH."$project_path"."sampleName.txt";
      file_put_contents($path_sampleName,$sampleName); 
    }


    public function checkfile_fasta($project_path){

        $name_list = array();
        $ref_fasta = array("gg_13_8_99.fasta",
                           "silva.v4.fasta",
                           "silva.bacteria.fasta",
                           "silva.v123.fasta",
                           "silva.v34.fasta",
                           "silva.v345.fasta",
                           "silva.v45.fasta",
                           "trainset16_022016.rdp.fasta");

         $path = $project_path;
         $findfasta = glob($path."*.{fasta,fst}", GLOB_BRACE); 
         foreach ($findfasta as $key => $file) {
            $name_fasta = basename($file);
             if(!in_array($name_fasta,$ref_fasta)){

                $re_name = str_replace("-","t", $name_fasta);
                rename($path.$name_fasta,$path.$re_name);
                list($n1,$n2) = explode("_",$name_fasta);
                array_push($name_list,$n1."\t");
             }
        } 
        $path_sampleName = $path."sampleName.txt";
        file_put_contents($path_sampleName,$name_list); 
    }



    public function numfile_fasta($project_path){
        $count_fasta = 0;
        $ref_fasta = array("gg_13_8_99.fasta",
                           "silva.v4.fasta",
                           "silva.bacteria.fasta",
                           "silva.v123.fasta",
                           "silva.v34.fasta",
                           "silva.v345.fasta",
                           "silva.v45.fasta",
                           "trainset16_022016.rdp.fasta");

         $path = $project_path;
         $findfasta = glob($path."*.{fasta,fst}", GLOB_BRACE); 
         foreach ($findfasta as $key => $file) {
             $name_fasta = basename($file);
             if(!in_array($name_fasta,$ref_fasta)){
                 $count_fasta++;
             }
        }
        return $count_fasta; 
    }
}