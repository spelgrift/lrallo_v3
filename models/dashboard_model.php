<?php

class Dashboard_Model extends Model {

	function __construct()
	{
		parent::__construct();
	}

	public function sortNav() {
		if(isset($_POST['listItem']))
		{
			foreach($_POST['listItem'] as $position => $ID)
			{
				$this->db->update('content', array('nav_position' => $position), "`contentID` = " . $ID);
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
 *	listContent - Builds array of all non-trashed content with subContent as sub-arrays
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
				'embedded-video',
				'shortcut'
			);
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
				switch($row['type'])
				{
					case "page" :
						// Append trailing / to path if item has a parent page
						if(strlen($path) > 0) {	$path = $path . "/";	}

						$typeArray['url'] = $row['url'];
						$typeArray['path'] = $path . $row['url'];

						$result = $this->db->select("SELECT pageID, name FROM page WHERE contentID = '".$row['contentID']."'");

						$typeArray['pageID'] = $result[0]['pageID'];
						$typeArray['name'] = $result[0]['name'];

						$typeArray['subContent'] = $this->_getContentArrayRecursive($type, $typeArray['pageID'], $typeArray['path']);
					break;
					case "video" :
						// Append trailing / to path if item has a parent page
						if(strlen($path) > 0) {	$path = $path . "/";	}

						$typeArray['url'] = $row['url'];
						$typeArray['path'] = $path . $row['url'];

						$result = $this->db->select("SELECT videoID, name FROM video WHERE contentID = '".$row['contentID']."'");

						$typeArray['videoID'] = $result[0]['videoID'];
						$typeArray['name'] = $result[0]['name'];
					break;
					case "gallery" :
						// Append trailing / to path if item has a parent page
						if(strlen($path) > 0) {	$path = $path . "/";	}

						$typeArray['url'] = $row['url'];
						$typeArray['path'] = $path . $row['url'];

						$result = $this->db->select("SELECT galleryID, name FROM gallery WHERE contentID = '".$row['contentID']."'");

						$typeArray['galleryID'] = $result[0]['galleryID'];
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
 *	renderContentRows - Renders html to go into <tbody> on page load and content list reload
 *
 */
	public function renderContentRows($contentList)
	{
		$contentRows = "";
		foreach($contentList as $row) {
			$contentRows .= $this->_renderContentHtmlRecursive($row);
		}
		if($contentRows == "") {
			$contentRows = "<tr class='placeholderRow'><td colspan='5'>No content found. Get started by clicking 'Add Content' above and creating a page.</td></tr>";
		}
		return $contentRows;
	}

	private function _renderContentHtmlRecursive($row, $subLevel = 0, $parentName = '-')
	{
		$rowHTML = "";
		$defaultPad = "&ensp;<i class='fa fa-level-up fa-rotate-90'></i>&ensp;";
		// Build pad based on subLevel
		$pad = "";
		if($subLevel == 1) {
			$pad = $defaultPad;
		} else if($subLevel > 1) {
			$pad = str_repeat("&emsp; ", ($subLevel - 1)).$defaultPad;
		}
		// Add vars common to all types
		$contentID = $row['contentID'];
		$path = $row['path'];
		$date = date('Y/m/d', strtotime($row['date']));
		$author = $row['author'];
		// Switch based on type
		switch($row['type'])
		{
			case "page" :
				$name = $row['name'];
				$nameTd = "<td class='listName'><span class='listPad'>$pad</span><a href='".URL.$path."'>$name</a></td>";
				$type = 'Page';
				$rowClass = 'contentListRow page visible';
				$parentLink = "<a href='".URL.$path."'>$name</a>";
			break;
			case "video" :
				$name = $row['name'];
				$nameTd = "<td class='listName'><span class='listPad'>$pad</span><a href='".URL.$path."'>$name</a></td>";
				$type = 'Video';
				$rowClass = 'contentListRow video';
				$parentLink = "<a href='".URL.$path."'>$name</a>";
			break;
			case "gallery" :
				$name = $row['name'];
				$nameTd = "<td class='listName'><span class='listPad'>$pad</span><a href='".URL.$path."'>$name</a></td>";
				$type = 'Gallery';
				$rowClass = 'contentListRow gallery';
				$parentLink = "<a href='".URL.$path."'>$name</a>";
			break;

			case "text" :
				$trimmedText = substr(htmlentities($row['text']), 0, 25).'...';
				$nameTd = "<td><span class='listPad'>$pad</span>$trimmedText</td>";
				$type = 'Text';
				$rowClass = 'contentListRow text';
			break;

			case "singleImage":
				$name = $row['name'];
				$nameTd = "<td><span class='listPad'>$pad</span>$name</td>";
				$type = 'Single Image';
				$rowClass = 'contentListRow singleImage';
			break;
		}
		// Echo HTML
		$rowHTML .= "<tr id='$contentID' class='$rowClass'>";

		$rowHTML .= $nameTd;						
		$rowHTML .= "<td>$type</td>";
		$rowHTML .= "<td>$parentName</td>";
		$rowHTML .= "<td class='hidden-xs'>$date</td>";
		$rowHTML .= "<td class='hidden-xs'>$author</td>";

		$rowHTML .= "<td>";
		$rowHTML .= "<a href='".URL.$path."' class='btn btn-primary btn-sm'>View</a> ";
		$rowHTML .= "<a href='".URL.$path."/edit' class='btn btn-primary btn-sm'>Edit</a> ";
		$rowHTML .= "<a href='#' id='$contentID' class='btn btn-primary btn-sm trashContent'>Trash</a>";
		$rowHTML .= "</td>";

		$rowHTML .= "</tr>";

		if(isset($row['subContent'])) {
			foreach($row['subContent'] as $row) {
				$rowHTML .= $this->_renderContentHtmlRecursive($row, $subLevel + 1, $parentLink);
			}
		}

		return $rowHTML;
	}

/**
 *	listTrash - Builds array of all trashed content
 *	@return array 
 *
 */
	public function listTrash() 
	{
		// Create empty array
		$returnArray = array();
		// Get content results from DB
		if($result = $this->db->select("SELECT contentID, type, parentPageID, parentGalID, author, dateTrashed FROM content WHERE trashed = '1' ORDER BY dateTrashed DESC"))
		{
			foreach($result as $row)
			{
				// Get parent name
				if($row['parentPageID'] > 0) {
					$result = $this->db->select("SELECT name FROM page WHERE pageID = ".$row['parentPageID']);
					$parent = $result[0]['name'];
				} else if($row['type'] == 'galImage') {
					$result = $this->db->select("SELECT name FROM gallery WHERE galleryID = ".$row['parentGalID']);
					$parent = $result[0]['name'];
				} else {
					$parent = '-';
				}
				// Add attributes common to all types
				$typeArray = array(
					'contentID' => $row['contentID'],
					'type' => $row['type'],
					'parentPageID' => $row['parentPageID'],
					'parent' => $parent,
					'dateTrashed' => $row['dateTrashed'],
					'author' => $row['author']
				);
				// Switch by type
				switch($row['type'])
				{
					case "page" :
						$result = $this->db->select("SELECT pageID, name FROM page WHERE contentID = '".$row['contentID']."'");

						$typeArray['pageID'] = $result[0]['pageID'];
						$typeArray['name'] = $result[0]['name'];
						break;
					case "video" :
						$result = $this->db->select("SELECT videoID, name FROM video WHERE contentID = '".$row['contentID']."'");

						$typeArray['videoID'] = $result[0]['videoID'];
						$typeArray['name'] = $result[0]['name'];
						break;
					case "gallery" :
						$result = $this->db->select("SELECT galleryID, name FROM gallery WHERE contentID = '".$row['contentID']."'");

						$typeArray['galleryID'] = $result[0]['galleryID'];
						$typeArray['name'] = $result[0]['name'];
						break;
					case "galImage" :
						$result = $this->db->select("SELECT name FROM galImage WHERE contentID = '".$row['contentID']."'");
						$typeArray['name'] = $result[0]['name'];
						break;
					case "text" :
						$result = $this->db->select("SELECT `textID`, `text` FROM `text` WHERE contentID = '".$row['contentID']."'");

						$typeArray['text'] = $result[0]['text'];
						break;
					case "singleImage" :
						$result = $this->db->select("SELECT singleImageID, name FROM singleImage WHERE contentID = '".$row['contentID']."'");

						$typeArray['singleImageID'] = $result[0]['singleImageID'];
						$typeArray['name'] = $result[0]['name'];
						break;
					case "navLink" :
						$result = $this->db->select("SELECT `name` FROM `navLink` WHERE contentID = '".$row['contentID']."'");

						$typeArray['name'] = $result[0]['name'];
						break;
				}

				$returnArray[] = $typeArray;
			}
		}
		return $returnArray;
	}

/**
 *	renderTrashRows - Renders html to go into <tbody> on page load and trash list reload
 *
 */
	public function renderTrashRows($trashList)
	{
		$trashRows = "";
		foreach($trashList as $row)
		{
			// Add vars common to all types
			$contentID = $row['contentID'];
			$parentPageID = $row['parentPageID'];
			$parent = $row['parent'];
			$date = date('Y/m/d', strtotime($row['dateTrashed']));
			$author = $row['author'];

			// Switch based on type
			switch($row['type'])
			{
				case "page" :
					$name = $row['name'];
					$nameTd = "<td class='listName'>$name</td>";
					$type = 'Page';
					$rowClass = 'contentListRow page visible';
				break;
				case "video" :
					$name = $row['name'];
					$nameTd = "<td class='listName'>$name</td>";
					$type = 'Video';
					$rowClass = 'contentListRow video visible';
				break;
				case "gallery" :
					$name = $row['name'];
					$nameTd = "<td class='listName'>$name</td>";
					$type = 'Gallery';
					$rowClass = 'contentListRow gallery visible';
				break;
				case 'galImage' :
					$nameTd = "<td>".$row['name']."</td>";
					$type = 'Gallery Image';
					$rowClass = 'contentListRow galImage visible';
				break;
				case "text" :
					$trimmedText = substr(htmlentities($row['text']), 0, 25).'...';
					$nameTd = "<td>$trimmedText</td>";
					$type = 'Text';
					$rowClass = 'contentListRow text visible';
				break;
				case "singleImage":
					$name = $row['name'];
					$nameTd = "<td>$name</td>";
					$type = 'Single Image';
					$rowClass = 'contentListRow singleImage visible';
				break;
				case "navLink" :
					$name = $row['name'];
					$nameTd = "<td>$name</td>";
					$type = 'Nav Link';
					$rowClass = 'contentListRow navLink visible';
				break;
			}

			// Echo HTML
			$trashRows .= "<tr id='$contentID' class='$rowClass'>";

			$trashRows .= "<td><input type='checkbox' class='trashCheck'></td>";

			$trashRows .= $nameTd;						
			$trashRows .= "<td>$type</td>";
			$trashRows .= "<td>$parent</td>";
			$trashRows .= "<td class='hidden-xs'>$date</td>";
			$trashRows .= "<td class='hidden-xs'>$author</td>";

			$trashRows .= "<td>";
			$trashRows .= "<a href='#' id='$contentID' class='restoreContent btn btn-primary btn-sm'>Restore</a> ";
			$trashRows .= "<a href='#' id='$contentID' class='deleteContent btn btn-danger btn-sm'>Delete</a>";
			$trashRows .= "</td>";

			$trashRows .= "</tr>";
		}
		if($trashRows == "") {
			$trashRows = "<tr class='placeholderRow'><td colspan='6'>(empty)</td></tr>";
		}
		return $trashRows;
	}
}