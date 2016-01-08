<?php

class Global_Model extends Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function loadNav()
	{
		if($contentArray = $this->db->select("SELECT contentID, url, parentPageID FROM content WHERE nav = 1 AND hidden = 0 AND trashed = 0 ORDER BY nav_position ASC"))
		{
			$i = 0;
			foreach($contentArray as $a){
				$contentArray[$i]['path'] = $this->_buildPath($a['url'], $a['parentPageID']);
				$query = "SELECT * FROM page WHERE contentID = :contentID";
				$pageArray = $this->db->select($query, array(':contentID' => $a['contentID']));
				$contentArray[$i]['name'] = $pageArray[0]['name'];
				$i++;
			}
		} else {
			$contentArray = array();
		}
		
		$nav = "";
		foreach($contentArray as $row)
		{
			$name = $row['name'];
			$path = $row['path'];

			$nav .= "<li><a href='" . URL . $path . "'>$name</a></li>";
		}

		return $nav;
	}
}

?>