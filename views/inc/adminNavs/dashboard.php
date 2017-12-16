<p class='navbar-text'>Dashboard</p>
<li>
	<a  class='adminTab active' data-tab='contentList' href='#'>
		<i class='fa fa-fw fa-list'></i> List Content
	</a>
</li>
<li class='dropdown'>
	<a href='#' class='dropdown-toggle adminTab' data-tab='contentList' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>
		<i class='fa fa-fw fa-plus'></i> New
	</a>
	<ul class='dropdown-menu inverse-dropdown'>
		<li><a class='addTab' data-id='page' href='#'>Page</a></li>
		<li><a class='addTab' data-id='gallery' href='#'>Image Gallery</a></li>
		<li><a class='addTab' data-id='video' href='#'>Video</a></li>
		<li><a class='addTab' data-id='navLink' href='#'>Navigation Link</a></li>
	</ul>
</li>
<li>
	<a  class='adminTab' data-tab='trash' href='#'>
		<i class='fa fa-fw fa-trash'></i> Trash
	</a>
</li>
<li>
	<a href='<?php echo URL; ?>page/edithome/'>
		<i class='fa fa-fw fa-home'></i> Edit Homepage
	</a>
</li>
<li>
	<a href='<?php echo URL; ?>blog/manage/'>
		<i class='fa fa-pencil-square-o'></i> Manage Blog
	</a>
</li>
<?php if (Session::get('role') == 'owner'): ?>
	<li><a href='<?php echo URL; ?>user'><i class="fa fa-users"></i> Users</a></li>
<?php endif; ?>
