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

        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');

      //  $data['rs'] = $this->mongo_db->get('projects');
        $id = ($this->session->userdata['logged_in']['_id']);
        $id = (string)$id;

        $share_project = $this->mongo_db->where(array("id_owner" => $id))->get('share_project');
        $to_you_project = $this->mongo_db->where(array("id_receiver" => $id))->get('share_project');
        $project = $this->mongo_db->select(array('_id', 'project_name'))->get('projects');
        $all_user = $this->mongo_db->select(array("_id","user_name"))->get('user_login');
        $project_show = array();

        $i =0;
        foreach ($share_project as $id_share){
            foreach ($project as $id_pro) {
                foreach ($all_user as $id_all_u) {
                    if ($id_pro['_id'] == $id_share['id_project'] and $id_all_u['_id'] == $id_share['id_receiver']) {
                        $project_show[$i]['owner_name'] = $this->session->userdata['logged_in']['username'];
                        $project_show[$i]['project_name'] = $id_pro['project_name'];
                        $project_show[$i]['receiver_name'] = $id_all_u['user_name'];
                        $project_show[$i]['id_share'] = $id_share['_id'];


                    }
                }
            }
            $i++;
        }


        $j =0;
        foreach ($to_you_project as $id_to_you){
            foreach ($project as $id_pro) {
                foreach ($all_user as $id_all_u) {
                    if ($id_pro['_id'] == $id_to_you['id_project'] and $id_all_u['_id'] == $id_to_you['id_receiver']) {
                        $project_to_u[$j]['owner_name'] = $id_all_u['user_name'];
                        $project_to_u[$j]['project_name'] = $id_pro['project_name'];
                        $project_to_u[$j]['receiver_name'] = $this->session->userdata['logged_in']['username'];
                        $project_to_u[$j]['id_share'] = $id_to_you['_id'];


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