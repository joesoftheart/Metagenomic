<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/18/17
 * Time: 11:14 PM
 */
class Search extends CI_Model{

    public function Search(){

    }


    public function getSearch($search){
        echo "getSearch";
        $data['rsw'] = $this->mongo_db->get('projects');
        return $data;

    }





}