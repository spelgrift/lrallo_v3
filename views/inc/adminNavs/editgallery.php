<?php
	$url = URL.$this->pageAttr['path']; 
?>

<p id='adminNavName' class='navbar-text'>Edit: <? echo $this->pageAttr['name']; ?></p>
<li>
	<a class=' adminTab active' data-tab='editSequence' href='#'><i class='fa fa-fw fa-arrows-alt'></i> Edit Sequence</a>
</li>
<li>
	<a id='addImages' class='adminTab addImages' data-tab='editSequence' href='#'><i class='fa fa-fw fa-plus'></i> Add Images</a>
</li>
<li>
	<a class='adminTab' data-tab='settings' href='#'><i class='fa fa-fw fa-wrench'></i> Settings</a>
</li>
<li>
	<a id='viewTab' href='<? echo $url; ?>'><i class='fa fa-fw fa-desktop'></i> View Gallery</a>
</li>