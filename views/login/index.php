<?php 
require 'views/inc/header.php'; 

$error = "";
if($this->error) {
	$error = "Incorrect username or password.";
}
?>
<div class='row'>
	<div class='col-sm-4 col-sm-offset-4 text-center'>
		<h3>Login</h3>
	</div>
	<div class='col-sm-4 col-sm-offset-4'>

		<form action='<? echo URL; ?>login/runstatic' method='post'>
			<div class="form-group">
				<label for="userInput">Username</label>
				<input type="text" class="form-control" id="userInput" name="login" placeholder="Username" autofocus>
			</div>
			<div class="form-group">
				<label for="passwordInput">Password</label>
				<input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password">
			</div>
			<button type="submit" class="btn btn-primary">Login</button>
			<p class='text-danger pull-right'><? echo $error; ?></p>
		</form>
	</div>
</div>
<?php require 'views/inc/footer.php'; ?>