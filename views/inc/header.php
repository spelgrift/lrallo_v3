<nav class="navbar navbar-default navbar-static-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNav">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL; ?>">Imageman</a>
		</div>
		<div class="collapse navbar-collapse" id="mainNav">
			<ul class="nav navbar-nav navbar-right">
				<?php

				foreach($this->nav as $row)
				{
					$name = $row['name'];
					$url = $row['url'];

					echo "<li><a href='" . URL . $url . "'>$name</a></li>";
				}

				?>
			</ul>
		</div>
	</div>
</nav>

<div class="container">

<?php 
	if(Session::get('loggedIn') == true)
	{
		require 'views/inc/adminNav.php';
	} else {
		require 'views/inc/loginForm.php';
	}
?>





