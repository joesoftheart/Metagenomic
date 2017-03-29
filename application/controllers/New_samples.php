<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/27/17
 * Time: 10:56 PM
 */


class New_samples extends CI_Controller{
    public function __construct()
    {


        parent::__construct();
    }



    public function index(){
        $this->load->view('header');
        $this->load->view('new_samples');
        $this->load->view('footer');



    }


}