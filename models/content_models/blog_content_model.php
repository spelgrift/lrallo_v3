<?php

class Blog_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	public function publishPost($contentID)
	{
		// Get POST
		$title = $_POST['title'];
		$body = $_POST['body'];

		// Make sure title is not empty
		if($title === ""){
			$this->_returnError('Title cannot be blank.');
			return false;
		}

		// Get timestamp
		$timestamp = $this->db->selectSingle('content', 'date', "contentID = $contentID");

		// Make url (timestamp + clean title)
		$url = date('ymdhis', strtotime($timestamp))."-".$this->_makeURL($title);

		// Update databases
		$this->db->update('content', array(
			'url' => $url,
			'hidden' => 0
		), "`contentID` = ".$contentID);
		$this->db->update('post', array(
			'title' => $title,
			'body' => $body
		), "`contentID` = ".$contentID);

		// Return url
		$windowPath = DEVPATH.BLOGURL."/editpost/".$url;
		$viewPath = URL.BLOGURL."/post/".$url;
		echo json_encode(array(
			'error' => false,
			'url' => $url,
			'title' => $title,
			'pageTitle' => $title." | ".BRAND,
			'windowPath' => $windowPath,
			'viewPath' => $viewPath
		));

	}

	public function getPostContent($posts) {
		foreach ($posts as $key => $post) {
			$posts[$key]['content'] = $this->getPageContent($post['postID'], 'post');
		}
		return $posts;
	}

	public function makeBlogImgName($postID)
	{
		$title = $this->db->selectSingle('post', 'title', "postID = $postID");
		return $this->_makeURL($title);
	}


}