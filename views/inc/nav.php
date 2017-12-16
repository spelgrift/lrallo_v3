<?php
// Build html for nav <li>'s from array
$nav = "";
foreach($this->nav as $row)
{
	$name = $row['name'];
	$path = URL . $row['path'];
	$contentID = $row['contentID'];
	$class = '';
	$dataID = '';

	if($row['type'] == 'navLink') {
		$path = $row['path'];
		$class = "class='navLink'";
		$dataID = "data-id='$contentID'";
	}

	$nav .= "<li $class $dataID id='listItem_$contentID'><a $class href='" . $path . "'>$name</a></li>\n";
}
echo $nav;
?>