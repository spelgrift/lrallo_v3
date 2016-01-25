<div id="addImageModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Single Image</h4>
			</div>

			<div class="modal-body">
				<div class='singleImageDropzone dropzone'>
				</div>
			</div>

			<div class="modal-footer">
				<div id='imageMsg' class='pull-left'></div>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='submitNewImage' class="btn btn-primary" disabled>Upload Image</button>
			</div>
		</div>
	</div>
</div>

<div id='singleImgDZTemplate' class='DZtemplate'>
	<div class='dz-preview dz-file-preview row'>
		<div class='col-sm-3'>
			<div class='dz-thumbnail'>
				<img data-dz-thumbnail />
			</div>
		</div>
		<div class='dz-details col-sm-9'>
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
