<?php
require '../config.php';

// header('Content-type: image/jpeg');

$image = new Imagick('../public/images/test.jpg');

$image->thumbnailImage(200, 0);

if($image->writeImage('../public/images/test_thumb.jpg'))
{
	echo "<img src='" . URL . "public/images/test_thumb.jpg' />";
}

?>