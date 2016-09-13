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
		if($contentArray = $this->db->select("SELECT contentID, url, parentPageID, type FROM content WHERE nav = 1 AND hidden = 0 AND trashed = 0 AND orphaned = 0 ORDER BY nav_position ASC"))
		{
			$i = 0; // Content array key
			foreach($contentArray as $row){
				// Build path given parent ID
				$contentArray[$i]['path'] = $this->_buildPath($row['url'], $row['parentPageID']);
				// Add relevant type specific attributes to content array
				$query = "SELECT name FROM ".$row['type']." WHERE contentID = :contentID";
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
			$path = URL . $row['path'];
			$contentID = $row['contentID'];
			$class = '';
			$dataID = '';

			if($row['type'] == 'navLink') {
				$path = $row['path'];
				$class = "class='navLink'";
				$dataID = "data-id='$contentID'";
			}

			$nav .= "<li $class $dataID id='listItem_$contentID'><a $class href='" . $path . "'>$name</a></li>";
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
			case 'home' :
				$adminNav = array(
					array(
						'url' => URL . "dashboard/edithome", 
						'name' => "<i class='fa fa-fw fa-sliders'></i>Edit Homepage",
					)
				);
				break;
			case 'pageIndex':
				$adminNav = array(
					array(
						'url' => URL . $pageURL . "/edit", 
						'name' => "<i class='fa fa-fw fa-sliders'></i> Edit Page",
					)
				);
				break;
			case 'galIndex':
				$adminNav = array(
					array(
						'url' => URL . $pageURL . "/edit", 
						'name' => "<i class='fa fa-fw fa-sliders'></i> Edit Gallery",
					)
				);
				break;
			case 'vidIndex':
				$adminNav = array(
					array(
						'url' => URL. $pageURL."/edit",
						'name' =>"<i class='fa fa-fw fa-sliders'></i> Edit Video"
					)
				);
				break;
			case 'editPage' :
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
								'name' => 'Page',
								'class' => 'addTab',
								'data-id' => 'page'
							),
							array(
								'url' => '#',
								'name' => 'Gallery Page',
								'class' => 'addTab',
								'data-id' => 'gallery'
							),
							array(
								'url' => '#',
								'name' => 'Video Page',
								'class' => 'addTab',
								'data-id' => 'video'
							),
							array(
								'url' => '#',
								'name' => 'Text/HTML',
								'class' => 'addTab',
								'data-id' => 'text'
							),
							array(
								'url' => '#',
								'name' => 'Single Image',
								'class' => 'addTab',
								'data-id' => 'singleImage'
							),
							array(
								'url' => '#',
								'name' => 'Slideshow',
								'class' => 'addTab',
								'data-id' => 'slideshow'
							),
							array(
								'url' => '#',
								'name' => 'Embedded Video',
								'class' => 'addTab',
								'data-id' => 'embedVideo'
							),
							array(
								'url' => '#',
								'name' => 'Shortcut',
								'class' => 'addTab',
								'data-id' => 'shortcut'
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
						'data-tab' => 'settings'
					),
					array(
						'url' => URL . $pageURL, 
						'name' => "<i class='fa fa-fw fa-desktop'></i> View Page",
						'id' => "viewTab"
					),
				);
				break;
			case 'editVideo' :
				$adminNav = array(
					array(
						'name' => $titleText,
						'id' => 'adminNavName'
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-wrench'></i> Settings",
						'class' => 'adminTab active'
					),
					array(
						'url' => URL . $pageURL, 
						'name' => "<i class='fa fa-fw fa-desktop'></i> View Video",
						'id' => "viewTab"
					)
				);
				break;
			case 'editGallery' :
				$adminNav = array(
					array(
						'name' => $titleText,
						'id' => 'adminNavName'
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-arrows-alt'></i> Edit Sequence",
						'data-tab' => 'editSequence',
						'class' => ' adminTab active'
					),
					array(
						'url' => "#",
						'name' => "<i class='fa fa-fw fa-plus'></i> Add Images",
						'class' => 'adminTab addImages',
						'data-tab' => 'editSequence',
						'id' => 'addImages'
					),
					array(
						'url' => "#", 
						'name' => "<i class='fa fa-fw fa-wrench'></i> Settings",
						'class' => 'adminTab',
						'data-tab' => 'settings'
					),
					array(
						'url' => URL . $pageURL, 
						'name' => "<i class='fa fa-fw fa-desktop'></i> View Gallery",
						'id' => "viewTab"
					)
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
								'data-id' => 'gallery'
							),
							array(
								'url' => '#',
								'name' => 'Video',
								'class' => 'addTab',
								'data-id' => 'video'
							),
							array(
								'url' => '#',
								'name' => 'Navigation Link',
								'class' => 'addTab',
								'data-id' => 'navLink'
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
}