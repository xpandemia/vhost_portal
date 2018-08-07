<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_DictionaryManager as Model_DictionaryManager;

class Controller_DictionaryManager extends Controller
{
	/*
		Dictionary manager actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_DictionaryManager();
		$this->view = new View();
	}

	/**
     * Displays dictionary manager page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		return $this->view->generate('dictionary-manager.php', 'main.php', DICT_MANAGER['hdr']);
	}

	/**
     * Displays dictionary manager sync page.
     *
     * @return mixed
     */
	public function actionSync()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules_sync(), null);
		}
		return $this->view->generate('dictionary-manager-sync.php', 'form.php', DICT_MANAGER['hdr'],  $this->form);
	}

	/**
     * Renews dictionary.
     *
     * @return mixed
     */
	public function actionRenew()
	{
		$this->form = $this->model->getForm($this->model->rules_sync(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules_sync());
		if ($this->form['validate']) {
			$this->form = $this->model->renew($this->form);
		}
		return $this->view->generate('dictionary-manager-sync.php', 'form.php', DICT_MANAGER['hdr'], $this->form);
	}

	/**
     * Displays dictionary manager add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules_add(), null);
		}
		return $this->view->generate('dictionary-manager-add.php', 'form.php', DICT_MANAGER['hdr'], $this->form);
	}

	/**
     * Shows dictionary log.
     *
     * @return mixed
     */
	public function actionLog()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$this->form['id'] = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, DICT_MANAGER['ctr'], 'Index', null, 'Отсутствует идент-р справочника!');
		}
		Basic_Helper::msgReset();
		return $this->view->generate('dictionary-manager-log.php', 'form.php', DICT_MANAGER['hdr'], $this->form);
	}

	/**
     * Shows dictionary.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, DICT_MANAGER['ctr'], 'Index', null, 'Отсутствует идент-р справочника!');
		}
		$user_row = $this->model->get($id);
		$this->form = $this->model->setForm($this->model->rules_add(), $user_row);
		$this->form['id'] = $id;
		Basic_Helper::msgReset();
		return $this->view->generate('dictionary-manager-add.php', 'form.php', DICT_MANAGER['hdr'], $this->form);
	}

	/**
     * Calls to dictionary delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes dictionary.
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
			return Basic_Helper::redirect(DICT_MANAGER['hdr'], 200, DICT_MANAGER['ctr'], 'Index', null, 'Ошибка удаления справочника!');
		}
	}

	/**
     * Saves dictionary.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules_add(), $_POST);
		$this->form['id'] = $id = htmlspecialchars($_POST['id']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules_add());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect(DICT_MANAGER['hdr'], 200, DICT_MANAGER['ctr'], 'Index', $this->form['success_msg']);
			}
		}
		return $this->view->generate('dictionary-manager-add.php', 'form.php', DICT_MANAGER['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
