<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_IndAchievs as Model_IndAchievs;

class Controller_IndAchievs extends Controller
{
	/*
		Individual achievments actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_IndAchievs();
		$this->view = new View();
	}

	/**
     * Displays individual achievments page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('ind-achievs.php', 'main.php', 'Индивидуальные достижения');
	}

	/**
     * Resets individual achievments add page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		return $this->view->generate('ind-achievs-add.php', 'form.php', IND_ACHIEVS['hdr'], $this->form);
	}

	/**
     * Displays individual achievments add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('ind-achievs-add.php', 'form.php', IND_ACHIEVS['hdr'], $this->form);
	}

	/**
     * Shows individual achievment.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р индивидуального достижения!</p>");
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		$this->form['id'] = $id;
		return $this->view->generate('ind-achievs-add.php', 'form.php', 'Изменение индивидуального достижения', $this->form);
	}

	/**
     * Calls to individual achievment delete confirm.
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
			$this->model->delete($this->form);
			return Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index');
		} else {
			return Basic_Helper::redirect(IND_ACHIEVS['hdr'], 200, IND_ACHIEVS['ctr'], 'Index');
		}
	}

	/**
     * Saves individual achievment.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect(IND_ACHIEVS['hdr'], 200, IND_ACHIEVS['ctr'], 'Index', $this->form['success_msg']);
			}
		}
		$this->form = $this->model->unsetScans($this->form);
		return $this->view->generate('ind-achievs-add.php', 'form.php', IND_ACHIEVS['hdr'], $this->form);
	}

	/**
     * Cancels individual achievment.
     *
     * @return mixed
     */
	public function actionCancel()
	{
		return Basic_Helper::redirect(IND_ACHIEVS['hdr'], 200, IND_ACHIEVS['ctr'], 'Index');
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
