<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/3/17
 * Time: 11:15 PM
 */

class Profile extends CI_Controller{


    public function __construct()
    {
        parent::__construct();
    }



    public function index(){
        $id_user = (string)$this->session->userdata['logged_in']['_id'];
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_user'] = $this->mongo_db->get_where('user_details',array("user_id" => $id_user));
        $this->load->view('header',$data);
        $this->load->view('profile',$data);
        $this->load->view('footer');



    }


    public function updata_profile($id){





    }


}