<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 8:34 PM
 */
class General_statistic extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){


        $this->load->view('header');
        $this->load->view('general_statistic');
        $this->load->view('footer');
    }

}