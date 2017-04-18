<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/18/17
 * Time: 11:14 PM
 */
class Search extends CI_Model{

    public function Search(){
        parent::Model();



    }


    public function getSearch($search){
        $data['rs'] = $this->mongo_db->like('project_name', $search, 'im', FALSE, TRUE);
        return $data;
    }





}