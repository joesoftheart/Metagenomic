<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Test extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('test');
        $this->load->view('footer');
    }

    public function test()
    {
        $dir = "../owncloud/data/joesoftheart/files/SAMPLE_OTU/output";

        $file_read = array('svg', 'html', 'js', 'css');
        $dir_ignore = array();

        $scan_result = scandir($dir);

        foreach ($scan_result as $key => $value) {

            if (!in_array($value, array('.', '..'))) {

                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

                    if (in_array($value, $dir_ignore)) {
                        continue;
                    }


                } else {

                    $type = explode('.', $value);
                    $type = array_reverse($type);
                    if (in_array($type[0], $file_read)) {
                        echo $value;
                        $file_name = preg_split("/[.]/", $value);
                        if (in_array("bin", $file_name)) {
                            rename($dir . "/" . $value, $dir . "/" . "bin.svg");
                        }
                        if (in_array("sharedsobs", $file_name)) {
                            rename($dir . "/" . $value, $dir . "/" . "sharedsobs.svg");
                        }
                        if (in_array("jclass", $file_name)) {
                            rename($dir . "/" . $value, $dir . "/" . "jclass.svg");
                        }
                        if (in_array("thetayc", $file_name)) {
                            rename($dir . "/" . $value, $dir . "/" . "thetayc.svg");
                        }
                    }
                }
            }
        }
    }


    public function scanDirectories($rootDir, $allData = array())
    {
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
        // run through content of root directory
        $dirContent = scandir($rootDir);
        foreach ($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir . '/' . $content;
            if (!in_array($content, $invisibleFileNames)) {
                // if content is file & readable, add to array
                if (is_file($path) && is_readable($path)) {
                    // save file name with path
                    $allData[] = $path;
                    // if content is a directory and readable, add path and name
                } elseif (is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    $allData = scanDirectories($path, $allData);
                }
            }
        }
        return $allData;
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
                        foreach ($cdir as $key => $value) {
                            $type = explode('.', $value);
                            $type = array_reverse($type);
                            if (in_array($type[0], $file_read)) {
                                echo $value;
                            }
                        }

                        $result_files[$value] = $value;
                        //echo "<br/>";
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
                        $file_in_dir = scandir($path_owncloud . "/" . $value);
                        echo "iok";


                    } else {
                        //echo "out";
                    }

                }
            }
        } else {
            echo "out";
        }


    }
}

?>