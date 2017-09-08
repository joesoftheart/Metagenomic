<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/5/17
 * Time: 4:34 PM
 */
class Document extends CI_Controller{
    public function __construct()
    {

        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }


    public  function index(){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
        $this->load->view("header",$data);
        $this->load->view("document");
        $this->load->view("footer");

    }




}