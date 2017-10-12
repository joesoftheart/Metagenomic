<?php
defined('BASEPATH') OR exit("No direct script access allowed");


class Complete_run extends CI_Controller {



    public function __construct()
    {
        parent::__construct();

    }

    public function index($id_project){
        ob_start();
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));

        $this->load->view('header');
        $this->load->view('complete_run',$data);
        $this->load->view('footer');





    }

    function on_check_remove_progress($id_project){
        $data = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));

        foreach ($data as $value){
            $a = $value['project_path'];

        }

        echo "on_check_remove"."\n";
        $path_dir = $a."/output/";
        if (is_dir($path_dir)) {
            if ($read = opendir($path_dir)){
                while (($file = readdir($read)) !== false) {

                    $allowed =  array('txt');
                    $ext = pathinfo($file, PATHINFO_EXTENSION);

                    if(in_array($ext,$allowed)) {

                        unlink($path_dir.$file);
                    }
                }

                closedir($read);
                redirect('projects/index/' . $id_project, 'refresh');
            }
        }


    }


}









/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/10/17
 * Time: 2:10 PM
 */