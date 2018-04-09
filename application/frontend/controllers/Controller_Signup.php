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

	public $form;

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
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		Captcha_Helper::create();
		return $this->view->generate('signup.php', 'form.php', SIGNUP['hdr'], $this->form);
	}

	/**
     * Refreshes CAPTCHA.
     *
     * @return mixed
     */
	public function actionCaptcha()
	{
		Captcha_Helper::create();
		return $this->actionIndex();
	}

	/**
     * Resets signup page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->actionIndex();
	}

	/**
     * Signs user up.
     *
     * @return mixed
     */
	public function actionSignup()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->signup($this->form);
			if (!$this->form['error_msg']) {
				$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
				$this->form['success_msg'] = 'Регистрация выполнена успешно. Пожалуйста, проверьте электронную почту.';
			}
		}
		Captcha_Helper::create();
		if (!$this->form['captcha_err']) {
			$this->form['captcha'] = null;
		}
		return $this->view->generate('signup.php', 'form.php', SIGNUP['hdr'], $this->form);
	}

	/**
     * Activates user account.
     *
     * @return mixed
     */
	public function actionActivation()
	{
		if (isset($_GET['code']) && !empty($_GET['code'])) {
			$code = htmlspecialchars($_GET['code']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует код активации.</p>");
		}
		if (isset($_GET['email']) && !empty($_GET['email'])) {
			$email = htmlspecialchars($_GET['email']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует адрес эл. почты.</p>");
		}
		if ($this->model->activate($code, $email)) {
			return Basic_Helper::redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
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
