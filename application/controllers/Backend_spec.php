<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 9:14 PM
 */


class Backend_spec extends CI_Controller{



    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){
        ob_start();

        $data['rs']  = $this->mongo_db->get_where('projects', array("user_id" => $this->session->userdata["logged_in"]["_id"]));
        $data['rs_cpu'] = shell_exec('lscpu');

        $this->load->view('header',$data);
        $this->load->view('backend_spec',$data);
        $this->load->view('footer');

    }

}