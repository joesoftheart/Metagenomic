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

    public function __construct()
    {
        parent::__construct();


    }


    public function index()
    {


    }

    public function edit_project($id)
    {
        $file_read = array('fastq');
        $project_path = $this->input->post("project_path") . "/input/";
        $show = $this->manage_file->num_file($file_read, $project_path);


        if ($this->input->post("save")) {
            $data_project = array("project_name" => $this->input->post("project_name"),
                "project_title" => $this->input->post("project_title"),
                "project_detail" => $this->input->post("project_detail"),
                "project_sequencing" => $this->input->post("project_sequencing"),
                "project_permission" => $this->input->post("project_permission"),
                "project_type" => $this->input->post("project_type"),
                "project_program" => $this->input->post("project_program"),
                "project_analysis" => $this->input->post("project_analysis"),
                "project_path" => $this->input->post("project_path"),
                "project_num_sam" => $show,
                "project_group_sam" => $show / 2,
                "project_date_time" => date("Y-m-d H:i:s")
            );

            $this->mongo_db->where(array("_id" => new \MongoId($id)))->set($data_project)->update('projects');
            redirect("all_projects", "refresh");

        }
        ob_start();
        $data['rs'] = $this->mongo_db->get_where('projects', array("_id" => new \MongoId($id)));

        $this->load->view("header", $data);
        $this->load->view("edit_project", $data);
        $this->load->view("footer");


    }
}