<?php 
require 'views/inc/header.php'; 

$link = $this->pageAttr['link'];
$source = $this->pageAttr['source'];
?>

<div class='row'>
	<div class='col-xs-12'>
	<? require 'views/inc/content/video/video.php'; ?>
	</div>
</div>

<?php require 'views/inc/footer.php'; ?>