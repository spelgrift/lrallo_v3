<?php

class Content_Model extends Model {

	function __construct(){parent::__construct();}

/*
 *
 * GENERAL FUNCTIONS - FOR ALL CONTENT TYPES
 *
 */

	/**
	 *	getPageContent
	 *	@param string $pageid 
	 *	@return array
	 *
	 */
	public function getPageContent($pageid = false)
	{
		if($pageid) // PageID passed, get content for that page
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
			else // No rows
			{
				return array();
			}
		} 
		else // No PageID, get content for homepage
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

/*
 *
 * PAGE FUNCTIONS
 *
 */

	// Add Page

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
 *	TEXT FUNCTIONS
 *
 */

	// Add Text

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

		// Advance positions of existing content
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