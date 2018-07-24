<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_EductypesDoctypes as Model_EductypesDoctypes;

class Controller_EductypesDoctypes extends Controller
{
	/*
		Eductypes doctypes actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_EductypesDoctypes();
		$this->view = new View();
	}

	/**
     * Displays eductypes doctypes page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('eductypes-doctypes.php', 'main.php', EDUCTYPES_DOCTYPES['hdr']);
	}

	/**
     * Displays eductypes doctypes add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('eductypes-doctypes-add.php', 'form.php', EDUCTYPES_DOCTYPES['hdr'], $this->form);
	}

	/**
     * Shows eductypes doctypes.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Отсутствует идент-р связи!');
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		$this->form['id'] = $id;
		return $this->view->generate('eductypes-doctypes-add.php', 'form.php', EDUCTYPES_DOCTYPES['hdr'], $this->form);
	}

	/**
     * Calls to eductypes doctypes delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes eductypes doctypes.
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
			$this->form['error_msg'] = 'Ошибка удаления связи документов с видами образования!';
			return $this->view->generate('delete-confirm.php', 'form.php', 'Удаление документа "'.$this->form['hdr'].'"', $this->form);
		}
	}

	/**
     * Saves eductypes doctypes.
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
				return Basic_Helper::redirect(EDUCTYPES_DOCTYPES['hdr'], 200, EDUCTYPES_DOCTYPES['ctr'], 'Index', $this->form['success_msg']);
			}
		}
		return $this->view->generate('eductypes-doctypes-add.php', 'form.php', EDUCTYPES_DOCTYPES['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
