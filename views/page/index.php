<?php 
require 'views/inc/header.php';

// If homepage, render custom html ('views/custom/home.php') before normal content (if it exists)
if($this->pageAttr['home']){
	$customFile = 'views/custom/home.php';
	if(file_exists($customFile)){
		require $customFile;
	}
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