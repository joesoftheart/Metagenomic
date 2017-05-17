<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 5:48 PM
 */
class Samples extends CI_Controller{

    public function __construct()
    {


        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){
        ob_start();
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
        $this->load->view('header',$data);
        $this->load->view('samples');
        $this->load->view('footer');

    }


}