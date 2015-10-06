<?php

class Page extends Controller
{
	function __construct()
	{
		parent::__construct();
	}

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
	private $_parentPage = 0;	

	public function edit($arg1 = null, $arg2 = null)
	{
		// echo "Edit Method! - Optional param: " . $arg1 . " - " . $arg2;
		// Add vars to view
		$this->view->pageTitle = "Edit Page: $this->_pageName";
		$this->view->pageName = $this->_pageName;
		$this->view->adminNav = array(array(
			'url' => $this->_pageURL, 
			'name' => "View Page",
		));

		// Render view
		$this->view->render('page/edit');

	}

/**
 *	buildPage - Builds page from DB elements and views.
 *					If method is passed in URL, call it!
 *					Otherwise, load the page
 *
 *
 */
	public function buildPage($url = array())
	{
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

		// Add vars to view
		$this->view->pageTitle = $this->_pageTitle;
		$this->view->pageName = $this->_pageName;
		$this->view->adminNav = array(array(
			'url' => $this->_pageURL . "/edit", 
			'name' => "Edit Page",
		));

		// display content
		$this->view->pageContent = $this->_displayContent($this->_pageID);

		// Render nav (global model)

		// Render Page with current class page attr			
		$this->view->render('page/index');
	}

/**
 *	_parseURL - Iterate over url segments testing if there is a method
 *					If there is, break the loop and run the method based on current class page attr 
 * 				Methods apply to the parent page
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
 *	_callControllerMethod - 	Call method from URL
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
 *	_loadHome - 	Sets view vars with homepage info
 *					
 *
 */
	private function _loadHome()
	{

		// Add vars to view
		$this->_pageTitle = "Home";
		$this->_pageName = "Home Page";

		// display content
		$this->view->pageContent = $this->_displayContent();
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

		$this->_pageID = $result['pageID'];
		$this->_pageName = $result['name'];
		$this->_pageTitle = $result['name'];
		$this->_pageURL = $result['url'];
		$this->_contentID = $result['contentID'];
		$this->_parentPage = $result['parentPageID'];
		return true;
	}

/**
 *	displayContent - 	Hit DB and retrieve content associated with this page.
 *							THIS COMPILES CONTENT DISPLAY HTML
 *							if no ID is given, retrieve and build html for Home content
 *
 */
	private function _displayContent($pageID = false)
	{
		$result = $this->model->getPageContent($pageID); // Returns array of rows from DB
		// print_r($result);

		// For each content item, set variables and include the view for the given type
		// Append to HTML string
		
		return $result;

	}
}
?>
