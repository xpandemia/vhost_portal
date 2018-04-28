<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_ApplicationSpec as Model_ApplicationSpec;

class Controller_ApplicationSpec extends Controller
{
	/*
		Application spec actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_ApplicationSpec();
		$this->view = new View();
	}

	/**
     * Displays application places add page.
     *
     * @return mixed
     */
	public function actionAddPlaces()
	{
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$this->form['pid'] = htmlspecialchars($_GET['pid']);
			$this->form['error_msg'] = null;
			$this->form['success_msg'] = null;
			return $this->view->generate('application-places-add.php', 'form.php', 'Выбор направлений подготовки', $this->form);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р заявления!</p>");
		}
	}

	/**
     * Saves application spec places.
     *
     * @return mixed
     */
	public function actionSavePlaces()
	{
		$this->form = $this->model->checkPlaces($_POST);
		if (!$this->form['error_msg']) {
			return Basic_Helper::redirect('Заявление', 200, 'Application', 'Edit/?id='.$this->form['pid'], 'Направления подготовки выбраны.');
		} else {
			return $this->view->generate('application-places-add.php', 'form.php', 'Выбор направлений подготовки', $this->form);
		}
	}

	/**
     * Saves application spec.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$id = $_POST['id'];
		$this->form = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
		$this->form = $this->model->getExams($this->form);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		$this->form['id'] = $id;
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				$id = $this->form['id'];
				$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
				$this->form['id'] = $id;
				$this->form['success_msg'] = 'Заявление успешно сохранено!';
				return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
			}
		} else {
			$this->form = $this->model->unsetScans($this->form);
		}
		return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
	}

	/**
     * Saves application spec as PDF.
     *
     * @return mixed
     */
	public function actionSavePdf()
	{
		$this->model->savePdf();
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}