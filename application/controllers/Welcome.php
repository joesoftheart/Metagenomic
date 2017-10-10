<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
    }


    public function index(){




    }

    public function fpdf(){
        $this->load->library('myfpdf');
        $this->load->library('mytcpdf');
        $data['txt'] = "My name is joesoftheart  hahahahahaha";
        $this->load->view('welcome',$data);
    }


    public function fpdf_html(){
        $this->load->library('myfpdf');
        $this->load->library('mytcpdf');
        $data['txt'] = "My name is joesoftheart  hahahahahaha";
        $this->load->view('welcome_html',$data);
    }


}




/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/22/17
 * Time: 2:52 PM
 */

?>