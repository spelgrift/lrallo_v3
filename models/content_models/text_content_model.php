<?php

class Text_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Text
	public function addText($parentPageID)
	{
		$text = $_POST['text'];

		// Validate length
		if($text == ""){ 
			$this->_returnError('Please enter some text!');
			return false;
		}

		$home = $parentPageID === 0 ? 1 : 0;
		// Advance positions of existing content
		$this->_advanceContentPositions($parentPageID, $home);

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'text',
			'parentPageID' => $parentPageID,
			'frontpage' => $home,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_TEXT
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
				'textID' => $textID,
				'text' => $text
			)
		);
		echo json_encode($results);
	}

	// Update Text
	public function updateText($contentID)
	{
		$text = $_POST['text'];
		if($this->db->update('text', array('text' => $text), '`contentID` ='.$contentID)){
			echo json_encode(array('error' => false));
		} else {
			echo json_encode(array('error' => true));
		}
	}
}