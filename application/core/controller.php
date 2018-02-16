<?php

namespace tinyframe\core;

class Controller
{

	/*
		BASE Controller

		Controllers process users actions
	*/

	public $model;
	public $view;
	
	function __construct()
	{
		$this->model = new Model();
		$this->view = new View();
	}

	/**
     * Displays a page.
     *
     * @return mixed
     */
	function actionIndex()
	{
		// todo
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
