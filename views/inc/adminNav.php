<nav id="adminNav" class="navbar navbar-inverse">
	<div class="container-fluid">

		<ul class="nav navbar-nav navbar-right">
			<li class='dropdown'>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i></a>
				<ul class='dropdown-menu inverse-dropdown'>
					<li><a href='<?php echo URL; ?>dashboard/'><i class="fa fa-sitemap"></i> Dashboard</a></li>
					<?php if (Session::get('role') == 'owner'): ?>
						<li><a href='<?php echo URL; ?>user'><i class="fa fa-users"></i> Users</a></li>
					<?php endif; ?>
					<li><a href='<?php echo URL; ?>login/logout/'><i class="fa fa-sign-out"></i> Logout</a></li>
				</ul>
			</li>			
		</ul>

		<ul class="nav navbar-nav">
			<?php

				foreach($this->adminNav as $row)
				{
					if(isset($row['dropdown']))
					{
						$dataTab = isset($row['data-tab']) ? "data-tab='" . $row['data-tab'] ."'" : "";
						$class = isset($row['class']) ? "class='dropdown-toggle " . $row['class'] ."'" : "class='dropdown-toggle'";

						echo "<li class='dropdown'>";
						echo "<a href='#' $class $dataTab data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $row['name'] . "</a>";
						echo "<ul class='dropdown-menu inverse-dropdown'>";

						foreach($row['items'] as $row)
						{
							$name = $row['name'];
							$url = $row['url'];
							$id = isset($row['id']) ? "id='" . $row['id'] ."'" : "";
							$class = isset($row['class']) ? "class='" . $row['class'] ."'" : "";
							$dataID = isset($row['data-id']) ? "data-id='" . $row['data-id'] ."'" : "";

							echo "<li><a $id $class $dataID href='$url'>$name</a></li>";
						}

						echo "</ul></li>";
					}
					else
					{
						if(isset($row['url']))
						{
							$name = $row['name'];
							$url = $row['url'];
							$id = isset($row['id']) ? "id='" . $row['id'] ."'" : "";
							$class = isset($row['class']) ? "class='" . $row['class'] ."'" : "";
							$dataTab = isset($row['data-tab']) ? "data-tab='" . $row['data-tab'] ."'" : "";

							echo "<li><a $id $class $dataTab href='$url'>$name</a></li>";
						}
						else
						{
							$name = $row['name'];

							echo "<p class='navbar-text'><strong>$name</strong>" . $this->pageName . "</p>";
						}
						
					}
				}
			?>
		</ul>
	</div>
</nav>


