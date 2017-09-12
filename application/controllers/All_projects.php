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
        $data['rs']  = $this->mongo_db->get_where('projects', array("user_id" => $this->session->userdata["logged_in"]["_id"]));
        $data['rs_user'] = $this->mongo_db->get('user_login');

        $this->load->view('header',$data);
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
        $query = $this->mongo_db->where($data)->get('share_project');
        if (count($query) > 0){

            echo "alrady exit";
            sleep(2);
            redirect("all_projects", "refresh");
        }else {
            echo "insert success";
            $this->mongo_db->insert('share_project', $data);

            sleep(2);
            redirect("all_projects", "refresh");
        }

    }


}