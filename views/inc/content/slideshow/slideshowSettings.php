<div id="slideshowSettingsModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Slideshow Settings</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for="ssAutoPlayCheck">Auto-Play</label>
						<div class="checkbox">
							<label>
								<input id='ssAutoPlayCheck' type="checkbox"> Advance to the next slide after given <strong>duration</strong> in milliseconds
							</label>
						</div>

					</div>
					<div class='form-group'>
						<label for='ssDurationInput'>Duration</label>
						<input id='ssDurationInput' type='text' class='form-control' placeholder='Slide Duration (milliseconds)'>
						<p class='error-block' id='ssDurationMsg'></p>
					</div>
					<div class='form-group'>
						<label for='ssAnimationSelect'>Animation Type</label>
						<select id='ssAnimationSelect' class='form-control'>
							<option value='slide'>slide</option>
							<option value='fade'>fade</option>
						</select>
					</div>
					<div class='form-group'>
						<label for='ssSpeedInput'>Animation Speed (milliseconds)</label>
						<input id='ssSpeedInput' type='text' class='form-control' placeholder='Animation Speed (milliseconds)'>
						<p class='error-block' id='ssSpeedMsg'></p>
					</div>



				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='saveSSSettings' data-type='' class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>