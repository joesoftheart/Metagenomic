<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/24/17
 * Time: 6:28 PM
 */
class Other_test extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        $this->load->view('header');
        $this->load->view('other_test');
        $this->load->view('footer');
    }


}