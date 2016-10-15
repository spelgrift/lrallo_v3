<?php

class Slideshow_Content_Model extends Content_Model {

	function __construct(){parent::__construct();}

	public function updateSlideshow($contentID)
	{
		$form = new Form();
		$form ->post('animationSpeed')
				->val('blank')
				->val('digit')
				->post('slideDuration')
				->val('blank')
				->val('digit')
				->post('autoplay')
				->post('animationType');
		if(!$form->submit()) {
			$error = $form->fetchError();
			$this->_returnError(reset($error), key($error));
			return false;
		}
		$data = $form->fetch(); // Form passed
		$this->db->update('slideshow', $data, '`contentID` ='.$contentID);
		return array('error' => false, 'results' => $data);
	}

}