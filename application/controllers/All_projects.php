<?php
defined('BASEPATH') OR exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 9:35 PM
 */

class All_projects extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){
        $data['rs'] = $this->mongo_db->get_where('projects',array("user_id" => $this->session->userdata["logged_in"]["_id"]));
        //$select = $this->mongo_db->
        //$data['rs'] = $this->mongo_db->where_in_all('user_id', array($this->session->userdata["logged_in"]["_id"]))->get('projects');

        $data['rs_user'] = $this->mongo_db->get('user_login');

        $this->load->view('header');
        $this->load->view('all_projects',$data);
        $this->load->view('footer');

    }


    public function delete_project($id){
        $this->mongo_db->where(array("_id" => new \MongoId($id)))->delete('projects');
        redirect("all_projects", "refresh");

    }

    public function share_project_to(){
        $data = array("id_owner" => $this->input->post('id_owner'),
            "id_project" => $this->input->post('id_project'),
            "id_receiver" => $this->input->post("id_receiver"));

        $this->mongo_db->insert('share_project', $data);
        echo "insert success";

    }


}