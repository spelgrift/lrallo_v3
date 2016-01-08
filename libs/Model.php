<?php

class Model {

	function __construct(){
		$this->db = new Database(dbTYPE, dbHOST, dbDATABASE, dbUSER, dbPASS);
	}

	protected function _buildPath($url, $parentPageID)
	{
		if($parentPageID == 0) {
			$path = $url;
		} else {
			$result = $this->db->select("SELECT contentID FROM page WHERE pageID = $parentPageID");
			$result = $this->db->select("SELECT url, parentPageID FROM content WHERE contentID = '".$result[0]['contentID']."'");
			$path = $result[0]['url']."/".$url;
			$path = $this->_buildPath($path, $result[0]['parentPageID']);
		}
		return $path;
	}

}

?>