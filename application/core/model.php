<?php

namespace tinyframe\core;

use tinyframe\core\exceptions\UploadException as UploadException;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\Files_Helper as Files_Helper;
use common\models\Model_Scans as Model_Scans;

class Model
{
	/*
		BASE Model

		Models usually include select data methods:
			> methods of native libraries methods PGSQL or MYSQL;
			> methods of data astraction libraries. PEAR MDB2 for example;
			> ORM methods;
			> methods for NoSQL;
			> and others.
	*/

	/**
     * Gets form data.
     *
     * @return array
     */
	public function getForm($rules, $post, $files = null)
	{
		if (is_array($rules) && is_array($post)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				switch ($rules[$field_name]['type']) {
					case 'checkbox':
						if (isset($post[$field_name])) {
							$form[$field_name] = 'checked';
						} else {
							$form[$field_name] = null;
						}
						break;
					case 'file':
						if (isset($post[$field_name.'_id'])) {
							$scans = new Model_Scans();
							$scans->id = $post[$field_name.'_id'];
							$scans_row = $scans->get();
								$form[$field_name.'_id'] = $scans_row['id'];
								$form[$field_name] = $scans_row['file_data'];
								$form[$field_name.'_name'] = $scans_row['file_name'];
								$form[$field_name.'_type'] = $scans_row['file_type'];
								$form[$field_name.'_size'] = $scans_row['file_size'];
						} else {
							if (is_array($files)) {
								if (!empty($_FILES[$field_name]['name'])) {
									if (!is_array($_FILES[$field_name]['error'])) {
										if ($_FILES[$field_name]['error'] === UPLOAD_ERR_OK) {
											$uploadfile = FILES_TEMP.$field_name.'_'.session_id().'.'.Files_Helper::getExtension($_FILES[$field_name]['name']);
											if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $uploadfile)) {
												$form[$field_name] = $uploadfile;
												$form[$field_name.'_name'] = $_FILES[$field_name]['name'];
												$form[$field_name.'_type'] = $_FILES[$field_name]['type'];
												$form[$field_name.'_size'] = $_FILES[$field_name]['size'];
											} else {
												throw new \RuntimeException('Возможная атака с помощью файловой загрузки!');
											}
										} else {
											throw new UploadException($_FILES[$field_name]['error']);
										}
									} else {
										throw new \RuntimeException('Множественная загрузка файлов!');
									}
								}
							} else {
								throw new \InvalidArgumentException('На входе функции Model.getForm отсутствует массив файлов!');
							}
						}
						break;
					default:
						if (isset($post[$field_name])) {
							$form[$field_name] = htmlspecialchars($post[$field_name]);
						} else {
							$form[$field_name] = null;
						}
				}
				$form[$field_name.'_cls'] = $rules[$field_name]['class'];
				$form[$field_name.'_scs'] = $rules[$field_name]['success'];
				$form[$field_name.'_err'] = null;
				$form[$field_name.'_vis'] = true;
			}
			return $form;
		} else {
			throw new \InvalidArgumentException('На входе функции Model.getForm могут быть только массивы!');
		}
	}

	/**
     * Validates form data.
     *
     * @return arrau
     */
	public function validateForm($form, $rules)
	{
		if (is_array($form) && is_array($rules)) {
			$form = $this->resetForm(false, $form, $rules);
			$form_helper = new Form_Helper();
			return $form_helper->validate($form, $rules);
		} else {
			throw new \InvalidArgumentException('На входе функции Model.validateForm могут быть только массивы!');
		}
	}

	/**
     * Sets form data.
     *
     * @return array
     */
	public function setForm($rules, $row)
	{
		if (is_array($rules)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				if ($row && isset($row[$field_name])) {
					switch ($rules[$field_name]['type']) {
						case 'date':
							$form[$field_name] = date($rules[$field_name]['format'], strtotime($row[$field_name]));
							break;
						case 'file':
							$form[$field_name.'_id'] = $row[$field_name.'_id'];
							$form[$field_name] = $row[$field_name];
							$form[$field_name.'_type'] = $row[$field_name.'_type'];
							break;
						case 'checkbox':
							if ($row[$field_name] == 1) {
								$form[$field_name] = 'checked';
							} else {
								$form[$field_name] = null;
							}
							break;
						default:
							$form[$field_name] = $row[$field_name];
					}
				} else {
					$form[$field_name] = null;
				}
				$form[$field_name.'_cls'] = $rules[$field_name]['class'];
				$form[$field_name.'_scs'] = $rules[$field_name]['success'];
				$form[$field_name.'_err'] = null;
				$form[$field_name.'_vis'] = true;
			}
			$form['success_msg'] = null;
			$form['error_msg'] = null;
			return $form;
		} else {
			throw new \InvalidArgumentException('На входе функции Model.setForm должен быть массив правил!');
		}
	}

	/**
     * Resets form data.
     *
     * @return array
     */
	public function resetForm($vars, $form, $rules)
	{
		if (is_array($rules)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				if ($vars === true) {
					switch ($rules[$field_name]['type']) {
						case 'file':
							$form[$field_name.'_id'] = null;
							$form[$field_name] = null;
							$form[$field_name.'_type'] = null;
							break;
						default:
							$form[$field_name] = null;
					}
				}
				$form[$field_name.'_cls'] = $rules[$field_name]['class'];
				$form[$field_name.'_scs'] = $rules[$field_name]['success'];
				$form[$field_name.'_err'] = null;
			}
			$form['success_msg'] = null;
			$form['error_msg'] = null;
			return $form;
		} else {
			throw new \InvalidArgumentException('На входе функции Model.resetForm должен быть массив правил!');
		}
	}

	/**
     * Sets form field error.
     *
     * @return array
     */
	public function setFormErrorField($form, $field, $msg, $global = 0)
	{
		$form['success_msg'] = null;
		if ($global == 1) {
			$form['error_msg'] = $msg;
		} else {
			$form['error_msg'] = null;
		}
		$form[$field.'_err'] = $msg;
		$form[$field.'_scs'] = null;
		$form[$field.'_cls'] = $form[$field.'_cls'].' is-invalid';
		$form['validate'] = false;
		return $form;
	}

	/**
     * Sets form file error.
     *
     * @return array
     */
	public function setFormErrorFile($form, $file, $msg)
	{
		$form[$file.'_err'] = $msg;
		$form[$file.'_scs'] = null;
		$form['validate'] = false;
		return $form;
	}

	/**
     * Sets form error.
     *
     * @return array
     */
	public function setFormError($form, $msg)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = $msg;
		$form['validate'] = false;
		return $form;
	}
}
