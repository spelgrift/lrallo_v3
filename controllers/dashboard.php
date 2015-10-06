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

	public function reloadNav()
	{
		$a = $this->globalModel->loadNav();
		foreach($a as $row)
		{
			$name = $row['name'];
			$url = $row['url'];

			echo "<li><a href='" . URL . $url . "'>$name</a></li>";
		}
	}



}
?>