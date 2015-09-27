<?php

class Error extends Controller {

	function __construct(){
		parent::__construct();
		
		$this->view->pageTitle = 'Error';
	}

	function index(){
		$this->view->render('error/index');
	}

}

?>