<?php

class Page_Model extends Model {

	function __construct()
	{
		parent::__construct();

	}


/**
 *	getPageInfo - 
 *	@param string $url The page url
 *	@return mixed, array of page attributes, false on no rows
 *
 */
	public function getPageInfo($url)
	{
		$query = "SELECT * FROM content WHERE url = :url";
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
 *	getPageContent
 *	@param string $pageid 
 *	@return mixed
 *
 */
	public function getPageContent($pageid)
	{
		if($pageid)
		{
			// SELECT * FROM content WHERE page = $pageid
			return "Content for page $pageid";
		} 
		else 
		{
			return "Home Page Content";
		}
		
	}

}

?>
