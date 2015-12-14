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
			$bootstrapWidthClasses = "col-xs-".$contentObject['xsWidth']." col-sm-".$contentObject['smWidth']." col-md-".$contentObject['mdWidth']." col-lg-".$contentObject['lgWidth'];
			$bootstrapOffsetClasses = "col-xs-offset-".$contentObject['xsOffset']." col-sm-offset-".$contentObject['smOffset']." col-md-offset-".$contentObject['mdOffset']." col-lg-offset-".$contentObject['lgOffset'];
			$class = "contentItem ".$bootstrapWidthClasses." ".$bootstrapOffsetClasses;

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