<?php

class Global_Model extends Model {

	public function __construct()
	{
		parent::__construct();
	}

/**
 *	loadNav - Sets admin nav array given page view
 *	@return string Nav html
 *
 */
	public function loadNav()
	{
		// Get attributes stored in content table as array
		if($contentArray = $this->db->select("SELECT contentID, url, parentPageID FROM content WHERE nav = 1 AND hidden = 0 AND trashed = 0 ORDER BY nav_position ASC"))
		{
			$i = 0; // Content array key
			foreach($contentArray as $row){
				// Build path given parent ID
				$contentArray[$i]['path'] = $this->_buildPath($row['url'], $row['parentPageID']);
				// Add relevant type specific attributes to content array
				$query = "SELECT * FROM page WHERE contentID = :contentID";
				$pageArray = $this->db->select($query, array(':contentID' => $row['contentID']));
				$contentArray[$i]['name'] = $pageArray[0]['name'];
				$i++;
			}
		} else {
			$contentArray = array();
		}
		// Build html for nav <li>'s from array
		$nav = "";
		foreach($contentArray as $row)
		{
			$name = $row['name'];
			$path = $row['path'];
			$contentID = $row['contentID'];

			$nav .= "<li id='listItem_$contentID'><a href='" . URL . $path . "'>$name</a></li>";
		}
		return $nav;
	}

/**
 *	adminNavArray - Builds admin nav array given page view
 *	@param string $view The view to load for
 *	@param string $pageURL The URL for the view page button 
 *	@param string $titleText Text to insert at the head of the nav bar
 *	@return array 
 *
 */
	public function adminNavArray($view, $pageURL = null, $titleText = null)
	{
		switch($view)
		{
			case 'index':
				$adminNav = array(
					array(
						'url' => URL . $pageURL . "/edit", 
						'name' => "<i class='fa fa-fw fa-sliders'></i> Edit Page",
					)
				);
				break;
			case 'home' :
				$adminNav = array(
					array(
						'url' => URL . "dashboard/edithome", 
						'name' => "<i class='fa fa-fw fa-sliders'></i>Edit Homepage",
					)
				);
				break;
			case 'edit' :
				$adminNav = array(
					array(
						'name' => $titleText,
						'id' => 'adminNavName'
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-arrows-alt'></i> Edit Layout",
						'data-tab' => 'contentArea',
						'class' => ' adminTab active'
					),
					array(
						'dropdown' => true,
						'name' => "<i class='fa fa-fw fa-plus'></i> Add Content",
						'class' => 'adminTab',
						'data-tab' => 'contentArea',
						'items' => array(
							array(
								'url' => '#',
								'name' => 'Subpage',
								'class' => 'addTab',
								'data-id' => 'page'
							),
							array(
								'url' => '#',
								'name' => 'Text',
								'class' => 'addTab',
								'data-id' => 'text'
							),
							array(
								'url' => '#',
								'name' => 'Spacer',
								'class' => 'addTab',
								'data-id' => 'spacer'
							)
						)
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-wrench'></i> Settings",
						'class' => 'adminTab',
						'data-tab' => 'pageSettings'
					),
					array(
						'url' => URL . $pageURL, 
						'name' => "<i class='fa fa-fw fa-desktop'></i> View Page",
						'id' => "viewTab"
					),
				);
				break;
			case 'dashboard' :
				$adminNav = array(
					array(
						'name' => 'Dashboard'
					),
					array(
						'url' => '#',
						'name' => "<i class='fa fa-fw fa-list'></i> List Content",
						'class' => "adminTab active",
						'data-tab' => 'contentList'
					),
					array(
						'dropdown' => true,
						'name' => "<i class='fa fa-fw fa-plus'></i> Add Content",
						'class' => 'adminTab',
						'data-tab' => 'contentList',
						'items' => array(
							array(
								'url' => '#',
								'name' => 'Page',
								'class' => 'addTab',
								'data-id' => 'page'
							),
							array(
								'url' => '#',
								'name' => 'Image Gallery',
								'class' => 'addTab',
								'data-id' => 'album'
							),
							array(
								'url' => '#',
								'name' => 'Video',
								'class' => 'addTab',
								'data-id' => 'video'
							)
						)
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-trash'></i> Trash",
						'class' => 'adminTab',
						'data-tab' => 'trash'
					),
					array(
						'url' => URL . 'dashboard/edithome/',
						'name' => "<i class='fa fa-fw fa-home'></i> Edit Homepage"
					)
				);
				break;
		}
		return $adminNav;
	}

/**
 *	listPages - Builds array of all non-trashed pages with subpages as sub-arrays
 *	@return array 
 *
 */
	public function listPages()
	{
		return $this->_getPageArrayRecursive("0");
	}

