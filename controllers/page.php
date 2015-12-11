<?php

class Page extends Controller
{
	function __construct(){parent::__construct();}

	// User given URL (Array)
	private $_URL = null;
	// If a method is called, this is the index for the method in the URL Array
	private $_urlKey = null;

	// Page Attributes
	private $_pageID = null;
	private $_pageName = null;
	private $_pageTitle = null;
	private $_pageURL = null;
	private $_contentID = null;
	private $_parentPageID = 0;	// default
	private $_parentPageURL = null;

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
		// Method or subpage(s) passed in URL, loop over url array
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
		// Add vars to view
		$this->view->pageTitle = "Edit Page: $this->_pageName";
		$this->view->pageName = $this->_pageName;
		$this->view->js = array('mustache.min.js', 'adminNav.js', 'addContent.js', 'contentControls.js');
		$this->view->adminNav = $this->model->adminNavArray('edit', $this->_pageURL);
		$this->view->pageContent = $this->contentModel->getPageContent($this->_pageID);
		$this->view->templates = $this->contentModel->buildTemplates();

		// Render view
		$this->view->render('page/edit');
	}

	public function addPage()
	{
		Auth::setAccess();
		$this->contentModel->addPage($this->_pageID);
	}

	public function addText()
	{
		Auth::setAccess();
		$this->contentModel->addText($this->_pageID);
	}

	public function trashContent($contentID)
	{
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			$this->contentModel->trashContent($contentID);
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
			$this->error();
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
		$this->view->adminNav = $this->model->adminNavArray('home', $this->_pageURL);

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
		if(!$result = $this->model->getPageInfo($url))
		{
			return false;
		}

		if(!$result['parentPageID'] == $this->_pageID) // Make sure parent page is valid 
		{
			return false;
		}

		// Set page vars
		$this->_pageID = $result['pageID'];
		$this->_pageName = $result['name'];
		$this->_pageTitle = $result['name'];
		$this->_contentID = $result['contentID'];
		$this->_parentPageID = $result['parentPageID'];

		if($this->_parentPageID == 0) {
			$this->_pageURL = $result['url'];
		} else {
			$this->_pageURL = $this->_pageURL . "/" . $result['url'];
		}

		// Set admin nav array
		$this->view->adminNav = $this->model->adminNavArray('index', $this->_pageURL);

		// Add vars to view
		$this->view->pageTitle = $this->_pageTitle;
		$this->view->pageName = $this->_pageName;

		// load content
		$this->view->pageContent = $this->contentModel->getPageContent($this->_pageID);

		return true;
	}


}
?>
