<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_DocsEduc as DocsEduc;
use frontend\models\Model_Application as Model_Application;
use frontend\models\Model_ApplicationSpec as Model_ApplicationSpec;

include ROOT_DIR.'/application/frontend/models/Model_ApplicationSpec.php';

class Controller_Application extends Controller
{
	/*
		Application actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_Application();
		$this->view = new View();
		// check resume
		$resume = new Resume();
		$resume_row = $resume->getStatusByUser();
		if ($resume_row) {
			if ($resume_row['status'] == $resume::STATUS_CREATED || $resume_row['status'] == $resume::STATUS_SAVED) {
				return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Анкета ещё не отправлена!');
			}
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Анкета ещё не создана!');
		}
		// check education documents
		$docs = new DocsEduc();
		$docs_row = $docs->getByUser();
		if (!$docs_row) {
			return Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Home', null, 'Нет ни одного документа об образовании!');
		}
	}

	/**
     * Displays application page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('application.php', 'main.php', 'Заявления');
	}

	/**
     * Resets application page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->view->generate('application-add.php', 'form.php', APP['hdr'], $this->form);
	}

	/**
     * Displays application add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('application-add.php', 'form.php', APP['hdr'], $this->form);
	}

	/**
     * Shows application specialities.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			$spec = new Model_ApplicationSpec();
			$spec_row = $spec->get($id);
			$this->form = $this->model->setForm($spec->rules(), $spec_row);
			$this->form['id'] = $id;
			return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	/**
     * Calls to application delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes application.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
		$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
		if ($this->model->delete($this->form)) {
			Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', $_SESSION[APP_CODE]['success_msg']);
		} else {
			Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index', null, $_SESSION[APP_CODE]['error_msg']);
		}
	}

	/**
     * Saves application.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect('Заявления', 200, APP['ctr'], 'Index', 'Создано новое заявление.');
			}
		} else {
			if (empty($this->form['error_msg'])) {
				$this->form['error_msg'] = '<strong>Ошибка при проверке данных заявления!</strong> Пожалуйста, проверьте все поля ввода.';
			}
		}
		return $this->view->generate('application-add.php', 'form.php', APP['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
