<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_Resume as Resume;
use frontend\models\Model_DocsEduc as Model_DocsEduc;



class Controller_DocsEduc extends Controller
{
	/*
		Education documents actions
	*/

	public $form;
	public $code;

	public function __construct()
	{
		$this->model = new Model_DocsEduc();
		$this->view = new View();
		// code
		if (isset($_POST['code'])) {
			$this->code = htmlspecialchars($_POST['code']);
		}
		else {
			$this->code = null;
		}
		// check resume
		$resume = new Resume();
		$resume_row = $resume->getStatusByUser();
		if ($resume_row)
		{
			if ($resume_row['status'] == $resume::STATUS_CREATED || $resume_row['status'] == $resume::STATUS_SAVED)
			{
				return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Анкета ещё не отправлена!');
			}
		} else
		{
			return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Анкета ещё не создана!');
		}
	}

	/**
     * Displays education documents page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('docs-educ.php', 'main.php', 'Документы об образовании');
	}

	/**
     * Resets education documents add page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->view->generate('docs-educ-add.php', 'form.php', DOCS_EDUC['hdr'], $this->form);
	}

	/**
     * Displays education documents add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('docs-educ-add.php', 'form.php', DOCS_EDUC['hdr'], $this->form);
	}

	/**
     * Shows education document.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р документа об образовании!</p>");
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		var_dump($this->model->get($id));
		exit();
		$this->form['id'] = $id;
		if (!empty($this->form['change_name_id'])) {
			$this->form['change_name_flag'] = 'checked';
		}
		return $this->view->generate('docs-educ-add.php', 'form.php', 'Изменение документа об образовании', $this->form);
	}

	/**
     * Calls to education document delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes education document.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		if (isset($_POST['id']) && isset($_POST['hdr']) && isset($_POST['ctr'])) {
			$this->form['id'] = htmlspecialchars($_POST['id']);
			$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
			$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
			$this->form = $this->model->delete($this->form);
			return Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', $this->form['success_msg'], $this->form['error_msg']);
		} else {
			return Basic_Helper::redirect('Документы об образовании', 200, DOCS_EDUC['ctr'], 'Index', null, 'Ошибка удаления документа об образовании!');
		}
	}

	/**
     * Saves education document.
     *
     * @return mixed
     */
	public function actionSave()
{
		$this->form = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		$this->form = $this->model->validateFormAdvanced($this->form);
		if ($this->form['validate'])
		{
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect('Документы об образовании', 200, DOCS_EDUC['ctr'], 'Index', $this->form['success_msg']);
			}
		}
		else {
			if (empty($this->form['error_msg'])) {
				$this->form['error_msg'] = '<strong>Ошибка при проверке данных документа об образовании!</strong> Пожалуйста, проверьте все поля ввода.';
			}
		}
		$this->form = $this->model->unsetScans($this->form);
		return $this->view->generate('docs-educ-add.php', 'form.php', DOCS_EDUC['hdr'], $this->form);
	}

	/**
     * Cancels education document.
     *
     * @return mixed
     */
	public function actionCancel()
	{
		return Basic_Helper::redirect(DOCS_EDUC['hdr'], 200, DOCS_EDUC['ctr'], 'Index');
	}

	/**
     * Prints education documents by user campaign JSON.
     *
     * @return void
     */
	public function actionDiplomasByUserCampaignJSON()
	{
		echo $this->model->getDiplomasByUserCampaignJSON($this->code);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
