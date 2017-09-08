<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

    }

	public function index()
	{
        ob_start();
            $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
            $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
            $data['rs']  = $this->mongo_db->get_where('projects', array("user_id" => $this->session->userdata["logged_in"]["_id"]));
            $this->load->view('header', $data);
            $this->load->view('index', $data);
            $this->load->view('footer');
	}

    // Show login page
    public function login() {

        $this->load->view('login');
    }

// Check for user login process
    public function user_login_process() {

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if(isset($this->session->userdata['logged_in'])){
                $this->load->view('index');
            }else{
                redirect("main/login", "refresh");
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
                $result_admin = $this->login_database->read_user_admin($username);

                if ($result != false && $result_admin != false){
                    foreach ($result as $rs) {

                        $session_data = array('username' => $rs['user_name'], 'email' => $rs['user_email'],'_id' => $rs['_id']);
                    }
                    $this->session->set_userdata('logged_in', $session_data);
                    redirect("admin_main", "refresh");

                }else if ($result != false) {
                    //print_r($result);
                    foreach ($result as $rs) {

                        $session_data = array('username' => $rs['user_name'], 'email' => $rs['user_email'],'_id' => $rs['_id']);
                    }


                    // Add user data in session
                    $this->session->set_userdata('logged_in', $session_data);
                    $this->session->set_userdata('current_project','');

                    redirect("main", "refresh");
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
        $this->session->sess_destroy();
        $this->session->unset_userdata('current_project','');
        $data['message_display'] = 'Successfully Logout';
        $this->load->view('login', $data);
    }




}

