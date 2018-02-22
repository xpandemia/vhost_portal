<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_ResetPwdRequest as Model_ResetPwdRequest;

class Controller_ResetPwdRequest extends Controller
{
	/*
		Reset password request actions
	*/

	public $form = 'reset_pwd_request';

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
		if (!isset($_SESSION[$this->form])) {
			$this->model->setForm($this->form, $this->model->rules(), null);
		}
		return $this->view->generate('reset-pwd-request.php', 'form.php', RESET_PWD_REQUEST_HDR);
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
		$this->model->getForm($this->model->rules(), $_POST);
		if ($this->model->validateForm($this->form, $this->model->rules())) {
			if ($this->model->sendEmail()) {
				$this->model->resetForm(true, $this->form, $this->model->rules());
				$_SESSION['login']['error_msg'] = null;
				$_SESSION['login']['success_msg'] = 'Вам отправлено письмо с инструкцией об изменении пароля. Пожалуйста, проверьте электронную почту.';
				return Basic_Helper::redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
			}
		}
		return Basic_Helper::redirect(RESET_PWD_REQUEST_HDR, 202, BEHAVIOR.'/ResetPwdRequest', 'Index');
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
