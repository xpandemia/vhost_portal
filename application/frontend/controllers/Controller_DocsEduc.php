<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use frontend\models\Model_DocsEduc as Model_DocsEduc;

class Controller_DocsEduc extends Controller
{
	/*
		Education documents actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_DocsEduc();
		$this->view = new View();
	}

	/**
     * Displays education documents page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('docs-educ.php', 'main.php', LOGIN['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
