<?php 
// echo "<pre>";
// print_r($this->pageList);
// echo "</pre>";
require 'views/inc/header.php';
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addText.php';
require 'views/inc/addContentForms/addSpacer.php';
?>

<div class='row tabPanel active' id='contentArea'>
<?php
foreach($this->pageContent as $item)
{
	$this->renderContent($item, true);
}
?>
</div>

<div class='tabPanel' id='pageSettings'>
	<form class='form-horizontal'>
		<div class='col-sm-12 text-center'>
			<h3>Settings</h3>
		</div>

		<div class='form-group'>
			<label for='settings-nameInput' class='col-sm-2 col-sm-offset-1 control-label'>Name</label>
			<div class='col-sm-6'>
				<input id='settings-nameInput' type='text' class='form-control' placeholder='Page Name' value="<?php echo $this->pageAttr['name']; ?>">
			</div>
		</div>

		<div class='form-group'>
			<label for='settings-urlInput' class='col-sm-2 col-sm-offset-1 control-label'>URL</label>
			<div class='col-sm-6'>
				<input id='settings-urlInput' type='text' class='form-control' placeholder='Page URL' value="<?php echo $this->pageAttr['url']; ?>">
			</div>
		</div>

		<div class='form-group'>
			<label for='settings-parentInput' class='col-sm-2 col-sm-offset-1 control-label'>Parent</label>
			<div class='col-sm-6'>
				<select id='settings-parentInput' class='form-control'>
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
			<label for='settings-NavCheck' class='col-sm-2 col-sm-offset-1 control-label'>Nav</label>
			<div class='col-sm-6'>
				<div class="checkbox">
					<label>
						<?php

						?>
						Include in navigation
					</label>
      		</div>
			</div>
		</div>

		<div class='form-group'>
			<label for='settings-NavInput' class='col-sm-2 col-sm-offset-1 control-label'>Nav</label>
			<div class='col-sm-6'>
				<div class="checkbox">
					<label>
						<input type="checkbox"> Include in navigation
					</label>
      		</div>
			</div>
		</div>

	</form>
</div>
<?php require 'views/inc/footer.php'; ?>