<form class='form-horizontal' id='settings' data-type='video'>
	<div class='col-sm-12 text-center'>
		<h3>Settings</h3>
	</div>

	<? require 'views/inc/pageSettings/commonFields.php'; ?>

	<!-- Link -->
	<div class='form-group'>
		<label for='settingsLinkInput' class='col-sm-2 col-sm-offset-1 control-label'>Source Link</label>
		<div class='col-sm-6'>
			<input id='settingsLinkInput' type='text' class='form-control' placeholder='Vimeo or Youtube Link' value="<?php echo $this->pageAttr['postedLink']; ?>">
		</div>
	</div>

	<!-- Description -->
	<div class='form-group'>
		<label for='settingsDescInput' class='col-sm-2 col-sm-offset-1 control-label'>Description</label>
		<div class='col-sm-6'>
			<textarea id='settingsDescInput' class='form-control' rows='3'placeholder='Video Description'><?php echo $this->pageAttr['description']; ?></textarea>
		</div>	
		<div class='col-sm-6 col-sm-offset-3'><hr></div>
	</div>

	<!-- SUBMIT -->
	<div class='form-group'>
		<div class='col-sm-6 col-sm-offset-3'>
			<a id='settingsSubmit' class='btn btn-primary' >Save Changes</a>
			<a id='settingsTrashPage' class='btn btn-danger'>Trash Video</a>
		</div>
	</div>
	<div id='settingsMsg' class='col-sm-6 col-sm-offset-3'></div>
</form>