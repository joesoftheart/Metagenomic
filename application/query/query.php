<?php
$id_project = "123";
$project_analysis = "";
$id = "11";
$search = "11";
$data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
$query = $this->mongo_db->like('project_name',$search, 'iu', FALSE, TRUE)->limit(3)->get_where('projects',array("user_id" => $this->session->userdata["logged_in"]["_id"]));
$data['rs_noti'] = $this->mongo_db->get('notification');
$this->mongo_db->insert('notification', $data);
$this->mongo_db->where(array("_id" => new \MongoId($id)))->delete('notification');
$this->mongo_db->where(array("_id" => new \MongoId($id)))->delete('users');
$this->mongo_db->where(array('id_project'=> $id_project))->set('classifly', $project_analysis)->update('advance_classifly');
$this->mongo_db->where(array('project_id'=> $id_project))->set($data)->update('sample_name');
$data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
$project = $this->mongo_db->select(array('_id', 'project_name'))->get('projects');
?>