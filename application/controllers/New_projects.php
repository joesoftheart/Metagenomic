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


        $this->load->view('header');
        $this->load->view('new_projects');
        $this->load->view('footer');

    }

    public function  insert_project(){
        if ($this->input->post("save") != null){
            $data = array("name_project" => $this->input->post("name_project"),
                "title_project" => $this->input->post("title_project"));
            echo $data['name_project'] . $data['title_project'];

            $this->mongo_db->insert('projects', $data);
            redirect("main", "refresh");


        }




    }


}