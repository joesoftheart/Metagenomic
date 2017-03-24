<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 5:59 PM
 */
class Used_resource extends CI_Controller{


    public function __construct()
    {


        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){

        $this->load->view('header');
        $this->load->view('used_resource');
        $this->load->view('footer');



    }
}