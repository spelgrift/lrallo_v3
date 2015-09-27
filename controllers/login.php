<?php

class Login extends Controller {

	function __construct() {
		parent::__construct();

		$this->view->pageTitle = 'Login';	
	}

	function index(){
		$this->view->render('login/index');
	}

	function run(){
		$this->model->run();
	}

	function logout()
	{
		Session::destroy();
		header('location:'. URL . 'login');
		exit;
	}

}
?>