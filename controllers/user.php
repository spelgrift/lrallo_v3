<?php

class User extends Controller {

	public function __construct()
	{
		parent::__construct();
		Auth::setAccess('owner');

		$this->view->pageTitle = 'Users';
		// $this->view->js = array('user.js');
	}

	public function index()
	{
		$this->view->userList = $this->model->userList();
		$this->view->render('user/index');
	}

	public function create()
	{
		$data = array();
		$data['login'] = $_POST['login'];
		$data['password'] = $_POST['password'];
		$data['role'] = $_POST['role'];

		// Error check!

		$this->model->create($data);
		header('location: ' . URL . 'user');
	}

	public function delete($id)
	{
		$this->model->delete($id);
		header('location: ' . URL . 'user');
	}

	public function edit($id)
	{
		// fetch individual user
		$this->view->user = $this->model->userList($id);
		$this->view->render('user/edit');
	}

	public function editSave($id)
	{
		$data = array();
		$data['id'] = $id;
		$data['login'] = $_POST['login'];
		$data['password'] = $_POST['password'];
		$data['role'] = $_POST['role'];

		// Error check!

		$this->model->editSave($data);
		header('location: ' . URL . 'user');
	}




}
?>