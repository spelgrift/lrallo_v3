<?php

class Page_Model extends Model {

	function __construct(){parent::__construct();}

/**
 *
 *	addPage - Adds subpage!
 *
 */
	public function addPage($parentPageID)
	{
		$name = $_POST['name'];

		// Validate length
		if($name == ""){
			$results = array(
				'error' => true,
				'error_msg' => 'You must enter a name!'
			);
			echo json_encode($results);
			return false;
		}
		// Create URL friendly string
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $name);
		$url = strtolower($url);

		// Make sure name/URL is not taken
		$query = "SELECT * FROM content WHERE url = :url";
		if($result = $this->db->select($query, array(':url' => $url))){
			$results = array(
				'error' => true,
				'error_msg' => 'A page with that name already exists.'
			);
			echo json_encode($results);
			return false;
		}

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'page',
			'url' => $url,
			'parentPageID' => $parentPageID
		));

		// Page DB entry
		$this->db->insert('page', array(
			'name' => $name,
			'contentid' => $this->db->lastInsertId()
		));

		$results = array('error' => false);
		echo json_encode($results);
	}

/**
 *
 *	addText - Adds a text block to the page!
 *
 */
	public function addText($parentPageID)
	{
		$text = $_POST['text'];

		// Validate length
		if($text == ""){
			$results = array(
				'error' => true,
				'error_msg' => 'Please enter some text!'
			);
			echo json_encode($results);
			return false;
		}

		// Advance position of existing content
		$this->_advanceContentPositions($parentPageID);

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'text',
			'parentPageID' => $parentPageID
		));
		$contentID = $this->db->lastInsertId();

		// Text DB entry
		$this->db->insert('text', array(
			'text' => $text,
			'contentid' => $contentID
		));
		$textID = $this->db->lastInsertId();


		$results = array(
			'error' => false,
			'results' => array(
				'contentID' => $contentID,
				'textID' => $textID
			)
		);
		echo json_encode($results);
	}

	public function trashContent($contentID)
	{
		if($this->db->update('content', array('trashed' => 1), "`contentID` = " . $contentID))
		{
			echo json_encode(array('error' => false));
		}
		else
		{
			echo json_encode(array('error' => true));
		}
		
	}

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
 *	getPageContent
 *	@param string $pageid 
 *	@return array
 *
 */
	public function getPageContent($pageid)
	{
		if($pageid)
		{
			$query = "SELECT contentID, type, position FROM content WHERE parentPageID = :parentPageID AND trashed = 0 ORDER BY position ASC";
			if($result = $this->db->select($query, array(':parentPageID' => $pageid)))
			{
				foreach($result as $key => $row)
				{
					if($a = $this->db->select("SELECT * FROM ".$row['type']." WHERE contentID = ".$row['contentID']))
					{
						foreach($a[0] as $typeKey => $value)
						{
							$result[$key][$typeKey] = $value;
						}
					}

				}
				return $result;
			}
			else
			{
				return array();
			}
		} 
		else 
		{
			return array();
		}
		
	}

/**
 *	buildTemplates - Populate array with mustache tags
 *	@return array
 *
 */
	public function buildTemplates()
	{
		$returnArray = array(
			array(
				'templateID' => 'textTemplate',
				'type' => 'text',
				'contentID' => "{{contentID}}",
				'textID' => "{{textID}}",
				'text' => "{{&text}}"
			)
		);

		return $returnArray;
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

/*
 *
 * UTILITY FUNCTIONS
 *
 */

	private function _advanceContentPositions($parentPageID = 0)
	{
		if ($result = $this->db->select("SELECT position, contentID FROM content WHERE parentPageID = '".$parentPageID."'"))
		{
			foreach($result as $row)
			{
				$postData = array('position' => $row['position'] + 1);
				$this->db->update('content', $postData, "`contentID` = ".$row['contentID']);
			}
		}
	}
}
?>