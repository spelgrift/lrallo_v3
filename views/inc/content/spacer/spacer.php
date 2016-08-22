<?php
if($adminControls){
	$attr = "class='$class editContent spacer' id='$id'";
} else {
	$attr = "class='$class spacer'";
}
?>

<div <?php echo $attr; ?>>
	
<?php 
if($adminControls) {
	require 'views/inc/content/spacer/spacerControls.php';
}
?>
</div>