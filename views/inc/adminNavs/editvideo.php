<?php
	$url = URL.$this->pageAttr['path']; 
?>

<p id='adminNavName' class='navbar-text'>Edit: <? echo $this->pageAttr['name']; ?></p>
<li>
	<a class='adminTab active' href='#'><i class='fa fa-fw fa-wrench'></i> Settings</a>
</li>
<li>
	<a id='viewTab' href='<? echo $url; ?>'><i class='fa fa-fw fa-desktop'></i> View Video</a>
</li>