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
	private $_pageAttrArray = array('pageID' => 0, 'path' => '');
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
			if(!$this->_loadHome())
			{
				$this->error();
				return false;
			}
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

	public function loadSlides($galleryID)
	{
		$this->view->images = $this->contentModel->getGalImages($galleryID);
		$this->view->render('inc/content/slideshow/slides', false);
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
				$this->view->pageAttr['home'] = false;
				$this->view->pageTitle = "Edit Page | ".$this->_pageAttrArray['name'];
				// Admin Nav
				$this->view->adminNav = 'editpage';
				// Content
				$this->view->pageContent = $this->contentModel->getPageContent($this->_pageAttrArray['pageID']);
				// Templates
				$this->view->templates = $this->contentModel->buildTemplates();
				// Gallery and Video list for embedding
				$this->view->galleryArray = $this->contentModel->listContent('gallery');
				$this->view->videoArray = $this->contentModel->listContent('video');
				// Javascript
				$this->view->js = array('editPage.min.js');
				// Additional CSS (TinyMCE)
				$this->view->css = array('skin.min.css');
				// Render view
				$this->view->render('page/edit');
				break;
			case 'gallery' :
				$this->view->pageTitle = "Edit Gallery | ".$this->_pageAttrArray['name'];
				// Admin Nav
				$this->view->adminNav = 'editgallery';
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
				$this->view->adminNav = 'editvideo';
				// Javascript
				$this->view->js = array('editVid.min.js');
				// Render view
				$this->view->render('video/edit');
				break;
		}
	}
/**
 *
 *	edithome - 	Edits the homepage
 *
 */
	public function edithome(){
		Auth::setAccess();
		if($this->_pageAttrArray['pageID'] != 0) {
			$this->error();
			return false;
		}
		// Get homepage settings
		$this->_pageAttrArray['homeSettings'] = $this->model->getHomeSettings();

		// Get homeTarget list
		$this->view->homeTargetList = $this->model->listHomeTargets();

		// Pass page attributes to view
		$this->view->pageAttr = $this->_pageAttrArray;
		$this->view->pageAttr['home'] = true;
		// Title
		$this->view->pageTitle = "Edit Homepage";
		// Admin Nav
		$this->view->adminNav = 'edithome';
		// Content
		$this->view->pageContent = $this->contentModel->getPageContent();
		// Templates
		$this->view->templates = $this->contentModel->buildTemplates();
		// Build page list for parent select
		$this->view->pageList = $this->model->listPages();
		// Gallery and Video list for embedding
		$this->view->galleryArray = $this->contentModel->listContent('gallery');
		$this->view->videoArray = $this->contentModel->listContent('video');
		// Javascript
		$this->view->js = array('editPage.min.js');
		// Render view
		$this->view->render('page/edit');
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

	public function updateHomeSettings()
	{
		Auth::setAccess();
		$this->model->updateHomeSettings();
	}

	public function updateContentSettings($contentID)
	{
		Auth::setAccess();
		$this->contentModel->updateContentSettings($contentID);
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

	public function addEmbedVideo($videoID = false)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('video');
		if($videoID) {
			if($result = $this->videoContentModel->addEmbedVideo($this->_pageAttrArray['pageID'], $videoID)) {
				echo json_encode($result);
			}
		} else {
			$this->videoContentModel->addVideo($this->_pageAttrArray['pageID'], false, true);
		}
	}

	public function addGallery()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGallery($this->_pageAttrArray['pageID']);
	}

	public function addSSgallery()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGallery($this->_pageAttrArray['pageID'], false, true);
	}

	public function uploadGalImages()
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		$this->galleryContentModel->addGalImages($_POST['galID'], $_POST['galURL']);
	}

	public function addSlideshow($galleryID)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('gallery');
		if($result = $this->galleryContentModel->addSlideshow($this->_pageAttrArray['pageID'], $galleryID)){
			echo json_encode($result);
		}
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
		$filename = isset($this->_pageAttrArray['url']) ? $this->_pageAttrArray['url'] : 'home';
		$this->imageContentModel->addSingleImage($this->_pageAttrArray['pageID'], $filename);
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

	public function updateSlideshow($contentID)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('slideshow');
		if($result = $this->slideshowContentModel->updateSlideshow($contentID)){
			echo json_encode($result);
		}
	}

	public function updateText($contentID)
	{
		Auth::setAccess();
		$this->_loadTypeContentModel('text');
		$this->textContentModel->updateText($contentID);
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
		// Hit DB for homepage type
		$homeSettings = $this->model->getHomeSettings();
		if($homeSettings['homeType'] == 'normal') {
			// Set homepage attributes
			$this->_pageAttrArray = array(
				'type' => 'page',
				'name' => 'Imageman',
				'path' => '',
				'home' => true
			);
			$this->_pageAttrArray['homeSettings'] = $homeSettings;
		} else {
			if(!$result = $this->model->loadPage('contentID', $homeSettings['homeTarget'])) {
				return false;
			}
			// Save full result array to class
			$this->_pageAttrArray = $result;
			$this->_pageAttrArray['path'] = '';
			// Set flag that this IS the homepage
			$this->_pageAttrArray['home'] = true;
			$this->_pageAttrArray['homeSettings'] = $homeSettings;
		}
		// Set name to BRAND
		$this->_pageAttrArray['name'] = BRAND;
		return true;
	}

/**
 *	_loadPage - hits DB and saves page info to the class. Returns false if page does not exist
 * @param string $url - The user defined page URL
 *
 */
	private function _loadPage($url)
	{
		// Make sure page exists
		if(!$result = $this->model->loadPage('url', $url))
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
		$this->view->adminNav = 'page';

		// If this is the homepage and it is set to normal, load homepage content.
		if($this->_pageAttrArray['home'] && $this->_pageAttrArray['homeSettings']['homeType'] == 'normal') {
			// Home content
			$this->view->pageContent = $this->contentModel->getPageContent();
		} else {
			switch($this->_pageAttrArray['type'])
			{
				case 'page' :			
					// Load content
					$this->view->pageContent = $this->contentModel->getPageContent($this->_pageAttrArray['pageID']);
				break;
				case 'gallery' :
					// Gal Images
					$this->view->galImages = $this->contentModel->getGalImages($this->_pageAttrArray['galleryID']);
				break;
			}	
		}	
	}
}