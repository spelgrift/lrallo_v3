<? foreach($this->posts as $post) {
	$this->postContent = $post['content'];
	require 'views/inc/blog/post.php';
}