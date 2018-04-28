<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use frontend\models\Model_AdmissionCampaign as Model_AdmissionCampaign;

class Controller_AdmissionCampaign extends Controller
{
	/*
		Admission campaigns actions
	*/

	public $code;

	public function __construct()
	{
		$this->model = new Model_AdmissionCampaign();
		// code
		if (isset($_POST['code'])) {
			$this->code = htmlspecialchars($_POST['code']);
		}
		else {
			$this->code = null;
		}
	}

	/**
     * Prints admission campaigns by university JSON.
     *
     * @return void
     */
	public function actionAdmCampByUniversityJSON()
	{
		echo $this->model->getByUniversityJSON($this->code);
	}

	public function __destruct()
	{
		$this->model = null;
	}
}
