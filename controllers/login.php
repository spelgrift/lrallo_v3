<?php

class Login extends Controller {

	function __construct() {
		parent::__construct();
		$this->view->pageTitle = 'Login';	
	}

	public function index($error = false){
		// JS (public)
		$this->view->js = array('public.min.js');
		$this->view->error = $error;
		$this->view->render('login/index');
	}

	public function run(){
		$this->model->run();
	}

	public function runstatic(){
		$this->model->run(true);
	}

	public function error(){
		$this->index(true);
	}

	public function logout()
	{
		Session::destroy();
		header('location:'. URL);
		exit;
	}
}