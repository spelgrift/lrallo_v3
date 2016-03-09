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
	
	public function error($arg = false)
	{
		$this->view->pageTitle = 'Page not found';
		$this->view->render('error/index');
	}

	public function loadNav(){
		$this->view->nav = $this->globalModel->loadNav();
	}
}