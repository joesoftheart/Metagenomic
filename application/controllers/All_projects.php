<?php
defined('BASEPATH') OR exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 9:35 PM
 */

class All_projects extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){
        $this->load->view('header');
        $this->load->view('all_projects');
        $this->load->view('footer');

    }


}