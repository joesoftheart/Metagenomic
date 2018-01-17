<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/20/17
 * Time: 5:38 PM
 */
class Session_login extends CI_Controller
{


    public function __construct()
    {

        parent::__construct();
        $this->load->library('session');


    }


    public function index()
    {


        if ($this->input->post("login") != null) {
            if ($this->input->post("username") == "admin" && $this->input->post("password") == "admin") {
                $ar = array("mysess_id" => $this->input->post("username"));


                $this->session->set_userdata($ar);
            }
        }


        if ($this->session->userdata("mysess_id") == null) {
            $data['sess'] = $this->form();
        } else {
            $data['sess'] = $this->session->userdata('mysess_id') . " " . anchor("session_login/logout", "Logout");
        }
        $this->load->view('header');
        $this->load->view('session_login', $data);
        $this->load->view('footer');


    }

    private function form()
    {
        $frm = "<form method='post' action=''>";
        $frm .= "<p>username : <input type='text' name='username' value=''/> ";
        $frm .= "password : <input type='text' name='password' value='' />";
        $frm .= "&nbsp;<input type='submit' name='login' value='submit'/></p>";
        $frm .= "</form>";
        return $frm;


    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect("session_login", "refresh");
        exit();
    }


}