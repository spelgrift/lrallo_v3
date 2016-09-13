<?php 
require 'views/inc/header.php'; 
$link = $this->pageAttr['link'];
$source = $this->pageAttr['source'];
// echo "<pre>";
// print_r($this->pageAttr);
// echo "</pre>";
?>

<form class='form-horizontal' id='settings' data-type='video'>
<!-- 	<div class='col-sm-6 col-sm-offset-3' id='previewEmbed'>
	<? // require 'views/inc/content/video/video.php'; ?>
	</div> -->

	<div class='col-sm-12 text-center'>
			<h3>Settings</h3>
		</div>

		<!-- Name -->
		<div class='form-group'>
			<label for='settingsNameInput' class='col-sm-2 col-sm-offset-1 control-label'>Name</label>
			<div class='col-sm-6'>
				<input id='settingsNameInput' type='text' class='form-control' placeholder='Page Name' value="<?php echo $this->pageAttr['name']; ?>">
			</div>
		</div>

		<!-- URL -->
		<div class='form-group'>
			<label for='settingsUrlInput' class='col-sm-2 col-sm-offset-1 control-label'>URL</label>
			<div class='col-sm-6'>
				<input id='settingsUrlInput' type='text' class='form-control' placeholder='Page URL' value="<?php echo $this->pageAttr['url']; ?>">
			</div>
		</div>

		<!-- Parent -->
		<div class='form-group'>
			<label for='settingsParentInput' class='col-sm-2 col-sm-offset-1 control-label'>Parent</label>
			<div class='col-sm-6'>
				<select id='settingsParentInput' class='form-control'>
				<?php
					if($this->pageAttr['parentPageID'] == 0) {
						echo "<option value='0' selected='selected'>-</option>";
					} else {
						echo "<option value='0'>-</option>";
					}
					$this->buildParentOptions($this->pageList, $this->pageAttr['parentPageID'], $this->pageAttr['pageID']);
				?>
				</select>
			</div>
		</div>

		<!-- Nav -->
		<div class='form-group'>
			<label for='settingsNavCheck' class='col-sm-2 col-sm-offset-1 control-label'>Nav</label>
			<div class='col-sm-6'>
				<div class="checkbox">
					<label>
						<?php
						if($this->pageAttr['nav'] == 1){
							echo "<input type='checkbox' id='settingsNavCheck' checked>";
						} else {
							echo "<input type='checkbox' id='settingsNavCheck'>";
						}

						?>
						Include in navigation
					</label>
      		</div>
			</div>
			<div class='col-sm-6 col-sm-offset-3'><hr></div>
		</div>

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

<? require 'views/inc/footer.php'; ?>