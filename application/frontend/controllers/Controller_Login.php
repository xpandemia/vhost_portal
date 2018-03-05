<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use frontend\models\Model_Login as Model_Login;

class Controller_Login extends Controller
{
	/*
		Login actions
	*/

	public $form;

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
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('login.php', 'form.php', LOGIN_HDR, $this->form);
	}

	/**
     * Resets login page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->view->generate('login.php', 'form.php', LOGIN_HDR, $this->form);
	}

	/**
     * Logs user in.
     *
     * @return mixed
     */
	public function actionLogin()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
				return $this->view->generate('main.php', 'main.php', APP_NAME);
			}
		}
		return $this->view->generate('login.php', 'form.php', LOGIN_HDR, $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
