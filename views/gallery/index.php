<?php 
// echo "<pre>";
// print_r($this->pageAttr);
// print_r($this->galImages);
// print_r($this);
// echo "</pre>";

require 'views/inc/header.php'; 

// Slideshow display settings
$class = "class='slideshow";
if($this->pageAttr['autoSlideshow'] == 1) {
	$class .= ' sm-auto';
} else {
	$class .= '';
}
if($this->pageAttr['animationType'] == 'slide') {
	$class .= '';
} else if($this->pageAttr['animationType'] == 'fade') {
	$class .= ' sm-fade';
}
$class .= "'";
$slideshowAttr = $class . " data-sm-speed='" . $this->pageAttr['animationSpeed'] . "' data-sm-duration='" . $this->pageAttr['slideDuration'] . "'";

// Set active slide
$activeSlide = 0;
if(isset($this->slide) && $this->slide < count($this->galImages)) {
	$activeSlide = $this->slide;
}
?>

<!-- SLIDESHOW VIEW -->
<div id='viewer' class='row tabPanel active'>
	<div class='col-xs-12'>
		<div <? echo $slideshowAttr; ?>>
			<div class='slides'>
			<?php
				foreach($this->galImages as $img)
				{
					$image = URL.$img[$this->_device.'Version'];
					$position = $img['position'];
					$caption = $img['caption'];

					if($position == $activeSlide) {
						$class = 'slide active';
					} else {
						$class = 'slide';
					}

					echo "<div class='$class' data-order='$position'><img src='$image' title=\"$caption\"></div>";
				}
			?>
			</div>
			<div class='arrow arrow-right'></div>
			<div class='arrow arrow-left'></div>
			<div class='slideControlBar'>
				<a href='#' class='sm-button' id='showThumbs'>
					<? echo file_get_contents("public/images/icon_thumbs.svg"); ?>		
				</a>
				<a href='#' class='sm-button' id='showCollage'>
					<? echo file_get_contents("public/images/icon_collage.svg"); ?>
				</a>
			</div>
		</div>
	</div>
	<div class='col-xs-12' id='caption'>
		<p></p>
	</div>
</div>

<!-- THUMBNAIL VIEW -->
<div id='thumbnails' class='row tabPanel'>
<?
// Display thumbnails
foreach($this->galImages as $image)
{
	$contentID = $image['contentID'];
	$imgID = $image['galImageID'];
	$thumb = URL.$image['thumb'];
	$position = $image['position'];
	$caption = $image['caption'];
	$adminControls = false;

	require 'views/inc/content/galleryThumb.php';
}	
?>
</div>

<!-- COLLAGE VIEW -->
<div id='collage' class='row tabPanel flex-images'>
<?
// Flex Images
foreach($this->galImages as $image)
{
	$position = $image['position'];
	$path = URL.$image[$this->_device.'Version'];
	$caption = $image['caption'];
	$w = $image['width'];
	$h = $image['height'];

	echo "<div class='item collageImage thumb' data-slide='$position' data-w='$w' data-h='$h'><div class='hover' title=\"$caption\"></div><img src='$path'></div>";
}
?>
</div>

<?php require 'views/inc/footer.php'; ?>