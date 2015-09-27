<?php require 'views/inc/header.php'; ?>

<?php 
// print_r($this->user); 
?>

<div class='container'>
<div class='row'>
	<div class='col-md-3'>

		<h3>Edit User</h3>
		<form role='form' method='post' action="<?php echo URL; ?>user/editSave/<?php echo $this->user['userid']; ?>">
			<div class='form-group'>
				<label>Login</label><input type='text' class='form-control' name='login' value="<?php echo $this->user['login']; ?>"/>
			</div>
			<div class='form-group'>
				<label>New Password</label><input type='text' class='form-control' name='password' />	
			</div>
			<div class='form-group'>
				<label>Role</label>
				<select class='form-control' name='role'>
					<option value='default' <?php if($this->user['role'] == 'default') echo 'selected'; ?>>Default</option>
					<option value='admin' <?php if($this->user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
				</select>
			</div>
			<input type='submit' />
		</form>

	</div>
</div>
</div>





<?php require 'views/inc/footer.php'; ?>