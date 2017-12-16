<?php

class Page_Model extends Model {

	function __construct(){parent::__construct();}

/**
 *	getPageInfo - Gets page info from DB, page can be any type (normal page, gallery, video)
 *	@param string $inputType 	'contentID' or 'url'
 *	@param string $input 		The actual value
 *	@return mixed, array of page attributes, false on no rows
 *
 */
	public function loadPage($inputType, $input)
	{
		$query = "SELECT contentID, url, type, parentPageID, frontpage, nav, hidden FROM content WHERE $inputType = :$inputType AND trashed = 0 AND (type = 'page' OR type = 'gallery' OR type = 'video')";
		$array = array(":$inputType" => $input);
		
		if($a = $this->db->select($query, $array))
		{
			$contentAttr = $a[0];
			// Get page info from appropriate table
			$query = "SELECT * FROM ".$contentAttr['type']." WHERE contentID = :contentID";
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
 *	getHomeSettings - 
 *	@return array of settings
 *
 */
	public function getHomeSettings()
	{
		$a = $this->db->select("SELECT homeType, homeTarget FROM settings");
		return $a[0];
	}

	public function updateHomeSettings()
	{
		$a = array(
			'homeType' => $_POST['type'],
			'homeTarget' => $_POST['target']
		);
		if($this->db->update('settings', $a)){
			echo json_encode(array(
				'error' => false,
				'type' => $_POST['type'],
				'target' => $_POST['target']
			));
		} else {
			$this->_returnError('Unknown Error');
		}
		
	}

/**
 *	listHomeTargets - 
 *	@return array of all pages, gallerys, videos
 *
 */
	public function listHomeTargets()
	{
		// Pages
		$query = "SELECT c.contentID, p.name
			FROM content AS c
			LEFT JOIN page as p on c.contentID = p.contentID
			WHERE c.type = 'page' AND c.trashed = 0";
		if(!$pages = $this->db->select($query)){ $pages = array(); }

		// Galleries
		$query = "SELECT c.contentID, g.name
			FROM content AS c
			LEFT JOIN gallery as g on c.contentID = g.contentID
			WHERE c.type = 'gallery' AND c.trashed = 0";
		if(!$galleries = $this->db->select($query)) { $galleries = array(); }
		// Videos
		$query = "SELECT c.contentID, v.name
			FROM content AS c
			LEFT JOIN video as v on c.contentID = v.contentID
			WHERE c.type = 'video' AND c.trashed = 0";
		if(!$videos = $this->db->select($query)) { $videos = array(); }

		return array_merge($pages, $galleries, $videos);

	}

/**
 *	listPages - Builds array of all non-trashed pages with subpages
 *					as sub-arrays. For use in Edit Page parent select
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
}