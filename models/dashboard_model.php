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
		$type = $row['type'];
		$parentType = $row['parentType'];

		// Set name if there is one, if not (text), use the first few characters
		$name = (isset($row['name'])) ? $row['name'] : substr(htmlentities($row['text']), 0, 25).'...';
		// If it is a page/gal/video/post, make the name a link
		if($type == 'page' || $type == 'gallery' || $type == 'video' || $type == 'post') {
			$nameTd = "<td class='listName'><span class='listPad'>$pad</span><a href='".URL.$path."'>$name</a></td>";
		} else {
			$nameTd = "<td><span class='listPad'>$pad</span>$name</td>";
		}
		$typeDisplay = ($type == 'embeddedVideo') ? "Embedded Video" : ucfirst($type);
		$typeDisplay = ($type == 'singleImage') ? "Single Image" : $typeDisplay;
		// Adjust edit link if type is post or parent is a post
		$editLink = URL.$path."/edit";
		if($type == 'post' || $parentType == 'post'){
			$editLink = URL.BLOGURL.'/editpost/'.substr($path, 10);
		}
		// Set class
		$rowClass = "contentListRow $type";
		if($type == "page") $rowClass .= ' visible';
		$parentLink = "<a href='".URL.$path."'>$name</a>";

		// Echo HTML
		$rowHTML .= "<tr id='$contentID' class='$rowClass'>\n";

		$rowHTML .= $nameTd."\n";						
		$rowHTML .= "<td>$typeDisplay</td>\n";
		$rowHTML .= "<td>$parentName</td>\n";
		$rowHTML .= "<td class='hidden-xs'>$date</td>\n";
		$rowHTML .= "<td class='hidden-xs'>$author</td>\n";

		$rowHTML .= "<td>\n";
		$rowHTML .= "<a href='".URL.$path."' class='btn btn-primary btn-sm'>View</a> ";
		$rowHTML .= "<a href='$editLink' class='btn btn-primary btn-sm'>Edit</a> ";
		$rowHTML .= "<a href='#' id='$contentID' class='btn btn-danger btn-sm trashContent'>Trash</a>\n";
		$rowHTML .= "</td>\n";

		$rowHTML .= "</tr>\n";

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
		if($result = $this->db->select("SELECT contentID, type, parentPageID, parentGalID, parentPostID, author, dateTrashed FROM content WHERE trashed = '1' ORDER BY dateTrashed DESC"))
		{
			foreach($result as $row)
			{
				// Get parent name
				if($row['parentPageID'] > 0) {
					$parent = $this->db->selectSingle('page', 'name', "pageID = '".$row['parentPageID']."'");
				} else if($row['type'] == 'galImage') {
					$parent = $this->db->selectSingle('gallery', 'name', "galleryID = '".$row['parentGalID']."'");
				} else if($row['parentPostID'] > 0){
					$parent = $this->db->selectSingle('post', 'title', "postID = '".$row['parentPostID']."'");
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
				$thisType = $row['type'];
				switch($thisType)
				{
					case "page" :
					case "gallery" :
					case "galImage" :
					case "video" :
					case "singleImage" :
					case "navLink" :
						$query = "SELECT ".$thisType."ID, name FROM ".$thisType." WHERE contentID = :contentID";
						$result = $this->db->select($query, array(':contentID' => $row['contentID']));

						$typeArray[$thisType.'ID'] = $result[0][$thisType.'ID'];
						$typeArray['name'] = $result[0]['name'];
					break;
					case "embeddedVideo" :
					case "slideshow" :
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
						$result = $this->db->select("SELECT `textID`, `text` FROM `text` WHERE contentID = '".$row['contentID']."'");
						$typeArray['name'] = substr(htmlentities($result[0]['text']), 0, 25).'...';
					break;
					case "post" :
						$result = $this->db->select("SELECT postID, title FROM post WHERE contentID = '".$row['contentID']."'");
						$typeArray['name'] = $result[0]['title'];
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
			$type = $row['type'];
			if($type == "page" || $type == "gallery" || $type =="video") {
				$nameTd = "<td class='listName'>".$row['name']."</td>";
			} else {
				$nameTd = "<td>".$row['name']."</td>";
			}
			$typeDisplay = ($type == 'embeddedVideo') ? "Embedded Video" : ucfirst($type);
			$typeDisplay = ($type == 'singleImage') ? "Single Image" : $typeDisplay;
			$typeDisplay = ($type == 'galImage') ? "Gallery Image" : $typeDisplay;

			$rowClass = "contentListRow $type visible";

			// Return HTML
			$trashRows .= "<tr id='$contentID' class='$rowClass'>";

			$trashRows .= "<td><input type='checkbox' class='trashCheck'></td>";

			$trashRows .= $nameTd;						
			$trashRows .= "<td>$typeDisplay</td>";
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