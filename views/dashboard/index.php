<?php 
// echo "<pre>";
// print_r($this->trashList);
// echo "</pre>";
require 'views/inc/header.php'; 
require 'views/inc/addContentForms/addPage.php';
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

<!-- LIST CONTENT -->
<div class='row tabPanel active' id='contentList'>
	<form class='form-inline listControls'>
		<div class='form-group'>
			<label>Filter list by type: </label>
			<select class='form-control' id='filterContentList'>
				<option value='all'>All Content</option>
				<option value='page' selected>Pages</option>
				<option value='text'>Text</option>
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
				<option value='text'>Text</option>
			</select>
		</div>
		<div class='form-group'>
			<a id='emptyTrash' class='btn btn-default' href='#'>Delete Selected</a>
			<a id='deleteSelected' class='btn btn-default' href='#'>Restore Selected</a>
			<a id='restoreSelected' class='btn btn-default' href='#'>Empty Trash</a>
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