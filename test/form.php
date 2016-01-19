<?php


require '../libs/Val.php';
require '../libs/Form.php';


if (isset($_REQUEST['run'])){
	$form = new Form();

	$form ->post('name')
			->val('minlength', 2)
			->post('age')
			->val('digit')
			->post('gender');
	if(!$form->submit()) {
		print_r($form->fetchError());
	} else {
		echo 'The form passed!';
		$data = $form->fetch()	;

		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	// try {
	// 	$form = new Form();

	// 	$form	->post('name')
	// 			->val('minlength', 2)

	// 			->post('age')
	// 			->val('digit')

	// 			->post('gender');

	// 	$form	->submit();

	// 	echo 'The form passed!';
	// 	$data = $form->fetch()	;

	// 	echo '<pre>';
	// 	print_r($data);
	// 	echo '</pre>';
		


	// } catch (Exception $e) {
	// 	print_r($e->getMessage());
	// }

	
}

?>
<form method='post' action='?run'>
	Name <input type='text' name='name' />
	Age <input type='text' name='age' />
	Gender <select name='gender'>
		<option value='m'>Male</option>
		<option value='f'>Female</option>
	</select>
	<input type='submit'>
</form>
