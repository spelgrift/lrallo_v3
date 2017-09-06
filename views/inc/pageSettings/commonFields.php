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
			if($this->pageAttr['parentPageID'] == 0 && $this->pageAttr['frontpage'] == 1) {
				echo "<option value='home' selected='selected'>Homepage</option>";
				echo "<option value='0'>-</option>";
			} else if($this->pageAttr['parentPageID'] == 0 && $this->pageAttr['frontpage'] == 0) {
				echo "<option value='home'>Homepage</option>";
				echo "<option value='0' selected='selected'>-</option>";
			} else {
				echo "<option value='home'>Homepage</option>";
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
</div>

<!-- Hidden -->
<div class='form-group'>
	<label for='settingsHiddenCheck' class='col-sm-2 col-sm-offset-1 control-label'>Privacy</label>
	<div class='col-sm-6'>
		<div class="checkbox">
			<label>
				<?php
				if($this->pageAttr['hidden'] == 1){
					echo "<input type='checkbox' id='settingsHiddenCheck' checked>";
				} else {
					echo "<input type='checkbox' id='settingsHiddenCheck'>";
				}

				?>
				Hide this page from view on pages and in the navigation (those with the URL can still see it)
			</label>
		</div>
	</div>
	<div class='col-sm-6 col-sm-offset-3'><hr></div>
</div>