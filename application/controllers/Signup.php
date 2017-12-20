<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 8:48 PM
 */
class Signup extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }


    public function index()
    {


        $this->load->view('header');
        $this->load->view('signup');
        $this->load->view('footer');

    }

// Show registration page
    public function user_registration_show()
    {
        $this->load->view('signup');

    }

// Validate and store registration data in database
    public function new_user_registration()
    {

// Check validation for user input in SignUp form
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email_value', 'Email', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('signup');
        } else {
            $data = array(
                'user_name' => $this->input->post('username'),
                'user_email' => $this->input->post('email_value'),
                'user_password' => $this->input->post('password')
            );
            $result = $this->login_database->registration_insert($data);
            if ($result == TRUE) {
                $data['message_display'] = 'Registration Successfully !';
                $this->load->view('login', $data);
            } else {
                $data['message_display'] = 'Username already exist!';
                $this->load->view('signup', $data);
            }
        }
    }

}

