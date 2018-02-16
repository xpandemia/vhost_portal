<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

class Controller_Main extends Controller
{
	/*
		Main actions
	*/

	/**
     * Displays main page.
     *
     * @return mixed
     */
	function actionIndex()
	{
		$this->view->generate('main.php', 'main.php', APP_NAME);
	}

	/**
     * Logs user out.
     *
     * @return mixed
     */
	public function actionLogout()
	{
		unset($_SESSION['user_id']);
		unset($_SESSION['user_role']);
		unset($_SESSION['user_logon']);
		session_destroy();
		session_start();
		$basic_helper = new Basic_Helper;
		return $basic_helper->redirect(LOGIN_HDR, 202, BEHAVIOR.'/Login', 'Index');
	}
}
