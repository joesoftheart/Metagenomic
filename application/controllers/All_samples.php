<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/27/17
 * Time: 10:56 PM
 */


class All_samples extends CI_Controller{
    public function __construct()
    {


        parent::__construct();
    }



    public function index(){
        $data['rs_mes'] = $this->mongo_db->get('messages');
        $this->load->view('header',$data);
        $this->load->view('all_samples');
        $this->load->view('footer');



    }


}