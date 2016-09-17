<?php 
// echo "<pre>";
// print_r($this->pageContent);
// echo "</pre>";
require 'views/inc/header.php';
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addGallery.php';
require 'views/inc/addContentForms/addSlideshow.php';
require 'views/inc/addContentForms/addVideo.php';
require 'views/inc/addContentForms/addText.php';
require 'views/inc/addContentForms/addImage.php';
require 'views/inc/addContentForms/addSpacer.php';
require 'views/inc/content/shortcut/shortcutSettings.php';
?>

<div class='row tabPanel active' id='contentArea'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
?>
</div>

<div class='tabPanel' id='settings' data-type='page'>
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
				<a id='settingsTrashPage' class='btn btn-danger'>Trash Page</a>
			</div>
		</div>
		<div id='settingsMsg' class='col-sm-6 col-sm-offset-3'></div>
	</form>
</div>
<?php require 'views/inc/footer.php'; ?>