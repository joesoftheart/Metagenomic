<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/27/17
 * Time: 7:59 PM
 */

class Share_projects extends CI_Controller{


    public function __construct()
    {
        parent::__construct();
    }

    public function index(){

        $data['rs'] = $this->mongo_db->get('share_project');

        $this->load->view('header');
        $this->load->view('share_projects',$data);
        $this->load->view('footer');

    }


}