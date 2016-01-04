<?php

class Page_Model extends Model {

	function __construct(){parent::__construct();}

/**
 *	getPageInfo - 
 *	@param string $url The page url
 *	@return mixed, array of page attributes, false on no rows
 *
 */
	public function getPageInfo($url)
	{
		$query = "SELECT * FROM content WHERE url = :url";
		if($a = $this->db->select($query, array(':url' => $url)))
		{
			$contentAttr = $a[0];
			// Get page info
			$query = "SELECT * FROM page WHERE contentID = :contentID";
			if($a = $this->db->select($query, array(':contentID' => $contentAttr['contentID'])))
			{
				$pageAttr = $a[0];
				foreach($contentAttr as $key => $value)
				{
					$pageAttr[$key] = $value;
				}
				return $pageAttr;
			}
		}
		return false;
	}

/**
 *	adminNavArray - Sets admin nav array for all given page view
 *	@param string $pageURL The URL for the view page button 
 *	@return array 
 *
 */
	public function adminNavArray($view, $pageURL)
	{
		switch($view)
		{
			case 'index':
				$adminNav = array(array(
					'url' => URL . $pageURL . "/edit", 
					'name' => "<i class='fa fa-fw fa-sliders'></i> Edit Page",
				));
				break;
			case 'home' :
				$adminNav = array(array(
					'url' => URL . "dashboard/edithome", 
					'name' => "<i class='fa fa-fw fa-sliders'></i>Edit Homepage",
				));
				break;
			case 'edit' :
				$adminNav = array(
					array(
						'name' => 'Edit: '
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
					// array(
					// 	'url' => "#", 
					// 	'name' => "<i class='fa fa-fw fa-trash'></i>Delete Page",
					// 	'id' => "deletePage"
					// )
				);
				break;
		}
		return $adminNav;
	}

/**
 *	listPages - Builds array of all existing, non-trashed pages with subpages as sub-arrays
 *	@return array 
 *
 */
	public function listPages()
	{
		return $this->_getPageArrayRecursive("0");
	}

	private function _getPageArrayRecursive($parentPageID)
	{
		$returnArray = array();

			if($result = $this->db->select("SELECT contentID, url, parentPageID FROM content WHERE type = 'page' AND trashed = '0' AND parentPageID = $parentPageID"))
			{
				foreach($result as $row)
				{
					$pageArray = array(
						'contentID' => $row['contentID'],
						'url' => $row['url'],
						'parentPageID' => $row['parentPageID']
					);
					if($result = $this->db->select("SELECT pageID, name FROM page WHERE contentID = '".$row['contentID']."'"))
					{
						foreach($result as $row)
						{
							$pageArray['pageID'] = $row['pageID'];
							$pageArray['name'] = $row['name'];
						}
					}

					$pageArray['subPages'] = $this->_getPageArrayRecursive($pageArray['pageID']);

					$returnArray[] = $pageArray;
				}
			}
			return $returnArray;
	}
}
?>