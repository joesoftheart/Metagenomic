<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 9:14 PM
 */


class Backend_statistic extends CI_Controller{



    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){

        $this->load->view('header');
        $this->load->view('backend_statistic');
        $this->load->view('footer');

    }

}