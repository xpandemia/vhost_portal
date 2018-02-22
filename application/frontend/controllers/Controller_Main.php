<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use frontend\models\Model_Main as Model_Main;

class Controller_Main extends Controller
{
	/*
		Main actions
	*/

	public function __construct()
	{
		$this->model = new Model_Main();
		$this->view = new View();
	}

	/**
     * Displays main page.
     *
     * @return mixed
     */
	function actionIndex()
	{
		$this->view->generate('main.php', 'main.php', APP_NAME);
	}

	/**
     * Calls to resume.
     *
     * @return mixed
     */
	function actionResume()
	{
		$this->model->resume();
	}

	/**
     * Logs user out.
     *
     * @return mixed
     */
	public function actionLogout()
	{
		$this->model->logout();
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
