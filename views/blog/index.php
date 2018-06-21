<?php require 'views/inc/header.php'; ?>
<div class='row' id='blogIntro'>
	<div class='col-xs-12'>
		<h3>Regarde-moi</h3>
		<p><strong>Lindsey goes to Paris // June 24 - July 7, 2018</strong>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eu dapibus urna. Sed ut pretium lacus, vel porttitor mauris. Nullam non euismod nibh. Morbi convallis augue id arcu sollicitudin vehicula. Duis at urna mollis, interdum urna at, tempus neque.</p>
		<p>Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi euismod ac sem eu convallis. Nam feugiat, tellus porttitor accumsan condimentum, justo enim gravida justo, quis ultricies neque nisi et tellus. Morbi justo velit, suscipit ut est eget, rutrum dignissim mauris. Morbi mattis sem leo, quis venenatis risus ultricies sit amet. Vivamus vel magna ultricies, luctus orci sed, egestas felis.</p>
	</div>
</div>
<div id="allPosts">
<?
foreach($this->posts as $post) {
	$this->postContent = $post['content'];
	require 'views/inc/blog/post.php';
}
?>
</div>



<? require 'views/inc/footer.php'; 