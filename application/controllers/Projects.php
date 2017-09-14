<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/13/17
 * Time: 11:07 PM
 */
class Projects extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('html');
        $CI = &get_instance();
        $CI->load->library("session");
        include(APPPATH . '../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
    }

    public function index($id_project)
    {
        ob_start();
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');
        $data['rs_process'] = $this->mongo_db->limit(1)->get('status_process');
        



       foreach ($data['rs'] as $r) {
             $sample_folder = $r['project_path'];
             $data['project_analysis'] = $r['project_analysis'];
        }
           $project = basename($sample_folder);
           $user = $this->session->userdata['logged_in']['username'];




        if ($data != null) {
            foreach ($data['rs'] as $r) {
                $ar = (string)$r['_id'];
            }
            $this->session->set_userdata('current_project', $ar);
        }


        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $num = null;
        $keywords_split_line = array();
        $progress = "owncloud/data/$user/files/$project/output/progress.txt";
        if (file_exists($progress)) {
            $file_progress = fopen($progress, "r");
            $keywords_split_line = preg_split("/[\n]/", fread($file_progress, filesize($progress)));
           // print_r($keywords_split_line);
            $num = count($keywords_split_line);
        }
       // echo $num;
        if(file_exists($progress) and $num < 18){
            redirect("/process/index/" . $id_project, 'refresh');
        }else {
             
          
            $this->load->view('header', $data);
            $this->load->view('projects', $data);
            $this->load->view('footer');

            $data['username'] = $user;
            $data['project']  = $project;
            $data['currentproject'] = $ar;
            $this->load->view('script_advance',$data);

          
        }
    }


    public function standard_run($id)
    {
        $data = $this->mongo_db->get_where("projects", array("_id" => new MongoId($id)));
        foreach ($data as $r) {
            $sample_folder = $r['project_path'];
            $id = $r['_id'];
            $project_analysis = $r['project_analysis'];
        }
        $project = basename($sample_folder);
        $user = $this->session->userdata['logged_in']['username'];

        $path = "owncloud/data/$user/files/$project";

        $config['upload_path'] = $path;
        $config['allowed_types'] = '*';
        $config['max_size'] = '3000';
        // $config['max_width'] = '1024';
        // $config['max_height'] = '1024';
        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload("design")) {
            $data = $this->upload->data();

        } else {
            echo "cannot upload design ";
        }

        if ($this->upload->do_upload("metadata")) {
            $data = $this->upload->data();

        } else {
            echo "cannot upload metadata";
        }


        $jobname = $user . "_" . $id . "_start_run";




        if ($project_analysis == "otu"){
            $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f Scripts/standard_run_otu.php $user $id $project $path";
            exec($cmd);
            redirect("/process/index/".$id);
        }else if ($project_analysis == "phylotype"){
            $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f Scripts/standard_run_phylotype.php $user $id $project $path";
            exec($cmd);
            redirect("/process/index/".$id);
        }else {
            echo "Not run";
        }
    }





}




