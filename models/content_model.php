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
			$query = "SELECT contentID, url, type, position, bootstrap FROM content WHERE parentPageID = :parentPageID AND trashed = 0 AND orphaned = 0 ORDER BY position ASC";
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
	 */
	public function buildTemplates()
	{
		return array(
			array(
				'templateID' => 'pageTemplate',
				'type' => 'page',
				'contentID' => "{{contentID}}",
				'bootstrap' => BS_PAGE,
				'pageID' => "{{pageID}}",
				'name' => "{{name}}",
				'url' => "{{url}}",
				'cover' => ""
			),
			array(
				'templateID' => 'textTemplate',
				'type' => 'text',
				'contentID' => "{{contentID}}",
				'bootstrap' => BS_TEXT,
				'textID' => "{{textID}}",
				'text' => "{{&text}}"
			),
			array(
				'templateID' => 'spacerTemplate',
				'type' => 'spacer',
				'contentID' => "{{contentID}}",
				'bootstrap' => 'col-xs-12'
			),
			array(
				'templateID' => 'singleImgTemplate',
				'type' => 'singleImage',
				'contentID' => '{{contentID}}',
				'bootstrap' => '{{bootstrap}}',
				'smVersion' => '{{smVersion}}',
				'lgVersion' => '{{lgVersion}}'
			)
		);
	}

	public function trashContent($contentID, $dashboard = false)
	{
		$timestamp = date('Y-m-d H:i:s');
		if($this->db->update('content', array('trashed' => 1, 'dateTrashed' => $timestamp), "`contentID` = " . $contentID))
		{
			$affectedRows = array($contentID);
			// If type is page, orphan associated content
			$query = "SELECT pageID FROM page WHERE contentID = :contentID";
			if($result = $this->db->select($query, array(':contentID' => $contentID)))
			{
				$affectedRows = $this->_orphanContent($result[0]['pageID'], $contentID, $affectedRows);
			}
			// If request came from dashboard, return array of affected child content to hide rows
			if($dashboard) {
				echo json_encode(array(
					'error' => false,
					'affectedRows' => $affectedRows
				));
				return;
			}
			echo json_encode(array('error' => false));
		}
		else
		{
			echo json_encode(array('error' => true));
		}		
	}

	public function deleteContent($contentID)
	{
		// Get content type
		if($result = $this->db->select("SELECT type FROM content WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			// Switch on content type
			switch($result[0]['type'])
			{
				case 'page' :
					// If item is a page, delete orphaned content
					if($pageResult = $this->db->select("SELECT contentID FROM content WHERE orphanedByID = :contentID", array(':contentID' => $contentID)))
					{
						foreach($pageResult as $row)
						{
							$this->deleteContent($row['contentID'], true);
						}
					}
					break;
				case 'gallery' :
					// Delete image files
					$this->_deleteGalImages($contentID);
					break;
				case 'galImage' :
					// Delete image files
					$this->_deleteGalImageFiles($contentID);
					break;
				case 'singleImage' :
					// Delete image files
					$this->_deleteSingleImgFiles($contentID);
					break;
			}
			// Delete record from type table
			$this->db->delete($result[0]['type'], "`contentID` = $contentID");
			// Delete content record
			$this->db->delete('content', "`contentID` = $contentID");
			return true;
		}
		return false;
	}

	public function emptyTrash()
	{
		if($result = $this->db->select("SELECT contentID FROM content WHERE trashed = 1"))
		{
			foreach($result as $row)
			{
				$this->deleteContent($row['contentID']);
			}
			echo json_encode(array('error' => false));
		}
	}

	public function restoreContent($contentID)
	{
		// Get content type
		if($result = $this->db->select("SELECT type FROM content WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			// If item is a page, restore orphaned content
			if($result[0]['type'] == 'page')
			{
				$this->db->update('content', array('orphaned' => 0, 'orphanedByID' => 0), "orphanedByID = ".$contentID);
			}
			// Update content record
			$this->db->update('content', array('trashed' => 0), "`contentID` = $contentID");
			return true;
		}
		return false;
	}

	public function sortContent()
	{
		if(isset($_POST['listItem']))
		{
			foreach($_POST['listItem'] as $position => $ID)
			{
				$this->db->update('content', array('position' => $position), "`contentID` = " . $ID);
			}
		}
	}

	public function saveResize($contentID)
	{
		$this->db->update('content', array('bootstrap' => $_POST['classes']), "`contentID` = " . $contentID);
		echo json_encode(array('error' => false));
	}

/*
 *
 * PAGE TYPE FUNCTIONS
 *
 */

	// Add Page
	public function addPage($parentPageID = "0")
	{
		$name = $_POST['name'];
		// Validate length
		if($name == ""){
			$this->_returnError('You must enter a name!');
			return false;
		}
		// Create URL friendly string
		$url = $this->_makeURL($name);

		// Make sure name/URL is not taken
		if(!$this->_checkTaken($url)){
			$this->_returnError('A page with this name already exists!');
			return false;
		}
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
			'contentid' => $contentID
		));
		// Success!
		$results = array(
			'error' => false,
			'results' => array(
				'name' => $name,
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

/*
 *
 * GALLERY TYPE FUNCTIONS
 *
 */
	// Get Gallery images for display on gallery type page
	public function getGalImages($galID)
	{
		$query = "SELECT c.contentID, c.position, g.galImageID, g.thumb, g.smVersion, g.lgVersion, g.caption, g.orientation, g.width, g.height
			FROM content AS c
			LEFT JOIN galImage AS g ON c.contentID = g.contentID
			WHERE c.trashed = 0 and c.hidden = 0 and c.parentGalID = :galID
			ORDER BY c.position ASC";
		if($result = $this->db->select($query, array(':galID' => $galID)))
		{
			return $result;
		}
		else
		{
			return array();
		}
	}

	// Add Gallery
	public function addGallery($parentPageID = "0")
	{
		$name = $_POST['name'];
		// Validate length
		if($name == ""){
			$this->_returnError('You must enter a name!');
			return false;
		}
		// Create URL friendly string
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $name);
		$url = strtolower($url);
		// Make sure name/URL is not taken
		if(!$this->_checkTaken($url)){
			$this->_returnError('A gallery (or page) with this name already exists');
			return false;
		}
		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'gallery',
			'url' => $url,
			'parentPageID' => $parentPageID,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_PAGE
		));
		$contentID = $this->db->lastInsertId();
		// Page DB entry
		$this->db->insert('gallery', array(
			'name' => $name,
			'contentid' => $contentID
		));
		$galID = $this->db->lastInsertId();
		// Success! (Return only galID and contentID for subsequent image upload)
		$results = array(
			'error' => false,
			'results' => array(
				'galID' => $galID,
				'galURL' => $url
			)
		);
		echo json_encode($results);
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
		// Get Gallery name and contentID
		$galInfo = $this->db->select("SELECT name, contentID FROM gallery WHERE galleryID = :galleryID", array(':galleryID' => $galID));
		$galName = $galInfo[0]['name'];
		$galContentID = $galInfo[0]['contentID'];

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
				'path' => URL.$galURL,
				'contentID' => $galContentID,
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

	private function _deleteGalImages($contentID)
	{
		if($result = $this->db->select("SELECT galleryID, coverPath FROM gallery WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			$galleryID = $result[0]['galleryID'];
			$cover = $result[0]['coverPath'];
			unlink($cover);
		} else {
			return;
		}
		$query = "SELECT c.contentID, g.galImageID, g.name, g.thumb, g.smVersion, g.lgVersion, g.original
			FROM content AS c
			LEFT JOIN galImage AS g ON c.contentID = g.contentID
			WHERE c.parentGalID = :galID";
		if($result = $this->db->select($query, array(':galID' => $galleryID)))
		{
			$imgCount = $this->db->rowCount;
			foreach ($result as $row) {
				// Delete display versions
				unlink($row['thumb']);
				unlink($row['smVersion']);
				unlink($row['lgVersion']);
				// If Save Originals is true, move original to deleted folder, otherwise, delete it
				if(SAVE_ORIGINALS) {
					$newPath = DELETED . $row['name'];
					rename($row['original'], $newPath);
				} else {
					unlink($row['original']);
				}
				// Delete DB records
				$this->db->delete('galImage', "galImageID = ".$row['galImageID']);
			}
			// Delete content DB records
			$this->db->delete('content', "parentGalID = '$galleryID' AND type = 'galImage'", $imgCount);
		}	
	}

	private function _deleteGalImageFiles($contentID)
	{
		if($result = $this->db->select("SELECT name, original, thumb, smVersion, lgVersion FROM galImage WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			// Delete display versions
			unlink($result[0]['thumb']);
			unlink($result[0]['smVersion']);
			unlink($result[0]['lgVersion']);
			// If Save Originals is true, move original to deleted folder, otherwise, delete it
			if(SAVE_ORIGINALS) {
				$newPath = DELETED . $result[0]['name'];
				rename($result[0]['original'], $newPath);
			} else {
				unlink($result[0]['original']);
			}
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

/*
 *
 * SINGLE IMAGE TYPE FUNCTIONS
 *
 */

	// Add Single Image
	public function addSingleImage($parentPageID, $parentUrl = 'frontpage')
	{
		// Check if there is a file
		if(empty($_FILES)) {
			$this->_returnError("No File!");
			return false;
		}

		// Check if there is a file error
		if($_FILES['file']['error'] == 1) {
			$this->_returnError("File Error!");
			unlink($_FILES['file']['tmp_name']);
			return false;
		}
		
		// Check if file is an image (not a very good check, admittedly)
		if(!preg_match("/\.(gif|jpg|png)$/i", $_FILES['file']['name'])){
			$this->_returnError("Invalid filetype");
			unlink($_FILES['file']['tmp_name']);
			return false;
		}

		// Great, move ahead with upload!
		// Get file info
		$fileTempPath = $_FILES['file']['tmp_name'];
		$fileName = $_FILES['file']['name'];
		$fileExt = end(explode(".", $fileName));

		$original = ORIGINALS . $fileName;

		// Attempt to save original file
		if(!move_uploaded_file($fileTempPath, $original)) {
			$this->_returnError("Error saving file.");
			return false;
		}

		// Resize to display versions
		$baseName = $parentUrl . "_" . date("Ymd-his") . "_";
		$smVersion = UPLOADS . $baseName . "sm." . $fileExt;
		$lgVersion = UPLOADS . $baseName . "lg." . $fileExt;

		Image::makeDisplayImgs($original, $smVersion, $lgVersion);

		// Get orientation and set bootstrap value accordingly
		$orientation = Image::getOrientation($original);
		if($orientation == 'portrait') {
			$bootstrap = 'col-xs-12 col-sm-6';
		} else {
			$bootstrap = 'col-xs-12';
		}

		// Content DB Entry
		$this->db->insert('content', array(
			'type' => 'singleImage',
			'parentPageID' => $parentPageID,
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

	private function _deleteSingleImgFiles($contentID)
	{
		if($result = $this->db->select("SELECT name, original, smVersion, lgVersion FROM singleImage WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			// Delete display versions
			unlink($result[0]['smVersion']);
			unlink($result[0]['lgVersion']);

			// If Save Originals is true, move original to deleted folder, otherwise, delete it
			if(SAVE_ORIGINALS) {
				$newPath = DELETED . $result[0]['name'];
				rename($result[0]['original'], $newPath);
			} else {
				unlink($result[0]['original']);
			}
		}
	}
/*
 *
 * NAV LINK TYPE FUNCTIONS
 *
 */

	// Add NavLink
	public function addNavLink()
	{
		// Validate
		$form = new Form();
		$form ->post('name')
				->val('blank')
				->post('url')
				->val('blank');
		if(!$form->submit()) { // Error
			$error = $form->fetchError();
			$this->_returnError(reset($error), key($error));
			return false;
		}
		$data = $form->fetch(); // Form passed

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'navLink',
			'url' => $data['url'],
			'parentPageID' => 0,
			'author' => $_SESSION['login'],
			'nav' => 1
		));

		// NavLink DB entry
		$this->db->insert('navLink', array(
			'name' => $data['name'],
			'contentid' => $this->db->lastInsertId()
		));

		echo json_encode(array('error' => false));
	}

	// Edit NavLink
	public function editNavLink($contentID)
	{
		// Validate
		$form = new Form();
		$form ->post('name')
				->val('blank')
				->post('url')
				->val('blank');
		if(!$form->submit()) { // Error
			$error = $form->fetchError();
			$this->_returnError(reset($error), key($error));
			return false;
		}
		$data = $form->fetch(); // Form passed

		// Update Content DB Entry
		$this->db->update('content', array('url' => $data['url']), "`contentID` = ".$contentID);
		$this->db->update('navLink', array('name' => $data['name']), "`contentID` = " .$contentID);
		echo json_encode(array('error' => false));
	}

/**
 *
 *	TEXT TYPE FUNCTIONS
 *
 */

	// Add Text

	public function addText($parentPageID)
	{
		$text = $_POST['text'];

		// Validate length
		if($text == ""){ 
			$this->_returnError('Please enter some text!');
			return false;
		}

		// Advance positions of existing content
		$this->_advanceContentPositions($parentPageID);

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'text',
			'parentPageID' => $parentPageID,
			'author' => $_SESSION['login'],
			'bootstrap' => BS_TEXT
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

/**
 *
 *	SPACER TYPE FUNCTIONS
 *
 */
	
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

/*
 *
 * UTILITY FUNCTIONS
 *
 */
	private function _makeURL($str)
	{
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $str);
		return strtolower($url);
	}

	private function _reArrayFiles(&$file_post)
	{
		$file_array = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
		for ($i=0; $i<$file_count; $i++) {
		  foreach ($file_keys as $key) {
		      $file_array[$i][$key] = $file_post[$key][$i];
		  }
		}
		return $file_array;
	}

	private function _checkTaken($url) {
		if($result = $this->db->select("SELECT contentID FROM content WHERE url = :url", array(':url' => $url))){
			return false;
		}
		return true;
	}

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

	private function _orphanContent($parentPageID, $trashedID, $affectedRows)
	{
		if($result = $this->db->select("SELECT contentID FROM content WHERE parentPageID = ".$parentPageID))
		{
			foreach($result as $row)
			{
				// Update DB with orphaned flag, the date orphaned, and the pageID of page whose deletion started this mess ($trashedID)
				$timestamp = date('Y-m-d H:i:s');
				$this->db->update('content', array(
					'orphaned' => 1,
					'dateOrphaned' => $timestamp,
					'orphanedByID' => $trashedID
				), "`contentID` = " . $row['contentID']);
				// Add contentIDs of affected content to array
				$affectedRows[] = $row['contentID'];
				// If pages are affected, call this method to add them to the array
				if($result = $this->db->select("SELECT pageID FROM page WHERE contentID = ".$row['contentID'])) {
					$affectedRows = $this->_orphanContent($result[0]['pageID'], $trashedID, $affectedRows);
				}
			}
		}
		return $affectedRows;
	}
}