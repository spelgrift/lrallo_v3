<div id='header'>
	Header <br>
	<a href='<?php echo URL; ?>'>Home </a>

	<?php if (Session::get('loggedIn') == true): ?>
		<a href='<?php echo URL; ?>dashboard/'>Dashboard</a>

		<?php if (Session::get('role') == 'owner'): ?>
			<a href='<?php echo URL; ?>user'>Users</a>
		<?php endif; ?>
		<a href='<?php echo URL; ?>login/logout/'>Logout</a>
	<?php else: ?>
		<a href='<?php echo URL; ?>login'>Login</a>
	<?php endif; ?>
</div>

<div id="content">