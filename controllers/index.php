<?php
class Index extends Controller {
	function __construct() {
		parent::__construct();	
		$this->view->pageTitle = 'Imageman';	
	}

	function index()
	{
		$this->view->render('index/index');
	}
}
?>