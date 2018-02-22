<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_Resume as Model_Resume;

class Controller_Resume extends Controller
{
	/*
		Resume actions
	*/

	public $form = 'resume';

	public function __construct()
	{
		$this->model = new Model_Resume();
		$this->view = new View();
	}

	/**
     * Displays resume page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (!isset($_SESSION[$this->form])) {
			$this->model->setForm($this->form, $this->model->rules(), null);
		}
		return $this->view->generate('resume.php', 'main.php', RESUME_HDR);
	}

	/**
     * Resets resume page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->actionIndex();
	}

	/**
     * Makes resume changes.
     *
     * @return mixed
     */
	public function actionResume()
	{
		if (!isset($_SESSION[$this->form]['is_edit'])) {
			$_SESSION['main']['error_msg'] = 'Признак изменения персональных данных не установлен!';
			$basic_helper->redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
		} else {
			$this->model->getForm($this->model->rules(), $_POST);
			if ($this->model->validateForm($this->form, $this->model->rules())) {
				if ($this->model->check()) {
					$_SESSION[$this->form]['success_msg'] = 'Персональные данные успешно сохранены!';
					$_SESSION['resume']['is_edit'] = true;
				}
			}
			return Basic_Helper::redirect(LOGIN_HDR, 202, BEHAVIOR.'/Resume', 'Index');
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
