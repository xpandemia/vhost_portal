<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_Scans as Model_Scans_Data;

class Model_Scans extends Model
{
	/*
		Scans processing
	*/

	/**
     * Gets scan from database.
     *
     * @return boolean
     */
	public function get($form)
	{
		$scans = new Model_Scans_Data();
		$scans->id = $form['id'];
		return array_merge($form, $scans->get());
	}

	/**
     * Deletes scan from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$scans = new Model_Scans_Data();
		$scans->id = $form['id'];
		if ($scans->clear() > 0) {
			return true;
		} else {
			return false;
		}
	}
}
