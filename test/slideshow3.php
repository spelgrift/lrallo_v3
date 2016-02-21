<?php
require '../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Slideshow Test</title>
<link href="<?php echo URL; ?>public/css/styles.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo URL; ?>public/js/encapsulatedPlugin.js"></script>
<script src="<?php echo URL; ?>public/js/slideMan.js"></script>
<script src="slideshowTest3.js"></script>

</head>
<body>

<div class='container'>

	<div class='row'>

		<div class='col-xs-12 col-sm-6'>
			<div id='test1' class='slideshow sm-auto' data-sm-speed='500' data-sm-duration='2000'>
				<div class='slides'>
					<div class='slide active' data-order='0'>
						<img src="<?php echo URL; ?>uploads/slideshow_test_000_sm.jpg">
					</div>
					<div class='slide' data-order='1'>
						<img src="<?php echo URL; ?>uploads/slideshow_test_001_sm.jpg">
					</div>
					<div class='slide' data-order='2'>
						<img src="<?php echo URL; ?>uploads/slideshow_test_002_sm.jpg">
					</div>
				</div>
				<div class='arrow arrow-right'></div>
				<div class='arrow arrow-left'></div>
			</div>
		</div>
		
		<div class='col-xs-12 col-sm-6'>
			<div id='test2' class='slideshow sm-auto sm-fade' data-sm-speed='400' data-sm-duration='3500'>
				<div class='slides'>
					<div class='slide active' data-order='0'>
						<img src="<?php echo URL; ?>uploads/slideshow_test_000_sm.jpg">
					</div>
					<div class='slide' data-order='1'>
						<img src="<?php echo URL; ?>uploads/slideshow_test_001_sm.jpg">
					</div>
					<div class='slide' data-order='2'>
						<img src="<?php echo URL; ?>uploads/slideshow_test_002_sm.jpg">
					</div>
				</div>
				<div class='arrow arrow-right'></div>
				<div class='arrow arrow-left'></div>
			</div>
		</div>

	</div>
	<a href='#' class='btn btn-primary' id='goToSlide'>Go to slide</a>
	<input type='text' id='getSlideID'>

</div>



</body>
</html>