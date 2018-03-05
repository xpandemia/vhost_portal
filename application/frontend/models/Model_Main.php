<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_User as Model_User;

class Model_Main extends Model
{
	/*
		Main processing
	*/

	/**
     * Makes resume view.
     *
     * @return void
     */
	public function resume() : void
	{
		Basic_Helper::redirect(RESUME_HDR, 202, BEHAVIOR.'/Resume', 'Index');
	}

	/**
     * Stops session user.
     *
     * @return void
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
