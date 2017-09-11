<div id="contentSettingsModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Content Settings</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for="contentParentSelect">Move to page</label>
						<select id='contentParentSelect' class='form-control'>
						<?php
							if($this->pageAttr['pageID'] == 0) {
								echo "<option value='home' selected='selected'>Homepage</option>";
							} else {
								echo "<option value='home'>Homepage</option>";
							}
							$this->buildParentOptions($this->pageList, $this->pageAttr['pageID'], $this->pageAttr['pageID']);
						?>
						</select>
						<p class='error-block' id='contentParentMsg'></p>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='saveContentSettings' data-type='' class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>