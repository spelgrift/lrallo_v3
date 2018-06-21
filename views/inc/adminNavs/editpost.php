<p id='adminNavName' class='navbar-text'><? echo ($this->newPost) ? 'New Post' : 'Edit Post'; ?></p>
<li class='dropdown'>
	<a href='#' class='dropdown-toggle adminTab' data-tab='contentArea' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'><i class='fa fa-fw fa-plus'></i> Add Content</a>
	<ul class='dropdown-menu inverse-dropdown'>
		<li><a class='addTab' data-id='singleImage' href='#'>Image</a></li>
		<li><a class='addTab' data-id='text' href='#'>Text/HTML</a></li>
		<li><a class='addTab' data-id='slideshow' href='#'>Slideshow</a></li>
		<li><a class='addTab' data-id='embedVideo' href='#'>Embedded Video</a></li>
		<li><a class='addTab' data-id='spacer' href='#'>Spacer</a></li>
	</ul>
</li>

<li>
	<a id='publishTab' data-id='<? echo $this->postAttr['contentID']; ?>' href='#'><i class='fa fa-fw fa-floppy-o'></i> Publish Changes</a>
</li>

<li>
	<a id='trashTab' href='#'>
		<i class='fa fa-fw fa-trash'></i> Trash Post
	</a>
</li>

<? if(!$this->newPost): ?>
<li>
	<a id='viewTab' href='<? echo URL.BLOGURL."/post/".$this->postAttr['url']; ?>'><i class='fa fa-fw fa-desktop'></i> View Post</a>
</li>
<? endif; ?>