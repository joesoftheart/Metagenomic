<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Graph_result extends CI_Controller{

    public function __construct()
    {

        parent::__construct();


    }

    public function index($id_project)
    {
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        $this->load->view('header');
        $this->load->view('graph_result',$data);
        $this->load->view('footer');

    }


}



/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/10/17
 * Time: 4:44 PM
 */