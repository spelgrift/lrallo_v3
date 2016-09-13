<div id="addVideoModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Video</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='newVideoName'>Name</label>
						<input type='text' class='form-control' id='newVideoName' placeholder='Video Name'>
						<p class='error-block' id='videoNameMsg'></p>
					</div>
					<div class='form-group'>
						<label for='newVideoLink'>YouTube or Vimeo URL</label>
						<input type='text' class='form-control' id='newVideoLink' placeholder='YouTube or Vimeo URL'>
						<p class='error-block' id='videoLinkMsg'></p>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='submitNewVideo' class="btn btn-primary">Add Video</button>
			</div>
		</div>
	</div>
</div>