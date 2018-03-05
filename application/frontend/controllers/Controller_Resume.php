<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_Personal as Model_Personal;
use frontend\models\Model_Resume as Model_Resume;

class Controller_Resume extends Controller
{
	/*
		Resume actions
	*/

	public $form;

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
		$personal = new Model_Personal();
		$personal->id_user = $_SESSION[APP_CODE]['user_id'];
		$row = $personal->getPersonalByUser();
		if ($row) {
			$this->form = $this->model->setForm($this->model->rules(), $row);
			$this->form['is_edit'] = true;
		} else {
			$this->form = $this->model->setForm($this->model->rules(), null);
			$this->form['is_edit'] = false;
		}
		return $this->view->generate('resume.php', 'form.php', RESUME_HDR, $this->form);
	}

	/**
     * Resets resume page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
			$personal = new Model_Personal();
			$personal->id_user = $_SESSION[APP_CODE]['user_id'];
			$row = $personal->getPersonalByUser();
			if ($row) {
				$this->form['is_edit'] = true;
			} else {
				$this->form['is_edit'] = false;
			}
		return $this->view->generate('resume.php', 'form.php', RESUME_HDR, $this->form);
	}

	/**
     * Makes resume changes.
     *
     * @return mixed
     */
	public function actionResume()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
			$personal = new Model_Personal();
			$personal->id_user = $_SESSION[APP_CODE]['user_id'];
			$row = $personal->getPersonalByUser();
			if ($row) {
				$this->form['is_edit'] = true;
				$this->form['personal_vis'] = false;
			} else {
				$this->form['is_edit'] = false;
				$this->form['personal_vis'] = true;
			}
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				$this->form['error_msg'] = null;
				$this->form['success_msg'] = 'Анкета успешно сохранена!';
				$this->form['is_edit'] = true;
			}
		}
		return $this->view->generate('resume.php', 'form.php', RESUME_HDR, $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
