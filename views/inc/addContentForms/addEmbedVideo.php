<div id="addEmbedVideoModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Embed Video</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='evSelect'>Select an existing video...</label>
						<select id='evSelect' class='form-control'>
							<option value='0' selected='selected'>-</option>
							<?
							foreach($this->videoArray as $video)
							{
								echo "<option value='".$video['videoID']."'>".$video['name']."</option>";
							}
							?>
						</select>
						<hr>
					</div>
					<div class='form-group'>
						<label for='newEVName'>...or create a new one.</label>
						<input type='text' class='form-control' id='newEVName' placeholder='Video Name'>
						<p class='error-block' id='evNameMsg'></p>
					</div>
					<div class='form-group'>
						<label for='newEVLink'>YouTube or Vimeo URL</label>
						<input type='text' class='form-control' id='newEVLink' placeholder='YouTube or Vimeo URL'>
						<p class='error-block' id='evLinkMsg'></p>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='submitNewEV' class="btn btn-primary" disabled>Add Video</button>
			</div>
		</div>
	</div>
</div>