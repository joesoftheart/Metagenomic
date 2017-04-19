<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/16/17
 * Time: 12:26 AM
 */
class Meta_test extends CI_Controller {


    public function __construct()
    {

        parent::__construct();
    }

    public function index(){

        $this->load->view('header');
        $this->load->view('test');
        $this->load->view('footer');
    }

    public function search() {
        if ($this->input->post('search')){
            $search =  $this->input->post('search');
            $query = $this->mongo_db->like('project_name',$search, 'iu', FALSE, TRUE)->get('projects');


            if(!empty($query)) {
                echo "<ul id='country-list'>";
                 foreach($query as $rs) {

            echo "<li>";
            echo $rs["project_name"];
            echo "</li>";
         }
             echo "</ul>";
         }



        }
    }


}