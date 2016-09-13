<?php

class Page extends Controller
{
	function __construct()
	{ 
		parent::__construct();
		// Instantiate content model
		$this->loadModel('content', false);
		$this->contentModel = new Content_Model();
	}

	// User given URL (Array)
	private $_URL = null;
	// If a method is called, this is the index for the method in the URL Array
	private $_urlKey = null;
	// Page Attributes
	private $_pageAttrArray = array('pageID' => 0);
	private $_pageURL = null; // For building subpage path

	private $_device = null;

/**
 *	index - 		Builds page from DB elements and views.
 *					If method is passed in URL, call it!
 *					Otherwise, render the page
 *
 */
	public function index($url = array())
	{
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
		// Build view object based on page type
		$this->_buildIndexView();

		switch($this->_pageAttrArray['type'])
		{
			case 'page' :
				$this->view->render('page/index');
				break;
			case 'gallery' :
				$this->view->render('gallery/index');
				break;
			case 'video' :
				$this->view->render('video/index');
				break;
		}				
	}

/**
 *
 *	Build Index Views (public page, gallery, video)
 *
 */
	private function _buildIndexView()
	{
		// JS (public)
		$this->view->js = array('public.min.js');
		// Add view vars based on type
		$this->view->pageAttr = $this->_pageAttrArray;
		$this->view->pageTitle = $this->_pageAttrArray['name'];
		switch($this->_pageAttrArray['type'])
		{
			case 'page' :
				// Switch if homepage
				if(!$this->_pageAttrArray['home']) {
					// Admin Nav
					$this->view->adminNav = $this->globalModel->adminNavArray('pageIndex', $this->_pageAttrArray['path']);				
					// Load content
					$this->view->pageContent = $this->contentModel->getPageContent($this->_pageAttrArray['pageID']);
				} else {
					// Home Admin Nav
					$this->view->adminNav = $this->globalModel->adminNavArray('home');
					// Home content
					$this->view->pageContent = $this->contentModel->getPageContent();
				}
				break;
			case 'gallery' :
				// Admin nav
				$this->view->adminNav = $this->globalModel->adminNavArray('galIndex', $this->_pageAttrArray['path']);
				// Gal Images
				$this->view->galImages = $this->contentModel->getGalImages($this->_pageAttrArray['galleryID']);
				break;
			case 'video' :
				// Admin nav
				$this->view->adminNav = $this->globalModel->adminNavArray('vidIndex', $this->_pageAttrArray['path']);
		}		
	}

/**
 *
 *	INDEX METHODS
 *
 */

	public function slide($position = 0)
	{
		if($this->_pageAttrArray['type'] !== 'gallery') {
			$this->error();
			return false;
		}
		$this->view->slide = $position;
		$this->_buildIndexView();
		// Render
		$this->view->render('gallery/index');
	}
/**
 *
 *	edit - 	Edit a page/gallery/video!
 *
 */
	public function edit()
	{
		Auth::setAccess();
		// Pass page attributes to view
		$this->view->pageAttr = $this->_pageAttrArray;
		// Build page list for parent select
		$this->view->pageList = $this->model->listPages();
		// Switch based on type
		switch($this->_pageAttrArray['type'])
		{
			case 'page' :
				$this->view->pageTitle = "Edit Page | ".$this->_pageAttrArray['name'];
				// Admin Nav
				$this->view->adminNav = $this->globalModel->adminNavArray('editPage', $this->_pageAttrArray['path'], "Edit: " . $this->_pageAttrArray['name']);
				// Content
				$this->view->pageContent = $this->contentModel->getPageContent($this->_pageAttrArray['pageID']);
				// Templates
				$this->view->templates = $this->contentModel->buildTemplates();
				// Javascript
				$this->view->js = array('editPage.min.js');
				// Render view
				$this->view->render('page/edit');
				break;
			case 'gallery' :
				$this->view->pageTitle = "Edit Gallery | ".$this->_pageAttrArray['name'];
				// Admin Nav
				$this->view->adminNav = $this->globalModel->adminNavArray('editGallery', $this->_pageAttrArray['path'], "Edit: " . $this->_pageAttrArray['name']);
				// Gal Images
				$this->view->galImages = $this->contentModel->getGalImages($this->_pageAttrArray['galleryID']);
				// Javascript
				$this->view->js = array('editGal.min.js');
				// Render view
				$this->view->render('gallery/edit');
				break;
			case 'video' :
				$this->view->pageTitle = "Edit Video | ".$this->_pageAttrArray['name'];
				// Admin Nav
				$this->view->adminNav = $this->globalModel->adminNavArray('editVideo', $this->_pageAttrArray['path'], "Edit: " . $this->_pageAttrArray['name']);
				// Javascript
				$this->view->js = array('editVid.min.js');
				// Render view
				$this->view->render('video/edit');
				break;
		}
	}

/**
 *
 *	EDIT PAGE METHODS
 *
 */
	public function updateSettings()
	{
		Auth::setAccess();
		$displayName = '';
		if($this->_pageAttrArray['type'] == 'page') {
			$displayName = $this->_pageAttrArray['displayName'];
		}
		$this->contentModel->updateSettings($this->_pageAttrArray['type'], $this->_pageAttrArray['contentID'], $displayName);
	}

