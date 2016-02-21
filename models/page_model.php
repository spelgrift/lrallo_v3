<?php

class Page_Model extends Model {

	function __construct(){parent::__construct();}

/**
 *	getPageInfo - Gets page infro from DB, page can be any type (normal page, gallery, video)
 *	@param string $url The page url
 *	@return mixed, array of page attributes, false on no rows
 *
 */
	public function getPageInfo($url)
	{
		$query = "SELECT contentID, url, type, parentPageID, nav, hidden FROM content WHERE url = :url AND trashed = 0 AND (type = 'page' OR type = 'gallery' OR type = 'video')";
		if($a = $this->db->select($query, array(':url' => $url)))
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
 *	updateSettings - Updates page settings. Wasn't that descriptive?
 *
 */
	public function updateSettings($pageID, $contentID)
	{
		$name = $_POST['name'];
		$url = $_POST['url'];
		$parent = $_POST['parent'];
		$nav = $_POST['nav'];
		$origName = $_POST['origName'];
		$origURL = $_POST['origURL'];

		// Validate length
		if($name == ""){
			$this->_returnError('Name cannot be blank!', 'name');
			return false;
		}
		if($url == ""){
			$this->_returnError('URL cannot be blank!', 'url');
			return false;
		}
		// Make sure URL uses correct characters
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $url);

		// Make sure name/URL are not taken
		$query = "SELECT * FROM content WHERE url = :url";
		if($url != $origURL && $result = $this->db->select($query, array(':url' => $url))){
			$this->_returnError('A page with that URL already exists.', 'url');
			return false;
		}
		$query = "SELECT * FROM page WHERE name = :name";
		if($name != $origName && $result = $this->db->select($query, array(':name' => $name))){
			$this->_returnError('A page with that name already exists.', 'name');
			return false;
		}

		// Content DB Update
		$this->db->update('content', array(
			'url' => $url,
			'parentPageID' => $parent,
			'nav' => $nav
		), "`contentID` = ".$contentID);

		// Page DB Update
		$this->db->update('page', array('name' => $name), "`pageID` = ".$pageID);

		$path = $this->_buildPath($url, $parent);
		$windowPath = DEVPATH . $path . "/edit";
		$viewPath = URL . $path;

		echo json_encode(array(
			'error' => false,
			'name' => $name,
			'url' => $url,
			'windowPath' => $windowPath,
			'viewPath' => $viewPath
		));
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
?>