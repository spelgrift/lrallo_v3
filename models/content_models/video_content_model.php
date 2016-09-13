<?php

class Video_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Video
	public function addVideo($parentPageID = "0")
	{
		if(!$nameArray = $this->_processName($_POST['name'], 'page')) {
			return false;
		}
		$url = $nameArray['url'];
		$name = $nameArray['name'];

		if(!$vidArray = $this->_processVideoLink($_POST['link'])) {
			return false;
		}

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'video',
			'url' => $url,
			'parentPageID' => $parentPageID,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_PAGE
		));
		$contentID = $this->db->lastInsertId();
		// Page DB entry
		$this->db->insert('video', array(
			'name' => $name,
			'displayName' => $name,
			'contentid' => $contentID,
			'source' => $vidArray['source'],
			'link' => $vidArray['link'],
			'postedLink' => $vidArray['postedLink']
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
				'type' => 'Video',
				'date' => date('Y/m/d'),
				'author' => $_SESSION['login'],
				'contentID' => $contentID
			)
		);
		echo json_encode($results);
	}

	// Process Video Link
	private function _processVideoLink($postedLink)
	{
		// Validate length
		if($postedLink == ""){
			$this->_returnError('You must enter a link!');
			return false;
		}

		if(strpos($postedLink, 'vimeo')) {
			$source = 'vimeo';
		} else if(strpos($postedLink, 'you')) {
			$source = 'youtube';
		} else {
			$this->_returnError('Invalid link');
			return false;
		}

		switch($source) {
			case 'vimeo':
				$link = end(explode("/", $postedLink));
				if(!is_numeric($link)) {
					$this->_returnError('Invalid link');
					return false;
				}	
			break;
			case 'youtube':
				if(strpos($postedLink, 'watch')) {
					$delimiter = "=";
				} else {
					$delimiter = "/";
				}
				$link = end(explode($delimiter, $postedLink));
				if(strlen($link) != 11) {
					$this->_returnError('Invalid link');
					return false;
				}
			break;
		}

		return array(
			'source' => $source,
			'link' => $link,
			'postedLink' => $postedLink
		);
	}
}