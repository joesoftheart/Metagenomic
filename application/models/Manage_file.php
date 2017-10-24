<?php

class Manage_file extends CI_Model{

    public function num_file($name_file,$path){
        $file_read = array();
        $count_files = 0;
                 $file_read = $name_file;
                $path_owncloud = $path;
                $cdir = array();
                $result_folder = array();
                $result_files = array();
                if (is_dir($path_owncloud)) {
                    $select_folder = array_diff(scandir($path_owncloud, 1), array('.', '..'));
                    $cdir = scandir($path_owncloud);

                    foreach ($cdir as $key => $value) {
                        if (!in_array($value, array('.', '..'))) {
                            $type = explode('.', $value);
                            $type = array_reverse($type);
                            if (in_array($type[0], $file_read)) {
                                $count_files++;
                            }
                        }
                    }
                }
        return $count_files;
    }


    public function all_seqs($name_file,$path){
        $file_read = array();
        $count_files = 0;
        $file_read = $name_file;
        $path_owncloud = $path;
        $cdir = array();
        $result_folder = array();
        $result_files = array();
        if (is_dir($path_owncloud)) {
            $select_folder = array_diff(scandir($path_owncloud, 1), array('.', '..'));
            $cdir = scandir($path_owncloud);

            foreach ($cdir as $key => $value) {
                if (!in_array($value, array('.', '..'))) {
                    $type = explode('.', $value);
                    $type = array_reverse($type);
                    if (in_array($type[0], $file_read)) {
                        $count_files++;
                    }
                }
            }
        }
        return $count_files;
    }



}




?>