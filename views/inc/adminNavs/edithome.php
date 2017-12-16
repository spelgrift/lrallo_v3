<?php
$layoutLiClass = '';
$addLiClass = '';
$settingsTabClass = '';

if($this->pageAttr['home'] && $this->pageAttr['homeSettings']['homeType'] == 'link') {
	$layoutLiClass = 'hidden';
	$addLiClass = 'hidden';
	$settingsTabClass = 'active';
}
?>
<p id='adminNavName' class='navbar-text'>Edit Homepage</p>
<li id='layoutLi' class='<? echo $layoutLiClass; ?>'>
	<a class='adminTab active' data-tab='contentArea' href='#'><i class='fa fa-fw fa-arrows-alt'></i> Edit Layout</a>
</li>
<li id='addLi' class='dropdown <? echo $addLiClass; ?>'>
	<a href='#' class='dropdown-toggle adminTab' data-tab='contentArea' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'><i class='fa fa-fw fa-plus'></i> Add Content</a>
	<ul class='dropdown-menu inverse-dropdown'>
		<li><a class='addTab' data-id='page' href='#'>Page</a></li>
		<li><a class='addTab' data-id='gallery' href='#'>Gallery Page</a></li>
		<li><a class='addTab' data-id='video' href='#'>Video Page</a></li>
		<li><a class='addTab' data-id='text' href='#'>Text/HTML</a></li>
		<li><a class='addTab' data-id='singleImage' href='#'>Single Image</a></li>
		<li><a class='addTab' data-id='slideshow' href='#'>Slideshow</a></li>
		<li><a class='addTab' data-id='embedVideo' href='#'>Embedded Video</a></li>
		<li><a class='addTab' data-id='shortcut' href='#'>Shortcut</a></li>
		<li><a class='addTab' data-id='spacer' href='#'>Spacer</a></li>
	</ul>
</li>
<li id='settingsLi'>
	<a class='adminTab <? echo $settingsTabClass; ?>' data-tab='homeSettings' href='#'><i class='fa fa-fw fa-wrench'></i> Settings</a>
</li>
<li>
	<a id='viewTab' href='<? echo URL; ?>'><i class='fa fa-fw fa-desktop'></i> View Homepage</a>
</li>


