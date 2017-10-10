<?php
defined('BASEPATH') OR exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 6:56 PM
 */
class New_projects extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
        //$this->load->library('mongo_db', array('activate'=>'metagenomic_db'),'mongo_db');

    }


    public function index(){
        ob_start();


        $this->load->view('header');
        $this->load->view('new_projects');
        $this->load->view('footer');

    }

    public function  insert_project(){
        if ($this->input->post("save") != null){
            $data = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_sequencing" => $this->input->post("project_sequencing"),
                "project_type" => $this->input->post("project_type"),
                "project_program" => $this->input->post("project_program"),
                "project_analysis" => $this->input->post("project_analysis"),
                "project_permission" => $this->input->post("project_permission"),
                "project_path" => $this->input->post("project_path"),
                "user_id" => $this->session->userdata["logged_in"]["_id"]);

            $this->mongo_db->insert('projects', $data);
            $this->create_symbolic_link($this->input->post("project_path"));

           // echo $this->input->post("project_path");
            redirect("main", "refresh");


        }




    }



    public function create_symbolic_link($private_path){
        $path_array = array("/var/www/html/owncloud/data/path_shared/99_otu_map.txt","/var/www/html/owncloud/data/path_shared/gg_13_8_99.fasta",
            "/var/www/html/owncloud/data/path_shared/gg_13_8_99.gg.tax","/var/www/html/owncloud/data/path_shared/silva.v4.fasta");

        $replace = str_replace("..","/var/www/html",$private_path);
        $path_private = $replace."/input/";
       // echo $path_private;
        foreach ($path_array as $value){
            exec("./Scripts/symbolic.sh $value  $path_private");
           // echo $test;
        }


    }


}