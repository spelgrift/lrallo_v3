<?
if($source == 'vimeo') {
	$embedSrc = "https://player.vimeo.com/video/".$link."?byline=0&amp;title=0&amp;portrait=0&amp;color=91250f";
} else if($source == 'youtube') {
	$embedSrc = "https://www.youtube.com/embed/".$link;
}
?>

<div class='embed-responsive embed-responsive-16by9'>
	<iframe class='embed-responsive-item' src="<? echo $embedSrc; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</div>