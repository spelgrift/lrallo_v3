<?php
if($adminControls){
	$attr = "class='$class' id='$id'";
} else {
	$attr = "class='$class'";
}
?>

<div <?php echo $attr; ?>>
<?php 
if($adminControls) {
	require 'views/inc/content/adminControls/textControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>
	
<div class='content'>
<?php echo $text; ?>
	
</div>
</div>