<?php

class Login_Model extends Model {

	function __construct(){parent::__construct();}

	public function run(){
		$login = $_POST['login'];
		$password = Hash::create('sha256', $_POST['password'], PASS_HASH_KEY);

		$data = $this->db->select("SELECT userid, role FROM user WHERE login = :login AND password = :password", array(':login' => $login, ':password' => $password));

		if($this->db->rowCount > 0){
			Session::init();
			Session::set('userid', $data[0]['userid']);
			Session::set('role', $data[0]['role']);
			Session::set('loggedIn', true);
			// header('location:'. URL . 'dashboard');
			echo json_encode('success');
		} else {
			echo json_encode('error');
			// header('location:'. URL . 'login');
		}
	}
}

?>