<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class  Test extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $this->load->view('header');
        $this->load->view('test');
        $this->load->view('footer');
    }

    public function chang_name()
    {

        $user = "joesoftheart";
        $project = "SAMPLE-WES-2023";
        $path_owncloud = "owncloud/data/joesoftheart/files/SAMPLE-WES-2023/output";
        $file_read = array('svg');
        $cdir = array();
        $result_folder = array();
        $result_files = array();
        if (is_dir($path_owncloud)) {

            $select_folder = array_diff(scandir($path_owncloud, 1), array('.', '..'));
            $cdir = scandir($path_owncloud);

            foreach ($cdir as $key => $value) {
                if (!in_array($value, array('.', '..'))) {
                    if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                        $result_folder[$value] = $value;
                    } else {


                        $result_files[$value] = $value;
                    }


                }
            }
        }

        $num_folder = count($result_folder);
        $num_files = count($result_files);

        echo $num_files;
        echo "<br/>";
        echo $num_folder;

        $count_files = 0;
        if ($cdir != null) {
            foreach ($cdir as $key => $value) {
                if (!in_array($value, array('.', '..'))) {
                  //  echo $path_owncloud . DIRECTORY_SEPARATOR . $value;
                    if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                        $file_in_dir = scandir($path_owncloud . "/". $value);
                        echo "iok";
                        foreach ($file_in_dir as $key => $value) {
                            $type = explode('.', $value);
                            $type = array_reverse($type);
                            if (in_array($type[0], $file_read)) {
                                $count_files++;
                                echo $value;
                            }
                        }


                    }else{
                        //echo "out";
                    }

                }
            }
        }else{
            echo "out";
        }


    }
}
?>