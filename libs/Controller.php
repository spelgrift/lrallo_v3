<?php

class Controller {

	function __construct(){
		$this->view = new View();
		Session::init();

		// Load global model (for loading nav from db on all controllers, etc.)
		$this->loadModel('global', false);
		$this->globalModel = new Global_Model();
		// Load navigation for all controllers/pages
		$this->loadNav();
	}

/**
 *	loadModel - 
 *	@param string $name The model name
 *	@param bool $inst Whether or not to instantiate
 *
 */
	public function loadModel($name, $inst = true){

		$path = 'models/'.$name.'_model.php';

		if(file_exists($path)){
			require $path;
			if($inst){
				$modelName = $name . '_Model';
				$this->model = new $modelName;
			}
		}
	}

/**
 *	_loadTypeContentModel - Loads the content model for given content type
 *									Creates an instance of the model in the class at
 *									$this->[type]ContentModel
 * @param string $type 	 -	The content type
 *
 */
	protected function _loadTypeContentModel($type)
	{
		$this->loadModel("content_models/".$type."_content", false);
		$varName = $type."ContentModel";
		$className = ucfirst($type)."_Content_Model";
		$this->$varName = new $className;
	}

	
	public function error()
	{
		// JS (public)
		$this->view->js = array('public.min.js');
		$this->view->pageTitle = 'Page not found';
		$this->view->render('error/index');
	}

	public function loadNav(){
		$this->view->nav = $this->globalModel->loadNav();
	}

	
}