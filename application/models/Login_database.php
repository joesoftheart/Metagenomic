<?php

Class Login_Database extends CI_Model {

// Insert registration data in database
    public function registration_insert($data) {
       // print_r($data);
// Query to check whether username already exist or not
        $query = $this->mongo_db->get_where('user_login',array("user_name" => $data['user_name']));
        if ( count($query) == 0) {

// Query to insert data in database
            $this->mongo_db->insert('user_login', $data);
            $c = $this->mongo_db->count('user_login');
            //echo $c;
            if ($c > 0) {
                return true;
            }
        } else {
            return false;
        }
    }

// Read data using username and password
    public function login($data) {

       // $this->mongo_db->where_or(array('user_name'=>$data['username'], 'user_password'=>$data['password']))->get('user_login');
        $query = $this->mongo_db->get_where('user_login',array("user_name" => $data['username'],"user_password" => $data['password']));
        $admin = array();
        if ($query != null) {
            foreach ($query as $q) {
                $id_user = (string)$q['_id'];

            }
            $admin = $this->mongo_db->get_where('roles', array("user_id" => $id_user));
        }

        //print_r($admin);
        $result = $query;
        $result_admin = $admin;
        if (count($query) == 1 && count($admin) == 0) {
            return $result;
        } else if(count($query) == 1 && count($admin) == 1){
            return $result;
        }else {
            return false;
        }
    }

// Read data from database to show data in admin page
    public function read_user_information($username) {
        $query = $this->mongo_db->get_where('user_login',array("user_name" => $username));
        $result = $query;
        if (count($query) == 1) {
            return $result;
        } else {
            return false;
        }
    }

    public function read_user_admin($username) {
        $query = $this->mongo_db->get_where('user_login',array("user_name" => $username));
        $admin = array();
        if ($query != null) {
            foreach ($query as $q) {
                $id_user = (string)$q['_id'];

            }
            $admin = $this->mongo_db->get_where('roles', array("user_id" => $id_user));
        }
        if (count($query) == 1 && count($admin) == 1) {
            return $admin;
        } else {
            return false;
        }
    }

}

?>