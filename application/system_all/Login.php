<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/6/17
 * Time: 6:16 PM
 */
class Login extends CI_Controller{
    public function __construct() {
        parent::__construct();

// Load form helper library
        $this->load->helper('form');
// Load form validation library
        $this->load->library('form_validation');
        $this->load->helper('security');
        $this->load->library('session');
// Load database
     //   $this->load->database();
        $this->load->model('login_database');
    }
// Show login page
    public function index() {
        $this->load->view('login');
    }
// Show registration page
    public function user_registration_show() {
        $this->load->view('signup');
    }

// Validate and store registration data in database
    public function new_user_registration() {

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

// Check for user login process
    public function user_login_process() {

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if(isset($this->session->userdata['logged_in'])){
                $this->load->view('admin_page');
            }else{
                $this->load->view('login');
            }
        } else {
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password')
            );
            $result = $this->login_database->login($data);
            if ($result == TRUE) {

                $username = $this->input->post('username');
                $result = $this->login_database->read_user_information($username);
                if ($result != false) {

                    foreach ($result as $rs) {

                        $session_data = array('username' => $rs['user_name'], 'email' => $rs['user_email']);
                    }
// Add user data in session
                        $this->session->set_userdata('logged_in', $session_data);

                    $this->load->view('admin_page');
                }
            } else {
                $data = array(
                    'error_message' => 'Invalid Username or Password'
                );
                $this->load->view('login', $data);
            }
        }
    }

// Logout from admin page
    public function logout() {

// Removing session data
        $sess_array = array(
            'username' => ''
        );
        $this->session->unset_userdata('logged_in', $sess_array);
        $data['message_display'] = 'Successfully Logout';
        $this->load->view('login', $data);
    }

}

?>