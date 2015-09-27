<?php

class Dashboard extends Controller {

	function __construct()
	{
		parent::__construct();
		Auth::setAccess();

		$this->view->pageTitle = 'DASHBOARD';
		$this->view->js = array('dashboard.js');
	}

	function index()
	{
		$this->view->render('dashboard/index');
	}


	function addPage()
	{
		$this->model->addPage();
	}

	function listPages()
	{
		$this->model->listPages();
	}

	function deletePage()
	{
		$this->model->deletePage();
	}



}
?>