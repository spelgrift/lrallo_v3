<?php

class Image_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Single Image
	public function addSingleImage($parentPageID, $parentUrl, $type = 'page')
	{
		if(!$image = $this->_saveOriginalImage($_FILES)) {
			return false;
		}

		// Advance positions of existing content
		$home = $parentPageID === 0 ? 1 : 0;
		$this->_advanceContentPositions($parentPageID, $home, $type);

		$original = $image['original'];
		$fileName = $image['fileName'];
		$fileExt = $image['fileExt'];

		// Resize to display versions
		$baseName = $parentUrl . "_" . date("Ymd-his") . "_";
		$smVersion = UPLOADS . $baseName . "sm." . $fileExt;
		$lgVersion = UPLOADS . $baseName . "lg." . $fileExt;

		Image::makeDisplayImgs($original, $smVersion, $lgVersion);

		// Get orientation and set bootstrap value accordingly
		$orientation = Image::getOrientation($original);
		if($orientation == 'portrait' || $orientation == 'square') {
			$bootstrap = 'col-xs-12 col-sm-6';
			if($type === 'post'){
				$bootstrap = 'col-xs-12 col-sm-4';
			}
		} else {
			$bootstrap = 'col-xs-12';
		}

		// Content DB Entry
		$typeID = "parent".ucfirst($type)."ID";
		$this->db->insert('content', array(
			'type' => 'singleImage',
			$typeID => $parentPageID,
			'frontpage' => $home,
			'author' => $_SESSION['login'],
			'bootstrap' => $bootstrap
		));
		$contentID = $this->db->lastInsertId();

		// Single Image DB Entry
		$this->db->insert('singleImage', array(
			'contentID' => $contentID,
			'name' => $fileName,
			'original' => $original,
			'smVersion' => $smVersion,
			'lgVersion' => $lgVersion,
			'orientation' => $orientation
		));

		// Success!
		$results = array(
			'error' => false,
			'results' => array(
				'contentID' => $contentID,
				'bootstrap' => $bootstrap,
				'smVersion' => $smVersion,
				'lgVersion' => $lgVersion
			)
		);
		echo json_encode($results);	
	}
}