<?php

class Model {

	function __construct(){
		$this->db = new Database(dbTYPE, dbHOST, dbDATABASE, dbUSER, dbPASS);
	}

/**
 *	buildPath - Recursive function that builds the path (/page/subpage/subsubpage)
 *	@param string $url The url of the page for which to build the full path
 *	@param bool $parentPageID The parent page of the page for which to build the path
 *
 */
	protected function _buildPath($url, $parentPageID)
	{
		if($parentPageID == 0) {
			$path = $url;
		} else {
			$result = $this->db->select("SELECT contentID FROM page WHERE pageID = :parentPageID", array(':parentPageID' => $parentPageID));
			$result = $this->db->select("SELECT url, parentPageID FROM content WHERE contentID = :contentID", array(':contentID' => $result[0]['contentID']));
			$path = $result[0]['url']."/".$url;
			$path = $this->_buildPath($path, $result[0]['parentPageID']);
		}
		return $path;
	}

	protected function _returnError($message, $field = null)
	{
		$results = array(
			'error' => true,
			'error_msg' => $message,
			'error_field' => $field
		);
		echo json_encode($results);
	}

}
?>