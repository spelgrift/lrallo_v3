<?php 
require 'views/inc/header.php'; 
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addText.php';
?>
<div class='row'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
?>
</div>
<?php require 'views/inc/footer.php'; ?>