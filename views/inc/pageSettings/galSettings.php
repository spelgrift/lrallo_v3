<div class='tabPanel' id='settings' data-type='gallery'>
	<form class='form-horizontal'>
		<div class='col-sm-12 text-center'>
			<h3>Settings</h3>
		</div>

		<? require 'views/inc/pageSettings/commonFields.php'; ?>

		<!-- Auto-Play -->
		<div class='form-group'>
			<label for='settingsAutoplayCheck' class='col-sm-2 col-sm-offset-1 control-label'>Auto-Play</label>
			<div class='col-sm-6'>
				<div class="checkbox">
					<label>
						<?php
						if($this->pageAttr['autoplay'] == 1){
							echo "<input type='checkbox' id='settingsAutoplayCheck' checked>";
						} else {
							echo "<input type='checkbox' id='settingsAutoplayCheck'>";
						}
						?>
						Advance to the next slide after given <strong>duration</strong> in milliseconds
					</label>
      		</div>
			</div>
		</div>

		<!-- Duration -->
		<div class='form-group'>
			<label for='settingsDurationInput' class='col-sm-2 col-sm-offset-1 control-label'>Duration</label>
			<div class='col-sm-6'>
				<input id='settingsDurationInput' type='text' class='form-control' placeholder='Slide Duration (milliseconds)' value="<?php echo $this->pageAttr['slideDuration']; ?>">
			</div>
		</div>

		<!-- Animation -->
		<div class='form-group'>
			<label for='settingsAnimationSelect' class='col-sm-2 col-sm-offset-1 control-label'>Animation</label>
			<div class='col-sm-6'>
				<select id='settingsAnimationSelect' class='form-control'>
				<?php
					if($this->pageAttr['animationType'] == 'slide') {
						echo "<option value='slide' selected='selected'>slide</option>";
						echo "<option value='fade'>fade</option>";
					} else {
						echo "<option value='slide'>slide</option>";
						echo "<option value='fade' selected='selected'>fade</option>";
					}
				?>
				</select>
			</div>
		</div>

		<!-- Default Display -->
		<div class='form-group'>
			<label for='settingsDisplayRadio' class='col-sm-2 col-sm-offset-1 control-label'>Display</label>
			<div id='settingsDisplayRadio' class='col-sm-6'>
				<?php
					$viewerChecked = '';
					$thumbsChecked = '';
					$collageChecked = '';
					switch($this->pageAttr['defaultDisplay'])
					{
						case 'viewer' :
							$viewerChecked = 'checked';
							break;
						case 'thumbs' :
							$thumbsChecked = 'checked';
							break;
						case 'collage' :
							$collageChecked = 'checked';
							break;
					}
				?>
				<p class='form-control-static'>Choose what displays when a visitor first views this gallery</p>
				<div class="radio">
					<label>
					<input type='radio' name='settingsDisplayRadio' value='viewer' <? echo $viewerChecked; ?>>
					Slideshow
					</label>
      		</div>
      		<div class="radio">
					<label>
					<input type='radio' name='settingsDisplayRadio' value='thumbs' <? echo $thumbsChecked; ?>>
					Thumbnails
					</label>
      		</div>
      		<div class="radio">
					<label>
					<input type='radio' name='settingsDisplayRadio' value='collage' <? echo $collageChecked; ?>>
					Image Collage
					</label>
      		</div>
			</div>
			<div class='col-sm-6 col-sm-offset-3'><hr></div>
		</div>

		<!-- SUBMIT -->
		<div class='form-group'>
			<div class='col-sm-6 col-sm-offset-3'>
				<a id='settingsSubmit' class='btn btn-primary' >Save Changes</a>
				<a id='settingsTrashPage' class='btn btn-danger'>Trash Gallery</a>
			</div>
		</div>
		<div id='settingsMsg' class='col-sm-6 col-sm-offset-3'></div>
	</form>
</div>