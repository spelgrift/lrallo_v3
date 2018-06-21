<?php

class Video_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Video
	public function addVideo($parentPageID, $dashboard = false , $embed = false, $type = "page")
	{
		if(!$nameArray = $this->_processName($_POST['name'], 'page')) {
			return false;
		}
		$url = $nameArray['url'];
		$name = $nameArray['name'];

		if(!$vidArray = $this->_processVideoLink($_POST['link'])) {
			return false;
		}

		$home = ($parentPageID === 0 && !$dashboard) ? 1 : 0;
		// Advance positions of existing content
		if(!$dashboard) {
			$this->_advanceContentPositions($parentPageID, $home, $type);
		}
		if($embed) {
			$evParent = $parentPageID;
			$parentPageID = 0;
			$home = 0;
		}

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'video',
			'url' => $url,
			'parentPageID' => $parentPageID,
			'frontpage' => $home,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_PAGE
		));
		$contentID = $this->db->lastInsertId();
		// Video DB entry
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
			$ev = $this->addEmbedVideo($evParent, $videoID, $type);
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
	public function addEmbedVideo($parentPageID, $videoID, $type = 'page')
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
		$path = $result[0]['url'];
		if($type === 'page') {
			$path = $this->_buildPath($result[0]['url'], $result[0]['parentPageID']);
		}
		$home = $parentPageID === 0 ? 1 : 0;
		$this->_advanceContentPositions($parentPageID, $home, $type);
		// Content DB entry
		$typeID = "parent".ucfirst($type)."ID";
		$this->db->insert('content', array(
			'type' => 'embeddedVideo',
			$typeID => $parentPageID,
			'frontpage' => $home,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_VIDEO
		));
		$contentID = $this->db->lastInsertId();
		// EV DB entry
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
}