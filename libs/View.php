<?php

class View {

	function __construct()
	{
		// Detect Device Size (sm, md, lg)
		$this->_device = $this->detectDevice();
	}

	public $adminNav = array();
	protected $_device = null;

	public function render($name, $headers = true){
		if($headers) {
			require 'views/inc/globalHeader.php';
		}
		require 'views/' . $name . '.php';
		if($headers) {
			require 'views/inc/globalFooter.php';
		}
		
	}

	public function renderContent($contentObject = array(), $adminControls = false)
	{
		$class = "contentItem ".$contentObject['bootstrap'];
		$id = "listItem_".$contentObject['contentID'];
		$contentID = $contentObject['contentID'];
		$type = $contentObject['type'];

		switch($type)
		{
			case 'page':
				$type = 'page';
				$ID = $contentObject['pageID'];
				$name = $contentObject['displayName'];
				$url = $contentObject['url'];
				$path = URL.$this->pageAttr['path']."/".$url;
				$cover = $contentObject['coverPath'];

				require 'views/inc/content/shortcut/shortcut.php';
				break;

			case 'gallery':
				$type = 'gallery';
				$ID = $contentObject['galleryID'];
				$name = $contentObject['name'];
				$url = $contentObject['url'];
				$path = URL.$this->pageAttr['path']."/".$url;
				$cover = $contentObject['coverPath'];

				require 'views/inc/content/shortcut/shortcut.php';
				break;

			case 'slideshow':
				$type = 'slideshow';
				$ID = $contentObject['slideshowID'];
				$autoplay = $contentObject['autoplay'];
				$animationType = $contentObject['animationType'];
				$animationSpeed = $contentObject['animationSpeed'];
				$slideDuration = $contentObject['slideDuration'];
				$galleryID = $contentObject['galleryID'];

				require 'views/inc/content/slideshow/slideshow.php';
				break;
			
			case 'video':
				$type = 'video';
				$ID = $contentObject['videoID'];
				$name = $contentObject['displayName'];
				$url = $contentObject['url'];
				$path = URL.$this->pageAttr['path']."/".$url;
				$cover = $contentObject['coverPath'];

				require 'views/inc/content/shortcut/shortcut.php';
				break;

			case 'text':
				$textID = $contentObject['textID'];
				$text = $contentObject['text'];

				require 'views/inc/content/text/text.php';

				break;

			case 'singleImage':
				$image = $contentObject[$this->_device.'Version'];

				require 'views/inc/content/singleImage/singleImage.php';
				
				break;

			case 'spacer':
				require 'views/inc/content/spacer/spacer.php';

				break;
		}
	}

	public function detectDevice(){
		require 'libs/Mobile_Detect.php';
		$detect = new Mobile_Detect;
		$device = 'lg';
		if($detect->isMobile()) {
			$device = 'sm';
			if($detect->isTablet()) {
				$device = 'sm';
			}
		}
		return $device;
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