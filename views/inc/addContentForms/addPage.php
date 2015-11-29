<div id="addPageModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Subpage</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='newPageName'>Page Name</label>
						<input type='text' class='form-control' id='newPageName' placeholder='Page Name'>
					</div>
				</form>
				<div id='pageMsg'></div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='submitNewPage' class="btn btn-primary">Add Page</button>
			</div>
		</div>
	</div>
</div>

