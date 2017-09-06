<?php

class Content_Model extends Model {

	function __construct() { parent::__construct(); }

/*
 *
 *
 * PAGE RENDERING METHODS
 *
 *
 */
	
	/**
	 *	getPageContent - Returns array of content attributes
	 *	@param string $pageid 
	 *	@return array
	 *
	 */
	public function getPageContent($pageid = false)
	{
		if($pageid) // PageID passed, get content for that page
		{
			$query = "SELECT contentID, url, type, position, bootstrap FROM content WHERE parentPageID = :parentPageID AND hidden = 0 AND trashed = 0 AND orphaned = 0 ORDER BY position ASC";
			$dbArray = array(':parentPageID' => $pageid);
		}
		else // No PageID, get content for homepage
		{
			$query = "SELECT contentID, url, type, position, bootstrap FROM content WHERE frontpage = 1 AND hidden = 0 AND trashed = 0 AND orphaned = 0 ORDER BY position ASC";
			$dbArray = array();
		}
		// Run query
		if($result = $this->db->select($query, $dbArray))
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

				if($row['type'] == 'slideshow') {
					$result[$key]['images'] = $this->getGalImages($result[$key]['galleryID']);
				}
				if($row['type'] == 'embeddedVideo') {
					$query = "SELECT source, link FROM video WHERE videoID = :videoID";
					$vidArray = $this->db->select($query, array(':videoID' => $result[$key]['videoID']));
					$result[$key]['source'] = $vidArray[0]['source'];
					$result[$key]['link'] = $vidArray[0]['link'];
				}
			}
			return $result;
		}
		else // No rows
		{
			return array();
		}
	}

	/**
	 *	getGalImages - Returns array of images to display on gallery type page
	 *	@param string $pageid 
	 *	@return array
	 *
	 */
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

