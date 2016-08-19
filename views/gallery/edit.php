<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addGalImages.php';

// Thumbnail template
$contentID = '{{contentID}}';
$imgID = '{{imgID}}';
$thumb = '{{thumb}}';
$position = '{{position}}';
$caption = "";
$adminControls = true;
echo "<script type='text/template' id='thumbTemplate'>";
require 'views/inc/content/galleryThumb.php';
echo "</script>";

// Image Settings Modal
require 'views/inc/content/adminControls/galImageSettings.php';
?>

<!-- EDIT SEQUENCE -->
<div class='row tabPanel active' id='editSequence' data-coverID='<? echo $this->pageAttr['coverID']; ?>'>
<?php
// Display sortable thumbnails
foreach($this->galImages as $image)
{
	$contentID = $image['contentID'];
	$imgID = $image['galImageID'];
	$thumb = URL.$image['thumb'];
	$position = $image['position'];
	$caption = $image['caption'];
	$adminControls = true;

	require 'views/inc/content/galleryThumb.php';
}
?>
</div>

<!-- SETTINGS -->
<div class='tabPanel' id='galSettings'>
	<form class='form-horizontal'>
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
			<label for='settings' class='col-sm-2 col-sm-offset-1 control-label'>Duration</label>
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
<?php require 'views/inc/footer.php'; ?>