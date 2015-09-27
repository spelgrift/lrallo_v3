<?php

class View {

		function __construct(){
			// echo 'The view.<br>';
		}

		public function render($name){
			require 'views/inc/globalHeader.php';
			require 'views/' . $name . '.php';
			require 'views/inc/globalFooter.php';
		}
}

?>