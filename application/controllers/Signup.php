<?php
defined('BASEPATH') OR exit ('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 8:48 PM
 */


class Signup extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }


    public function index(){


        $this->load->view('header');
        $this->load->view('signup');
        $this->load->view('footer');

    }

}

