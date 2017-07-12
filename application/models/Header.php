<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/3/17
 * Time: 8:38 PM
 */

class Header extends CI_Model {

    public function header()
    {
        $data['rs_mes'] = $this->mongo_db->get('messages');
    }

}