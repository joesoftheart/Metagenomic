<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/9/17
 * Time: 5:39 PM
 */
class Read_file extends CI_Controller{


    public function __construct()
    {

        parent::__construct();
        $this->load->helper('url');
        //      $this->load->helper('file');

    }

    public function index(){

        $this->load->view('header');
        $this->load->view('read_file');
        $this->load->view('footer');

    }

    // This upload file type
    public function upload_file(){
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1024';
        $config['max_width'] = '1024';
        $config['max_height'] = '1024';
        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload("pictures")) {
            $data = $this->upload->data();

        }else{
            echo $this->upload->display_errors();
        }
    }


}