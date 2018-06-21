<?php

class Rallo_In_Paris_Model extends Model {

	function __construct(){parent::__construct();}

	public function newPost() {
		$title = 'Untitled';
		$body = '<p>Write something here...</p>';

		// Content DB entry
		$this->db->insert('content', array(
			'type' => 'post',
			'hidden' => 1,
			'author' => $_SESSION['login'],
			'url' => date('ymdhis')."-untitled"
		));
		$contentID = $this->db->lastInsertId();

		// Post DB entry
		$this->db->insert('post', array(
			'contentID' => $contentID,
			'title' => $title,
			'body' => $body
		));
		$postID = $this->db->lastInsertId();
		$date = $this->db->selectSingle('content', 'date', "contentID = $contentID");

		return array(
			'title' => $title,
			'body' => $body,
			'contentID' => $contentID,
			'postID' => $postID,
			'date' => $date
		);
	}

	public function getPosts($withHidden = false, $limit = false, $lastID = 999999)
	{
		if($withHidden) {
			$cols = "c.contentID, c.date, c.url, c.hidden, p.postID, p.title, p.body";
			$where = "c.trashed = 0 and c.type = 'post'";
		} else {
			$cols = "c.contentID, c.date, c.url, p.postID, p.title, p.body";
			$where = "c.trashed = 0 and c.hidden = 0 and c.type = 'post'";
		}

		if($limit) {
			$where .= " and p.postID < $lastID";
			$qLimit = " LIMIT $limit";
		} else {
			$qLimit = "";
		}
		$query = "SELECT $cols
			FROM content AS c
			LEFT JOIN post AS p ON c.contentID = p.contentID
			WHERE $where
			ORDER BY c.date DESC
			$qLimit";
		if($result = $this->db->select($query)){
			return $result;
		} else {
			return array();
		}
	}

	public function getPost($url, $withHidden = false)
	{
		if($withHidden) {
			$cols = "c.contentID, c.date, c.url, c.hidden, p.postID, p.title, p.body";
			$where = "c.trashed = 0 and c.type = 'post'";
		} else {
			$cols = "c.contentID, c.date, c.url, p.postID, p.title, p.body";
			$where = "c.trashed = 0 and c.hidden = 0 and c.type = 'post'";
		}
		$query = "SELECT $cols
			FROM content AS c
			LEFT JOIN post AS p ON c.contentID = p.contentID
			WHERE $where and c.url = :url";
		if($result = $this->db->select($query, array(':url' => $url))){
			return $result[0];
		} else {
			return false;
		}
	}

	public function togglePublic($contentID)
	{
		$hidden = $this->db->selectSingle('content', 'hidden', "contentID = $contentID");
		if($hidden === false) {
			$this->_returnError('Error');
			return false;
		}
		if($hidden == 0){
			$newStatus = 1;
			$return = "Publish Post";
		} else {
			$newStatus = 0;
			$return = "Hide Post";
		}

		$this->db->update('content', array('hidden' => $newStatus), "contentID = $contentID");
		echo $return;
	}


}