<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_Langs as Model_Langs;

class Controller_Langs extends Controller
{
	/*
		Langs actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_Langs();
		$this->view = new View();
	}

	/**
     * Displays langs page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('langs.php', 'main.php', 'Языки');
	}

	/**
     * Displays lang add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('langs-add.php', 'form.php', LANGS['hdr'], $this->form);
	}

	/**
     * Shows lang.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р языка!');
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		$this->form['id'] = $id;
		return $this->view->generate('langs-add.php', 'form.php', LANGS['hdr'], $this->form);
	}

	/**
     * Calls to lang delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes lang.
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
			return Basic_Helper::redirect('Языки', 200, LANGS['ctr'], 'Index', null, 'Ошибка удаления языка!');
		}
	}

	/**
     * Saves lang.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form['id'] = $id = htmlspecialchars($_POST['id']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect('Языки', 200, LANGS['ctr'], 'Index', $this->form['success_msg']);
			}
		}
		return $this->view->generate('langs-add.php', 'form.php', LANGS['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
