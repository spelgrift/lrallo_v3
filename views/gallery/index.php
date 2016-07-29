<?php 
// echo "<pre>";
// print_r($this->pageAttr);
// print_r($this->galImages);
// echo "</pre>";

require 'views/inc/header.php'; 

// Slideshow display settings
$class = "class='slideshow";
if($this->pageAttr['autoSlideshow'] == 1) {
	$class .= ' sm-auto';
} else {
	$class .= '';
}
if($this->pageAttr['animationType'] == 'slide') {
	$class .= '';
} else if($this->pageAttr['animationType'] == 'fade') {
	$class .= ' sm-fade';
}
$class .= "'";
$slideshowAttr = $class . " data-sm-speed='" . $this->pageAttr['animationSpeed'] . "' data-sm-duration='" . $this->pageAttr['slideDuration'] . "'";

// Set active slide
$activeSlide = 0;
?>

<div id='viewer' class='row'>
	<div class='col-xs-12'>
		<div <? echo $slideshowAttr; ?>>
			<div class='slides'>
			<?php
				foreach($this->galImages as $img)
				{
					$image = URL.$img[$this->_device.'Version'];
					$position = $img['position'];

					if($position == $activeSlide) {
						$class = 'slide active';
					} else {
						$class = 'slide';
					}

					echo "<div class='$class' data-order='$position'><img src='$image'></div>";
				}
			?>
			</div>
			<div class='arrow arrow-right'></div>
			<div class='arrow arrow-left'></div>
		</div>

</div>

<?php require 'views/inc/footer.php'; ?>