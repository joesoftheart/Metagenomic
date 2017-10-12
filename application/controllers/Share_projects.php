<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/27/17
 * Time: 7:59 PM
 */

class Share_projects extends CI_Controller{


    public function __construct()
    {
        parent::__construct();
    }

    public function index(){


        $id = ($this->session->userdata['logged_in']['_id']);
        $id = (string)$id;

        $you_share_project = $this->mongo_db->where(array("id_owner" => $id))->get('share_project');
        $to_you_project = $this->mongo_db->where(array("id_receiver" => $id))->get('share_project');
        $project = $this->mongo_db->select(array('_id', 'project_name'))->get('projects');
        $all_user = $this->mongo_db->select(array("_id","user_name"))->get('user_login');
        $project_show = array();

        $i =0;
        foreach ($you_share_project as $you_share){
            foreach ($project as $pro) {
                foreach ($all_user as $all_u) {
                    if ($pro['_id'] == $you_share['id_project'] and $all_u['_id'] == $you_share['id_receiver']) {
                        $project_show[$i]['owner_name'] = $this->session->userdata['logged_in']['username'];
                        $project_show[$i]['project_name'] = $pro['project_name'];
                        $project_show[$i]['receiver_name'] = $all_u['user_name'];
                        $project_show[$i]['id_share'] = $you_share['_id'];


                    }
                }
            }
            $i++;
        }


       $project_to_u = array();

        $j =0;
        foreach ($to_you_project as $to_you){
            foreach ($project as $pro) {
                foreach ($all_user as $all_u) {
                    if ($pro['_id'] == $to_you['id_project'] and $all_u['_id'] == $to_you['id_owner']) {
                        $project_to_u[$j]['owner_name'] = $all_u['user_name'];
                        $project_to_u[$j]['project_name'] = $pro['project_name'];
                        $project_to_u[$j]['receiver_name'] = $this->session->userdata['logged_in']['username'];
                        $project_to_u[$j]['id_project'] = $pro['_id'];

                    }
                }
            }
            $j++;
        }


        $data['rs'] = $project_show;
        $data['rs_to_u'] = $project_to_u;

       // $data['rs'] = $this->mongo_db->where_in('_id',$project_show)->get('projects');

        $this->load->view('header',$data);
        $this->load->view('share_projects',$data);
        $this->load->view('footer');

    }

    public function delete_your_share($id){
        // echo $id;
        $this->mongo_db->where(array("_id" => new MongoId($id)))->delete('share_project');
        redirect("share_projects", "refresh");




    }


}