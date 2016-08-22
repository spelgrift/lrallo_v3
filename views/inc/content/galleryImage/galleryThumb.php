<?php
$attr = '';
if($adminControls) {
	$attr = "id='listItem_$contentID'";
}

echo "<div class='col-xs-4 col-sm-2 thumbnail' $attr>";

$attr = "class='thumb img-responsive' data-slide='$position'";
if($adminControls) {
	require 'views/inc/content/galleryImage/galThumbControls.php';
	$attr = "class='thumb handle img-responsive'";
	echo "<a class='thumbLink' href='".URL.$this->pageAttr['url']."/slide/$position'>";
}

echo "<img $attr src='$thumb' title=\"$caption\">";
if($adminControls) { echo "</a>"; }
echo "</div>";