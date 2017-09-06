<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addGalImages.php';

// Thumbnail template
$contentID = '{{contentID}}';
$imgID = '{{imgID}}';
$thumb = '{{thumb}}';
$position = '{{position}}';
$caption = "";
$adminControls = true;
echo "<script type='text/template' id='thumbTemplate'>";
require 'views/inc/content/galleryImage/galleryThumb.php';
echo "</script>";

// Image Settings Modal
require 'views/inc/content/galleryImage/galImageSettings.php';
?>

<!-- EDIT SEQUENCE -->
<div class='row tabPanel active' id='editSequence' data-coverID='<? echo $this->pageAttr['coverID']; ?>'>
<?php
// Display sortable thumbnails
foreach($this->galImages as $image)
{
	$contentID = $image['contentID'];
	$imgID = $image['galImageID'];
	$thumb = URL.$image['thumb'];
	$position = $image['position'];
	$caption = $image['caption'];
	$adminControls = true;

	require 'views/inc/content/galleryImage/galleryThumb.php';
}
?>
</div>

<?php
// Settings
require 'views/inc/pageSettings/galSettings.php';

require 'views/inc/footer.php'; 
?>