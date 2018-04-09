<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Calc_Helper as Calc_Helper;
use common\models\Model_Resume as Model_Resume_Data;
use frontend\models\Model_Resume as Model_Resume;

class Controller_Resume extends Controller
{
	/*
		Resume actions
	*/

	public $form;
	public $resume;

	public function __construct()
	{
		$this->model = new Model_Resume();
		$this->view = new View();
		$this->resume = new Model_Resume_Data();
		$this->resume->id_user = $_SESSION[APP_CODE]['user_id'];
	}

	/**
     * Displays resume page.
     *
     * @return mixed
     */
	public function actionIndex()
	{
		$row = $this->resume->getByUser();
		if ($row) {
			$this->form = $this->model->setForm($this->model->rules(), $row);
			$this->form['id'] = $row['id'];
			$this->form['status'] = $row['status'];
			if (!empty($this->form['passport_type_old'])) {
				$this->form['passport_old_yes'] = 'checked';
			}
			$this->form = $this->model->setAddressReg($this->form);
			$this->form = $this->model->setAddressRes($this->form);
		} else {
			$this->resume->dt_created = date('Y-m-d H:i:s');
			if ($this->resume->save()) {
				$this->form = $this->model->setForm($this->model->rules(), null);
				$row = $this->resume->getByUser();
				$this->form['id'] = $row['id'];
				$this->form['status'] = $row['status'];
			} else {
				$this->form['error_msg'] = 'Ошибка при создании анкеты!';
				Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', $this->form);
			}
		}
		return $this->view->generate('resume.php', 'form.php', RESUME['hdr'], $this->form);
	}

	/**
     * Resets resume page.
     *
     * @return mixed
     */
	public function actionReset()
	{
		$this->form = $this->model->resetForm(true, $this->form, $this->model->rules());
		$this->form = $this->model->resetAddressReg($this->form);
		$this->form = $this->model->resetAddressRes($this->form);
			$row = $this->resume->getByUser();
			if ($row) {
				$this->form['id'] = $row['id'];
				$this->form['status'] = $row['status'];
			} else {
				$this->form['error_msg'] = 'Ошибка при создании анкеты!';
				Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', $this->form);
			}
		return $this->view->generate('resume.php', 'form.php', RESUME['hdr'], $this->form);
	}

	/**
     * Makes resume changes.
     *
     * @return mixed
     */
	public function actionResume()
	{
		$this->form = $this->model->getForm($this->model->rules(), $_POST, $_FILES);
		$this->form = $this->model->getAddressReg($this->form);
		$this->form = $this->model->getAddressRes($this->form);
			$row = $this->resume->getByUser();
			if ($row) {
				$this->form['id'] = $row['id'];
				$this->form['status'] = $row['status'];
			} else {
				$this->form['error_msg'] = 'Ошибка при создании анкеты!';
				Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', $this->form);
			}
			($this->form['status'] === $this->resume::STATUS_CREATED) ? $this->form['personal_vis'] = true : $this->form['personal_vis'] = false;
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		$this->form = $this->model->validateAgreement($this->form);
		$this->form = $this->model->validatePassportOld($this->form);
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				$this->form = null;
				$this->form['error_msg'] = null;
				$this->form['success_msg'] = 'Анкета успешно сохранена!';
				return $this->view->generate('main.php', 'main.php', APP_NAME, $this->form);
			}
		}
		return $this->view->generate('resume.php', 'form.php', RESUME['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
		$this->resume = null;
	}
}
