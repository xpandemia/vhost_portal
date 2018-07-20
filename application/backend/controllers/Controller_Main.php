<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_Main as Model_Main;

class Controller_Main extends Controller
{
	/*
		Main actions
	*/

	public function __construct()
	{
		$this->model = new Model_Main();
		$this->view = new View();
		// check user
		if (isset($_SESSION[APP_CODE]['user_name'])) {
			$this->model->checkUser();
		} else {
			Basic_Helper::redirectHome();
		}
	}

	/**
     * Displays main page.
     *
     * @return mixed
     */
	function actionIndex()
	{
		Basic_Helper::msgReset();
		return $this->view->generate('main.php', 'main.php', APP_NAME);
	}

	/**
     * Calls to users.
     *
     * @return void
     */
	function actionUser() : void
	{
		Basic_Helper::redirect(USER['hdr'], 202, USER['ctr'], 'Index');
	}

	/**
     * Calls to dictionary manager.
     *
     * @return void
     */
	function actionDictManager() : void
	{
		Basic_Helper::redirect(DICT_MANAGER['hdr'], 202, DICT_MANAGER['ctr'], 'Index');
	}

	/**
     * Calls to dictionary ege.
     *
     * @return void
     */
	function actionDictEge() : void
	{
		Basic_Helper::redirect(DICT_EGE['hdr'], 202, DICT_EGE['ctr'], 'Index');
	}

	/**
     * Calls to educlevels doctypes.
     *
     * @return void
     */
	function actionEduclevelsDoctypes() : void
	{
		Basic_Helper::redirect(EDUCLEVELS_DOCTYPES['hdr'], 202, EDUCLEVELS_DOCTYPES['ctr'], 'Index');
	}

	/**
     * Calls to eductypes doctypes.
     *
     * @return void
     */
	function actionEductypesDoctypes() : void
	{
		Basic_Helper::redirect(EDUCTYPES_DOCTYPES['hdr'], 202, EDUCTYPES_DOCTYPES['ctr'], 'Index');
	}

	/**
     * Logs user out.
     *
     * @return mixed
     */
	public function actionLogout()
	{
		return $this->model->logout();
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
