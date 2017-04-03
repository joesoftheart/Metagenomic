<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/3/17
 * Time: 8:04 PM
 */


class Messages extends CI_Controller{

    function __construct()
    {
        parent::__construct();



    }



    public function index(){
        $data['rs_mes'] = $this->mongo_db->get('messages');
        $data['rs_message'] = $this->mongo_db->get('messages');

        $this->load->view('header',$data);
        $this->load->view('messages',$data);
        $this->load->view('footer');

    }





}