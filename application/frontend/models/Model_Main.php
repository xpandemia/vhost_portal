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
     * Stops session user.
     *
     * @return void
     */
	public function logout()
	{
		$user = new Model_User();
		$user->unsetUser();
		ob_end_clean(); // discard output buffer
		session_destroy();
		session_start();
		ob_start(); // start output buffer
		Basic_Helper::redirect(LOGIN['hdr'], 202, LOGIN['ctr'], 'Index');
	}
}
