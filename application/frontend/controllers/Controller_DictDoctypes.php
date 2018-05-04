<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use frontend\models\Model_DictDoctypes as Model_DictDoctypes;

class Controller_DictDoctypes extends Controller
{
	/*
		Dictionary document types actions
	*/

	public $code;

	public function __construct()
	{
		$this->model = new Model_DictDoctypes();
		// code
		if (isset($_POST['code'])) {
			$this->code = htmlspecialchars($_POST['code']);
		}
		else {
			$this->code = null;
		}
	}

	/**
     * Prints education documents by education type code JSON.
     *
     * @return void
     */
	public function actionDiplomasByEducCodeJSON()
	{
		echo $this->model->getDiplomasByEducCodeJSON($this->code);
	}

	public function __destruct()
	{
		$this->model = null;
	}
}
