<?php

class User_Model extends Model {

	public function __construct()
	{
		parent::__construct();
	}


	public function userList($id = false)
	{
		if($id)
		{
			$result = $this->db->select('SELECT userid, login, role FROM user WHERE userid = :id', array(':id' => $id));
			return $result[0];
		}

		return $this->db->select('SELECT userid, login, role FROM user');
	}

	public function create($data)
	{
		$this->db->insert('user', array(
			'login' => $data['login'],
			'password' => Hash::create('sha256', $data['password'], PASS_HASH_KEY),
			'role' => $data['role']
		));
	}

	public function delete($id)
	{
		$result = $this->db->select('SELECT role FROM user WHERE userid = :id', array(':id' => $id));
		if($result[0]['role'] == 'owner')
		return false;

		$this->db->delete('user', "`userid` = $id");
	}

	public function editSave($data)
	{
		$postData = array(
			'login' => $data['login'],
			'password' => Hash::create('sha256', $data['password'], PASS_HASH_KEY),
			'role' => $data['role']
		);
		$this->db->update('user', $postData, "`userid` = {$data['id']}");
	}
}

?>