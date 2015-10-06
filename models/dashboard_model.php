<?php

class Dashboard_Model extends Model {

	function __construct()
	{
		parent::__construct();
	}


	// REDO ALL OF THIS!

	function addPage()
	{
		$name = $_POST['pageName'];

		// Check if taken

		// Create URL friendly string
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $name);

		// Content DB entry - Include parent page if there is one
		$this->db->insert('content', array(
			'type' => 'page',
			'url' => $url
		));
		// Page DB entry
		$this->db->insert('page', array(
			'name' => $name,
			'contentid' => $this->db->lastInsertId()
		));

		// Send data back for insertion on page
		$data = array(
			'name' => $name,
			'pageid' => $this->db->lastInsertId(),
			'url' => URL . $url
		);

		echo json_encode($data);
	}

	function listPages()
	{
		if($result = $this->db->select("SELECT contentID, url FROM content WHERE type = 'page'"))
		{
			$array = array(URL);
			$returnArray = array();
			foreach($result as $a){
				// Get the page info given content id
				$query = "SELECT * FROM page WHERE contentID = :contentID";
				$result = $this->db->select($query, array(':contentID' => $a['contentID']));
				$pageAttr = $result[0];
				$pageAttr['url'] = $a['url'];
				$returnArray[] = $pageAttr;
			}
			$array[] = $returnArray;
			echo json_encode($array);			
		}

	}

	function deletePage()
	{
		$pageID = (int) $_POST['id'];
		$result = $this->db->select("SELECT contentID FROM page WHERE pageID = :pageID", array(':pageID' => $pageID));
		$contentID = $result[0]['contentID'];
		$this->db->delete('page', "`pageID` = $pageID");
		$this->db->delete('content', "`contentID` = $contentID");
		// $sth = $this->db->prepare('DELETE FROM page WHERE pageID = "' . $pageID . '"');
		// $sth->execute();
	}
}

?>