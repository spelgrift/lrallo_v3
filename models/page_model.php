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
		$query = "SELECT * FROM content WHERE url = :url AND trashed = 0";
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
			$results = array(
				'error' => true,
				'error_msg' => 'Name cannot be blank!',
				'error_field' => 'name'
			);
			echo json_encode($results);
			return false;
		}
		if($url == ""){
			$results = array(
				'error' => true,
				'error_msg' => 'URL cannot be blank!',
				'error_field' => 'url'
			);
			echo json_encode($results);
			return false;
		}
		// Make sure URL uses correct characters
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $url);

		// Make sure name/URL are not taken
		$query = "SELECT * FROM content WHERE url = :url";
		if($url != $origURL && $result = $this->db->select($query, array(':url' => $url))){
			$results = array(
				'error' => true,
				'error_msg' => 'A page with that URL already exists.',
				'error_field' => 'url'
			);
			echo json_encode($results);
			return false;
		}
		$query = "SELECT * FROM page WHERE name = :name";
		if($name != $origName && $result = $this->db->select($query, array(':name' => $name))){
			$results = array(
				'error' => true,
				'error_msg' => 'A page with that name already exists.',
				'error_field' => 'name'
			);
			echo json_encode($results);
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
}
?>