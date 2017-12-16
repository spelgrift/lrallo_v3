<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addGallery.php';
require 'views/inc/addContentForms/addSlideshow.php';
require 'views/inc/addContentForms/addVideo.php';
require 'views/inc/addContentForms/addEmbedVideo.php';
require 'views/inc/addContentForms/addImage.php';
require 'views/inc/addContentForms/addSpacer.php';
require 'views/inc/content/adminControls/contentControls.php';
require 'views/inc/content/shortcut/shortcutSettings.php';
require 'views/inc/content/slideshow/slideshowSettings.php';
require 'views/inc/content/text/editHTML.php';

// If homepage, render custom html ('views/custom/home.php') before normal content (if it exists)
if($this->pageAttr['home']){
	$customFile = 'views/custom/home.php';
	if(file_exists($customFile)){
		require $customFile;
	}
}

// If Home-type is set to 'normal', show content area like normal. Otherwise hide it and show settings only.
$contentAreaClass = 'active';
$homeSettingsClass = '';
if($this->pageAttr['home'] && $this->pageAttr['homeSettings']['homeType'] == 'link') {
	$contentAreaClass = '';
	$homeSettingsClass = 'active';
}

// echo "<pre>";
// print_r($this->pageAttr);
// echo "</pre>";
?>

<div class='row tabPanel <? echo $contentAreaClass; ?>' id='contentArea'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
if(count($this->pageContent) == 0) {
	$class = 'contentPlaceholder';
} else {
	$class = 'contentPlaceholder hidden';
}
echo "<div class='$class'>Nothing here yet. Click \"Add Content\" to do just that.</div>";
?>
</div>

<?php
if(!$this->pageAttr['home']) {
	require 'views/inc/pageSettings/pageSettings.php';
} else {
	require 'views/inc/pageSettings/homeSettings.php';
}

require 'views/inc/footer.php';
?>