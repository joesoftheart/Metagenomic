<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 8:34 PM
 */
class General_statistic extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index()
    {
        ob_start();


        $data['rs_users'] = $this->mongo_db->get('user_login');
        $data['rs_projects'] = $this->mongo_db->get('projects');
        $id_me = (string)$this->session->userdata['logged_in']['_id'];
        $data['rs_your_p'] = $this->mongo_db->get_where('projects', array('user_id' => new MongoId($id_me)));
        $data['rs_ticket'] = $this->mongo_db->get('ticket_support');
        $data['rs_u_ticket'] = $this->mongo_db->get_where('ticket_support', array('user_id' => $id_me));
        // $data['rs_user'] = $this->mongo_db->get('user_login');


        $this->load->view('header', $data);
        $this->load->view('general_statistic', $data);
        $this->load->view('footer');
    }

}