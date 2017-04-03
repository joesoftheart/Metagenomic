<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/14/17
 * Time: 10:14 PM
 */
class Update extends CI_Controller{


    function __construct()
    {
        parent::__construct();

    }


    function  index(){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs'] = $this->mongo_db->get('users');
        $this->load->view('header',$data);
        $this->load->view('update',$data);
        $this->load->view('footer');


    }


    public function update_data($id_edit){
        //

        if ($this->input->post('save') != null) {
            $data = array ("name" => $this->input->post("name"),
                "select" => $this->input->post("input")
            );
            $this->mongo_db->where(array("_id" => new \MongoId($id_edit)))->set($data)->update('users');
            redirect("insert", "refresh");


        }else{
            $data['rs'] = $this->mongo_db->get_where('users', array('_id' => new \MongoId($id_edit)));
            $this->load->view('header');
            $this->load->view('update',$data);
            $this->load->view('footer');
        }



    }


}