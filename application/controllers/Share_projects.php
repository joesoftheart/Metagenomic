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

      //  $data['rs'] = $this->mongo_db->get('projects');
        $id = ($this->session->userdata['logged_in']['_id']);
        $id = (string)$id;

        $data_select = $this->mongo_db->where_or(array("id_receiver" => $id,"id_owner" => $id))->get('share_project');
        $id_pro = array();
        $i = 0;
        foreach ($data_select as $r){
            $id_pro[$i] =  $r['id_project'];
            $i++;
        }
        print_r($id_pro);
        $data['rs_share'] = $this->mongo_db->where_in($id_pro)->get('projects');
        print_r($data);



        $this->load->view('header');
        // $this->load->view('share_projects',$data);
        $this->load->view('footer');

    }


}