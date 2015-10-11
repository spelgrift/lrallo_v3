<?php

class Global_Model extends Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function loadNav()
	{
		if($contentArray = $this->db->select("SELECT contentID, url FROM content WHERE nav = 1 AND hidden = 0 AND trashed = 0 ORDER BY nav_position ASC"))
		{
			$i = 0;
			foreach($contentArray as $a){
				$query = "SELECT * FROM page WHERE contentID = :contentID";
				$pageArray = $this->db->select($query, array(':contentID' => $a['contentID']));
				// print_r($pageArray[0]);
				// echo $pageArray[0]['name'] . " ";
				$contentArray[$i]['name'] = $pageArray[0]['name'];
				$i++;
			}
		} else {
			$contentArray = array();
		}

		
		return $contentArray;


	}



}

?>