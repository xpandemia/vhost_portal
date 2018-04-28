<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_Ege as Model_Ege;

class Controller_Ege extends Controller
{
	/*
		Ege actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_Ege();
		$this->view = new View();
	}

	/**
     * Displays ege page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('ege.php', 'main.php', EGE['hdr']);
	}

	/**
     * Resets ege page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->view->generate('ege-add.php', 'form.php', EGE['hdr'], $this->form);
	}

	/**
     * Displays ege add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('ege-add.php', 'form.php', EGE['hdr'], $this->form);
	}

	/**
     * Shows ege disciplines.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$this->form['pid'] = htmlspecialchars($_GET['id']);
			$this->form['success_msg'] = null;
			$this->form['error_msg'] = null;
			return $this->view->generate('ege-disciplines.php', 'main.php', 'Дисциплины ЕГЭ', $this->form);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р документа ЕГЭ!</p>");
		}
	}

	/**
     * Calls to ege document delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes ege.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
		$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
		if ($this->model->delete($this->form)) {
			Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index');
		} else {
			$this->form['error_msg'] = 'Ошибка удаления документа ЕГЭ! Свяжитесь с администратором.';
			return $this->view->generate('delete-confirm.php', 'form.php', 'Удаление документа '.$this->form['ctr'], $this->form);
		}
	}

	/**
     * Saves ege.
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
				return $this->view->generate('ege.php', 'main.php', EGE['hdr'], $this->form);
			}
		}
		return $this->view->generate('ege-add.php', 'form.php', EGE['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
