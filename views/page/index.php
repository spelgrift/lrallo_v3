<?php require 'views/inc/header.php'; ?>

<div class='row'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item);
}
?>
</div>

<?php require 'views/inc/footer.php'; ?>