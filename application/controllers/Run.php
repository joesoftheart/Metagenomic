<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/8/17
 * Time: 6:59 PM
 */
class Run extends CI_Controller{



    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
        include (APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
    }


    public function run(){

        $user = "joesoftheart";
        $project = "SAMPLE-WES1053";
        $path = "owncloud/data/$user/files/$project/data/input/stability.files";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";


       // file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);


        $cmd = "php -f Scripts/test_run.php";

        exec($cmd);



    }
    public function loopp(){
        $cmd = "/usr/bin/php -f Scripts/loopp.php";

       echo exec($cmd);


    }

}