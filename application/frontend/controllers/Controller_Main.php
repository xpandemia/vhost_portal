<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_Personal as Personal;
use common\models\Model_Resume as Resume;
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
		// check user
		if (isset($_SESSION[APP_CODE]['user_name'])) {
			if (!isset($_SESSION[APP_CODE]['user_id'])) {
				if (!$this->model->checkUser()) {
					exit("<p><strong>Ошибка!</strong> Авторизация не выполнена.</p>");
				}
			}
		} else {
			return Basic_Helper::redirectHome();
		}
		// check personal
		$personal = new Personal();
		$personal_row = $personal->getByUser();
		if ($personal_row && !empty($personal_row['code1s'])) {
			$resume = new Resume();
			$resume_row = $resume->getStatusByUser();
			if ($resume_row['status'] != $resume::STATUS_APPROVED) {
				$resume->id = $personal_row['id_resume'];
				$resume->status = $resume::STATUS_APPROVED;
				$resume->changeStatus();
			}
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
     * Returns home.
     *
     * @return mixed
     */
	function actionHome()
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
     * Calls to individual achievments.
     *
     * @return mixed
     */
	function actionIndAchievs()
	{
		return Basic_Helper::redirect(IND_ACHIEVS['hdr'], 202, IND_ACHIEVS['ctr'], 'Index');
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
