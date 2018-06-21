<?php
	$newURL = URL.BLOGURL."/newpost";
	$viewURL = URL.BLOGURL; 
?>

<p id='adminNavName' class='navbar-text'>Manage Blog</p>
<li>
	<a class='adminTab active' data-tab='contentArea' href='#'><i class='fa fa-fw fa-list'></i> All Posts</a>
</li>
<li>
	<a id='newTab' href='<? echo $newURL; ?>'><i class='fa fa-fw fa-plus'></i> New Post</a>
</li>
<li>
	<a id='viewTab' href='<? echo $viewURL; ?>'><i class='fa fa-fw fa-desktop'></i> View Blog</a>
</li>


