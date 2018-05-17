<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_DocsEduc as DocsEduc;
use common\models\Model_DictEductypes as Model_DictEductypes;
use common\models\Model_DictDoctypes as Model_DictDoctypes;
use common\models\Model_DictScans as Model_DictScans;

include ROOT_DIR.'/application/frontend/models/Model_Scans.php';

class Model_DocsEduc extends Model
{
	/*
		Education documents processing
	*/

	/**
     * Education documents rules.
     *
     * @return array
     */
	public function rules()
	{
		$rules = [
                'educ_type' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Вид образования обязателен для заполнения!'],
								'success' => 'Вид образования заполнен верно.'
                               ],
                'doc_type' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Тип документа обязателен для заполнения!'],
								'success' => 'Тип документа заполнен верно.'
                               ],
                'series' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB_ALL, 'msg' => 'Для серии можно использовать только цифры и буквы!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!'],
                            'success' => 'Серия заполнена верно.'
                           ],
                'numb' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Номер обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для номера можно использовать только цифры!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 20, 'msg' => 'Слишком длинный номер!'],
                            'success' => 'Номер заполнен верно.'
                           ],
                'school' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Наименование учебного заведения обязательно для заполнения!'],
                            'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования учебного заведения можно использовать только русские буквы, тире, точки, запятые, № и пробелы!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 150, 'msg' => 'Слишком длинное наименование учебного заведения!'],
                            'success' => 'Наименование учебного заведения заполнено верно.'
                           ],
				'dt_issue' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Дата выдачи обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
                                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!'],
                                'success' => 'Дата выдачи заполнена верно.'
                               ],
                'end_year' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Год окончания обязателен для заполнения!'],
	                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для года окончания можно использовать только цифры!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 4, 'msg' => 'Слишком длинный год окончания!'],
	                            'compared' => ['value' => date('Y'), 'type' => '<=', 'msg' => 'Год окончания больше текущего года!'],
	                            'success' => 'Год окончания заполнен верно.'
	                           ],
	            'change_name_flag' => [
			                            'type' => 'checkbox',
			                            'class' => 'form-check-input',
			                            'success' => ''
			                           ],
	            'change_name' => [
									'type' => 'file',
									'class' => 'form-control',
									'size' => ['value' => FILES_SIZE['value'], 'msg' => 'Размер скан-копии "Свидетельство о перемене имени" превышает '.FILES_SIZE['value'].' '.FILES_SIZE['size'].' !'],
									'ext' => ['value' => FILES_EXT_SCANS, 'msg' => 'Недопустимый тип скан-копии "Свидетельство о перемене имени"!'],
									'success' => 'Скан-копия "Свидетельство о перемене имени" заполнена верно.'
									]
	            ];
		$scans = Model_Scans::createRules('docs_educ');
		return array_merge($rules, $scans);
	}

	/**
     * Validates education document advanced.
     *
     * @return array
     */
	public function validateFormAdvanced($form)
	{
		if ($form['change_name_flag'] == 'checked' && empty($form['change_name_name'])) {
			$form = $this->setFormErrorFile($form, 'change_name', 'Скан-копия "Свидетельство о перемене имени" обязательна для заполнения!');
		}
		return $form;
	}

	/**
     * Gets education document from database.
     *
     * @return array
     */
	public function get($id)
	{
		$docs = new DocsEduc();
		$docs->id = $id;
		return $docs->get();
	}

	/**
     * Deletes education document from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$docs = new DocsEduc();
		$docs->id = $form['id'];
		if ($docs->clear() > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Unsets education document files.
     *
     * @return array
     */
	public function unsetScans($form)
	{
		// change_name
		if ($form['change_name_flag'] == 'checked') {
			if (empty($form['change_name_name'])) {
				$form['change_name_err'] = 'Скан-копия "Свидетельство о перемене имени" обязательна для заполнения!';
				$form['change_name_scs'] = null;
				$form['validate'] = false;
			}
		}
		return Model_Scans::unsets('docs_educ', $form);
	}

	/**
     * Checks education document data.
     *
     * @return array
     */
	public function check($form)
	{
		$docs = new DocsEduc();
		$docs->id_user = $_SESSION[APP_CODE]['user_id'];
			$eductype = new Model_DictEductypes();
			$eductype->code = $form['educ_type'];
			$row_eductype = $eductype->getByCode();
		$docs->id_eductype = $row_eductype['id'];
			$doctype = new Model_DictDoctypes();
			$doctype->code = $form['doc_type'];
			$row_doctype = $doctype->getByCode();
		$docs->id_doctype = $row_doctype['id'];
		$docs->series = (empty($form['series'])) ? null : $form['series'];
		$docs->numb = $form['numb'];
		$docs->school = $form['school'];
		$docs->dt_issue = date('Y-m-d', strtotime($form['dt_issue']));
		$docs->end_year = $form['end_year'];
		$row_docs = $docs->getByNumb();
		if ($row_docs) {
			$docs->id = $row_docs['id'];
			if ($docs->changeAll()) {
				$form['error_msg'] = null;
				$form['success_msg'] = 'Изменен документ об образовании № '.$row_docs['id'].'.';
				$form['id'] = $row_docs['id'];
			} else {
				$form['error_msg'] = 'Ошибка при изменении документа об образовании!';
				return $form;
			}
		} else {
			$form['id'] = $docs->save();
			if ($form['id'] > 0) {
				$form['error_msg'] = null;
				$form['success_msg'] = 'Создан новый документ об образовании.';
			} else {
				unset($form['id']);
				$form['error_msg'] = 'Ошибка при создании документа об образовании!';
				return $form;
			}
		}
		/* change_name */
		if ($form['change_name_flag'] == 'checked') {
			$form = Model_Scans::push('docs_educ', 'change_name', $form);
		}
		/* scans */
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = 'docs_educ';
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			foreach ($dict_scans_arr as $dict_scans_row) {
				$form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
				if (!empty($form['error_msg'])) {
					return $form;
				}
			}
		}
		return $form;
	}
}
