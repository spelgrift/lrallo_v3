<div id="addNavLinkModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Navigation Link</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='newNavName'>Displayed Text</label>
						<input type='text' class='form-control' id='newNavName' placeholder='Nav Link'>
					</div>
				</form>
				<form>
					<div class='form-group'>
						<label for='newNavName'>Destination URL</label>
						<input type='text' class='form-control' id='newNavUrl' placeholder='http://...'>
					</div>
				</form>
				<div id='navLinkMsg'></div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='submitNewNavLink' class="btn btn-primary">Add Link</button>
			</div>
		</div>
	</div>
</div>