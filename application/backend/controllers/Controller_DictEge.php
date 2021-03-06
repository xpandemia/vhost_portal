<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_DictEge as Model_DictEge;

class Controller_DictEge extends Controller
{
	/*
		Dictionary ege actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_DictEge();
		$this->view = new View();
	}

	/**
     * Displays dictionary ege page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('dict-ege.php', 'main.php', 'Дисциплины ЕГЭ');
	}

	/**
     * Displays dictionary ege add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('dict-ege-add.php', 'form.php', DICT_EGE['hdr'], $this->form);
	}

	/**
     * Shows dictionary ege.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р дисциплины ЕГЭ!');
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		$this->form['id'] = $id;
		return $this->view->generate('dict-ege-add.php', 'form.php', DICT_EGE['hdr'], $this->form);
	}

	/**
     * Calls to dictionary ege delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes dictionary ege.
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
			return Basic_Helper::redirect('Дисциплины ЕГЭ', 200, DICT_EGE['ctr'], 'Index', null, 'Ошибка удаления дисциплины ЕГЭ!');
		}
	}

	/**
     * Saves dictionary ege.
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
				return Basic_Helper::redirect(DICT_EGE['hdr'], 200, DICT_EGE['ctr'], 'Index', $this->form['success_msg']);
			}
		}
		return $this->view->generate('dict-ege-add.php', 'form.php', DICT_EGE['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
