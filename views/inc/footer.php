<?php

if(Session::get('loggedIn') == true)
{
	$loginHtml = "<a href='" . URL . "login/logout/'>Logout</a>";
}
else
{
	$loginHtml = "<a id='login' href='" . URL . "login'>Login</a>";
}

?>
</div> <!-- End of page container div -->

<footer class="text-center">
	<div class="footer-above">
		<div class="container">
			<div class="row">
			</div>			
		</div>
	</div>
	<div class="footer-below">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					&copy; Footer | <?php echo $loginHtml; ?>
				</div>
			</div>
		</div>
	</div>
</footer>

