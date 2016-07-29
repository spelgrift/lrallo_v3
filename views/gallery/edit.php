<?php 
require 'views/inc/header.php';
require 'views/inc/addContentForms/addGalImages.php';

// Thumbnail template
$contentID = '{{contentID}}';
$imgID = '{{imgID}}';
$thumb = '{{thumb}}';
echo "<script type='text/template' id='thumbTemplate'>";
require 'views/inc/content/adminControls/galImageThumb.php';
echo "</script>";
?>


<?
// Image Settings Modal
require 'views/inc/content/adminControls/galImageSettings.php';
?>



<?
// THUMBS
?>

<div class='row tabPanel active' id='editSequence'>
<?php
// Display sortable thumbnails
foreach($this->galImages as $image)
{
	$contentID = $image['contentID'];
	$imgID = $image['galImageID'];
	$thumb = URL.$image['thumb'];
	$caption = $image['caption'];

	require 'views/inc/content/adminControls/galImageThumb.php';
}
?>
</div>
<?
// GALLERY SETTINGS
?>
<div class='tabPanel' id='galSettings'>
	<form class='form-horizontal'>
		<div class='col-sm-12 text-center'>
			<h3>Settings</h3>
		</div>

		<div class='form-group'>
			<label for='settingsNameInput' class='col-sm-2 col-sm-offset-1 control-label'>Name</label>
			<div class='col-sm-6'>
				<input id='settingsNameInput' type='text' class='form-control' placeholder='Page Name' value="<?php echo $this->pageAttr['name']; ?>">
			</div>
		</div>

		<div class='form-group'>
			<label for='settingsUrlInput' class='col-sm-2 col-sm-offset-1 control-label'>URL</label>
			<div class='col-sm-6'>
				<input id='settingsUrlInput' type='text' class='form-control' placeholder='Page URL' value="<?php echo $this->pageAttr['url']; ?>">
			</div>
		</div>

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
		</div>

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