<?php
class Blog extends Controller {
	function __construct() {
		parent::__construct();
	}

	function index()
	{
		$this->view->pageTitle = BRAND." - Blog";
		// Admin nav - manage blog
		$this->view->adminNav = 'blogindex';
		// JS - public
		$this->view->js = array('public.min.js');
		// Get post list from DB

		// Render
		$this->view->render('blog/index');
	}

	function post($url = false)
	{
		if(!$url) { $this->error(); }
		// Get post attr
		// Admin nav - edit post
		// JS - public
		$this->view->js = array('public.min.js');
		$this->view->render('blog/post');
	}

	function manage()
	{
		Auth::setAccess();
		$this->view->pageTitle = "Manage Blog";
		$this->view->adminNav = 'manageblog';
		// JS - blogadmin
		$this->view->js = array('blogAdmin.min.js');
		// Get post list
		// Render
		$this->view->render('blog/manage');
	}

	function newpost() {
		Auth::setAccess();
	}

	function editpost($url = false) {
		Auth::setAccess();


	}

	


}