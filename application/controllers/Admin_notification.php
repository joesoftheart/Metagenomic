<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/21/17
 * Time: 12:17 AM
 */
class Admin_notification extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {

        $data['rs_noti'] = $this->mongo_db->get('notification');

        $this->load->view('admin_header');
        $this->load->view('admin_notification', $data);
        $this->load->view('admin_footer');

    }


    public function insert_noti()
    {
        $time = new MongoTimestamp();
        $data = array("subject" => $this->input->post('subject'),
            "description" => $this->input->post('description'),
            "new" => $this->input->post('new'),
            "status" => $this->input->post('status'),
            "date" => $time

        );

        $this->mongo_db->insert('notification', $data);

        redirect('admin_notification', 'refresh');

    }

    public function delete_noti($id)
    {

        $this->mongo_db->where(array("_id" => new \MongoId($id)))->delete('notification');
        redirect('admin_notification', 'refresh');

    }


}