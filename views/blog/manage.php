<?php require 'views/inc/header.php'; ?>

<!-- LIST POSTS -->
<div class='row' id='postList'>
	<div class="col-sm-12 text-center"><h3>All Posts</h3></div>
	<div class='col-sm-8 col-sm-offset-2 table-responsive'>
		<table class='table table-hover'>
			<?php 
			foreach($this->posts as $post) {
				require "views/inc/blog/postListRow.php";
			}
			if(count($this->posts) == 0) {
				echo "<tr class='placeholderRow'><td colspan='4'>No posts yet. Get started by clicking 'New Post'.</td></tr>";
			}
			?>
		</table>
	</div>
</div>




<?php require 'views/inc/footer.php'; ?>