/**
 *	listContent - 	Builds array of all non-trashed content of given type
 *						with subContent as sub-arrays
 *	@return array 
 *
 */
	public function listContent($type = 'all')
	{
		return $this->_getContentArrayRecursive($type, "0");
	}

	private function _getContentArrayRecursive($type, $parentPageID, $path = "")
	{
		// Build WHERE clause based on type
		if($type === 'all')
		{
			$type = array(
				'page',
				'singleImage',
				'gallery',
				'slideshow',
				'video',
				'text',
				'embeddedVideo',
				'shortcut'
			);
		} else if(is_string($type)) {
			$type = array($type);
		}
		$where = "";
		foreach($type as $str) {
			$where .= "type = '$str' OR ";
		}
		$where = rtrim($where, 'OR ') . " ";

		// If pages are included in the requested types, group content by parent page
		if(in_array('page', $type))
		{
			$parentCondition = "AND parentPageID = $parentPageID";
		} else {
			$parentCondition = "";
		}
		// Create empty array
		$returnArray = array();
		// Get content results from DB
		if($result = $this->db->select("SELECT contentID, url, type, parentPageID, author, `date` FROM content WHERE trashed = '0' $parentCondition AND ( $where ) ORDER BY contentID DESC"))
		{
			foreach($result as $row)
			{
				// Add attributes common to all types
				$typeArray = array(
					'contentID' => $row['contentID'],
					'type' => $row['type'],
					'parentPageID' => $row['parentPageID'],
					'date' => $row['date'],
					'author' => $row['author']
				);
				// Switch by type
				$thisType = $row['type'];
				switch($thisType)
				{
					case "page":
					case "gallery":
					case "video" :
						// Append trailing / to path if item has a parent page
						if(strlen($path) > 0) {	
							$path = $path . "/";	
						}
						// Create/save path + url
						$typeArray['url'] = $row['url'];
						$typeArray['path'] = $path . $row['url'];

						$query = "SELECT ".$thisType."ID, name FROM ".$thisType." WHERE contentID = :contentID";
						$result = $this->db->select($query, array(':contentID' => $row['contentID']));

						$typeArray[$thisType.'ID'] = $result[0][$thisType.'ID'];
						$typeArray['name'] = $result[0]['name'];

						// If page, get subcontent
						if($thisType == "page") {
							$typeArray['subContent'] = $this->_getContentArrayRecursive($type, $typeArray['pageID'], $typeArray['path']);
						}
					break;
					case "embeddedVideo" :
					case "slideshow" :
						$typeArray['path'] = $path;

						$evQuery = "SELECT v.name
							FROM embeddedVideo AS e
							LEFT JOIN video AS v ON e.videoID = v.videoID
							WHERE e.contentID = :contentID";
						$svQuery = "SELECT g.name
							FROM slideshow AS s
							LEFT JOIN gallery as g ON s.galleryID = g.galleryID
							WHERE s.contentID = :contentID";
						$query = ($thisType == "slideshow") ? $svQuery : $evQuery;

						$result = $this->db->select($query, array(':contentID' => $row['contentID']));
						$typeArray['name'] = $result[0]['name'];
					break;
					case "text" :
						$typeArray['path'] = $path;

						$result = $this->db->select("SELECT `textID`, `text` FROM `text` WHERE contentID = '".$row['contentID']."'");

						$typeArray['textID'] = $result[0]['textID'];
						$typeArray['text'] = $result[0]['text'];
					break;
					case "singleImage" :
						$typeArray['path'] = $path;

						$result = $this->db->select("SELECT singleImageID, name FROM singleImage WHERE contentID = '".$row['contentID']."'");

						$typeArray['singleImageID'] = $result[0]['singleImageID'];
						$typeArray['name'] = $result[0]['name'];
					break;
				}

				$returnArray[] = $typeArray;
			}
		}
		return $returnArray;
	}

	/**
	 *	buildTemplates - Populate array with mustache tags
	 *	@return array
	 *
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
				'displayName' => "{{name}}",
				'url' => "{{url}}",
				'coverPath' => ""
			),
			array(
				'templateID' => 'videoTemplate',
				'type' => 'video',
				'contentID' => "{{contentID}}",
				'bootstrap' => BS_PAGE,
				'videoID' => "{{videoID}}",
				'displayName' => "{{name}}",
				'url' => "{{url}}",
				'coverPath' => ""
			),
			array(
				'templateID' => 'evTemplate',
				'type' => 'embeddedVideo',
				'contentID' => "{{contentID}}",
				'bootstrap' => BS_VIDEO,
				'embeddedVideoID' => "{{evID}}",
				'source' => '',
				'link' => ''
			),
			array(
				'templateID' => 'galleryTemplate',
				'type' => 'gallery',
				'contentID' => "{{contentID}}",
				'bootstrap' => BS_PAGE,
				'galleryID' => "{{galleryID}}",
				'name' => "{{name}}",
				'url' => "{{url}}",
				'coverPath' => "{{coverPath}}"
			),
			array(
				'templateID' => 'slideshowTemplate',
				'type' => 'slideshow',
				'contentID' => '{{contentID}}',
				'bootstrap' => BS_SLIDESHOW,
				'slideshowID' => "{{slideshowID}}",
				'galleryID' => "{{galleryID}}",
				'autoplay' => "0",
				'animationType' => "slide",
				'animationSpeed' => "500",
				'slideDuration' => "3000"
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

/*
 *
 *
 * API BACKEND METHODS
 *
 *
 */

	/**
	 *	updateSettings - Updates page settings. Wasn't that descriptive?
	 *
	 */
	public function updateSettings($type, $contentID, $displayName = "")
	{
		// Common attributes
		$name = $_POST['name'];
		$url = $_POST['url'];
		$parent = $_POST['parent'];
		$nav = $_POST['nav'];
		$hidden = $_POST['hidden'];
		$origName = $_POST['origName'];
		$origURL = $_POST['origURL'];
		// Type specific attributes
		switch($type) {
			case 'gallery':
				$animation = $_POST['animation'];
				$autoplay = $_POST['autoplay'];
				$duration = $_POST['duration'];
				$display = $_POST['display'];
				// Validate duration
				if($duration == "" || !is_numeric($duration))
				{
					$this->_returnError('You must enter a number', 'duration');
					return false;
				}
			break;
			case 'video':
				$postedLink = $_POST['link'];
				$description = $_POST['description'];
				// Validate link
				if(!$vidArray = $this->_processVideoLink($postedLink)) {
					return false;
				}
			break;
		}
		// Process Name and URL
		if(!$url = $this->_processNameUrl($type, $name, $url, $origName, $origURL)) {
			return false;
		}
		// Handle homepage logic
		if($parent === 'home'){
			$parent = 0;
			$home = 1;
		} else {
			$home = 0;
		}
		// Content DB Update
		$this->db->update('content', array(
			'url' => $url,
			'parentPageID' => $parent,
			'frontpage' => $home,
			'nav' => $nav,
			'hidden' => $hidden
		), "`contentID` = ".$contentID);
		// Type DB Update
		switch($type)
		{
			case 'page':
				$fields = array('name' => $name);
				if($origName == $displayName) {
					$fields['displayName'] = $name;
				}
				$this->db->update('page', $fields, "`contentID` = ".$contentID);
			break;
			case 'gallery':
				$this->db->update('gallery', array(
					'name' => $name,
					'autoplay' => $autoplay,
					'animationType' => $animation,
					'defaultDisplay' => $display,
					'slideDuration' => $duration
				), "`contentID` = ".$contentID);
			break;
			case 'video':
				$this->db->update('video', array(
					'name' => $name,
					'description' => $description,
					'source' => $vidArray['source'],
					'link' => $vidArray['link'],
					'postedLink' => $vidArray['postedLink']
				), "`contentID` = ".$contentID);
			break;
		}
		$path = $this->_buildPath($url, $parent);
		$windowPath = DEVPATH . $path . "/edit";
		$viewPath = URL . $path;
		echo json_encode(array(
			'error' => false,
			'name' => $name,
			'url' => $url,
			'windowPath' => $windowPath,
			'viewPath' => $viewPath
		));
	}
	/**
	 *	trashContent - Marks content item with the 'trashed' flag
	 *
	 */
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

	/**
	 *	deleteContent - Deletes content DB reference and associated files
	 *
	 */
	public function deleteContent($contentID)
	{
		// Get content type
		if($result = $this->db->select("SELECT type FROM content WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			$type = $result[0]['type'];
			// Switch on content type
			switch($type)
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
					// Delete cover image files
					$this->_deleteCoverImage($type, $contentID);
					break;
				case 'video' :
					// Delete cover image files
					$this->_deleteCoverImage($type, $contentID);
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

	/**
	 *	emptyTrash - Deletes all trashed content items 
	 *
	 */
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

	/**
	 *	restoreContent - Removes 'trashed' flag from given content item
	 *
	 */
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

	/**
	 *	sortContent - updates position data
	 *
	 */
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

	/**
	 *	saveResize - Saves bootstrap classes to DB
	 *
	 */
	public function saveResize($contentID)
	{
		$this->db->update('content', array('bootstrap' => $_POST['classes']), "`contentID` = " . $contentID);
		echo json_encode(array('error' => false));
	}

/*
 *
 *
 * FILE DELETION METHODS (private)
 *
 *
 */

	private function _deleteCoverImage($type, $contentID)
	{
		if($result = $this->db->select("SELECT coverPath, coverOriginal FROM $type WHERE contentID = :contentID", array(':contentID' => $contentID))){
			$cover = $result[0]['coverPath'];
			$original = $result[0]['coverOriginal'];
			if(file_exists($cover)) {
				unlink($cover);
				unlink($original);
			}
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
 *
 * UTILITY METHODS (protected, used by type classes)
 *
 *
 */

	protected function _makeURL($str)
	{
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $str);
		return strtolower($url);
	}

	// For making new pages
	protected function _processName($name, $type)
	{
		// Validate length
		if($name == ""){
			$this->_returnError('You must enter a name!');
			return false;
		}
		// Create URL friendly string
		$url = $this->_makeURL($name);

		// Make sure name/URL is not taken
		if(!$this->_checkTaken($url)){
			$this->_returnError("A $type with this name already exists!");
			return false;
		}
		return array(
			'name' => $name,
			'url' => $url
		);
	}

	// For updating settings
	protected function _processNameUrl($type, $name, $url, $origName, $origURL)
	{
		// Validate length
		if($name == ""){
			$this->_returnError('Name cannot be blank!', 'name');
			return false;
		}
		if($url == ""){
			$this->_returnError('URL cannot be blank!', 'url');
			return false;
		}
		// Make sure URL uses correct characters
		$url = preg_replace('#[^a-z.0-9_]#i', '_', $url);

		// Make sure name/URL are not taken
		$query = "SELECT * FROM content WHERE url = :url";
		if($url != $origURL && $result = $this->db->select($query, array(':url' => $url))){
			$this->_returnError("A $type with that URL already exists.", 'url');
			return false;
		}
		$query = "SELECT * FROM $type WHERE name = :name";
		if($name != $origName && $result = $this->db->select($query, array(':name' => $name))){
			$this->_returnError("A $type with that name already exists.", 'name');
			return false;
		}
		return $url;
	}

	// Process Video Link
	protected function _processVideoLink($postedLink)
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

	protected function _saveOriginalImage($files)
	{
		// Check if there is a file
		if(empty($files)) {
			$this->_returnError("No File!");
			return false;
		}

		// Check if there is a file error
		if($files['file']['error'] == 1) {
			$this->_returnError("File Error!");
			unlink($files['file']['tmp_name']);
			return false;
		}
		
		// Check if file is an image (not a very good check, admittedly)
		if(!preg_match("/\.(gif|jpg|png)$/i", $files['file']['name'])){
			$this->_returnError("Invalid filetype");
			unlink($files['file']['tmp_name']);
			return false;
		}

		// Great, move ahead with upload!
		// Get file info
		$fileTempPath = $files['file']['tmp_name'];
		$fileName = $files['file']['name'];
		$fileExt = end(explode(".", $fileName));

		$original = ORIGINALS . date("Ymd-his")."_".$fileName;

		// Attempt to save original file
		if(!move_uploaded_file($fileTempPath, $original)) {
			$this->_returnError("Error saving file.");
			return false;
		}

		return array(
			'original' => $original,
			'fileName' => $fileName,
			'fileExt' => $fileExt
		);
	}

	protected function _reArrayFiles(&$file_post)
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

	protected function _checkTaken($url) {
		if($result = $this->db->select("SELECT contentID FROM content WHERE url = :url", array(':url' => $url))){
			return false;
		}
		return true;
	}

	protected function _advanceContentPositions($parentPageID = 0, $home = 0)
	{
		if($home === 0) {
			$query = "SELECT position, contentID FROM content WHERE parentPageID = '".$parentPageID."'";
		} else {
			$query = "SELECT position, contentID FROM content WHERE frontpage = 1";
		}
		if ($result = $this->db->select($query))
		{
			foreach($result as $row)
			{
				$postData = array('position' => $row['position'] + 1);
				$this->db->update('content', $postData, "`contentID` = ".$row['contentID']);
			}
		}
	}

	protected function _orphanContent($parentPageID, $trashedID, $affectedRows)
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