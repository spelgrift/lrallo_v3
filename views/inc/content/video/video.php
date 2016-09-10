<?
if($source == 'vimeo') {
	$embedSrc = "https://player.vimeo.com/video/".$link;
} else if($source == 'youtube') {
	$embedSrc = "https://www.youtube.com/embed/".$link;
}
?>

<div class='embed-responsive embed-responsive-16by9'>
	<iframe class='embed-responsive-item' src="<? echo $embedSrc; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</div>