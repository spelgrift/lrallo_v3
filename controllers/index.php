<?php

class Index extends Controller {

	function __construct() {
		parent::__construct();	

		$this->view->pageTitle = 'Home';	
	}

	function index(){
		// echo Hash::create('sha256', 'sg3kbp', PASS_HASH_KEY);
		$this->view->render('index/index');
	}



}
?>