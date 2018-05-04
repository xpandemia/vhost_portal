<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_AdmissionCampaign as AdmissionCampaign;

class Model_AdmissionCampaign extends Model
{
	/*
		Admission campaigns processing
	*/

	public $adm;

	public function __construct()
	{
		$this->adm = new AdmissionCampaign();
	}

	/**
     * Gets admission campaigns by university JSON.
     *
     * @return JSON
     */
	public function getByUniversityJSON($university) : string
	{
		$this->adm->university = $university;
		$adm = $this->adm->getByUniversity();
			foreach ($adm as $value) {
				$adm_json[] = ['code' => $value['code'],
								'description' => $value['description']];
			}
			return json_encode($adm_json);
	}
}
