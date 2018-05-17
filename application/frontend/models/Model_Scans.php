<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_Docs as Model_Docs;
use common\models\Model_Scans as Scans;

class Model_Scans extends Model
{
	/*
		Scans processing
	*/

	/**
     * Creates scan rules.
     *
     * @return array
     */
	public static function createRules($doc_code)
	{
		$scans = new Model_DictScans();
		$scans->doc_code = $doc_code;
		$scans_arr = $scans->getByDocument();
		if ($scans_arr) {
			foreach ($scans_arr as $scans_row) {
				if ($scans_row['required'] == 1) {
					$rules[$scans_row['scan_code']] = [
													'type' => 'file',
													'class' => 'form-control',
													'required' => ['default' => '', 'msg' => 'Скан-копия "'.ucfirst($scans_row['scan_name']).'" обязательна для заполнения!'],
													'size' => ['value' => FILES_SIZE['value'], 'msg' => 'Размер скан-копии "'.ucfirst($scans_row['scan_name']).'" превышает '.FILES_SIZE['value'].' '.FILES_SIZE['size'].' !'],
													'ext' => ['value' => FILES_EXT_SCANS, 'msg' => 'Недопустимый тип скан-копии "'.ucfirst($scans_row['scan_name']).'"!'],
													'success' => 'Скан-копия "'.ucfirst($scans_row['scan_name']).'" заполнена верно.'
													];
				} else {
					$rules[$scans_row['scan_code']] = [
													'type' => 'file',
													'class' => 'form-control',
													'size' => ['value' => FILES_SIZE['value'], 'msg' => 'Размер скан-копии "'.ucfirst($scans_row['scan_name']).'" превышает '.FILES_SIZE['value'].' '.FILES_SIZE['size'].' !'],
													'ext' => ['value' => FILES_EXT_SCANS, 'msg' => 'Недопустимый тип скан-копии "'.ucfirst($scans_row['scan_name']).'"!'],
													'success' => 'Скан-копия "'.ucfirst($scans_row['scan_name']).'" заполнена верно.'
													];
				}
			}
		}
		return $rules;
	}

	/**
     * Gets scan from database.
     *
     * @return boolean
     */
	public function get($form)
	{
		$scans = new Scans();
		$scans->id = $form['id'];
		return array_merge($form, $scans->get());
	}

	/**
     * Pushes scan.
     *
     * @return array
     */
	public static function push($doc_code, $scan_code, $form)
	{
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = $doc_code;
		$dict_scans->scan_code = $scan_code;
		$dict_scans_row = $dict_scans->getByCode();
		if ($dict_scans_row) {
			if (!empty($form[$dict_scans_row['scan_code'].'_name']) && empty($form[$dict_scans_row['scan_code'].'_id'])) {
				if (isset($form['id']) && !empty($form['id'])) {
					$scans = new Scans();
					$scans->id_user = $_SESSION[APP_CODE]['user_id'];
						$docs = new Model_Docs();
						$docs->doc_code = $doc_code;
						$docs_row = $docs->getByCode();
					$scans->id_doc = (int) $docs_row['id'];
					$scans->id_row = (int) $form['id'];
					$scans->id_scans = (int) $dict_scans_row['id'];
					$scans->file_data = fopen($form[$scan_code], 'rb');
					$scans->file_name = $form[$scan_code.'_name'];
					$scans->file_type = $form[$scan_code.'_type'];
					$scans->file_size = (int) $form[$scan_code.'_size'];
					// save
					if ($scans->save() > 0) {
						$form['error_msg'] = null;
					} else {
						$form['success_msg'] = null;
						$form['error_msg'] = 'Ошибка при сохранении скан-копии "'.$dict_scans_row['scan_name'].'"!';
						return $form;
					}
					fclose($scans->file_data);
					unlink($form[$dict_scans_row['scan_code']]);
				} else {
					$form['success_msg'] = null;
					$form['error_msg'] = 'Не задан идент-р документа скан-копии "'.$dict_scans_row['scan_name'].'"!';
				}
			}
		} else {
			$form['success_msg'] = null;
			$form['error_msg'] = 'Ошибка при сохранении скан-копии "'.$dict_scans_row['scan_name'].'"!';
		}
		return $form;
	}

	/**
     * Unpushes scan.
     *
     * @return array
     */
	public static function unpush($doc_code, $scan_code, $form)
	{
		$scans = new Scans();
		$scans_row = $scans->getByDocScan($doc_code, $form['id'], $scan_code);
		if ($scans_row) {
			$scans->id = $scans_row['id'];
			if ($scans->clear() > 0) {
				$form[$scan_code.'_id'] = null;
				$form[$scan_code] = null;
				$form[$scan_code.'_id'] = null;
				$form[$scan_code.'_name'] = null;
				$form[$scan_code.'_type'] = null;
				$form[$scan_code.'_size'] = null;
				$form[$scan_code.'_scs'] = null;
				$form[$scan_code.'_err'] = null;
			}
		}
		return $form;
	}

	/**
     * Unsets scans.
     *
     * @return array
     */
	public static function unsets($doc_code, $form)
	{
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = $doc_code;
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			$docs = new Model_Docs();
			$docs->doc_code = $doc_code;
			$docs_row = $docs->getByCode();
			$scans = new Scans();
			foreach ($dict_scans_arr as $dict_scans_row) {
				$scans->id_doc = $docs_row['id'];
				$scans->id_scans = $dict_scans_row['id'];
				if (!$scans->getByDoc()) {
					$form[$dict_scans_row['scan_code'].'_id'] = null;
					$form[$dict_scans_row['scan_code']] = null;
					$form[$dict_scans_row['scan_code'].'_id'] = null;
					$form[$dict_scans_row['scan_code'].'_name'] = null;
					$form[$dict_scans_row['scan_code'].'_type'] = null;
					$form[$dict_scans_row['scan_code'].'_size'] = null;
					$form[$dict_scans_row['scan_code'].'_scs'] = null;
					if (empty($form[$dict_scans_row['scan_code'].'_err']) && $dict_scans_row['required'] == 1) {
						$form[$dict_scans_row['scan_code'].'_err'] = 'Скан-копия "'.ucfirst($dict_scans_row['scan_name']).'" обязательна для заполнения!';
					}
				}
			}
		}
		return $form;
	}

	/**
     * Deletes scan from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$scans = new Scans();
		$scans->id = $form['id'];
		if ($scans->clear() > 0) {
			return true;
		} else {
			return false;
		}
	}
}
