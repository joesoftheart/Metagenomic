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
        include('setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
    }

   

    public function index($id_project)
    {
        ob_start();
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        $data['rs_process'] = $this->mongo_db->limit(1)->get('status_process');

        $project_program = null;

        foreach ($data['rs'] as $r) {
            $sample_folder = $r['project_path'];
            $data['project_analysis'] = $r['project_analysis'];
            $project_program = $r['project_program'];

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
        if (file_exists($progress) and $num < 18) {
            redirect("/process/index/" . $id_project, 'refresh');
        } else if (file_exists($progress) and $num = 18) {
            redirect("/complete_run/index/" . $id_project, 'refresh');
        } else {

           if($project_program == "mothur"){


                $this->load->view('header', $data);
                $this->load->view('projects', $data);
                $this->load->view('footer');

                $data['username'] = $user;
                $data['project'] = $project;
                $data['currentproject'] = $ar;
                $img_source = 'images/check.png';
                $img_code = base64_encode(file_get_contents($img_source));
                $data['src'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;
                $img_source = 'images/ajax-loader.gif';
                $img_code = base64_encode(file_get_contents($img_source));
                $data['srcload'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;
                $this->load->view('script_advance', $data);

           }elseif ($project_program == "qiime") {

                $status = 'null';
                $step_run = 'null';
                $id_job = 'null';
                $data['current'] = $id_project;
                #Query data status-process
                 $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
                    foreach ($array_status as $r){
                            $status   = $r['status'];
                            $step_run = $r['step_run'];              
                            $id_job = $r['job_id'];
                            
                     }

                $path_sampleName = FCPATH."owncloud/data/$user/files/$project/input/sampleName.txt";
                $file_text = file_get_contents($path_sampleName);
                $get_Name = explode("\t", $file_text);
                $result = array_filter($get_Name);
                
                $data['sampleName'] = $result;
                $data['status'] = $status;
                $data['step_run'] = $step_run;
                $data['id_job'] = $id_job;

                $img_source = 'images/check.png';
                $img_code = base64_encode(file_get_contents($img_source));
                $data['src'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

                $img_source = 'images/ajax-loader.gif';
                $img_code = base64_encode(file_get_contents($img_source));
                $data['srcload'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

                $this->load->view('header');
                $this->load->view('run_qiime',$data);
                $this->load->view('footer');
            
            
               # code...
           }elseif ($project_program == "mothur_qiime") {

                $status = 'null';
                $step_run = 'null';
                $id_job = 'null';
                $data['current'] = $id_project;
                #Query data status-process
                 $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
                    foreach ($array_status as $r){
                            $status   = $r['status'];
                            $step_run = $r['step_run'];              
                            $id_job = $r['job_id'];
                            
                     }

                $data['status'] = $status;
                $data['step_run'] = $step_run;
                $data['id_job'] = $id_job;

                $img_source = 'images/check.png';
                $img_code = base64_encode(file_get_contents($img_source));
                $data['src'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

                $img_source = 'images/ajax-loader.gif';
                $img_code = base64_encode(file_get_contents($img_source));
                $data['srcload'] = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

                $this->load->view('header');
                $this->load->view('run_mothur_qiime',$data);
                $this->load->view('footer');
           }

        }
    }



    public function save_oligos(){
        $data = $this->mongo_db->get_where("projects", array("_id" => new MongoId($this->session->userdata['current_project'])));
        foreach ($data as $r) {
            $sample_folder = $r['project_path'];
            $id = $r['_id'];
            $project_analysis = $r['project_analysis'];
        }
        $project = basename($sample_folder);
        $user = $this->session->userdata['logged_in']['username'];

        $path = "owncloud/data/$user/files/$project/input/";
        $primer = $this->input->post("col1[]");
        $primer_seq = $this->input->post("col2[]");
        $primer_type = $this->input->post("col3[]");
        $barcode = $this->input->post("colbar1[]");
        $barcode_seq = $this->input->post("colbar2[]");
        $barcode_type = $this->input->post("colbar3[]");
        $barcode_add_on = $this->input->post("colbar4[]");
        $myfile = $path."/oligo.oligos";
        for ($i=0;$i < count($primer);$i++){
            echo $primer[$i];
            file_put_contents($myfile, $primer[$i]."\t".$primer_seq[$i]."\t".$primer_type[$i]."\n", FILE_APPEND);
        }
        for ($i=0;$i < count($barcode);$i++){
            echo $barcode[$i];
            file_put_contents($myfile, $barcode[$i]."\t".$barcode_seq[$i]."\t".$barcode_type[$i]."\t".$barcode_add_on[$i]."\n", FILE_APPEND);
        }
    }


    public function standard_run($id)
    {
        echo "Standard";

        if ($this->input->post("max_amb") != null) {

            $data = array("max_amb" => $this->input->post("max_amb"),
                "max_homo" => $this->input->post("max_homo"),
                "min_read" => $this->input->post("min_read"),
                "max_read" => $this->input->post("max_read"),
                "align_seq" => $this->input->post("align_seq"),
                "diffs" => $this->input->post("diffs"),
                "cutoff" => $this->input->post("cutoff"),
                "db_taxon" => $this->input->post("db_taxon"),
                "rm_taxon" => $this->input->post("rm_taxon"),
                "tax_level" => $this->input->post("tax_level"),
                "calculators_bio" => "pearson",
                "graph" => "NMDS",
                "calculators" => "thetayc",
                "mode" => "standard",
                "project_id" => $id
            );

            $check_pro = $this->mongo_db->get_where('projects_run', array("project_id" => $id));

            if ($check_pro != null) {
                $this->mongo_db->where(array("project_id" => $id))->set($data)->update('projects_run');
            } else {

                $this->mongo_db->insert('projects_run', $data);
            }


        } else {
            echo "..........";

        }

        $data = $this->mongo_db->get_where("projects", array("_id" => new MongoId($id)));
        foreach ($data as $r) {
            $sample_folder = $r['project_path'];
            $id = $r['_id'];
            $project_analysis = $r['project_analysis'];
        }
        $project = basename($sample_folder);
        $user = $this->session->userdata['logged_in']['username'];

        $path = "owncloud/data/$user/files/$project/input";

        $config['upload_path'] = $path;
        $config['allowed_types'] = '*';
        $config['max_size'] = '3000';
        //$config['allowed_types']  = 'metadata|oligos|design';
        // $config['max_width'] = '1024';
        // $config['max_height'] = '1024';
        $this->load->library('upload');
        $this->upload->initialize($config);

        if ( ! $this->upload->do_upload('oligos'))
        {
            $error = array('error' => $this->upload->display_errors());

            echo  $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());

            echo 'upload_success';
        }
        if ( ! $this->upload->do_upload('design'))
        {
            $error = array('error' => $this->upload->display_errors());

            echo  $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());

            echo 'upload_success';
        }
        if ( ! $this->upload->do_upload('metadata'))
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('upload_form', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());

            echo 'upload_success';
        }

        $jobname = $user . "_" . $id . "_start_run";


        if ($project_analysis == "OTUs") {
            $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/php -f Scripts/standard_run_otu.php $user $id $project $path";
            exec($cmd);
            redirect("/process/index/" . $id);
        } else if ($project_analysis == "phylotype") {
            $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/php -f Scripts/standard_run_phylotype.php $user $id $project $path";
            exec($cmd);
            redirect("/process/index/" . $id);
        } else {
            echo "Not run";
        }
    }


}




