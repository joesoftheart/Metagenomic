<?php
defined('BASEPATH') OR exit('No direct access script allowed');




class All_samples extends CI_Controller{

    public function __construct()
    {


        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){

        $this->load->view('header');
        $this->load->view('all_samples');
        $this->load->view('footer');



    }


}