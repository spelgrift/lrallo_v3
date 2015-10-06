<?php

class Bootstrap 
{

	private $_url = null;
	private $_controller = null;
	/**
	 *  __construct - Loads controller based on URL input
	 */
	function __construct(){}


	/**
	 *  init - Starts the bootstrap
	 *	imageman.com/								load page controller - hit db - render frontpage
	 *	imageman.com/pagename/					load page controller - hit db - render given page
	 *	imageman.com/dashboard/method 		load given controller, run methods
	 * imageman.com/asdfljhoij/				load page controller - hit db - render error
	 * 
	 */
	public function init()
	{
		// Sets the protected $_url
		$this->_getUrl();

		// Loads page controller with no args if no URL is set
		if (empty($this->_url[0]))
		{
			$this->_loadDefaultController();
			return false;
		}

		// Load existing controller if there is one, otherwise load page and pass URL, call Method if page has a controller file
		$this->_loadController();
	}


	/**
	 * Fetches the $_GET from the URL
	 */
	private function _getUrl()
	{
		$url = isset($_GET['url']) ? $_GET['url'] : null;
		$url = rtrim( $url, '/' );
		$url = filter_var($url, FILTER_SANITIZE_URL);
		$this->_url = explode( '/', $url );
	}


	/**
	 *  Loads the default controller if no URL entered
	 */
	private function _loadDefaultController()
	{
		require 'controllers/page.php';
		$this->_controller = new Page();
		$this->_controller->loadModel('page');
		$this->_controller->buildPage();
	}


	/**
	 *  Loads the given controller if one exists and calls a method if necessary, otherwise loads a page and passes the URL
	 */
	private function _loadController()
	{
		$file = 'controllers/' . $this->_url[0] . '.php';
		if(file_exists( $file ))
		{
			require $file;
			$this->_controller = new $this->_url[0];
			$this->_controller->loadModel($this->_url[0]);
			if(!$this->_callControllerMethod())
			{
				$this->_controller->index();
			}
		}
		else
		{
			require 'controllers/page.php';
			$this->_controller = new Page();
			$this->_controller->loadModel('page');
			$this->_controller->buildPage($this->_url);
		}
	}

	/**
	 *  Calls method on controller
	 */
	private function _callControllerMethod()
	{
		if(count($this->_url) > 1)
		{
			if(!method_exists($this->_controller, $this->_url[1]))
			{
				$this->_controller->error();
				exit;
			}
			$params = $this->_url;
			unset($params[0]); // removing controller
			unset($params[1]); // removing method

			call_user_func_array(array($this->_controller, $this->_url[1]), $params);
			return true;
		}
		else
		{
			return false;
		}
	}

}

?>