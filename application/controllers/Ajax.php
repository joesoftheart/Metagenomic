<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
    }



    public function index(){


    }

    public function sessiontimeout(){
        echo "Joeeeeeeeeee";

        $lastActivity = $this->session->userdata('last_activity');
        $configtimeout = $this->config->item("sess_expiration");
        $sessonExpireson = $lastActivity+$configtimeout;


        $threshold = $sessonExpireson - 300; //five minutes before session time out

        $current_time = time();

        if($current_time>=$threshold){
            $this->session->set_userdata('last_activity', time()); //THIS LINE DOES THE TRICK
            echo "Session Re-registered";
        }else{
            echo "Not yet time to re-register";
        }

        exit;
    }

}
?>