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

            //echo date("Y-m-d H:i:s");

        $path_file_phylotype = array("final.tx.summary","file_after_reverse.csv","file_phylum_count.txt","final.tx.2.subsample.shared",
            "final.tx.thetayc.2.lt.ave.nmds.axes","final.tx.groups.summary","final.tx.groups.rarefaction","soilpro.pearson.corr.axes");

        $path_file_otu = array("final.opti_mcc.summary","file_after_reverse.csv","file_phylum_count.txt","final.opti_mcc.0.03.subsample.shared",
            "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes","final.opti_mcc.groups.summary","final.opti_mcc.groups.rarefaction","soilpro.pearson.corr.axes");


        $path_owncloud_phylotype = "owncloud/data/joesoftheart/files/SAMPLE-WES-2023/output/";
        $path_owncloud_otu = "owncloud/data/joesoftheart/files/SAMPLE_OTU/output/";


        $chao_shanon = file_get_contents($path_owncloud_phylotype."final.tx.groups.ave-std.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array() ;
        $arr_chao = array();
        $arr_shanon = array();
        $index =0;
        foreach ($row as $value => $data){
            if ($data != null){
                $row_data = preg_split("/\s+/", $data);

                if($row_data[0] == 2) {
                    $arr_out[$index] = $row_data[5];
                    $arr_chao[$index] = $row_data[9];
                    $arr_shanon[$index] = $row_data[12];
                    $index++;
                }
            }

        }


        echo "Min otu :" . min($arr_out)."<br>";
        echo "Max otu :" . max($arr_out)."<br>";
        echo "Min chao :" . min($arr_chao)."<br>";
        echo "Max chao :" . max($arr_chao)."<br>";
        echo "Min shanon :" . min($arr_shanon)."<br>";
        echo "Max shanon :" . max($arr_shanon)."<br>";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";
        $rare_index = array();
        $name_sample = array();
        $save_value = array();
        $rarefaction = file_get_contents($path_owncloud_phylotype."final.tx.groups.rarefaction");
        $row = explode("\n", $rarefaction);


        $check_index = preg_split('/\s+/', $row[0]);


          for($i=0;$i<count($check_index);$i++){
            $save_index =   explode('-', $check_index[$i]);
              if ($save_index[0] == "2") {
                  array_push($rare_index, $i);
                  array_push($name_sample, $save_index[1]);
              }
          }


        array_shift($row);

        foreach ($row as $value => $data){
            if ($data != null){
                $row_data_rare = preg_split('/\s+/', $data);


                for ($i=0;$i<count($rare_index);$i++){

                        if (is_numeric($row_data_rare[$rare_index[$i]])) {
                                if (isset($save_value[$i])){

                                }else{
                                    $save_value[$i] = "0";
                                }
                            if ($save_value[$i] < $row_data_rare[$rare_index[$i]]) {
                                $save_value[$i] = $row_data_rare[$rare_index[$i]];
                            } else {

                            }
                        } else {
                            if ($save_value[$i] == null) {
                                $save_value[$i] = "0";
                            } else {

                            }
                        }





                }





            }



        }
        echo $name_sample[0].$save_value[0];
        echo '<br>';
        echo $name_sample[1].$save_value[1];
        echo '<br>';
        echo $name_sample[2].$save_value[2];
        echo '<br>';
        echo $name_sample[3].$save_value[3];
        echo '<br>';
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";


        $name_sample_phylumn = array();
        $value_phylumn =array();
        $phylumn_count = file_get_contents($path_owncloud_phylotype."file_phylum_count.txt");
        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);

        for($i=0;$i<count($check_index);$i++){
           $name_sample_phylumn[$i] = $check_index[$i];
        }

        foreach ($row as $value => $data){
            $row_data_phy = preg_split('/\s+/', $data);

            if ($data != null) {

                for($j=0;$j<count($name_sample_phylumn);$j++){


                    
                }



            }
        }







































        foreach ($path_file_phylotype as $value){
            if (file_exists($path_owncloud_phylotype . $value)) {
                echo "Have file". $value;
                echo "<br>";
//                $file = fopen($path_owncloud_phylotype . $value,"r") or die('Unable file');
//                echo fread($file, filesize($path_owncloud_phylotype . $value));
//
//
//
//                fclose($file);

               // echo $file;
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