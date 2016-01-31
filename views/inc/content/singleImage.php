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
	require 'views/inc/content/adminControls/singleImageControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>
	
<div class='content'>
<?php
echo "<img class='img-responsive visible-xs-block' src='".URL.$smVersion."'>";
echo "<img class='img-responsive hidden-xs' src='".URL.$lgVersion."'>"; 
?>
	
</div>
</div>