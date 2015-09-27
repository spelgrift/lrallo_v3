<?php

class Controller {

		function __construct(){
			$this->view = new View();
			Session::init();

			// Load global model (for loading nav from db, etc.)
		}

		public function loadModel($name){

			$path = 'models/'.$name.'_model.php';

			if(file_exists($path)){
				require $path;
				$modelName = $name . '_Model';
				$this->model = new $modelName;
			}
		}
		
		public function error($arg = false)
		{
			$this->view->pageTitle = 'Page not found';
			$this->view->render('error/index');
		}
}

?>