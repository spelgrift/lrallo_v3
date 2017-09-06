<div class='tabPanel' id='settings' data-type='page'>
	<form class='form-horizontal'>
		<div class='col-sm-12 text-center'>
			<h3>Settings</h3>
		</div>

		<? require 'views/inc/pageSettings/commonFields.php'; ?>

		<div class='form-group'>
			<div class='col-sm-6 col-sm-offset-3'>
				<a id='settingsSubmit' class='btn btn-primary' >Save Changes</a>
				<a id='settingsTrashPage' class='btn btn-danger'>Trash Page</a>
			</div>
		</div>
		<div id='settingsMsg' class='col-sm-6 col-sm-offset-3'></div>
	</form>
</div>