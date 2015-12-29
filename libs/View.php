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

			switch($contentObject['type'])
			{
				case 'text':

					$contentID = $contentObject['contentID'];
					$textID = $contentObject['textID'];
					$text = $contentObject['text'];

					require 'views/inc/content/text.php';

					break;
			}
		}

		
}

?>