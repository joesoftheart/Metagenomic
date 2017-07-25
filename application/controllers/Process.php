
<?php
defined('BASEPATH') Or exit('No direct script access allowed');


class Process extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
    }



    public function index($id_project){
        ob_start();
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
        $this->load->view('header',$data);
        $this->load->view('process',$data);
        $this->load->view('footer');

    }


}