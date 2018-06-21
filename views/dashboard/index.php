<?php 
// echo "<pre>";
// print_r($this->trashList);
// echo "</pre>";
require 'views/inc/header.php'; 
require 'views/inc/addContentForms/addPage.php';
require 'views/inc/addContentForms/addGallery.php';
require 'views/inc/addContentForms/addVideo.php';
require 'views/inc/addContentForms/addNavLink.php';
?>

<!-- List Item Templates -->
<script type='text/template' id='pageListTemplate'>
	<tr id='{{contentID}}' class='contentListRow page visible'>
		<td class='listName'><a href='{{path}}'>{{name}}</a></td>
		<td>{{type}}</td>
		<td>{{parent}}</td>
		<td class='hidden-xs'>{{date}}</td>
		<td class='hidden-xs'>{{author}}</td>
		<td>
			<a href='{{path}}' class='btn btn-primary btn-sm'>View</a>
			<a href='{{path}}/edit' class='btn btn-primary btn-sm'>Edit</a>
			<a href='#' id='{{contentID}}' class='btn btn-primary btn-sm trashContent'>Trash</a>
		</td>
	</tr>
</script>

<!-- Edit NavLink Modal -->
<div id="editNavLinkModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Navigation Link</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class='form-group'>
						<label for='editNavName'>Displayed Text</label>
						<input type='text' class='form-control' id='editNavName' placeholder='Nav Link'>
					</div>
				</form>
				<form>
					<div class='form-group'>
						<label for='editNavUrl'>Destination URL</label>
						<input type='text' class='form-control' id='editNavUrl' placeholder='http://...'>
					</div>
				</form>
				<div id='editNavLinkMsg'></div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id='deleteNavLink' class="btn btn-danger">Delete Link</button>
				<button type="button" id='submitEditNavLink' class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<!-- LIST CONTENT -->
<div class='row tabPanel active' id='contentList'>
	<form class='form-inline listControls'>
		<div class='form-group'>
			<label>Filter list by type: </label>
			<select class='form-control' id='filterContentList'>
				<option value='all'>All Content</option>
				<option value='page' selected>Pages</option>
				<option value='gallery' >Galleries</option>
				<option value='slideshow' >Slideshows</option>
				<option value='video'>Videos</option>
				<option value='embeddedVideo'>Embedded Videos</option>
				<option value='text'>Text</option>
				<option value='singleImage'>Images</option>
				<option value='post'>Blog Posts</option>
			</select>
		</div>
	</form>
	<div class='col-sm-12 col-lg-10 table-responsive'>
		<table class='table table-hover'>
			<thead>
				<tr>
					<td>Name</td>
					<td>Type</td>
					<td>Parent</td>
					<td class='hidden-xs'>Date Created</td>
					<td class='hidden-xs'>Author</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<?php	echo $this->contentRows;?>
			</tbody>
		</table>
	</div>
</div>

<!-- TRASH -->
<div class='row tabPanel' id='trash'>
	<form class='form-inline listControls'>
		
		<div class='form-group'>
			<label>Filter list by type: </label>
			<select class='form-control' id='filterTrashList'>
				<option value='all' selected>All Content</option>
				<option value='page'>Pages</option>
				<option value='gallery'>Galleries</option>
				<option value='galImage'>Gallery Images</option>
				<option value='slideshow' >Slideshows</option>
				<option vlaue='video'>Videos</option>
				<option value='text'>Text</option>
				<option value='singleImage'>Images</option>
				<option value='navLink'>Nav Links</option>
			</select>
		</div>
		<div class='form-group'>
			<a class='btn btn-default deleteSelected' href='#'>Delete Selected</a>
			<a class='btn btn-default restoreSelected' href='#'>Restore Selected</a>
			<a class='btn btn-default emptyTrash' href='#'>Empty Trash</a>
		</div>
		
	</form>
	<div class='col-sm-12 col-lg-10 table-responsive'>
		<table class='table table-hover'>
			<thead>
				<tr>
					<td><input type='checkbox' id='trashCheckAll'></td>
					<td>Name</td>
					<td>Type</td>
					<td>Parent</td>
					<td class='hidden-xs'>Date Trashed</td>
					<td class='hidden-xs'>Author</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<?php echo $this->trashRows; ?>
			</tbody>
		</table>
	</div>
</div>

<?php require 'views/inc/footer.php'; ?>