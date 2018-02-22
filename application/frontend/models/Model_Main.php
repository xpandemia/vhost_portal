<?php

namespace frontend\models;

include_once ROOT_DIR.'/application/frontend/models/Model_Resume.php';

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_User as Model_User;
use common\models\Model_Personal as Model_Personal;
use frontend\models\Model_Resume as Model_Resume;

class Model_Main extends Model
{
	/*
		Main processing
	*/

	/**
     * Makes resume view.
     *
     * @return nothing
     */
	public function resume()
	{
		$personal = new Model_Personal();
		$personal->id_user = $_SESSION['user']['id'];
		$row = $personal->getPersonalByUser();
		$resume = new Model_Resume();
		$resume->setForm($resume->form, $resume->rules(), $row);
		if ($row) {
			$_SESSION['resume']['is_edit'] = true;
		} else {
			$_SESSION['resume']['is_edit'] = false;
		}
		Basic_Helper::redirect(RESUME_HDR, 202, BEHAVIOR.'/Resume', 'Index');
	}

	/**
     * Stops session user.
     *
     * @return nothing
     */
	public function logout()
	{
		$user = new Model_User();
		$user->unsetUser();
		session_destroy();
		session_start();
		Basic_Helper::redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
	}
}
