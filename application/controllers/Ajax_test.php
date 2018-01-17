<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/22/17
 * Time: 8:22 PM
 */
class Ajax_test extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
    }


    public function index()
    {
        $this->load->view('header');
        $this->load->view('ajax_test');
        $this->load->view('footer');

    }


    public function call_data()
    {


//         echo chdir("../test_run/exampledatatraining/exampledatatraining/");
//         echo  "My name joe";
//         echo getcwd();
//         $run = "../../../mothur/mothur preprocess.batch";
//         echo exec($run);
        echo "My name joe the end My name joe the endMy name joe the endMy name joe the endMy name joe the endMy name
          joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the end";
        echo "My name joe the end My name joe the endMy name joe the endMy name joe the endMy name joe the endMy name
          joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the end";
        echo "My name joe the end My name joe the endMy name joe the endMy name joe the endMy name joe the endMy name
          joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the end";
        echo "My name joe the end My name joe the endMy name joe the endMy name joe the endMy name joe the endMy name
          joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the endMy name joe the end";
    }


}