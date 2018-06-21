<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addGallery.php';
require 'views/inc/addContentForms/addSlideshow.php';
require 'views/inc/addContentForms/addEmbedVideo.php';
require 'views/inc/addContentForms/addImage.php';
require 'views/inc/addContentForms/addSpacer.php';
require 'views/inc/content/slideshow/slideshowSettings.php';
require 'views/inc/content/text/editHTML.php';
?>

<div id='editPostMsg' class='text-center'></div>

<div class='blogDate text-center'><? echo date('F j, Y', strtotime($this->postAttr['date'])); ?></div>
<div class='row'>
	<div class='col-sm-6 col-sm-offset-3 text-center'>
		<h2 class='blog-input' id='blogTitleInput'><? echo $this->postAttr['title']; ?></h2>
	</div>
</div>
<div class='row'>
	<div class='col-sm-6 col-sm-offset-3'>
		<div class='blog-input' id='blogBodyInput'>
			<? echo $this->postAttr['body']; ?>
		</div>
	</div>
</div>

<div class='row postContent' id='contentArea'>
<?php
foreach($this->postContent as $item)
{
	$this->renderContent($item, true);
}
if(count($this->postContent) == 0) {
	$class = 'contentPlaceholder';
} else {
	$class = 'contentPlaceholder hidden';
}
echo "<div class='$class'>Click \"Add Content\" to add some cool stuff to your post.</div>";
?>
</div>




<?php require 'views/inc/footer.php'; ?>