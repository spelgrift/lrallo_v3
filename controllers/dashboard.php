<?php

class Dashboard extends Controller {

	function __construct()
	{
		parent::__construct();
		Auth::setAccess();

		// Instantiate Content Model
		$this->loadModel('content', false);
		$this->contentModel = new Content_Model();
	}

	public function index()
	{
		// Add View Vars
		$this->view->pageTitle = 'DASHBOARD';
		$this->view->adminNav = $this->globalModel->adminNavArray('dashboard');
		$this->view->contentRows = $this->model->renderContentRows($this->model->listContent());
		$this->view->trashRows = $this->model->renderTrashRows($this->model->listTrash());
		$this->view->js = array('mustache.min.js', 'events.js', 'adminNav.js', 'addContentDashboard.js', 'sortableNav.js', 'contentList.js', 'trash.js');
		// Render View
		$this->view->render('dashboard/index');
	}


	public function addPage()
	{
		$this->contentModel->addPage("0");
	}

	public function trashContent($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			if($affectedRows = $this->contentModel->trashContent($contentID, true))
			{
				echo json_encode(array(
					'error' => false,
					'affectedRows' => $affectedRows
				));
			}
		}
	}

	public function deleteContent($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			$this->contentModel->deleteContent($contentID);
		}
	}

	public function restoreContent($contentID)
	{
		$this->contentModel->restoreContent($contentID);
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

	public function reloadTrash()
	{
		echo $this->model->renderTrashRows($this->model->listTrash());
	}

	public function reloadContentList()
	{
		echo $this->model->renderContentRows($this->model->listContent());
	}

}
?>