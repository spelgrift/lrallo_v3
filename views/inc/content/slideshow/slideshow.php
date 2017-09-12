<?php
if($adminControls){
	$attr = "class='$class editContent' id='$id'";
} else {
	$attr = "class='$class'";
}
?>

<div <?php echo $attr; ?>>

<?php 
if($adminControls) {
	require 'views/inc/content/slideshow/slideshowControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>

<div class='content'>
<?
// Slideshow display settings
$class = "class='slideshow";
if($autoplay == 1) {
	$class .= ' sm-auto';
} else {
	$class .= '';
}
if($animationType == 'slide') {
	$class .= '';
} else if($animationType == 'fade') {
	$class .= ' sm-fade';
}
if($hideControls == 1) {
	$class .= ' sm-hide-controls';
} else {
	$class .= '';
}
$class .= " sm-aspect-$aspectRatio";
$class .= "'";
$slideshowAttr = $class . " data-sm-speed='$animationSpeed' data-sm-duration='$slideDuration' data-gal-id='$galleryID'";
?>

<div <? echo $slideshowAttr; ?>>
	<div class='slides'>
<?
// foreach($images as $img)
// {
// 	$image = URL.$img[$this->_device.'Version'];
// 	$position = $img['position'];
// 	$caption = $img['caption'];

// 	if($position == 0) {
// 		$class = 'slide active';
// 	} else {
// 		$class = 'slide';
// 	}

// 	echo "<div class='$class' data-order='$position'><img src='$image' title=\"$caption\"></div>";
// }
?>
	</div>
	<div class='arrow arrow-right'></div>
	<div class='arrow arrow-left'></div>
</div>

</div>
</div>