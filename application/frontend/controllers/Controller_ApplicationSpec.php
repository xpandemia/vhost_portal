<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use frontend\models\Model_ApplicationSpec as Model_ApplicationSpec;

include ROOT_DIR.'/application/frontend/models/Model_Application.php';

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
     * Synchronizes individual achievments for application.
     *
     * @return mixed
     */
	public function actionSyncIa()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			$spec_row = $this->model->get($id);
			$this->form = $this->model->setForm($this->model->rules(), $spec_row);
			$this->form['id'] = $id;
			$this->form = $this->model->syncIa($this->form);
			return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
		} else {
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	/**
     * Cancels application spec page.
     *
     * @return mixed
     */
	public function actionCancel()
	{
		return Basic_Helper::redirect('Заявления', 200, APP['ctr'], 'Index');
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
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	/**
     * Cancels application places add page.
     *
     * @return mixed
     */
	public function actionCancelPlaces()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			return Basic_Helper::redirect('Заявления', 200, APP['ctr'], 'Edit/?id='.$id);
		} else {
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
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
			return Basic_Helper::redirect(APP['hdr'], 200, APP['ctr'], 'Edit/?id='.$this->form['pid'], 'Направления подготовки выбраны.');
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
		$id = htmlspecialchars($_POST['id']);
			$this->form = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
			$this->form['id'] = $id;
			$this->form = $this->model->getExams($this->form);
			$this->form = $this->model->saveExams($this->form);
				$this->form = $this->model->validateForm($this->form, $this->model->rules());
				$this->form = $this->model->validateFormAdvanced($this->form);
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
			}
		} else {
			$this->form['error_msg'] = '<strong>Ошибка при проверке данных заявления!</strong> Пожалуйста, проверьте все поля ввода.';
		}
		$this->form = $this->model->unsetScans($this->form);
		return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
	}

	/**
     * Sends application.
     *
     * @return mixed
     */
	public function actionSend()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			$spec_row = $this->model->get($id);
			$this->form = $this->model->setForm($this->model->rules(), $spec_row);
			$this->form['id'] = $id;
			$this->form = $this->model->send($this->form);
			return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
		} else {
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	/**
     * Changes application.
     *
     * @return mixed
     */
	public function actionChange()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			$spec_row = $this->model->get($id);
			$this->form = $this->model->setForm($this->model->rules(), $spec_row);
			$this->form['id'] = $id;
			$this->form = $this->model->change($this->form);
			return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
		} else {
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	/**
     * Recalls application.
     *
     * @return mixed
     */
	public function actionRecall()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
			$spec_row = $this->model->get($id);
			$this->form = $this->model->setForm($this->model->rules(), $spec_row);
			$this->form['id'] = $id;
			$this->form = $this->model->recall($this->form);
			return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
		} else {
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	/**
     * Saves application spec as PDF.
     *
     * @return mixed
     */
	public function actionSavePdf()
	{
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$id = htmlspecialchars($_GET['pid']);
			$place = new ApplicationPlaces();
			$place->pid = $id;
			if ($place->getSpecsByApp()) {
				$this->model->savePdf(htmlspecialchars($id));
			} else {
				$spec_row = $this->model->get($id);
				$this->form = $this->model->setForm($this->model->rules(), $spec_row);
				$this->form['id'] = $id;
				$this->form['error_msg'] = 'Направления подготовки не выбраны!';
				return $this->view->generate('application-edit.php', 'main.php', 'Заявление', $this->form);
			}
		} else {
			return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р заявления!');
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
