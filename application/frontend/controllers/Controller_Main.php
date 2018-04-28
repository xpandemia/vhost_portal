<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_User as Model_User;
use frontend\models\Model_Main as Model_Main;

use phpCAS;

class Controller_Main extends Controller
{
	/*
		Main actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_Main();
		$this->view = new View();
	}

	/**
     * Displays main page.
     *
     * @return mixed
     */
	function actionIndex()
	{
		if (isset($_SESSION[APP_CODE]['user_name'])) {
			if (!isset($_SESSION[APP_CODE]['user_id'])) {
				if ($this->model->checkUser()) {
					Basic_Helper::msgReset();
					return $this->view->generate('main.php', 'main.php', APP_NAME);
				} else {
					exit("<p><strong>Ошибка!</strong> Авторизация не выполнена.</p>");
				}
			} else {
				Basic_Helper::msgReset();
				return $this->view->generate('main.php', 'main.php', APP_NAME);
			}
		} else {
			return Basic_Helper::redirectHome();
		}
	}

	/**
     * Calls to resume.
     *
     * @return mixed
     */
	function actionResume()
	{
		return Basic_Helper::redirect(RESUME['hdr'], 202, RESUME['ctr'], 'Index');
	}

	/**
     * Calls to education docs.
     *
     * @return mixed
     */
	function actionDocseduc()
	{
		return Basic_Helper::redirect('Документы об образовании', 202, DOCS_EDUC['ctr'], 'Index');
	}

	/**
     * Calls to ege.
     *
     * @return mixed
     */
	function actionEge()
	{
		return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index');
	}

	/**
     * Calls to application.
     *
     * @return mixed
     */
	function actionApplication()
	{
		return Basic_Helper::redirect('Заявления', 202, APP['ctr'], 'Index');
	}

	/**
     * Logs user out.
     *
     * @return mixed
     */
	public function actionLogout()
	{
		$this->model->logout();
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
