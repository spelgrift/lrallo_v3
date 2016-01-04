<?php

class View {

	function __construct(){}

	public $adminNav = array();

	public function render($name){
		require 'views/inc/globalHeader.php';
		require 'views/' . $name . '.php';
		require 'views/inc/globalFooter.php';
	}

	public function renderContent($contentObject = array(), $adminControls = false)
	{
		$class = "contentItem ".$contentObject['bootstrap'];
		$id = "listItem_".$contentObject['contentID'];
		$contentID = $contentObject['contentID'];

		switch($contentObject['type'])
		{
			case 'text':
				$textID = $contentObject['textID'];
				$text = $contentObject['text'];

				require 'views/inc/content/text.php';

				break;

			case 'spacer':
				require 'views/inc/content/spacer.php';

				break;
		}
	}

	public function buildParentOptions($pageList, $thisParent, $thisID, $subLevel = 0)
	{
		$pad = "";
		if($subLevel == 1) {
			$pad = "&ensp;&rsaquo; ";
		} else if($subLevel > 1) {
			$pad = str_repeat("&ensp; ", ($subLevel - 1))."&ensp;&rsaquo; ";
		}

		foreach($pageList as $row)
		{
			if($row['pageID'] == $thisParent) {
				echo "<option value='".$row['pageID']."' selected>".$pad.$row['name']."</option>";
				if(!empty($row['subPages'])) {
					$this->buildParentOptions($row['subPages'], $thisParent, $thisID, $subLevel + 1);
				}
			} else if($row['pageID'] != $thisID) {
				echo "<option value='".$row['pageID']."'>".$pad.$row['name']."</option>";
				if(!empty($row['subPages'])) {
					$this->buildParentOptions($row['subPages'], $thisParent, $thisID, $subLevel + 1);
				}
			}
		}
	}

		
}

?>