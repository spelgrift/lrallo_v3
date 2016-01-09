<?php 
// echo "<pre>";
// print_r($this->pageList);
// echo "</pre>";
require 'views/inc/header.php'; 
require 'views/inc/addContentForms/addPage.php';
?>

<!-- List Item Template -->
<script type='text/template' id='pageListTemplate'>
	<tr>
		<td class='listName'><a href='{{path}}'>{{name}}</a></td>
		<td>{{type}}</td>
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
	<div class='col-sm-12 col-lg-10 table-responsive'>
		<table class='table table-striped'>
			<thead>
				<tr>
					<td>Name</td>
					<td>Type</td>
					<td class='hidden-xs'>Date Created</td>
					<td class='hidden-xs'>Author</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($this->pageList as $row) {
						displayPageList($row);
					}
					function displayPageList($row, $subLevel = 0)
					{
						$pad = "";
						if($subLevel == 1) {
							$pad = "&mdash; ";
						} else if($subLevel > 1) {
							$pad = str_repeat("&emsp; ", ($subLevel - 1))."&mdash; ";
						}

						$contentID = $row['contentID'];
						$name = $row['name'];
						$path = $row['path'];
						$type = 'Page';
						$date = date('Y/m/d', strtotime($row['date']));
						$author = $row['author'];
						echo '<tr>';

						echo "<td class='listName'>$pad<a href='".URL.$path."'>$name</a></td>";						
						echo "<td>$type</td>";
						echo "<td class='hidden-xs'>$date</td>";
						echo "<td class='hidden-xs'>$author</td>";

						echo "<td class='text-center'>";
						echo "<a href='".URL.$path."' class='btn btn-primary btn-sm'>View</a> ";
						echo "<a href='".URL.$path."/edit' class='btn btn-primary btn-sm'>Edit</a> ";
						echo "<a href='#' id='$contentID' class='btn btn-primary btn-sm trashContent'>Trash</a>";
						echo "</td>";

						echo "</tr>";

						foreach($row['subPages'] as $row) {
							displayPageList($row, $subLevel + 1);
						}
					}
					
				?>
			</tbody>
		</table>
	</table>
</div>

<div class='row tabPanel' id='trash'>
	<h2>Trash</h2>
</div>





<?php require 'views/inc/footer.php'; ?>