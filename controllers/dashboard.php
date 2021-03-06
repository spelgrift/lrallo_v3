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
		$this->view->adminNav = 'dashboard';
		$this->view->contentRows = $this->model->renderContentRows($this->contentModel->listContent());
		$this->view->trashRows = $this->model->renderTrashRows($this->model->listTrash());
		// JS (admin)
		$this->view->js = array('dashboard.min.js');
		// Render View
		$this->view->render('dashboard/index');
	}

	public function addPage()
	{
		$this->_loadTypeContentModel('page');
		$this->pageContentModel->addPage(0, true); // args: parentPageID, dashboard request
	}

	public function addVideo()
	{
		$this->_loadTypeContentModel('video');
		$this->videoContentModel->addVideo(0, true);
	}

	public function addGallery()
	{
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGallery(0, true);
	}

	public function uploadGalImages()
	{
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGalImages($_POST['galID'], $_POST['galURL'], true);
	}

	public function addNavLink()
	{
		$this->model->addNavLink();
	}

	public function editNavLink($contentID)
	{
		$this->model->editNavLink($contentID);
	}

	public function trashNavLink($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE") {
			$this->contentModel->trashContent($contentID);
		}
	}

	public function trashContent($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE") {
			$this->contentModel->trashContent($contentID, true);
		}
	}

	public function deleteContent($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE") {
			if($this->contentModel->deleteContent($contentID))	{
				echo json_encode(array('error' => false));	
			} else {
				echo json_encode(array('error' => true));	
			}		
		}
	}

	public function deleteMultiple()
	{
		$error = false;
		foreach($_POST['checkedItems'] as $contentID) {
			if(is_numeric($contentID))	{
				$this->contentModel->deleteContent($contentID);
			} else {
				$error = true;
			}
		}
		echo json_encode(array('error' => $error));
	}

	public function restoreContent($contentID)
	{
		if($this->contentModel->restoreContent($contentID)) {
			echo json_encode(array('error' => false));	
		} else {
			echo json_encode(array('error' => true));	
		}
	}

	public function restoreMultiple()
	{
		$error = false;
		foreach($_POST['checkedItems'] as $contentID) {
			if(is_numeric($contentID))	{
				$this->contentModel->restoreContent($contentID);
			} else {
				$error = true;
			}
		}
		echo json_encode(array('error' => $error));
	}

	public function emptyTrash()
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE") {
			$this->contentModel->emptyTrash();
		}
	}

	public function sortNav()
	{
		$this->model->sortNav();
	}

	public function reloadNav()
	{
		$this->view->nav = $this->globalModel->loadNav();
		$this->view->render('inc/nav', false);
	}

	public function reloadTrash()
	{
		echo $this->model->renderTrashRows($this->model->listTrash());
	}

	public function reloadContentList()
	{
		echo $this->model->renderContentRows($this->contentModel->listContent());
	}

}