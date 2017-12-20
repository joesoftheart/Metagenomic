<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/21/17
 * Time: 8:57 PM
 */
class Notification_all extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        ob_start();

        $data['rs_notifi'] = $this->mongo_db->get('notification');
        $data['rs_mes'] = $this->mongo_db->get('messages');


        $this->load->view('header', $data);
        $this->load->view('notification_all', $data);
        $this->load->view('footer');


    }


}