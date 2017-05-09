<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/7/17
 * Time: 5:59 PM
 */
class Used_resource extends CI_Controller{


    public function __construct()
    {


        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }

    public function index(){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
//        $data_cpu = $this->get_server_cpu_usage();
//        $data_ram = $this->get_server_memory_usage();
//        $data['rs'] = array("data_cpu" => $data_cpu, "data_ram" => $data_ram);
        $username = ($this->session->userdata['logged_in']['username']);
        $disk_me = exec('du -sh  /var/www/html/owncloud/data/ | grep G'.$username.'/files');
        $disk_other = exec('du -sh  /var/www/html/owncloud/data/ | grep G');
        $disk_me = (string)$disk_me;
        $disk_me = explode("\t", $disk_me);
        $disk_me =  trim($disk_me[0],'G');


        if ($disk_me != null){
            $data['rs_dm'] = $disk_me;
        }else{
            $data['rs_dm'] = 0;
        }

        $disk_other = (string)$disk_other;
        $disk_other = explode("\t", $disk_other);
        $disk_other = trim($disk_other[0], "G");

        if ($disk_other != null){
            $data['rs_do'] = $disk_other;
        }else{
            $data['rs_do'] = 0;
        };




        $this->load->view('header',$data);
        $this->load->view('used_resource');
        $this->load->view('footer');




    }

    public function get_server_memory_usage(){

        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = $mem[2]/$mem[1]*100;
        print $memory_usage;
        return $memory_usage;
    }

    public function get_server_cpu_usage(){

        $load = sys_getloadavg();
        print $load[0];
        return $load[0];

    }
}