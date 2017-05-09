<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/30/17
 * Time: 6:01 PM
 */

class  Edit_project extends CI_Controller {

    public function __construct()
    {
        parent::__construct();


    }


    public function index(){


    }

    public function edit_project($id){

        if ($this->input->post("save")){
            $data_project = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_permission" => $this->input->post("project_permission"),
                "project_type" => $this->input->post("project_type"),
                "project_path" => $this->input->post("project_path")
            );

            $this->mongo_db->where(array("_id" => new \MongoId($id)))->set($data_project)->update('projects');
            redirect("all_projects", "refresh");

        }
        $data['rs'] = $this->mongo_db->get_where('projects', array("_id" => new \MongoId($id)));
        $data['rs_mes'] = $this->mongo_db->get('messages');
        $this->load->view("header",$data);
        $this->load->view("edit_project",$data);
        $this->load->view("footer");




    }
}