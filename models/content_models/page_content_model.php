<?php

class Page_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Page
	public function addPage($parentPageID = "0")
	{
		if(!$nameArray = $this->_processName($_POST['name'], 'page')) {
			return false;
		}
		$url = $nameArray['url'];
		$name = $nameArray['name'];
		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'page',
			'url' => $url,
			'parentPageID' => $parentPageID,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_PAGE
		));
		$contentID = $this->db->lastInsertId();
		// Page DB entry
		$this->db->insert('page', array(
			'name' => $name,
			'displayName' => $name,
			'contentid' => $contentID
		));
		// Success!
		$results = array(
			'error' => false,
			'results' => array(
				'name' => $name,
				'displayName' => $name,
				'url' => $url,
				'path' => URL.$url,
				'parent' => '-',
				'type' => 'Page',
				'date' => date('Y/m/d'),
				'author' => $_SESSION['login'],
				'contentID' => $contentID
			)
		);
		echo json_encode($results);
	}

	// Add Spacer
	public function addSpacer($parentPageID)
	{
		// Advance positions of existing content
		$this->_advanceContentPositions($parentPageID);

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'spacer',
			'parentPageID' => $parentPageID,
			'bootstrap' => 'col-xs-12'
		));
		$contentID = $this->db->lastInsertId();

		$results = array(
			'error' => false,
			'results' => array('contentID' => $contentID)
		);
		echo json_encode($results);
	}
}