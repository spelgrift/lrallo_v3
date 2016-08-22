<div id="shortcutSettingsModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Shortcut Settings</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='inputShortcutName'>Display Name</label>
						<input type='text' class='form-control' id='inputShortcutName' placeholder='Display Name'>
						<p class='error-block' id='shortcutNameMsg'></p>
					</div>

				</form>
				<div class='updateCoverDropzone dropzone row'></div>
				<p class='text-danger' id='shortcutCoverMsg'></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='saveShortcutSettings' data-type='' class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<div id='shortcutCoverDZTemplate' class='DZtemplate'>
	<div class='dz-preview dz-file-preview row'>
		<div class='col-xs-4 col-sm-6'>
			<div class='dz-thumbnail'>
				<img class='img-responsive' data-dz-thumbnail />
			</div>
		</div>
		<div class='dz-details col-xs-8 col-sm-6'>
			<span class='dz-filename' data-dz-name></span>
			<span class='dz-filesize' data-dz-size></span>
			<strong class='dz-error text-danger' data-dz-errormessage></strong>
			<div class="progress" role="progressbar">
         	<div class="progress-bar" style="width:0%;" data-dz-uploadprogress></div>
         </div>
			<a class='btn btn-sm btn-danger removeImage' href='#' data-dz-remove>Remove Image</a>
		</div>		
	</div>
</div>