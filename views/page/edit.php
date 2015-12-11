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
<div class='row tabPanel active' id='contentArea'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
?>
</div>

<div class='row tabPanel' id='pageSettings'>
	<div class='col-sm-12'>
		<h3>Page Settings</h3>
	</div>
</div>
<?php require 'views/inc/footer.php'; ?>