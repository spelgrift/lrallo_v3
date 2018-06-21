<?php 
require 'views/inc/header.php';

// Render custom html ('views/custom/[pageURL].php') before normal content (if it exists)
if($this->pageAttr['home']){
	$customFile = 'views/custom/home.php';
} else {
	$customFile = 'views/custom/'.$this->pageAttr['url'].'.php';
}
if(file_exists($customFile)){
	require $customFile;
}
?>



<div class='row'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item);
}
?>
</div>

<?php require 'views/inc/footer.php'; ?>