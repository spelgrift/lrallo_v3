<?php

class Gallery_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	// Add Gallery
	public function addGallery($parentPageID, $dashboard = false , $slideshow = false, $type = 'page')
	{
		if(!$nameArray = $this->_processName($_POST['name'], 'gallery')) {
			return false;
		}
		$url = $nameArray['url'];
		$name = $nameArray['name'];

		$home = ($parentPageID === 0 && !$dashboard) ? 1 : 0;
		// Advance positions of existing content
		if(!$dashboard) {
			$this->_advanceContentPositions($parentPageID, $home, $type);
		}

		$typeID = "parent".ucfirst($type)."ID";
		if($slideshow) {
			$ssParent = $parentPageID;
			$parentPageID = 0;
			$home = 0;
			$typeID = 'parentPageID';
		}
		// Content DB entry
		
		$this->db->insert('content', array(
			'type' => 'gallery',
			'url' => $url,
			$typeID => $parentPageID,
			'frontpage' => $home,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_PAGE
		));
		$contentID = $this->db->lastInsertId();
		// Page DB entry
		$this->db->insert('gallery', array(
			'name' => $name,
			'contentID' => $contentID
		));
		$galID = $this->db->lastInsertId();
		// If this is a slideshow, make the slideshow!
		if($slideshow) {
			$ss = $this->addSlideshow($ssParent, $galID, $type);
			$results = array(
				'error' => false,
				'results' => array(
					'galID' => $galID,
					'galURL' => $url,
					'ssID' => $ss['results']['slideshowID'],
					'contentID' => $ss['results']['contentID']
				)
			);
		} else { // Otherwise just return gallery info for image upload
			$results = array(
				'error' => false,
				'results' => array(
					'galID' => $galID,
					'galURL' => $url
				)
			);
		}
		echo json_encode($results);
	}

	// Add Slideshow
	public function addSlideshow($parentPageID, $galID, $type = 'page')
	{
		// Make sure gal exists
		if(!$this->db->select("SELECT name FROM gallery WHERE galleryID = :galID", array(':galID' => $galID))) {
			$this->_returnError("Gallery doesn't exist");
			return false;
		}
		$home = $parentPageID === 0 ? 1 : 0;
		$this->_advanceContentPositions($parentPageID, $home, $type);
		
		// Content DB entry
		$typeID = "parent".ucfirst($type)."ID";
		$this->db->insert('content', array(
			'type' => 'slideshow',
			$typeID => $parentPageID,
			'frontpage' => $home,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_SLIDESHOW
		));
		$contentID = $this->db->lastInsertId();
		// SS DB entry
		$this->db->insert('slideshow', array(
			'contentID' => $contentID,
			'galleryID' => $galID
		));
		$ssID = $this->db->lastInsertId();
		$result = array(
			'error' => false,
			'results' => array(
				'slideshowID' => $ssID,
				'contentID' => $contentID,
				'galleryID' => $galID
			)
		);
		return $result;
	}

	// Add Images
	public function addGalImages($galID, $galURL, $dashboard = false)
	{
		set_time_limit(600);
		// Make sure some files were uploaded
		if(empty($_FILES)) {
			$this->_returnError("No Files!");
			return false;
		}
		// Check to see if a cover has been created
		$hasCover = $this->_checkCover($galID);
		// Count images currently in this gallery for position value and filename counter
		$imgCount = $this->db->countRows('content', "type = 'galImage' AND parentGalID = :galID", array(':galID' => $galID));
		$position = $imgCount;
		$paddedCount = str_pad((int) $imgCount, 3, "0", STR_PAD_LEFT);
		// Image base name
		$baseName = $galURL."_".$paddedCount;
		// Reorder file array into one that makes sense
		$fileArray = $this->_reArrayFiles($_FILES['file']);
		// Array to track errors
		$error = array();
		// Array to hold successful uploads
		$savedImages = array();
		// Iterate over array
		foreach($fileArray as $file)
		{
			// Check if there was a file error
			if($file['error'] == 1) {
				$error[] = array('name' => $file['name'], 'error' => 'File Error');
				continue;
			}
			// Make sure file is an image
			if(!preg_match("/\.(gif|jpg|png)$/i", $file['name'])){
				$error[] = array('name' => $file['name'], 'error' => 'Invalid Type');
				unlink($file['tmp_name']);
				continue;
			}
			// Get file Info
			$fileTempPath = $file['tmp_name'];
			$fileName = $file['name'];
			$fileExt = end(explode(".", $fileName));
			$original = ORIGINALS . $fileName;
			// Attempt to save original file
			if(!move_uploaded_file($fileTempPath, $original)) {
				$error[] = array('name' => $fileName, 'error' => 'Error Saving File');
				continue;
			}
			// Check if name exists, if so, increment file name until a unique one is found
			$f = false;
			while(!$f) {
				$testName = UPLOADS.$baseName."_sm.".$fileExt;
				if(!file_exists($testName)){
					$smVersion = UPLOADS.$baseName."_sm.".$fileExt;
					$lgVersion = UPLOADS.$baseName."_lg.".$fileExt;
					$thumb = THUMBS.$baseName."_thumb.".$fileExt;
					$f = true;
				} else {
					$baseName++;
				}
			}
			// Make display versions
			Image::makeDisplayImgs($original, $smVersion, $lgVersion);
			// Make thumbnail
			Image::makeThumbnail($smVersion, $thumb);
			// Get width + height
			list($imgW, $imgH) = getimagesize($smVersion);
			// Get orientation
			$orientation = Image::getOrientation($original);
			// DB Entries
			$this->db->insert('content', array(
				'type' => 'galImage',
				'parentGalID' => $galID,
				'position' => $position,
				'author' => $_SESSION['login']
			));
			$contentID = $this->db->lastInsertId();
			$this->db->insert('galImage', array(
				'contentID' => $contentID,
				'name' => $fileName,
				'original' => $original,
				'thumb' => $thumb,
				'smVersion' => $smVersion,
				'lgVersion' => $lgVersion,
				'orientation' => $orientation,
				'width' => $imgW,
				'height' => $imgH
			));
			$imgID = $this->db->lastInsertId();

			// Make cover if necessary
			if(!$hasCover && $orientation == 'landscape')
			{
				$coverPath = COVERS.$galURL."_cover.".$fileExt;
				Image::makeCover($smVersion, $coverPath);
				$this->db->update('gallery', array(
					'coverPath' => $coverPath,
					'coverID' => $imgID
				), "`galleryID` = ".$galID);
				$hasCover = true;
			}

			// Add data for this image to array
			$savedImages[] = array(
				'contentID' => $contentID,
				'imgID' => $imgID,
				'position' => $position,
				'thumb' => URL.$thumb,
				'smVersion' => URL.$smVersion,
				'lgVersion' => URL.$lgVersion,
				'caption' => ''
			);
			// Increment filename and position
			$baseName++;
			$position++;
		}
		// Get Gallery name, contentID, and coverPath
		$galInfo = $this->db->select("SELECT name, contentID, coverPath FROM gallery WHERE galleryID = :galleryID", array(':galleryID' => $galID));
		$galName = $galInfo[0]['name'];
		$galContentID = $galInfo[0]['contentID'];
		$coverPath = $galInfo[0]['coverPath'];

		// If request came from dashboard, return data for content list
		if($dashboard)	{
			$returnDetails = array(
				'name' => $galName,
				'path' => URL.$galURL,
				'parent' => '-',
				'type' => 'Gallery',
				'date' => date('Y/m/d'),
				'author' => $_SESSION['login'],
				'contentID' => $galContentID
			);
		} else {
			$returnDetails = array(
				'name' => $galName,
				'type' => 'gallery',
				'url' => $galURL,
				'galleryID' => $galID,
				'contentID' => $galContentID,
				'coverPath' => $coverPath,
				'images' => $savedImages
			);
		}
		
		// Report any errors
		if(!empty($error)) {
			$results = array(
				'error' => true,
				'error_msg' => 'There were errors with your upload, however some files may have saved correctly. See the error list below.',
				'error_details' => $error,
				'results' => $returnDetails
			);
		} else {
			$results = array(
				'error' => false,
				'results' => $returnDetails
			);
		}
		echo json_encode($results);
	}

	// Update Gal Image Caption

	public function updateGalCaption($galImageID)
	{
		// Get user input
		$caption = $_POST['caption'];

		// Update DB
		$this->db->update('galImage', array('caption' => $caption), "`galImageID` = ".$galImageID);
		echo json_encode(array('error' => false));
	}

	// Update Gal Cover

	public function updateGalCover($galID, $galURL, $currentCover, $newCoverImgID)
	{
		unlink($currentCover);
		// Get original image
		$query = "SELECT original FROM galImage WHERE galImageID = :imgID";
		if($result = $this->db->select($query, array(':imgID' => $newCoverImgID)))
		{
			$srcImg = $result[0]['original'];
			$coverPath = COVERS.$galURL."_cover.jpg";
			Image::makeCover($srcImg, $coverPath);
			$this->db->update('gallery', array(
				'coverPath' => $coverPath,
				'coverID' => $newCoverImgID
			), "`galleryID` = ".$galID);
			echo json_encode(array('error' => false));
		}
	}

	// Returns true if a cover has been created
	private function _checkCover($galID)
	{
		$query = "SELECT coverPath FROM gallery WHERE galID = :galID";
		if($result = $this->db->select($query, array(':galID' => $galID)))
		{
			if($result[0]['coverPath'] == "") {
				return false;
			} else {
				return true;
			}
		}
	}
}