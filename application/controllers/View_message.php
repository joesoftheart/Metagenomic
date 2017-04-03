<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/3/17
 * Time: 9:32 PM
 */



class View_message extends CI_Controller{
    public function __construct()
    {
        parent::__construct();

    }


    public function view_message($id){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_message'] = $this->mongo_db->get_where('messages',array("_id" => new \MongoId($id)));
        $this->load->view('header',$data);
        $this->load->view('view_message',$data);
        $this->load->view('footer');





    }





}