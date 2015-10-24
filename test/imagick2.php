<?php
require '../config.php';
require '../libs/Image.php';

$srcImg = '../public/images/test2.jpg';
$destImgCatrom = '../public/images/test2_RESIZED_Catrom.jpg';
$destImgLanczos = '../public/images/test2_RESIZED_Lanczos.jpg';

$time1 = microtime(true);

Image::fitImage($srcImg, $destImgCatrom, 1280, 1280);
$time2 = microtime(true);

echo "<strong>FILTER_CATROM</strong> Imageprocessing took " . ($time2-$time1) . " seconds.<br>";

echo "<img src='$destImgCatrom' /><br>";

$time1 = microtime(true);

Image::fitImageL($srcImg, $destImgLanczos, 1280, 1280);
$time2 = microtime(true);

echo "<strong>FILTER_LANCZOS</strong> Imageprocessing took " . ($time2-$time1) . " seconds.<br>";

echo "<img src='$destImgLanczos' />";


?>