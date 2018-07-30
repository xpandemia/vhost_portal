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
			if (isset($_GET['page']) && !empty($_GET['page'])) {
				$this->form['page'] = htmlspecialchars($_GET['page']);
			} else {
				$this->form['page'] = 1;
			}
			if (isset($_GET['step']) && !empty($_GET['step'])) {
				$this->form['step'] = htmlspecialchars($_GET['step']);
			} else {
				$this->form['step'] = 'next';
			}
		} else {
			$this->form = $this->model->paginationStart();
			if (empty($this->form)) {
				return Basic_Helper::redirect(APP_NAME, 202, APP['ctr'], 'Index', null, 'Не могу открыть раздел "Пользователи"!');
			}
		}
		return $this->view->generate('user.php', 'main.php', 'Пользователи',  $this->form);
	}

	/**
     * Displays user add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (!isset($this->form)) {
			$this->form = $this->model->setForm($this->model->rules(), null);
		}
		return $this->view->generate('user-add.php', 'form.php', USER['hdr'], $this->form);
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
			return Basic_Helper::redirect(APP_NAME, 202, USER['ctr'], 'Index', null, 'Отсутствует идент-р пользователя!');
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		$this->form['id'] = $id;
		return $this->view->generate('user-add.php', 'form.php', USER['hdr'], $this->form);
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
			return Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], 'Index/?id='.$this->form['id'], $this->form['success_msg'], $this->form['error_msg']);
		} else {
			return Basic_Helper::redirect('Пользователи', 200, USER['ctr'], 'Index', null, 'Ошибка удаления пользователя!');
		}
	}

	/**
     * Saves user.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form['id'] = $id = htmlspecialchars($_POST['id']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				return Basic_Helper::redirect('Пользователи', 200, USER['ctr'], 'Index/?id='.$this->form['id'], $this->form['success_msg']);
			}
		}
		return $this->view->generate('user-add.php', 'form.php', USER['hdr'], $this->form);
	}

	/**
     * Searches for user.
     *
     * @return mixed
     */
	public function actionSearch()
	{
		if (isset($_POST['search']) && !empty($_POST['search'])) {
			$this->form = $this->model->search(htmlspecialchars($_POST['search']));
			if (empty($this->form)) {
				return Basic_Helper::redirect(APP_NAME, 202, USER['ctr'], 'Index', null, 'Ничего не найдено!');
			} else {
				return $this->view->generate('user.php', 'main.php', 'Пользователи',  $this->form);
			}
		} else {
			return Basic_Helper::redirect(APP_NAME, 202, USER['ctr'], 'Index', null, 'Не указан критерий поиска!');
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
