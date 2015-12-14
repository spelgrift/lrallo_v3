<div class='<?php echo $class; ?>'>
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