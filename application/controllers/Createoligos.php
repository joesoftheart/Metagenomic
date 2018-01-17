<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Createoligos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {


        ob_start();


        $data['rs'] = $this->mongo_db->get_where('projects', array("user_id" => $this->session->userdata["logged_in"]["_id"]));
        $this->load->view('header');
        $this->load->view('create_oligos', $data);
        $this->load->view('footer');
    }

}

