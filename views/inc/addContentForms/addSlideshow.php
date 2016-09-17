<div id="addSSModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Slideshow</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='ssSelect'>Select an existing gallery...</label>
						<select id='ssSelect' class='form-control'>
							<option value='0' selected='selected'>-</option>
							<?
							foreach($this->galleryArray as $gallery)
							{
								echo "<option value='".$gallery['galleryID']."'>".$gallery['name']."</option>";
							}
							?>
						</select>
						<hr>
					</div>
					<div class='form-group'>
						<label for='newSSName'>...or create a new one.</label>
						<input type='text' class='form-control' id='newSSName' placeholder='New Gallery'>
					</div>
				</form>
				<div class='addSSDropzone dropzone row'></div>

			</div>

			<div class="modal-footer">
				<div class='row'>
					<div class='col-sm-7'>
						<p id='ssMsg' class='pull-left text-danger text-left'></p>
						<div id='ssProgress' class='imageProcessing progress active' role='progressbar' aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
							<div class="progress-bar" style="width:0%;" data-dz-uploadprogress></div>
						</div>
						<div id='ssLoading' class='imageProcessing pull-left text-left'>
							<p><img class='loading' src='<? echo URL; ?>public/images/loading.gif'>Processing images...</p>
						</div>
					</div>

					<div class='col-sm-5'>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="button" id='submitNewSS' class="btn btn-primary" disabled>Add Slideshow</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

