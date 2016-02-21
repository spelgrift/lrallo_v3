<?php
require '../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Imagewall Test</title>
<link href="<?php echo URL; ?>public/css/styles.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src='jquery.flex-images.js'></script>
<script type='text/javascript'>

$(function() {

	$('.flex-images').flexImages({rowHeight: 300});

});

</script>


</head>
<body>



<div class='container'>

	<div class='flex-images'>

<?php

	$filenames = array(
		'bleep_000_sm.jpg',
		'bleep_001_sm.jpg',
		'bleep_002_sm.jpg',
		'bleep_003_sm.jpg',
		'bleep_004_sm.jpg',
		'slideshow_test_000_sm.jpg',
		'slideshow_test_001_sm.jpg',
		'slideshow_test_002_sm.jpg',
		'bleep_005_sm.jpg',
		'bleep_006_sm.jpg',
		'bleep_007_sm.jpg'
	);

	$relPath = "../uploads/";
	$browserPath = URL.'uploads/';

	foreach($filenames as $img) {
		// get width and height
		list($w, $h) = getimagesize($relPath.$img);
		echo "<div class='item' data-w='".$w."' data-h='".$h."'><img src='".$browserPath.$img."'></div>";
	}

?>

	</div>

</div>



</body>
</html>