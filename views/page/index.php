<?php require 'views/inc/header.php'; ?>

<div class='row'>
<?php
// echo "<pre>";
// print_r($this->pageAttr);
// echo "</pre>";
foreach($this->pageContent as $item)
{
	$this->renderContent($item);
}
?>
</div>

<?php require 'views/inc/footer.php'; ?>