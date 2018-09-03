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
		Basic_Helper::redirect(USER['hdr'], 202, USER['ctr'], USER['act']);
	}

	/**
     * Calls to dictionary manager.
     *
     * @return void
     */
	function actionDictionaryManager() : void
	{
		Basic_Helper::redirect(DICT_MANAGER['hdr'], 202, DICT_MANAGER['ctr'], 'Index');
	}

	/**
     * Calls to dictionary countries.
     *
     * @return void
     */
	function actionDictCountries() : void
	{
		Basic_Helper::redirect('Страны мира', 202, DICT_COUNTRIES['ctr'], 'Index');
	}

	/**
     * Calls to documents.
     *
     * @return void
     */
	function actionDocs() : void
	{
		Basic_Helper::redirect('Документы', 202, DOCS['ctr'], 'Index');
	}

	/**
     * Calls to dictionary scans.
     *
     * @return void
     */
	function actionDictScans() : void
	{
		Basic_Helper::redirect('Скан-копии', 202, DICT_SCANS['ctr'], 'Index');
	}

	/**
     * Calls to dictionary university.
     *
     * @return void
     */
	function actionDictUniversity() : void
	{
		Basic_Helper::redirect('Места поступления', 202, DICT_UNIVERSITY['ctr'], 'Index');
	}

	/**
     * Calls to dictionary ege.
     *
     * @return void
     */
	function actionDictEge() : void
	{
		Basic_Helper::redirect('Дисциплины ЕГЭ', 202, DICT_EGE['ctr'], 'Index');
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
     * Calls to langs.
     *
     * @return void
     */
	function actionLangs() : void
	{
		Basic_Helper::redirect(LANGS['hdr'], 202, LANGS['ctr'], 'Index');
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