	public function sortContent()
	{
		Auth::setAccess();
		$this->contentModel->sortContent();
	}

	public function trashContent($contentID = false)
	{
		Auth::setAccess();
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			if(!$contentID) $contentID = $this->_pageAttrArray['contentID'];
			$this->contentModel->trashContent($contentID);
		}
	}

	public function saveResize($contentID)
	{
		Auth::setAccess();
		$this->contentModel->saveResize($contentID);
	}

	public function addPage()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('page');
		$this->pageContentModel->addPage($this->_pageAttrArray['pageID']);
	}

	public function addVideo()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('video');
		$this->videoContentModel->addVideo($this->_pageAttrArray['pageID']);
	}

	public function addGallery()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGallery($this->_pageAttrArray['pageID']);
	}

	public function uploadGalImages()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGalImages($_POST['galID'], $_POST['galURL']);
	}

	public function addText()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('text');
		$this->textContentModel->addText($this->_pageAttrArray['pageID']);
	}

	public function addSingleImage()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('image');
		$this->imageContentModel->addSingleImage($this->_pageAttrArray['pageID'], $this->_pageAttrArray['url']);
	}

	public function addSpacer()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('page');
		$this->pageContentModel->addSpacer($this->_pageAttrArray['pageID']);
	}

	public function deleteSpacer($contentID)
	{
		Auth::setAccess();
		if($_SERVER['REQUEST_METHOD'] == "DELETE")
		{
			$this->contentModel->deleteContent($contentID);
		}
	}

	public function updateShortcut($contentID)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('shortcut');
		$this->shortcutContentModel->updateShortcut($contentID);
	}

	public function updateShortcutCover($contentID, $type)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('shortcut');
		$this->shortcutContentModel->updateShortcutCover($contentID, $type);
	}

/**
 *
 *	EDIT GALLERY METHODS
 *
 */
	public function addGalImages()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGalImages($this->_pageAttrArray['galleryID'], $this->_pageAttrArray['url']);
	}
	public function sortGalImages()
	{
		Auth::setAccess();
		$this->contentModel->sortContent();
	}

	public function updateCaption($galImageID)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->updateGalCaption($galImageID);
	}

	public function newCover($galImageID)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->updateGalCover($this->_pageAttrArray['galleryID'], $this->_pageAttrArray['url'], $this->_pageAttrArray['coverPath'], $galImageID);
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
 *	_loadHome - Sets view vars with homepage attributes
 *					
 *
 */
	private function _loadHome()
	{
		// Set homepage attributes
		$this->_pageAttrArray = array(
			'type' => 'page',
			'name' => 'Imageman',
			'home' => true
		);
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
		// Set flag that this is NOT the homepage
		$this->_pageAttrArray['home'] = false;
		return true;
	}
}