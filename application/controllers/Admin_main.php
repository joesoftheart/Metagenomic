<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/21/17
 * Time: 7:41 PM
 */

class Admin_main extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
    }


    public function index(){
        $this->load->view('admin_header');
        $this->load->view('admin_main');
        $this->load->view('admin_footer');

    }

}