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

        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');

        $this->load->view('header',$data);
        $this->load->view('new_projects');
        $this->load->view('footer');

    }

    public function  insert_project(){
        if ($this->input->post("save") != null){
            $data = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_type" => $this->input->post("project_type"),
                "project_permission" => $this->input->post("project_permission"),
                "project_path" => $this->input->post("project_path"),
                "user_id" => $this->session->userdata["logged_in"]["_id"]);

            $this->mongo_db->insert('projects', $data);
            redirect("main", "refresh");


        }




    }


}