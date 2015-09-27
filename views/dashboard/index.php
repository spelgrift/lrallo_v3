<?php require 'views/inc/header.php'; ?>



Dashboard. Logged in only.

<br>
<h4>Add Page</h4>

<form id="addPageForm" action="<?php echo URL;?>dashboard/addPage/" method="post">
	<label>Name</label><input type="text" name="pageName" />
	<input type="submit" />
</form>

<div id='pageList'>

</div>





<?php require 'views/inc/footer.php'; ?>