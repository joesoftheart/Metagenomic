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
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_user'] = $this->mongo_db->get('profile');
        $this->load->view('header',$data);
        $this->load->view('profile',$data);
        $this->load->view('footer');



    }


}