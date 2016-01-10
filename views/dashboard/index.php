<?php 
// echo "<pre>";
// print_r($this->pageList);
// echo "</pre>";
require 'views/inc/header.php'; 
require 'views/inc/addContentForms/addPage.php';
?>

<!-- List Item Templates -->
<script type='text/template' id='pageListTemplate'>
	<tr>
		<td class='listName'><a href='{{path}}'>{{name}}</a></td>
		<td>{{type}}</td>
		<td>{{parent}}</td>
		<td class='hidden-xs'>{{date}}</td>
		<td class='hidden-xs'>{{author}}</td>
		<td class='text-center'>
			<a href='{{path}}' class='btn btn-primary btn-sm'>View</a>
			<a href='{{path}}/edit' class='btn btn-primary btn-sm'>Edit</a>
			<a href='#' id='{{$contentID}}' class='btn btn-primary btn-sm trashContent'>Trash</a>
		</td>
	</tr>
</script>

<!-- LIST CONTENT -->
<div class='row tabPanel active' id='contentList'>
	<form class='form-inline pull-right'>
		<div class='form-group'>
			<label>Filter list by type: </label>
			<select class='form-control' id='filterContentList'>
				<option value='all'>All Content</option>
				<option value='page' selected>Pages</option>
				<option value='text'>Text</option>
			</select>
		</div>
	</form>
	<div class='col-sm-12 col-lg-9 table-responsive'>
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
				<?php
					foreach($this->contentList as $row) {
						displayContentList($row);
					}

					function displayContentList($row, $subLevel = 0, $parentName = '-')
					{
						$defaultPad = "&ensp;<i class='fa fa-level-up fa-rotate-90'></i>&ensp;";
						// Build pad based on subLevel
						$pad = "";
						if($subLevel == 1) {
							$pad = $defaultPad;
						} else if($subLevel > 1) {
							$pad = str_repeat("&emsp; ", ($subLevel - 1)).$defaultPad;
						}

						// Add vars common to all types
						$contentID = $row['contentID'];
						$path = $row['path'];
						$date = date('Y/m/d', strtotime($row['date']));
						$author = $row['author'];

						// Switch based on type
						switch($row['type'])
						{
							case "page" :
								$name = $row['name'];
								$nameTd = "<td class='listName'><span class='listPad'>$pad</span><a href='".URL.$path."'>$name</a></td>";
								$type = 'Page';
								$rowClass = 'contentListRow page visible';
								$parentLink = "<a href='".URL.$path."'>$name</a>";
							break;
							case "text" :
								$trimmedText = substr(htmlentities($row['text']), 0, 25).'...';
								$nameTd = "<td><span class='listPad'>$pad</span>$trimmedText</td>";
								$type = 'Text';
								$rowClass = 'contentListRow text';
							break;
						}

						// Echo HTML
						echo "<tr class='$rowClass'>";

						echo $nameTd;						
						echo "<td>$type</td>";
						echo "<td>$parentName</td>";
						echo "<td class='hidden-xs'>$date</td>";
						echo "<td class='hidden-xs'>$author</td>";

						echo "<td>";
						echo "<a href='".URL.$path."' class='btn btn-primary btn-sm'>View</a> ";
						echo "<a href='".URL.$path."/edit' class='btn btn-primary btn-sm'>Edit</a> ";
						echo "<a href='#' id='$contentID' class='btn btn-primary btn-sm trashContent'>Trash</a>";
						echo "</td>";

						echo "</tr>";

						if(isset($row['subContent'])) {
							foreach($row['subContent'] as $row) {
								displayContentList($row, $subLevel + 1, $parentLink);
							}
						}
					}					
				?>
			</tbody>
		</table>
	</div>
</div>

<div class='row tabPanel' id='trash'>
	<h2>Trash</h2>
</div>





<?php require 'views/inc/footer.php'; ?>