	private function _getPageArrayRecursive($parentPageID, $path = "")
	{
		$returnArray = array();
		// Append trailing / to path if item has a parent page
		if(strlen($path) > 0) {	$path = $path . "/";	}
		// 
		if($result = $this->db->select("SELECT contentID, url, parentPageID, author, `date` FROM content WHERE type = 'page' AND trashed = '0' AND parentPageID = $parentPageID"))
		{
			
			foreach($result as $row)
			{
				$pageArray = array(
					'contentID' => $row['contentID'],
					'url' => $row['url'],
					'path' => $path . $row['url'],
					'parentPageID' => $row['parentPageID'],
					'date' => $row['date'],
					'author' => $row['author']
				);
				if($result = $this->db->select("SELECT pageID, name FROM page WHERE contentID = '".$row['contentID']."'"))
				{
					foreach($result as $row)
					{
						$pageArray['pageID'] = $row['pageID'];
						$pageArray['name'] = $row['name'];
					}
				}

				$pageArray['subPages'] = $this->_getPageArrayRecursive($pageArray['pageID'], $pageArray['path']);

				$returnArray[] = $pageArray;
			}
		}
		return $returnArray;
	}

/**
 *	listContent - Builds array of all non-trashed content with subContent as sub-arrays
 *	@return array 
 *
 */
	public function listContent($type = 'all')
	{
		return $this->_getContentArrayRecursive($type, "0");
	}

	private function _getContentArrayRecursive($type, $parentPageID, $path = "")
	{
		// Build WHERE clause based on type
		if($type === 'all')
		{
			$type = array(
				'page',
				'image',
				'album',
				'slideshow',
				'video',
				'text',
				'embedded-video',
				'shortcut'
			);
		}
		$where = "";
		foreach($type as $str) {
			$where .= "type = '$str' OR ";
		}
		$where = rtrim($where, 'OR ') . " ";

		// If pages are included in the requested types, group content by parent page
		if(in_array('page', $type))
		{
			$parentCondition = "AND parentPageID = $parentPageID";
		} else {
			$parentCondition = "";
		}
		// Create empty array
		$returnArray = array();
		// Get content results from DB
		if($result = $this->db->select("SELECT contentID, url, type, parentPageID, author, `date` FROM content WHERE trashed = '0' $parentCondition AND ( $where ) ORDER BY contentID DESC"))
		{
			foreach($result as $row)
			{
				// Add attributes common to all types
				$typeArray = array(
					'contentID' => $row['contentID'],
					'type' => $row['type'],
					'parentPageID' => $row['parentPageID'],
					'date' => $row['date'],
					'author' => $row['author']
				);
				// Switch by type
				switch($row['type'])
				{
					case "page" :
						// Append trailing / to path if item has a parent page
						if(strlen($path) > 0) {	$path = $path . "/";	}

						$typeArray['url'] = $row['url'];
						$typeArray['path'] = $path . $row['url'];

						if($result = $this->db->select("SELECT pageID, name FROM page WHERE contentID = '".$row['contentID']."'"))
						{
							foreach($result as $row)
							{
								$typeArray['pageID'] = $row['pageID'];
								$typeArray['name'] = $row['name'];
							}
						}
						$typeArray['subContent'] = $this->_getContentArrayRecursive($type, $typeArray['pageID'], $typeArray['path']);
					break;
					case "text" :
						$typeArray['path'] = $path;

						if($result = $this->db->select("SELECT `textID`, `text` FROM `text` WHERE contentID = '".$row['contentID']."'"))
						{
							foreach($result as $row)
							{
								$typeArray['textID'] = $row['textID'];
								$typeArray['text'] = $row['text'];
							}
						}
					break;
				}

				$returnArray[] = $typeArray;
			}
		}
		return $returnArray;
	}
}
?>