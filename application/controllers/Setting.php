<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/11/17
 * Time: 10:38 PM
 */
class Setting extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
    }



    public function index(){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');

        $this->load->view('header',$data);
        $this->load->view('setting');
        $this->load->view('footer');


    }


}