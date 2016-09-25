<?php

class Video_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Video
	public function addVideo($parentPageID = "0", $embed = false)
	{
		if(!$nameArray = $this->_processName($_POST['name'], 'page')) {
			return false;
		}
		$url = $nameArray['url'];
		$name = $nameArray['name'];
		if($embed) {
			$evParent = $parentPageID;
			$parentPageID = "0";
		}

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
		$videoID = $this->db->lastInsertId();
		// If embeded, make the embedded video!
		if($embed) {
			$ev = $this->addEmbedVideo($evParent, $videoID);
			$results = array(
				'error' => false,
				'results' => array(
					'contentID' => $ev['results']['contentID'],
					'evID' => $ev['results']['evID'],
					'videoID' => $videoID,
					'source' => $vidArray['source'],
					'link' => $vidArray['link'],
					'path' => $ev['results']['path']
				)
			);
		} else {
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
		}
		// Success!
		echo json_encode($results);
	}

	// Add Embedded Video
	public function addEmbedVideo($parentPageID, $videoID)
	{
		// Make sure video exists and get source/link to return
		$query = "SELECT c.url, c.parentPageID, v.source, v.link
			FROM video AS v
			LEFT JOIN content as c ON v.contentID = c.contentID
			WHERE v.videoID = :vidID";
		if(!$result = $this->db->select($query, array(':vidID' => $videoID))) {
			$this->_returnError("Video doesn't exist");
			return false;
		}
		$source = $result[0]['source'];
		$link = $result[0]['link'];
		$path = $this->_buildPath($result[0]['url'], $result[0]['parentPageID']);
		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'embeddedVideo',
			'parentPageID' => $parentPageID,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_VIDEO
		));
		$contentID = $this->db->lastInsertId();
		// SS DB entry
		$this->db->insert('embeddedVideo', array(
			'contentID' => $contentID,
			'videoID' => $videoID
		));
		$evID = $this->db->lastInsertId();
		$result = array(
			'error' => false,
			'results' => array(
				'evID' => $evID,
				'contentID' => $contentID,
				'videoID' => $videoID,
				'source' => $source,
				'link' => $link,
				'path' => $path
			)
		);
		return $result;
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