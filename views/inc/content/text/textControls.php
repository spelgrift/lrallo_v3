<ul class='contentControlMenu' <?php echo "id='$contentID' data-id='$textID'"; ?>>
	<li><a class='editHTML' href='#'><i class="fa fa-code"></i></a></li>
	<li><a class='resizeContent' href='#'><i class="fa fa-arrows-h"></i></a></li>
<? if(isset($this->pageAttr)): ?>
	<li><a class='editContentSettings' href='#'><i class="fa fa-cog"></i></a></li>
<? endif; ?>
	<li><a class='trashContent' href='#'><i class='fa fa-fw fa-trash'></i></a></li>
	<li class='handle'><i class="fa fa-plus"></i></li>
</ul>