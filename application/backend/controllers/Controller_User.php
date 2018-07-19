<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_User as Model_User;

class Controller_User extends Controller
{
	/*
		Users actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_User();
		$this->view = new View();
	}

	/**
     * Displays users page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('user.php', 'main.php', USER['hdr'],  $this->form);
	}

	/**
     * Login as user.
     *
     * @return mixed
     */
	public function actionMask()
	{
		if ($this->model->mask()) {
			Basic_Helper::redirectHome();
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
