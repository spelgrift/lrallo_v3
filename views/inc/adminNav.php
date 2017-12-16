<?php
if(isset($this->pageAttr['pageID'])) {
	$dataID = "data-id='".$this->pageAttr['pageID']."'";
} else {
	$dataID = '';
}

?>
<nav id="adminNav" class="navbar navbar-inverse navbar-static-top" <? echo $dataID; ?>>
	<div class="container-fluid">

		<ul class="nav navbar-nav">
			<?php 
			if(isset($this->adminNav)) {
				require('views/inc/adminNavs/'.$this->adminNav.'.php');
			}
			?>
		</ul>
		<ul class="nav navbar-nav navbar-right">
		<? if(!isset($this->adminNav) || $this->adminNav != 'dashboard'): ?>
		
			<li><a href='<?php echo URL; ?>dashboard/'><i class="fa fa-sitemap"></i> Dashboard</a></li>
		<? endif; ?>
			<li><a href='<?php echo URL; ?>login/logout/'><i class="fa fa-sign-out"></i> Logout</a></li>
		</ul>
	</div>
</nav>