<?php 
$postID = $post['postID'];
$contentID = $post['contentID'];
$title = $post['title'];
$url = $post['url'];
$date = date('Y.m.d', strtotime($post['date']));
$hidden = $post['hidden'];

if($hidden == 0) {
	$publishLink = 'Hide Post';
} else {
	$publishLink = 'Publish Post';
}
?>

<tr>
	<td class='listName'><a href='<? echo URL.BLOGURL."/post/$url"; ?>'><? echo $title; ?></a></td>
	<td><? echo $date; ?></td>
	<td><a class='btn btn-default btn-sm togglePublic' href='#' id='<? echo $contentID; ?>'><? echo $publishLink; ?></a></td>
	<td>
		<a class='btn btn-primary btn-sm' href='<? echo URL.BLOGURL."/post/$url"; ?>'>View</a>
		<a class='btn btn-primary btn-sm' href='<? echo URL.BLOGURL."/editpost/$url"; ?>'>Edit</a>
		<a class='btn btn-danger btn-sm trashPost' href='#' id='<? echo $contentID; ?>'>Trash</a>
	</td>
</tr>