<?php

class Global_Model extends Model {

	public function __construct()	{ parent::__construct(); }

/**
 *	loadNav - Loads nav html
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
		return $contentArray;
	}
}