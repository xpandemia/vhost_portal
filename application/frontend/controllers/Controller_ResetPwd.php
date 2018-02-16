<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_ResetPwd as Model_ResetPwd;

class Controller_ResetPwd extends Controller
{
	/*
		Reset password actions
	*/

	public $form = 'reset_pwd';

	public function __construct()
	{
		$this->model = new Model_ResetPwd();
		$this->view = new View();
	}

	/**
     * Displays reset password page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (!isset($_SESSION[$this->form])) {
			$this->model->reset(true);
		}
		if (isset($_SESSION[$this->form]['pwd_token']) && isset($_SESSION[$this->form]['email'])) {
			$_SESSION[$this->form]['success_msg'] = 'Ваш запрос на восстановление пароля подтвержден';	
		}
		return $this->view->generate('reset-pwd.php', 'form.php', RESET_PWD_HDR);
	}

	/**
     * Resets reset password page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->reset(true);
		return $this->actionIndex();
	}

	/**
     * Resets user password.
     *
     * @return mixed
     */
	public function actionResetPwd()
	{
		$basic_helper = new Basic_Helper;
		$this->model->getpost($_POST);
		if ($this->model->validate()) {
			if ($this->model->resetPwd()) {
				$this->model->reset(true);
				$_SESSION[$form]['pwd_token'] = null;
				$_SESSION[$form]['email'] = null;
				$_SESSION['login']['error_msg'] = null;
				$_SESSION['login']['success_msg'] = 'Ваш пароль успешно изменён.';
				return $basic_helper->redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
			}
		}
		return $basic_helper->redirect(RESET_PWD_REQUEST_HDR, 202, BEHAVIOR.'/ResetPwd', 'Index');
	}

	/**
     * Check reset password token.
     *
     * @return mixed
     */
	public function actionCheckResetPwd()
	{
		if (isset($_GET['pwd_token']) && !empty($_GET['pwd_token'])) {
			$_SESSION[$this->form]['pwd_token'] = $_GET['pwd_token'];
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует признак изменения пароля.</p>");
		}
		if (isset($_GET['email']) && !empty($_GET['email'])) {
			$_SESSION[$this->form]['email'] = $_GET['email'];
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует Email.</p>");
		}
		return $this->actionReset();
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
