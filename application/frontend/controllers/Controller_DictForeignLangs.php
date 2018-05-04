<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use frontend\models\Model_DictForeignLangs as Model_DictForeignLangs;

class Controller_DictForeignLangs extends Controller
{
	/*
		Dictionary foreign languages actions
	*/

	public function __construct()
	{
		$this->model = new Model_DictForeignLangs();
	}

	/**
     * Prints foreign languages JSON.
     *
     * @return void
     */
	public function actionForeignLangsJSON()
	{
		echo $this->model->getForeignLangsJSON();
	}

	public function __destruct()
	{
		$this->model = null;
	}
}
