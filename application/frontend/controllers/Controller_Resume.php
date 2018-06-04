<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use tinyframe\core\View as View;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Calc_Helper as Calc_Helper;
use common\models\Model_Contacts as Model_Contacts;
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
		} else {
			if ($this->resume->save()) {
				$row = $this->resume->getByUser();
					$contacts = new Model_Contacts();
					$contacts->id_user = $row['id_user'];
					$contacts->id_resume = $row['id'];
					$contacts->type = (int) $contacts::TYPE_EMAIL;
					$contacts->contact = $_SESSION[APP_CODE]['user_email'];
						if ($contacts->save()) {
						$row = $this->resume->getByUser();
						$this->form = $this->model->setForm($this->model->rules(), $row);
						$this->form['id'] = $row['id'];
						$this->form['status'] = $row['status'];
					} else {
						$this->form['error_msg'] = 'Ошибка при создании анкеты!';
						Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', $this->form);
					}
			} else {
				$this->form['error_msg'] = 'Ошибка при создании анкеты!';
				Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', $this->form);
			}
		}
		$this->form = $this->model->setAddressReg($this->form);
		$this->form = $this->model->setAddressRes($this->form);
		$this->form = $this->model->setForeignLangs($this->form);
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
		$this->form = $this->model->resetForeignLangs($this->form);
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
		$this->form = $this->model->getForeignLangs($this->form);
			$row = $this->resume->getByUser();
			if ($row) {
				$this->form['id'] = $row['id'];
				$this->form['status'] = $row['status'];
			} else {
				Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', null, 'Ошибка при получении анкеты!');
			}
			($this->form['status'] == $this->resume::STATUS_CREATED) ? $this->form['personal_vis'] = true : $this->form['personal_vis'] = false;
		$this->form = $this->model->validateForm($this->form, $this->model->rules());
		$this->form = $this->model->validateFormAdvanced($this->form);
		$this->form = $this->model->validateAgreement($this->form);
		$this->form = $this->model->validatePassport($this->form);
		$this->form = $this->model->validatePassportOld($this->form);
		if ($this->form['validate']) {
			$this->form = $this->model->check($this->form);
			if (!$this->form['error_msg']) {
				$this->form['success_msg'] = 'Анкета успешно сохранена.';
			}
		} else {
			$this->form['error_msg'] = '<strong>Ошибка при проверке данных анкеты!</strong> Пожалуйста, проверьте все поля ввода.';
			if ($this->form['status'] != $this->resume::STATUS_CREATED) {
				$this->form = $this->model->setAddressReg($this->form);
				$this->form = $this->model->setAddressRes($this->form);
			}
			$this->form = $this->model->unsetScans($this->form);
		}
		$this->form = $this->model->setForeignLangs($this->form);
		return $this->view->generate('resume.php', 'form.php', RESUME['hdr'], $this->form);
	}

	/**
     * Sends resume.
     *
     * @return mixed
     */
	public function actionSend()
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
			$this->form = $this->model->setForeignLangs($this->form);
		} else {
			Basic_Helper::redirect(APP_NAME, 202, 'Main', 'Index', null, 'Ошибка при получении анкеты!');
		}
		$this->form = $this->model->send($this->form);
		return $this->view->generate('resume.php', 'form.php', RESUME['hdr'], $this->form);
	}

	public function __destruct()
	{
		$this->model = null;
		$this->view = null;
		$this->resume = null;
	}
}
