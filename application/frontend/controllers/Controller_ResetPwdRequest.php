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
			$this->model->reset(true);
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
		$this->model->reset(true);
		return $this->actionIndex();
	}

	/**
     * Sends email reset password request.
     *
     * @return mixed
     */
	public function actionSendEmail()
	{
		$basic_helper = new Basic_Helper;
		$this->model->getpost($_POST);
		if ($this->model->validate()) {
			if ($this->model->sendEmail()) {
				$this->model->reset(true);
				$_SESSION['login']['error_msg'] = null;
				$_SESSION['login']['success_msg'] = 'Вам отправлено письмо с инструкцией об изменении пароля. Пожалуйста, проверьте электронную почту.';
				return $basic_helper->redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
			}
		}
		return $basic_helper->redirect(RESET_PWD_REQUEST_HDR, 202, BEHAVIOR.'/ResetPwdRequest', 'Index');
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
