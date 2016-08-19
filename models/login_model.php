<?php

class Login_Model extends Model {

	function __construct(){parent::__construct();}

	public function run($static = false){

		$form = new Form();
		$form ->post('login')
				->val('blank')
				->post('password')
				->val('blank');
		if(!$form->submit()) { // Error
			$this->_error($static);
			return false;
		}
		$data = $form->fetch();
		$login = $data['login'];
		$password = Hash::create('sha256', $data['password'], PASS_HASH_KEY);
		$query = "SELECT userid, login, role FROM user WHERE login = :login AND password = :password";

		if(!$result = $this->db->select($query, array(':login' => $login, ':password' => $password))) {
			$this->_error($static);
			return false;
		}

		Session::init();
		Session::set('userid', $result[0]['userid']);
		Session::set('login', $result[0]['login']);
		Session::set('role', $result[0]['role']);
		Session::set('loggedIn', true);

		if($static) {
			header('location:'. URL . 'dashboard');
		}
		echo json_encode('success');
	}

	private function _error($static) {
		if($static) {
				header('location:'. URL . 'login/error');
			}
		echo json_encode('error');
	}
}