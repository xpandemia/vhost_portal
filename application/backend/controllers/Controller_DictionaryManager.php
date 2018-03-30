<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
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
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('dictionary-manager.php', 'main.php', DICT_MANAGER['hdr'],  $this->form);
	}

	/**
     * Renews dictionary.
     *
     * @return mixed
     */
	public function actionRenew()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->renew($this->form);
		}
		return $this->view->generate('dictionary-manager.php', 'main.php', DICT_MANAGER['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
