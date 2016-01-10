<?php

class Dashboard extends Controller {

	function __construct()
	{
		parent::__construct();
		Auth::setAccess();

		// Instantiate Content Model
		$this->loadModel('content', false);
		$this->contentModel = new Content_Model();

		// echo "<pre>";
		// print_r($this->globalModel->listContent());
		// echo "</pre>";
	}

	public function index()
	{
		
		// Add View Vars
		$this->view->pageTitle = 'DASHBOARD';
		$this->view->adminNav = $this->globalModel->adminNavArray('dashboard');
		$this->view->pageList = $this->globalModel->listPages();
		$this->view->contentList = $this->globalModel->listContent();
		$this->view->js = array('mustache.min.js', 'adminNav.js', 'addContentDashboard.js', 'sortableNav.js', 'contentList.js');
		// Render View
		$this->view->render('dashboard/index');
	}


	public function addPage()
	{
		$this->contentModel->addPage("0");
	}

	public function sortNav()
	{
		$this->model->sortNav();
	}

	public function deletePage()
	{
		$this->model->deletePage();
	}

	public function reloadNav()
	{
		echo $this->globalModel->loadNav();
	}

}
?>