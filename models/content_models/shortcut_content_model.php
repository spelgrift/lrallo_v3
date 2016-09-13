<?php

class Shortcut_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	public function updateShortcut($contentID)
	{
		$name = $_POST['name'];
		$type = $_POST['type'];
		// Validate length
		if($name == ""){
			$this->_returnError('Name cannot be blank');
			return false;
		}
		// Update DB
		$this->db->update($type, array('displayName' => $name), '`contentID` ='.$contentID);
		echo json_encode(array('error' => false));
	}

	public function updateShortcutCover($contentID, $type)
	{
		if(!$image = $this->_saveOriginalImage($_FILES)) { return false; }
		$original = $image['original'];
		// Get page url and current cover
		$query = "SELECT c.url, t.coverPath, t.coverOriginal
			FROM content AS c
			LEFT JOIN $type AS t ON c.contentID = t.contentID
			WHERE c.contentID = :contentID";
		if(!$result = $this->db->select($query, array(':contentID' => $contentID))) {
			return false;
		}
		$oldCover = $result[0]['coverPath'];
		$oldOriginal = $result[0]['coverOriginal'];
		$url = $result[0]['url'];
		// Unlink old cover
		if($oldCover != "") {
			unlink($oldCover);
		}
		if($oldOriginal != "") {
			unlink($oldOriginal);
		}
		// Make cover image
		$coverPath = COVERS.$url.date('Ymd-his')."_cover.jpg";
		Image::makeCover($original, $coverPath);
		// Update DB
		$this->db->update($type, array(
			'coverPath' => $coverPath,
			'coverOriginal' => $original
		), "`contentID` = ".$contentID);

		$results = array(
			'coverPath' => $coverPath,
			'url' => ''
		);

		echo json_encode(array(
			'error' => false,
			'results' => $results
		));
	}
}