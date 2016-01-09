<?php

class Page extends Controller
{
	function __construct(){parent::__construct();}

	// User given URL (Array)
	private $_URL = null;
	// If a method is called, this is the index for the method in the URL Array
	private $_urlKey = null;

	// Page Attributes
	private $_pageAttrArray = array('pageID' => 0);
	private $_pageURL = null; // For building subpage path

/**
 *	index - 		Builds page from DB elements and views.
 *					If method is passed in URL, call it!
 *					Otherwise, load the page
 *
 *
 */
	public function index($url = array())
	{
		// Instantiate content model
		$this->loadModel('content', false);
		$this->contentModel = new Content_Model();

		// Set URL
		$this->_URL = $url;

		// If URL is empty, load home page
		if (empty($url[0])) 
		{
			$this->_loadHome();
		}
		// Method or subpage(s) passed in URL, loop over url array, loading each page or calling method
		else if(count($url) > 1) 
		{
			$result = $this->_parseURL($this->_URL);
			switch(true)
			{
				case $result === 'method' :
					$this->_callControllerMethod($this->_urlKey);
					return false;
					break;

				case $result === false :
					$this->error();
					return false;
					break;
			}
		}
		// Just one URL passed, load that page (if it exists)
		else 
		{
			if (!$this->_loadPage($url[0]))
			{
				$this->error();
				return false;
			}
		}

		// Render Page 			
		$this->view->render('page/index');
	}

/**
 *	edit - 	Edit a page!
 *
 *
 */
	public function edit()
	{
		Auth::setAccess();
		// Pass page attributes to view
		$this->view->pageAttr = $this->_pageAttrArray;
		$this->view->pageTitle = "Edit Page: ".$this->_pageAttrArray['name'];
		// Build page list for parent select
		$this->view->pageList = $this->globalModel->listPages();
		// Admin Nav
		$this->view->adminNav = $this->globalModel->adminNavArray('edit', $this->_pageAttrArray['path'], "Edit: " . $this->_pageAttrArray['name']);
		// Content
		$this->view->pageContent = $this->contentModel->getPageContent($this->_pageAttrArray['pageID']);
		// Templates
		$this->view->templates = $this->contentModel->buildTemplates();
		// Javascript
		$this->view->js = array('mustache.min.js', 'adminNav.js', 'pageSettings.js', 'addPageContent.js', 'contentControls.js', 'contentResize.js');
		// Render view
		$this->view->render('page/edit');
	}

	public function updateSettings()
	{
		$this->model->updateSettings($this->_pageAttrArray['pageID'], $this->_pageAttrArray['contentID']);
	}

	public function sortContent()
	{
		$this->contentModel->sortContent();
	}

	public function saveResize($contentID)
	{
		Auth::setAccess();
		$this->contentModel->saveResize($contentID);
	}

	public function addPage()
	{
		Auth::setAccess();
		$this->contentModel->addPage($this->_pageAttrArray['pageID']);
	}

	public function addText()
	{
		Auth::setAccess();
		$this->contentModel->addText($this->_pageAttrArray['pageID']);
	}

	public function addSpacer()
	{
		Auth::setAccess();
		$this->contentModel->addSpacer($this->_pageAttrArray['pageID']);
	}

	public function trashContent($contentID = false)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			if(!$contentID) {
				$contentID = $this->_pageAttrArray['contentID'];
			}
			$this->contentModel->trashContent($contentID);
		}
	}

	public function deleteSpacer($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			$this->contentModel->deleteContent($contentID);
		}
	}

/**
 *	_parseURL - Iterate over url segments, loading each page if it exists,
 *					or testing if there is a method. If there is, break the 
 *					loop and run the method based on the currently loaded page.
 *
 */
	private function _parseURL($url)
	{
		// Load base page controller
		if (!$this->_loadPage($url[0]))
		{
			return false;
		}

		unset($url[0]); // remove the base page controller from array
		$i = 1; // URL array key
		foreach($url as $item) // iterate through url array, checking if method or if page exists
		{
			// Try to call method
			if(method_exists(__CLASS__, $item)) 
			{
				$this->_urlKey = $i;
				return 'method';
			}	
			// Check if page exists, has current page as parent
			else if($this->_loadPage($item))	
			{
				$i++;
			}
			else
			{
				return false;
			}		
		}
		return true;
	}

/**
 *	_callControllerMethod - Call method from URL
 *
 */
	private function _callControllerMethod($urlKey)
	{
		// slice array at current index to get params
		$params = array_slice($this->_URL, $urlKey + 1);
		// Call method
		call_user_func_array(array(__CLASS__, $this->_URL[$urlKey]), $params);
	}

/**
 *	_loadHome - Sets view vars with homepage info
 *					
 *
 */
	private function _loadHome()
	{
		// Set page vars
		$this->_pageTitle = "Home";
		$this->_pageName = "Home Page";

		// Set admin nav for homepage
		$this->view->adminNav = $this->globalModel->adminNavArray('home', $this->_pageURL);

		// Add vars to view
		$this->view->pageTitle = $this->_pageTitle;
		$this->view->pageName = $this->_pageName;

		// load content
		$this->view->pageContent = $this->contentModel->getPageContent();
	}

/**
 *	_loadPage - hits DB and saves page info to the class. Returns false if page does not exist
 * @param string $url - The user defined page URL
 *
 */
	private function _loadPage($url)
	{
		// Make sure page exists
		if(!$result = $this->model->getPageInfo($url))
		{
			return false;
		}

		// Make sure parent page is valid
		if(!$result['parentPageID'] == $this->_pageAttrArray['pageID'])  
		{
			return false;
		}

		// Save full result array to class
		$this->_pageAttrArray = $result;

		// If page has a parent, build the path from the URLs from the previously loaded pages, otherwise, save the URL
		if($this->_pageAttrArray['parentPageID'] != 0) {
			$this->_pageAttrArray['path'] = $this->_pageURL . "/" . $result['url'];
			$this->_pageURL = $this->_pageAttrArray['path'];			
		} else {
			$this->_pageAttrArray['path'] = $result['url'];
			$this->_pageURL = $result['url'];
		}

		// Set admin nav array
		$this->view->adminNav = $this->globalModel->adminNavArray('index', $this->_pageAttrArray['path']);

		// Pass page attributes to view
		$this->view->pageAttr = $this->_pageAttrArray;
		$this->view->pageTitle = $this->_pageAttrArray['name'];

		// Load content
		$this->view->pageContent = $this->contentModel->getPageContent($this->_pageAttrArray['pageID']);

		return true;
	}
}
?>