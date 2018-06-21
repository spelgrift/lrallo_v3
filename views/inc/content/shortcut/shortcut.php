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
	require 'views/inc/content/shortcut/shortcutControls.php';
	require 'views/inc/content/adminControls/resizeControls.php';
}
?>
	
<div class='content'>
	<div class='shortcut'>
		<?
		if($cover != "") 
		{
			echo "<a class='coverLink' href='$path'><img src='".URL.$cover."' class='shortcutCover img-responsive'></a>";
		}
		echo "<a class='shortcutTitleOverlay' href='$path'><span class='blockName'>$name</span></a>"; 
		?>
	</div>
	
</div>
</div>