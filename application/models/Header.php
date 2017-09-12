<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/3/17
 * Time: 8:38 PM
 */

class Header extends CI_Model {

    public function getMessage()
    {
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');

        return $data['rs_mes'];
    }
    public function getNotification()
    {
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');

        return $data['rs_notifi'];
    }

    public function getProgressProject(){
        $data['rs_pg_pro']  = $this->mongo_db->get_where('projects', array("user_id" => $this->session->userdata["logged_in"]["_id"]));

        return $data['rs_pg_pro'];
    }



    public function getProgress($path){
        $path_file_progress = $path . "/output/progress.txt";

        if (file_exists($path_file_progress)) {
                $file_progress = fopen($path_file_progress, "r");
                $keywords_split_line = preg_split("/[\n]/", fread($file_progress, filesize($path_file_progress)));
                //print_r($keywords_split_line);
                $num = count($keywords_split_line);
                return $num / 18 * 100;
        }else{
            return 0;
        }




    }

}