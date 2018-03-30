<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use frontend\models\Model_ResetPwdRequest as Model_ResetPwdRequest;
use frontend\models\Model_Login as Model_Login;

class Controller_ResetPwdRequest extends Controller
{
	/*
		Reset password request actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_ResetPwdRequest();
		$this->view = new View();
	}

	/**
     * Displays reset password request page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('reset-pwd-request.php', 'form.php', RESET_PWD_REQUEST['hdr'], $this->form);
	}

	/**
     * Resets reset password request page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->actionIndex();
	}

	/**
     * Sends email reset password request.
     *
     * @return mixed
     */
	public function actionSendEmail()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->sendEmail($this->form);
			if (!$this->form['error_msg']) {
				$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
				$this->form['success_msg'] = 'Вам отправлено письмо с инструкцией об изменении пароля. Пожалуйста, проверьте электронную почту.';
			}
		}
		return $this->view->generate('reset-pwd-request.php', 'form.php', RESET_PWD_REQUEST['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
