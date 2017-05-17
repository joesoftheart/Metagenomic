<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/12/17
 * Time: 4:54 PM
 */


class Edit_profile extends CI_Controller{

    public function __construct()
    {

        parent::__construct();
    }




    public function edit_profile($id_user){

        ob_start();
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');


        $data['rs_user'] = $this->mongo_db->get_where('user_details',array("user_id" => $id_user));



        $this->load->view('header',$data);
        $this->load->view('edit_profile');
        $this->load->view('footer');

    }

}