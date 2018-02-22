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
			$this->model->setForm($this->form, $this->model->rules(), null);
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
		$this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->actionIndex();
	}

	/**
     * Logs user in.
     *
     * @return mixed
     */
	public function actionLogin()
	{
		$this->model->getForm($this->model->rules(), $_POST);
		if ($this->model->validateForm($this->form, $this->model->rules())) {
			if ($this->model->check()) {
				$this->model->resetForm(true, $this->form, $this->model->rules());
				return Basic_Helper::redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
			}
		}
		return Basic_Helper::redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
