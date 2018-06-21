<?php
if(Session::get('loggedIn') == true)
{
	require 'views/inc/adminNav.php';
} else {
	require 'views/inc/loginForm.php';
}
?>
<nav id="mainNavBar" class="navbar navbar-default navbar-static-top">
	<div class="container">
		<div class="navbar-header">
			<a href='#' class="navbar-toggle" data-toggle="collapse" data-target="#mainNav"><i class="fa fa-bars fa-lg"></i></a>
			<a class="navbar-brand" href="<?php echo URL; ?>"><?php echo BRAND; ?></a>
		</div>
		<div class="collapse navbar-collapse navbar-left" id="mainNav">
			<ul class="nav navbar-nav">
				<? require('views/inc/nav.php'); ?>
			</ul>
		</div>
	</div>
</nav>

<div class="container">

<?php 
	// if(Session::get('loggedIn') == true)
	// {
	// 	require 'views/inc/adminNav.php';
	// } else {
	// 	require 'views/inc/loginForm.php';
	// }


	if(isset($this->templates)){
		foreach($this->templates as $template)
		{
			$templateID = $template['templateID'];
			echo "<div class='DZtemplate' id='$templateID'>";
			$this->renderContent($template, true);
			echo "</div>";
		}
	}

?>