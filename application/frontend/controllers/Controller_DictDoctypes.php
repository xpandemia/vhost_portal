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
     * Prints passports JSON.
     *
     * @return void
     */
	public function actionPassportsJSON()
	{
		echo $this->model->getPassportsJSON();
	}

	/**
     * Prints russian passports JSON.
     *
     * @return void
     */
	public function actionPassportsRussianJSON()
	{
		echo $this->model->getPassportsRussianJSON();
	}

	/**
     * Prints foreign passports JSON.
     *
     * @return void
     */
	public function actionPassportsForeignJSON()
	{
		echo $this->model->getPassportsForeignJSON();
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
