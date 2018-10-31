<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 6:56 PM
 */
class New_projects extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
        //$this->load->library('mongo_db', array('activate'=>'metagenomic_db'),'mongo_db');

    }


    public function index()
    {
        ob_start();


        $this->load->view('header');
        $this->load->view('new_projects');
        $this->load->view('footer');

    }

    public function insert_project()
    {
        if ($this->input->post("save") != null) {


            $file_read = array('fastq');

            $project_path = $this->input->post("project_path") . "/input/";
            $project_program = $this->input->post("project_program");
            $project_platform_type = $this->input->post("project_platform_type");

            if($project_platform_type == "proton_without" ){

                $count_fasta = $this->numfile_fasta($project_path);              
                $data = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_sequencing" => $this->input->post("project_sequencing"),
                "project_type" => $this->input->post("project_type"),
                "project_program" => $this->input->post("project_program"),
                "project_analysis" => $this->input->post("project_analysis"),
                "project_permission" => $this->input->post("project_permission"),
                "project_path" => $this->input->post("project_path"),
                "project_platform_sam" => $this->input->post("project_platform"),
                "project_platform_type" => $this->input->post("project_platform_type"),
                "project_num_sam" => $count_fasta,
                "project_group_sam" => $count_fasta,
                "project_date_time" => date("Y-m-d H:i:s"),
                "user_id" => $this->session->userdata["logged_in"]["_id"]);
                 $this->mongo_db->insert('projects', $data);

            }else if($project_platform_type != "proton_without"){
           
               $show = $this->manage_file->num_file($file_read, $project_path);
               $data = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_sequencing" => $this->input->post("project_sequencing"),
                "project_type" => $this->input->post("project_type"),
                "project_program" => $this->input->post("project_program"),
                "project_analysis" => $this->input->post("project_analysis"),
                "project_permission" => $this->input->post("project_permission"),
                "project_path" => $this->input->post("project_path"),
                "project_platform_sam" => $this->input->post("project_platform"),
                "project_platform_type" => $this->input->post("project_platform_type"),
                "project_num_sam" => $show,
                "project_group_sam" => $show / 2,
                "project_date_time" => date("Y-m-d H:i:s"),
                "user_id" => $this->session->userdata["logged_in"]["_id"]);
                 $this->mongo_db->insert('projects', $data);
            }
         
            
            # check Tool
            if($project_program == "mothur"){

                $this->create_symbolic_link($this->input->post("project_path"));

            }elseif ($project_program == "qiime") {

                $this->create_symbolic_link_qiime($this->input->post("project_path"));
                # sampleName.txt
                if($project_platform_type == "proton_without"){
                     $this->checkfile_fasta($project_path);

                }else{
                     $this->checkfile_run($project_path);
                }

            }elseif ($project_program == "mothur_qiime") {

                $this->create_symbolic_link($this->input->post("project_path"));
                $this->create_symbolic_link_qiime($this->input->post("project_path"));
                # sampleName.txt
                if($project_platform_type == "proton_without"){
                     $this->checkfile_fasta($project_path);

                }else{
                     $this->checkfile_run($project_path);
                }

            }elseif ($project_program == "qiime2") {

                $this->create_symbolic_link_qiime2_fasta($this->input->post("project_path"));
                # sampleName.txt
                if($project_platform_type == "proton_without"){
                     $this->checkfile_fasta_qiime2($project_path);

                }else{
                     //$this->checkfile_run($project_path);
                }

            }

             redirect("main", "refresh");
        }


    }


    public function create_symbolic_link($private_path)
    {
        $path_array = array("/var/www/html/owncloud/data/path_shared/99_otu_map.txt", "/var/www/html/owncloud/data/path_shared/gg_13_8_99.fasta",
            "/var/www/html/owncloud/data/path_shared/gg_13_8_99.gg.tax", "/var/www/html/owncloud/data/path_shared/silva.v4.fasta");

        $replace = str_replace("..", "/var/www/html", $private_path);
        $path_private = $replace . "/input/";
        // echo $path_private;
        foreach ($path_array as $value) {
            exec("./Scripts/symbolic.sh $value  $path_private");
            // echo $test;
        }

        $this->create_symbolic_link_primer16S($this->input->post("project_path"));

    }


    public function create_symbolic_link_primer16S($private_path)
    {

        $path_array = array(
            "/var/www/html/owncloud/data/primer16S/silva.nr_v128.align",
            "/var/www/html/owncloud/data/primer16S/silva.nr_v128.tax",
            "/var/www/html/owncloud/data/primer16S/silva.bacteria.fasta",
            "/var/www/html/owncloud/data/primer16S/silva.v123.fasta",
            "/var/www/html/owncloud/data/primer16S/silva.v34.fasta",
            "/var/www/html/owncloud/data/primer16S/silva.v345.fasta",
            "/var/www/html/owncloud/data/primer16S/silva.v45.fasta",
            "/var/www/html/owncloud/data/primer16S/trainset16_022016.rdp.fasta",
            "/var/www/html/owncloud/data/primer16S/trainset16_022016.rdp.tax"

        );

        $replace = str_replace("..", "/var/www/html", $private_path);
        $path_private = $replace . "/input/";

        foreach ($path_array as $value) {
            exec("./Scripts/symbolic.sh $value  $path_private");

        }
    }


    public function create_symbolic_link_qiime($private_path){

        $path_array = array(
            "/var/www/html/owncloud/data/path_qiime/alpha_params.txt",
            "/var/www/html/owncloud/data/path_qiime/uc_fast_paramsmodi.txt"
        );

        $replace = str_replace("..", "/var/www/html", $private_path);
        $path_private = $replace . "/input/";

        foreach ($path_array as $value) {
            exec("./Scripts/symbolic.sh $value  $path_private");

        }
    }
    

    public function create_symbolic_link_qiime2_fasta($private_path){

         $path_array = array(
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayFullLength.qza",
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayV13.qza",
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayV34.qza",
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayV35.qza",
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayV4.qza",
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayV45.qza",
            "/var/www/html/owncloud/data/path_qiime2018/classifierNaiveBayV56.qza",
            "/var/www/html/owncloud/data/path_qiime2018/gg_13_8_99_otus.qza",
            "/var/www/html/owncloud/data/path_qiime2018/v13_27F_534R.qza",
            "/var/www/html/owncloud/data/path_qiime2018/v34_341F_802R.qza",
            "/var/www/html/owncloud/data/path_qiime2018/v35_341F_909R.qza",
            "/var/www/html/owncloud/data/path_qiime2018/v45_518F_926R.qza",
            "/var/www/html/owncloud/data/path_qiime2018/v4_515F_806R.qza",
            "/var/www/html/owncloud/data/path_qiime2018/v56_785F_1081R.qza"
        );

        $replace = str_replace("..", "/var/www/html", $private_path);
        $path_private = $replace . "/input/";

        foreach ($path_array as $value) {
            exec("./Scripts/symbolic.sh $value  $path_private");

        }

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
                 array_push($name_list, $n1."\t");
             }
        } 

         $path_sampleName = $path."sampleName.txt";
         file_put_contents($path_sampleName,$name_list); 
    }



    public function checkfile_fasta_qiime2($project_path){

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
                 
                 $search = array('-','_');
                 $re_name = str_replace($search,"t", $name_fasta);
                 rename($path.$name_fasta,$path.$re_name);
                 list($n1,$n2) = explode(".",$re_name);
                 array_push($name_list, $n1."\t");
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