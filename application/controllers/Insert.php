<?php
defined('BASEPATH') OR exit('No direct script access allowed ');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/10/17
 * Time: 5:34 PM
 */
class Insert extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
       // $this->load->library('mongo_db', array('activate'=>'metagenomic_db'),'mongo_db');
        $CI = &get_instance();
        $CI->load->library("session");
    }



    public function index(){
        ob_start();
        $data['rs'] = $this->mongo_db->get('users');
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');

        $this->load->view('header');
        $this->load->view('insert',$data);
        $this->load->view('footer');

    }

    // insert data to mongodb
    public function insert_data(){


        if ($this->input->post("save") != null) {
            $data = array ("name" => $this->input->post("name"),
                "select" => $this->input->post("input")
                );
            echo $data['select'].$data['name'];
            $this->mongo_db->insert('users',$data);
            redirect("insert", "refresh");
        }
    }


    public function delete_data($id){
        $this->mongo_db->where(array("_id" => new \MongoId($id)))->delete('users');
        redirect("insert", "refresh");



    }





}