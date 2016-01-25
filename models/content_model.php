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
			$query = "SELECT contentID, type, position, bootstrap FROM content WHERE parentPageID = :parentPageID AND trashed = 0 AND orphaned = 0 ORDER BY position ASC";
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
				'templateID' => 'textTemplate',
				'type' => 'text',
				'contentID' => "{{contentID}}",
				'bootstrap' => 'col-xs-12',
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
				'mdVersion' => '{{mdVersion}}',
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
				return $affectedRows;
			}
			return true;
		}
		else
		{
			return false;
		}		
	}

	public function deleteContent($contentID)
	{
		// Get content type
		if($result = $this->db->select("SELECT type FROM content WHERE contentID = :contentID", array(':contentID' => $contentID)))
		{
			// If item is a page, delete orphaned content
			if($result[0]['type'] == 'page')
			{
				if($pageResult = $this->db->select("SELECT contentID FROM content WHERE orphanedByID = :contentID", array(':contentID' => $contentID)))
				{
					foreach($pageResult as $row)
					{
						$this->deleteContent($row['contentID'], true);
					}
				}
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

	public function addPage($parentPageID)
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
		if($result = $this->db->select("SELECT * FROM content WHERE url = :url", array(':url' => $url))){
			$this->_returnError('A page with that name already exists.');
			return false;
		}

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'page',
			'url' => $url,
			'parentPageID' => $parentPageID,
			'author' => $_SESSION['login']
		));
		$contentID = $this->db->lastInsertId();

		// Page DB entry
		$this->db->insert('page', array(
			'name' => $name,
			'contentid' => $contentID
		));

		$results = array(
			'error' => false,
			'name' => $name,
			'path' => URL.$url,
			'parent' => '-',
			'type' => 'Page',
			'date' => date('Y/m/d'),
			'author' => $_SESSION['login'],
			'contentID' => $contentID
		);
		echo json_encode($results);
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
		$mdVersion = UPLOADS . $baseName . "md." . $fileExt;
		$lgVersion = UPLOADS . $baseName . "lg." . $fileExt;

		Image::makeDisplayImgs($original, $smVersion, $mdVersion, $lgVersion);

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
			'mdVersion' => $mdVersion,
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
				'mdVersion' => $mdVersion,
				'lgVersion' => $lgVersion
			)
		);
		echo json_encode($results);	
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
			'author' => $_SESSION['login']
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
			'parentPageID' => $parentPageID
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
	private function _returnError($message, $field = null)
	{
		$results = array(
			'error' => true,
			'error_msg' => $message,
			'error_field' => $field
		);
		echo json_encode($results);
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
?>