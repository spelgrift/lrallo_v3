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
						'id' => "layoutTab",
						'class' => 'active'
					),
					array(
						'dropdown' => true,
						'name' => "<i class='fa fa-fw fa-plus'></i> Add Content",
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
							)
						)
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-wrench'></i> Settings",
						'id' => "settingsTab"
					),
					array(
						'url' => URL . $pageURL, 
						'name' => "<i class='fa fa-fw fa-desktop'></i> View Page",
						'id' => "viewTab"
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-trash'></i>Delete Page",
						'id' => "deletePage"
					)
				);
				break;
		}
		return $adminNav;
	}
}
?>