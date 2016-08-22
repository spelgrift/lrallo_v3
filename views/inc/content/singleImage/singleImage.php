<?php
if($adminControls){
	$attr = "class='$class editContent' id='$id'";
} else {
	$attr = "class='$class'";
}
?>

<div <?php echo $attr; ?>>
<?php 
if($adminControls) {
	require 'views/inc/content/singleImage/singleImageControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>
	
<div class='content'>
<?php
echo "<img class='img-responsive' src='".URL.$image."'>";
?>
	
</div>
</div>