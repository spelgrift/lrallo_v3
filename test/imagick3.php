<?php
require '../config.php';
require '../libs/Image.php';

$srcImg = '../public/images/test3.jpg';
$destImg = '../public/images/test3_thumb.jpg';
Image::makeThumbnail($srcImg, $destImg);

echo "<img src='$destImg' />";

?>