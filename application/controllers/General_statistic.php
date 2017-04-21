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

        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
        $data['rs_users'] = $this->mongo_db->get('user_login');
        $data['rs_projects'] = $this->mongo_db->get('projects');
         $data['rs_ticket'] = $this->mongo_db->get('ticket_support');
        // $data['rs_user'] = $this->mongo_db->get('user_login');




        $this->load->view('header');
        $this->load->view('general_statistic',$data);
        $this->load->view('footer');
    }

}