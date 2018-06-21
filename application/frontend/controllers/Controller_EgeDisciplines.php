<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use frontend\models\Model_EgeDisciplines as Model_EgeDisciplines;

class Controller_EgeDisciplines extends Controller
{
	/*
		Ege disciplines actions
	*/

	public $form;

	public function __construct()
	{
		$this->model = new Model_EgeDisciplines();
		$this->view = new View();
	}

	/**
     * Displays ege page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$this->form['pid'] = htmlspecialchars($_GET['pid']);
			$this->form['error_msg'] = null;
			$this->form['success_msg'] = null;
			return $this->view->generate('ege-disciplines.php', 'main.php', 'Дисциплины ЕГЭ', $this->form);
		} else {
			return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index', null, 'Отсутствует идент-р результатов ЕГЭ!');
		}
	}

	/**
     * Resets ege disciplines page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
			$this->form['pid'] = htmlspecialchars($_GET['pid']);
			return $this->view->generate('ege-disciplines-add.php', 'form.php', EGE_DSP['hdr'], $this->form);
		} else {
			return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index', null, 'Отсутствует идент-р результатов ЕГЭ!');
		}
	}

	/**
     * Displays ege disciplines add page.
     *
     * @return mixed
     */
	public function actionAdd()
	{
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$this->form = $this->model->setForm($this->model->rules(), null);
			$this->form['pid'] = htmlspecialchars($_GET['pid']);
			return $this->view->generate('ege-disciplines-add.php', 'form.php', EGE_DSP['hdr'], $this->form);
		} else {
			return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index', null, 'Отсутствует идент-р результатов ЕГЭ!');
		}
	}

	/**
     * Shows ege discipline.
     *
     * @return mixed
     */
	public function actionEdit()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index', null, 'Отсутствует идент-р дисциплины ЕГЭ!');
		}
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$pid = htmlspecialchars($_GET['pid']);
		} else {
			return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index', null, 'Отсутствует идент-р результатов ЕГЭ!');
		}
		$this->form = $this->model->setForm($this->model->rules(), $this->model->get($id));
		$this->form['id'] = $id;
		$this->form['pid'] = $pid;
		return $this->view->generate('ege-disciplines-add.php', 'form.php', EGE_DSP['hdr'], $this->form);
	}

	/**
     * Calls to ege discipline delete confirm.
     *
     * @return mixed
     */
	public function actionDeleteConfirm()
	{
		return $this->actionDelDocConfirm($this->form, $_GET);
	}

	/**
     * Deletes ege discipline.
     *
     * @return mixed
     */
	public function actionDelete()
	{
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['pid'] = htmlspecialchars($_POST['pid']);
		$this->form['hdr'] = htmlspecialchars($_POST['hdr']);
		$this->form['ctr'] = htmlspecialchars($_POST['ctr']);
		if ($this->model->delete($this->form)) {
			return Basic_Helper::redirect('Дисциплины ЕГЭ', 200, EGE_DSP['ctr'], 'Index/?pid='.$this->form['pid']);
		} else {
			$this->form['error_msg'] = 'Ошибка удаления записи '.$this->form['ctr'].'! Свяжитесь с администратором.';
			return $this->view->generate('delete-confirm.php', 'form.php', 'Удаление записи '.$this->form['ctr'], $this->form);
		}
	}

	/**
     * Saves ege discipline.
     *
     * @return mixed
     */
	public function actionSave()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST);
		$this->form['id'] = htmlspecialchars($_POST['id']);
		$this->form['pid'] = htmlspecialchars($_POST['pid']);
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				if (!$_SESSION[APP_CODE]['error_msg']) {
					return Basic_Helper::redirect('Дисциплины ЕГЭ', 200, EGE_DSP['ctr'], 'Index/?pid='.$this->form['pid'], $_SESSION[APP_CODE]['success_msg']);
				} else {
					return Basic_Helper::redirect('Дисциплины ЕГЭ', 200, EGE_DSP['ctr'], 'Index/?pid='.$this->form['pid'], null, $_SESSION[APP_CODE]['error_msg']);
				}
			}
		} else {
			if (empty($this->form['error_msg'])) {
				$this->form['error_msg'] = '<strong>Ошибка при проверке данных дисциплин ЕГЭ!</strong> Пожалуйста, проверьте все поля ввода.';
			}
			return $this->view->generate('ege-disciplines-add.php', 'form.php', EGE_DSP['hdr'], $this->form);
		}
	}

	/**
     * Cancels ege disciplines.
     *
     * @return mixed
     */
	public function actionCancel()
	{
		if (isset($_GET['pid']) && !empty($_GET['pid'])) {
			$pid = htmlspecialchars($_GET['pid']);
			return Basic_Helper::redirect('Дисциплины ЕГЭ', 200, EGE_DSP['ctr'], 'Index/?pid='.$pid);
		} else {
			return Basic_Helper::redirect(EGE['hdr'], 202, EGE['ctr'], 'Index', null, 'Отсутствует идент-р результатов ЕГЭ!');
		}
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
	}
}
