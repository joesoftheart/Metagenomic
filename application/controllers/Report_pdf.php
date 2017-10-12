<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_pdf extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
    }


    public function index(){




    }

    public function fpdf($id_project){
        $data = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));

        foreach ($data as $value){
            $project_name = $value['project_name'];

        }
        $this->load->library('myfpdf');
        $this->load->library('mytcpdf');
        $data['txt'] = $project_name;
        $this->load->view('reportpdf',$data);
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