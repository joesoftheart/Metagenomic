<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/11/17
 * Time: 10:38 PM
 */
class Setting extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {

        $data['rs_st'] = $this->mongo_db->get('settings');

        $this->load->view('header', $data);
        $this->load->view('setting');
        $this->load->view('footer');


    }


    public function update_setting()
    {
        $data = array("noti_process" => $this->input->post("noti_process"),
            "noti_reboot" => $this->input->post("noti_reboot"),
            "noti_nax_storage" => $this->input->post("noti_max_storage"),
            "auto_logout" => $this->input->post("auto_logout"),
            "noti_email" => $this->input->post("noti_email"));
        print_r($data);


    }


}