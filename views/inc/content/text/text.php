<?php
if($adminControls){
	$attr = "class='$class editContent' id='$id'";
} else {
	$attr = "class='$class'";
}

echo "<div ".$attr.">";

if($adminControls) {
	require 'views/inc/content/text/textControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>
	
<div class='content'>
<?php echo $text; ?>
	
</div>
</div>