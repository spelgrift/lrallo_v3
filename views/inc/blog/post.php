<?php

$contentID = $post['contentID'];
$postID = $post['postID'];
$url = URL.BLOGURL."/post/".$post['url'];
$date = date('F j, Y', strtotime($post['date']));
$title = $post['title'];
$body = $post['body'];

?>

<div class='blogPost' data-id='<? echo $postID; ?>'>
	<div class='blogDate text-center'><? echo $date; ?></div>
	<div class='row'>
		<div class='col-sm-6 col-sm-offset-3 text-center'>
			<h2><a href="<? echo $url; ?>"><? echo $title; ?></a></h2>
		</div>
	</div>
	<div class='row'>
		<div class='col-sm-6 col-sm-offset-3'>
			<? echo $body; ?>
		</div>
	</div>
	<div class='row'>
		<?php
		foreach($this->postContent as $item) {
			$this->renderContent($item);
		}
		?>
	</div>
</div>