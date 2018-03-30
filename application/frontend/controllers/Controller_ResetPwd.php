<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use frontend\models\Model_ResetPwd as Model_ResetPwd;

class Controller_ResetPwd extends Controller
{
	/*
		Reset password actions
	*/

	public $form;

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
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		if (isset($_SESSION[APP_CODE]['pwd_token']) && isset($_SESSION[APP_CODE]['email'])) {
			$this->form['success_msg'] = 'Ваш запрос на восстановление пароля подтвержден.';
		}
		return $this->view->generate('reset-pwd.php', 'form.php', RESET_PWD['hdr'], $this->form);
	}

	/**
     * Resets reset password page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->actionIndex();
	}

	/**
     * Resets user password.
     *
     * @return mixed
     */
	public function actionResetPwd()
	{
		if (isset($_SESSION[APP_CODE]['pwd_token']) && isset($_SESSION[APP_CODE]['email'])) {
			$this->form = $this->model->getForm($this->model->rules(), $_POST);
			$this->form = $this->model->validateForm($this->form, $this->model->rules());
			if ($this->form['validate']) {
				$this->form = $this->model->resetPwd($this->form);
				if (!$this->form['error_msg']) {
					$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
					$_SESSION[APP_CODE]['pwd_token'] = null;
					$_SESSION[APP_CODE]['email'] = null;
					$this->form['success_msg'] = 'Ваш пароль успешно изменён.';
				}
			}
		} else {
			$this->form['error_msg'] = 'Отсутствует признак изменения пароля или адрес эл. почты.';
		}
		return $this->view->generate('reset-pwd.php', 'form.php', RESET_PWD['hdr'], $this->form);
	}

	/**
     * Check reset password token.
     *
     * @return mixed
     */
	public function actionCheckResetPwd()
	{
		if (isset($_GET['pwd_token']) && !empty($_GET['pwd_token'])) {
			$_SESSION[APP_CODE]['pwd_token'] = $_GET['pwd_token'];
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует признак изменения пароля.</p>");
		}
		if (isset($_GET['email']) && !empty($_GET['email'])) {
			$_SESSION[APP_CODE]['email'] = $_GET['email'];
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует адрес эл. почты.</p>");
		}
		return $this->actionIndex();
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
