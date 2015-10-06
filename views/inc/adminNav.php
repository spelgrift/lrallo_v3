<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<ul class="nav navbar-nav navbar-right">
			<li><a href='<?php echo URL; ?>dashboard/'>Dashboard</a></li>
			<?php if (Session::get('role') == 'owner'): ?>
				<li><a href='<?php echo URL; ?>user'>Users</a></li>
			<?php endif; ?>
			<li><a href='<?php echo URL; ?>login/logout/'>Logout</a></li>
		</ul>
		<ul class="nav navbar-nav">
			<?php

				foreach($this->adminNav as $row)
				{
					$name = $row['name'];
					$url = $row['url'];

					echo "<li><a href='" . URL . $url . "'>$name</a></li>";
				}

				?>
		</ul>
	</div>
</nav>


