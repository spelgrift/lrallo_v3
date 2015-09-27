<?php require 'views/inc/header.php'; ?>


<div class='container'>
<div class='row'>
	<div class='col-md-3'>

		<h3>Add User</h3>
		<form role='form' method='post' action='<?php echo URL; ?>user/create'>
			<div class='form-group'>
				<label>Login</label><input type='text' class='form-control' name='login' />
			</div>
			<div class='form-group'>
				<label>Password</label><input type='text' class='form-control' name='password' />	
			</div>
			<div class='form-group'>
				<label>Role</label>
				<select class='form-control' name='role'>
					<option value='default'>Default</option>
					<option value='admin'>Admin</option>
				</select>
			</div>
			<input type='submit' />
		</form>

	</div>

	<div class='col-md-6 col-md-offset-1'>

		<h3>User List</h3>
		<table class='table'>
		<?php
			foreach($this->userList as $key => $value) {
				echo '<tr>';
				echo '<td>' . $value['userid'] . '</td>';
				echo '<td>' . $value['login'] . '</td>';
				echo '<td>' . $value['role'] . '</td>';
				echo '<td><a href="'.URL.'user/edit/'.$value['userid'].'">Edit</a></td>';
				echo '<td><a href="'.URL.'user/delete/'.$value['userid'].'">Delete</a></td>';
				echo '<tr>';
			}

			// print_r($this->userList);
		?>
		</table>
	</div>
</div>
</div>





<?php require 'views/inc/footer.php'; ?>