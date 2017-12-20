<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_pdf extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {


    }

    public function fpdf($id_project)
    {
        $data['projects_t'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        $data['projects_run_t'] = $this->mongo_db->get_where('projects_run', array('project_id' => $id_project));


        $this->load->library('myfpdf');
        $this->load->library('mytcpdf');

        $this->load->view('reportpdf', $data);
    }


    public function fpdf_html()
    {
        $this->load->library('myfpdf');
        $this->load->library('mytcpdf');
        $data['txt'] = "My name is joesoftheart  hahahahahaha";
        $this->load->view('welcome_html', $data);
    }


}


/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/22/17
 * Time: 2:52 PM
 */

?>