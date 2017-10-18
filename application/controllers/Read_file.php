<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/9/17
 * Time: 5:39 PM
 */
class Read_file extends CI_Controller{


    public function __construct()
    {

        parent::__construct();
        $this->load->helper('url');
        //      $this->load->helper('file');

    }

    public function index(){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');

        $this->load->view('header',$data);
        $this->load->view('read_file');
        $this->load->view('footer');

    }

    // This upload file type
    public function upload_file(){
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1024';
        $config['max_width'] = '1024';
        $config['max_height'] = '1024';
        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload("pictures")) {
            $data = $this->upload->data();

        }else{
            echo $this->upload->display_errors();
        }
    }


    public function read_file(){
        $path_file_phylotype = array("final.tx.summary","file_after_reverse.csv","file_phylum_count.txt","final.tx.2.subsample.shared",
            "final.tx.thetayc.2.lt.ave.nmds.axes","final.tx.groups.summary","final.tx.groups.rarefaction","soilpro.pearson.corr.axes");

        $path_file_otu = array("final.opti_mcc.summary","file_after_reverse.csv","file_phylum_count.txt","final.opti_mcc.0.03.subsample.shared",
            "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes","final.opti_mcc.groups.summary","final.opti_mcc.groups.rarefaction","soilpro.pearson.corr.axes");


        $path_owncloud_phylotype = "owncloud/data/joesoftheart/files/SAMPLE-WES-2023/output/";
        $path_owncloud_otu = "owncloud/data/joesoftheart/files/SAMPLE_OTU/output/";

        foreach ($path_file_phylotype as $value){
            if (file_exists($path_owncloud_phylotype . $value)) {
                echo "Have file". $value;
                echo "<br>";
                $file = fopen($path_owncloud_phylotype . $value,"r");

                echo $file;
            }else{
                echo "No file". $value;
                echo "<br>";
            }
        }
        echo "____________________";
        echo "<br>";
        foreach ($path_file_otu as $value){
            if (file_exists($path_owncloud_otu . $value)) {
                echo "Have file". $value;
                echo "<br>";
            }else{
                echo "No file". $value;
                echo "<br>";
            }
        }
    }


}