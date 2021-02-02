<?php

namespace backend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use backend\models\Model_User as Model_User;

class Controller_User extends Controller
{
	/*
		Users actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_User();
		$this->view = new View();
	}

	/**
     * Displays users page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$this->form['id'] = htmlspecialchars($_GET['id']);
			if (isset($_GET['step']) && !empty($_GET['step'])) {
				$this->form['step'] = htmlspecialchars($_GET['step']);
			} else {
				if ($this->model->getPageNumber($this->form['id']) === 1) {
					$this->form['step'] = 'next';
				} else {
					$this->form['step'] = 'prev';
				}
			}
		} else {
			$this->form = $this->model->paginationStart();
			if (empty($this->form)) {
				return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Не могу открыть раздел "'.USER['hdr'].'"!');
			}
		}
		return $this->view->generate('user.php', 'main.php', USER['hdr'],  $this->form);
	}

	/**
     * Displays user add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules_add(), null);
		}
		Basic_Helper::msgReset();
		return $this->view->generate('user-add.php', 'form.php', USER_ADD['hdr'], $this->form);
	}

	/**
     * Shows user.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, USER['ctr'], USER['act'], null, 'Отсутствует идент-р пользователя!');
		}
		$user_row = $this->model->get($id);
		$this->form = $this->model->setForm($this->model->rules_edit(), $user_row);
		$this->form['id'] = $id;
		$this->form['status'] = $user_row['status'];
		Basic_Helper::msgReset();
		return $this->view->generate('user-edit.php', 'form.php', USER_EDIT['hdr'], $this->form);
	}

	/**
     * Calls to user delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes user.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		if (isset($_POST['id']) && isset($_POST['hdr']) && isset($_POST['ctr'])) {
			$this->form['id'] = htmlspecialchars($_POST['id']);
			$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
			$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
			$this->form = $this->model->delete($this->form);
			return Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], USER['act'].'/?id='.$this->form['id'], $this->form['success_msg'], $this->form['error_msg']);
		} else {
			return Basic_Helper::redirect(USER['hdr'], 200, USER['ctr'], USER['act'], null, 'Ошибка удаления пользователя!');
		}
	}

	/**
     * Creates user.
     *
     * @return mixed
     */
	public function actionCreate()
	{
		$this->form = $this->model->getForm($this->model->rules_add(), $_POST);
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules_add());
		if ($this->form['validate']) {
			$this->form = $this->model->create($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect(USER['hdr'], 200, USER['ctr'], USER['act'].'/?id='.$this->form['id'], $this->form['success_msg']);
			}
		}
		Basic_Helper::msgReset();
		return $this->view->generate('user-add.php', 'form.php', USER_ADD['hdr'], $this->form);
	}

	/**
     * Changes user.
     *
     * @return mixed
     */
	public function actionChange()
	{

		$this->form = $this->model->getForm($this->model->rules_edit(), $_POST);
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['status'] = htmlspecialchars($_POST['status']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules_edit());
		if (/*$this->form['validate']*/ false) {
			$this->form = $this->model->change($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect(USER['hdr'], 200, USER['ctr'], USER['act'].'/?id='.$this->form['id'], $this->form['success_msg']);
			}
		}
		Basic_Helper::msgReset();
		return $this->view->generate('user-edit.php', 'form.php', USER_EDIT['hdr'], $this->form);
	}

	/**
     * Searches for user.
     *
     * @return mixed
     */
	public function actionSearch()
	{
		$this->form = $this->model->search($this->form, $_POST);
		if (isset($this->form['error_msg']) && !empty($this->form['error_msg'])) {
			return Basic_Helper::redirect(APP_NAME, 202, USER['ctr'], USER['act'], null, $this->form['error_msg']);
		} else {
			Basic_Helper::msgReset();
			return $this->view->generate('user.php', 'main.php', USER['hdr'],  $this->form);
		}
	}

	/**
     * Logins as user.
     *
     * @return mixed
     */
	public function actionMask()
	{
		if ($this->model->mask()) {
			Basic_Helper::redirectHome();
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
