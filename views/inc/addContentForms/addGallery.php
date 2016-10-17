<div id="addGalleryModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Image Gallery</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='newGalName'>Gallery Name</label>
						<input type='text' class='form-control' id='newGalName' placeholder='New Gallery'>
					</div>
				</form>
				<div class='addGalleryDropzone dropzone row'></div>
			</div>

			<div class="modal-footer">
				<div class='row'>
					<div class='col-sm-8'>
						<p id='galleryMsg' class='pull-left text-danger text-left'></p>
						<div id='galleryProgress' class='progress active' role='progressbar' aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
							<div class="progress-bar" style="width:0%;" data-dz-uploadprogress></div>
						</div>
						<div id='galleryLoading' class='pull-left text-left'>
							<p><img class='loading' src='<? echo URL; ?>public/images/loading.gif'>Processing images...</p>
						</div>
					</div>

					<div class='col-sm-4'>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="button" id='submitNewGal' class="btn btn-primary" disabled>Add Gallery</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id='galDZTemplate' class='DZtemplate'>
	<div class='dz-preview dz-file-preview gallery-preview col-xs-6 col-sm-3 text-center'>
		<div class='dz-gal-thumb'>
			<img data-dz-thumbnail />
			<a class='dz-gal-remove' href='#' data-dz-remove>Remove</a>
		</div>
		<div class='dz-gal-error' data-dz-errormessage></div>	
	</div>
</div>
