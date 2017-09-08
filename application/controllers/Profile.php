<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/3/17
 * Time: 11:15 PM
 */

class Profile extends CI_Controller{


    public function __construct()
    {
        parent::__construct();
    }



    public function index(){
        ob_start();
        $id_user = (string)$this->session->userdata['logged_in']['_id'];
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
        $data['rs_user'] = $this->mongo_db->get_where('user_details',array("user_id" => $id_user));

        $this->load->view('header',$data);
        $this->load->view('profile',$data);
        $this->load->view('footer');



    }

    public function change_img($username,$id_user)
    {
        echo "OK";
        $data = array();
        $config = array(
            'upload_path' => 'images/',
            'allowed_types' => 'gif|jpg|png|jpeg',
            'max_size' => '1024',
            'max_width' => '1024',
            'max_height' => '1024',
            'file_name' => 'joesoftheart.png'
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('pictures')) {
            $error = array('error' => $this->upload->display_errors());
             var_dump( $error); die; //check errors
        } else {
            $fileName = $this->upload->data();
            $data['pictures'] = $fileName['file_name'];
        }
        $data_user = array("first_name" => "", "last_name" => "",
            "address" => "",
            "tel" => "",
            "gender" => "",
            "user_id" => $id_user,
            "user_img_profile" => $this->uri->segment(4));

        $data = $this->mongo_db->get_where('user_details',array("_id" => $id_user));
        if ($data != null ){
            $this->mongo_db->where(array("user_id" => $id_user))->set($data_user)->update('user_details');
            redirect("profile", "refresh");
        }else{
            $this->mongo_db->insert('user_details', $data_user);
            redirect('profile', 'refresh');
        }

       // redirect("profile", "refresh");
    }


    public function update_profile($id_user){
        $data_user = array("first_name" => $this->input->post("first_name"),
            "last_name" => $this->input->post("last_name"),
            "address" => $this->input->post("address"),
            "tel" => $this->input->post("tel"),
            "gender" => $this->input->post("gender"),
            "user_id" => $id_user,
            "user_img_profile" => "");




        $data = $this->mongo_db->get_where('user_details',array("user_id" => $id_user));
        if ($data != null ){
            $this->mongo_db->where(array("user_id" => $id_user))->set($data_user)->update('user_details');
            redirect("profile", "refresh");
        }else{
            $this->mongo_db->insert('user_details', $data_user);
            redirect('profile', 'refresh');
        }








    }


}