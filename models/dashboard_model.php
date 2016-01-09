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
}
?>