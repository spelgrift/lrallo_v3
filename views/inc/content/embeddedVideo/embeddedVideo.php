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
	require 'views/inc/content/embeddedVideo/embeddedVideoControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>
	
	<div class='content'>
	<?
	if($source == 'vimeo') {
		$embedSrc = "https://player.vimeo.com/video/".$link;
	} else if($source == 'youtube') {
		$embedSrc = "https://www.youtube.com/embed/".$link;
	} else {
		$embedSrc = "";
	}
	?>
	<div class='embed-responsive embed-responsive-16by9'>
		<iframe class='embed-responsive-item' src="<? echo $embedSrc; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
	</div>
	
	</div>
</div>