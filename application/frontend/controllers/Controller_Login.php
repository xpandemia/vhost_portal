<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use frontend\models\Model_Login as Model_Login;

class Controller_Login extends Controller
{
	/*
		Login actions
	*/

	public $form = 'login';

	public function __construct()
	{
		$this->model = new Model_Login();
		$this->view = new View();
	}

	/**
     * Displays login page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (!isset($_SESSION[$this->form])) {
			$this->model->reset(true);
		}
		return $this->view->generate('login.php', 'form.php', LOGIN_HDR);
	}

	/**
     * Resets login page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->reset(true);
		return $this->actionIndex();
	}

	/**
     * Logs user in.
     *
     * @return mixed
     */
	public function actionLogin()
	{
		$basic_helper = new Basic_Helper;
		$form_helper = new Form_Helper();
		$this->model->getpost($_POST);
		if ($this->model->validate()) {
			if ($this->model->check()) {
				$_SESSION['user_id'] = $_SESSION['login']['username'];
				$_SESSION['user_role'] = 'guest';
				$_SESSION['user_logon'] = 1;
				$this->model->reset(true);
				return $basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
			}
		}
		return $basic_helper->redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
