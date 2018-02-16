<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Captcha_Helper as Captcha_Helper;
use frontend\models\Model_Signup as Model_Signup;

class Controller_Signup extends Controller
{
	/*
		Signup actions
	*/

	public $form = 'signup';

	public function __construct()
	{
		$this->model = new Model_Signup();
		$this->view = new View();
	}

	/**
     * Displays signup page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (!isset($_SESSION[$this->form])) {
			$captcha = new Captcha_Helper();
			$captcha->create();
			$this->model->reset(true);
		}
		return $this->view->generate('signup.php', 'form.php', SIGNUP_HDR);
	}

	/**
     * Refreshes CAPTCHA.
     *
     * @return mixed
     */
	public function actionCaptcha()
	{
		$captcha = new Captcha_Helper();
		$captcha->create();
		return $this->actionIndex();
	}

	/**
     * Resets signup page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->reset(true);
		return $this->actionIndex();
	}

	/**
     * Sign user up.
     *
     * @return mixed
     */
	public function actionSignup()
	{
		$basic_helper = new Basic_Helper;
		$this->model->getpost($_POST);
		if ($this->model->validate()) {
			if ($this->model->signup()) {
				$this->model->reset(true);
				$_SESSION['login']['error_msg'] = null;
				$_SESSION['login']['success_msg'] = 'Регистрация выполнена успешно. Пожалуйста, проверьте электронную почту.';
				unlink(ROOT_DIR.'/images/temp/captcha/captcha_'.session_id().'.png');
				return $basic_helper->redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
			}
		}
		return $basic_helper->redirect(SIGNUP_HDR, 202, BEHAVIOR.'/Signup', 'Index');
	}

	/**
     * Activates user account.
     *
     * @return mixed
     */
	public function actionActivation()
	{
		if (isset($_GET['code']) && !empty($_GET['code'])) {
			$code = $_GET['code'];
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует код активации.</p>");
		}
		if (isset($_GET['email']) && !empty($_GET['email'])) {
			$email = $_GET['email'];
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует Email.</p>");
		}
		if ($this->model->activate($code, $email)) {
			$_SESSION['user_id'] = $_SESSION[$this->form]['username'];
			$_SESSION['user_role'] = 'guest';
			$_SESSION['user_logon'] = 1;
			$basic_helper = new Basic_Helper;
			return $basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
		} else {
			exit("<p><strong>Ошибка!</strong> Активация не выполнена.</p>");
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
