<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_Scans as Model_Scans;

class Controller_Scans extends Controller
{
	/*
		Scans actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_Scans();
		$this->view = new View();
	}

	/**
     * Shows scan.
     *
     * @return mixed
     */
	public function actionShow()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$this->form['id'] = htmlspecialchars($_GET['id']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р скан-копии!</p>");
		}
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$this->form['pid'] = htmlspecialchars($_GET['pid']);
		}
		if (isset($_GET['ctr']) && !empty($_GET['ctr'])) {
			$this->form['ctr'] = htmlspecialchars($_GET['ctr']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует тип скан-копии!</p>");
		}
		if (isset($_GET['act']) && !empty($_GET['act'])) {
			$this->form['act'] = htmlspecialchars($_GET['act']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует действие скан-копии!</p>");
		}
		return $this->view->generate('scans-show.php', 'form.php', 'Просмотр скан-копии', $this->model->get($this->form));
	}

	/**
     * Displays scan delete confirmation page.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$this->form['id'] = htmlspecialchars($_GET['id']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует идент-р скан-копии!</p>");
		}
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$this->form['pid'] = htmlspecialchars($_GET['pid']);
		}
		if (isset($_GET['hdr']) && !empty($_GET['hdr'])) {
			$this->form['hdr'] = htmlspecialchars($_GET['hdr']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует заголовок скан-копии!</p>");
		}
		if (isset($_GET['ctr']) && !empty($_GET['ctr'])) {
			$this->form['ctr'] = htmlspecialchars($_GET['ctr']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует тип скан-копии!</p>");
		}
		if (isset($_GET['act']) && !empty($_GET['act'])) {
			$this->form['act'] = htmlspecialchars($_GET['act']);
		} else {
			exit("<p><strong>Ошибка!</strong> Отсутствует действие скан-копии!</p>");
		}
		$this->form['error_msg'] = null;
		return $this->view->generate('scans-delete.php', 'form.php', 'Удаление скан-копии', $this->form);
	}

	/**
     * Deletes scan.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['pid'] = htmlspecialchars($_POST['pid']);
		$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
		$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
		$this->form['act'] = htmlspecialchars($_POST['act']);
		if ($this->model->delete($this->form)) {
			if (empty($this->form['pid'])) {
				Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], $this->form['act']);
			} else {
				Basic_Helper::redirect($this->form['hdr'], 200, $this->form['ctr'], $this->form['act'].'/?id='.$this->form['pid']);
			}
		} else {
			$this->form['error_msg'] = 'Ошибка удаления скан-копии! Свяжитесь с администратором.';
			return $this->view->generate('scans-delete.php', 'form.php', 'Удаление скан-копии', $this->form);
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
