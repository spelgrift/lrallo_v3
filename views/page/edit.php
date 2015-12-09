<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addText.php';
?>

<!-- <pre>
<?php 
	// print_r($this->pageContent); 
	// print_r($this->templates);
?>
</pre> -->
<div class='row' id='contentArea'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
?>
</div>
<?php require 'views/inc/footer.php'; ?>