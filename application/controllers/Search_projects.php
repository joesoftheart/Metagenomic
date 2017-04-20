<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/16/17
 * Time: 12:26 AM
 */
class Search_projects extends CI_Controller {


    public function __construct()
    {

        parent::__construct();
    }

    public function index(){

    }

    public function search() {
        if ($this->input->post('search')){
            $search =  $this->input->post('search');
            $query = $this->mongo_db->like('project_name',$search, 'iu', FALSE, TRUE)->limit(3)->get_where('projects',array("user_id" => $this->session->userdata["logged_in"]["_id"]));


            if(!empty($query)) {

                 foreach($query as $rs) {
                     echo "<a href='";
                     echo site_url("projects/index/".$rs['_id']);
                     echo "'>";
            echo "<li style='height: 30px;background-color: #ffffff;'>";
                     echo "<p style='margin-left: 5px' >";
            echo $rs["project_name"];
                     echo "</p>";
            echo "</li>";
                     echo "</a>";
         }

         }






        }
    }


}