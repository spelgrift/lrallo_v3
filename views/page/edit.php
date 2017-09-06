<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addGallery.php';
require 'views/inc/addContentForms/addSlideshow.php';
require 'views/inc/addContentForms/addVideo.php';
require 'views/inc/addContentForms/addEmbedVideo.php';
require 'views/inc/addContentForms/addText.php';
require 'views/inc/addContentForms/addImage.php';
require 'views/inc/addContentForms/addSpacer.php';
require 'views/inc/content/shortcut/shortcutSettings.php';
require 'views/inc/content/slideshow/slideshowSettings.php';

// If homepage, render custom html ('views/custom/home.php') before normal content (if it exists)
if($this->pageAttr['home']){
	$customFile = 'views/custom/home.php';
	if(file_exists($customFile)){
		require $customFile;
	}
}
?>

<div class='row tabPanel active' id='contentArea'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
?>
</div>

<?php
if(!$this->pageAttr['home']) {
	require 'views/inc/pageSettings/pageSettings.php';
}

require 'views/inc/footer.php';
?>