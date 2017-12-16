<div class='tabPanel <? echo $homeSettingsClass; ?>' id='homeSettings' data-type='page'>
	<form class='form-horizontal'>
		<div class='col-sm-12 text-center'>
			<h3>Homepage Settings</h3>
		</div>

		<!-- Homepage Type -->
		<div class='form-group'>
			<label for='settingsHomeTypeRadio' class='col-sm-2 col-sm-offset-1 control-label'>Homepage Type</label>
			<div id='settingsHomeType' class='col-sm-6'>
				<?php
					$normalChecked = '';
					$linkChecked = '';
					if($this->pageAttr['homeSettings']['homeType'] == 'normal')
					{
						$normalChecked = 'checked';
						$selectState = 'disabled';
					} else {
						$linkChecked = 'checked';
						$selectState = '';
					}
				?>
				<div class="radio">
					<label>
					<input type='radio' name='settingsHomeType' value='normal' <? echo $normalChecked; ?>>
					Add content like a normal page.
					</label>
      		</div>
      		<div class="radio">
					<label>
					<input type='radio' name='settingsHomeType' value='link' <? echo $linkChecked; ?>>
					Use an existing page, gallery or video as the homepage
					</label>
      		</div>
			</div>
		</div>


		<!-- Target -->
		<div class='form-group'>
			<label for='settingsHomeTargetSelect' class='col-sm-2 col-sm-offset-1 control-label'>Use as Homepage</label>
			<div class='col-sm-6'>
				<select id='settingsHomeTargetSelect' class='form-control' <? echo $selectState; ?>>
				<?php
					// Null option
					if(is_null($this->pageAttr['homeSettings']['homeTarget'])) {
						$nullSelected = "selected='selected'";
					} else {
						$nullSelected = "";
					}
					echo "<option value='0' $nullSelected>-</option>";

					foreach ($this->homeTargetList as $row) {
						if($row['contentID'] == $this->pageAttr['homeSettings']['homeTarget']){
							$rowSelected = "selected='selected'";
						} else {
							$rowSelected = "";
						}
						echo "<option value='".$row['contentID']."' $rowSelected>".$row['name']."</option>";
					}
				?>
				</select>
			</div>
			<div class='col-sm-6 col-sm-offset-3'><hr></div>
		</div>


		<div class='form-group'>
			<div class='col-sm-6 col-sm-offset-3'>
				<a id='homeSettingsSubmit' class='btn btn-primary'>Save Changes</a>
			</div>
		</div>
		<div id='settingsMsg' class='col-sm-6 col-sm-offset-3'></div>
	</form>
</div>