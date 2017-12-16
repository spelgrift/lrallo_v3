<?php
if(Session::get('loggedIn') == true)
{
	require 'views/inc/adminNav.php';
} else {
	require 'views/inc/loginForm.php';
}
?>
<nav class="navbar navbar-default navbar-static-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNav">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL; ?>"><?php echo BRAND; ?></a>
		</div>
		<div class="collapse navbar-collapse" id="mainNav">
			<ul class="nav navbar-nav navbar-right">
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