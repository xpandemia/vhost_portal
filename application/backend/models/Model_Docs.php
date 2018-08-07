<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_Docs as Docs;

class Model_Docs extends Model
{
	/*
		Documents processing
	*/

	/**
     * Dictionary ege rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'doc_code' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Код обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_CODE, 'msg' => 'Для кода можно использовать '.MSG_CODE.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 20, 'msg' => 'Слишком длинный код!'],
                                'success' => 'Код заполнен верно.'
                               ],
                'doc_name' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для наименования можно использовать '.MSG_TEXT_RUS.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинное наименование!'],
                                'success' => 'Наименование заполнено верно.'
                               ],
                'table_name' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование таблицы обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_CODE, 'msg' => 'Для наименования таблицы можно использовать '.MSG_CODE.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование таблицы!'],
                                'success' => 'Наименование таблицы заполнено верно.'
                               ]
            ];
	}

	/**
     * Gets documents from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ds = new Docs();
		$ds->id = $id;
		return $ds->get();
	}

	/**
     * Deletes documents from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$ds = new Docs();
		$ds->id = $form['id'];
		if ($ds->existsDictScans()) {
			$form['error_msg'] = 'Удалять документы, которые используются в справочнике скан-копий, нельзя!';
		} else {
			if ($ds->clear() > 0) {
				$form['success_msg'] = 'Документ № '.$ds->id.' удалён.';
			} else {
				$form['error_msg'] = 'Ошибка удаления документа № '.$ds->id.'! Свяжитесь с администратором.';
			}
		}
		return $form;
	}

	/**
     * Checks documents data.
     *
     * @return array
     */
	public function check($form)
	{
		$ds = new Docs();
		$ds->doc_code = $form['doc_code'];
		$ds->doc_name = $form['doc_name'];
		$ds->table_name = $form['table_name'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$ds->id = $form['id'];
			if ($ds->existsDictScans()) {
				$form['error_msg'] = 'Изменять документы, которые используются в справочнике скан-копий, нельзя!';
				return $form;
			} elseif ($ds->existsCodeExcept()) {
				$form['error_msg'] = 'Документ с таким кодом уже есть!';
				return $form;
			} elseif ($ds->existsNameExcept()) {
				$form['error_msg'] = 'Документ с таким наименованием уже есть!';
				return $form;
			} else {
				if ($ds->changeAll()) {
					$form['success_msg'] = 'Изменён документ № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении документа № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($ds->existsCode()) {
				$form['error_msg'] = 'Документ с таким кодом уже есть!';
				return $form;
			} elseif ($ds->existsNameExcept()) {
				$form['error_msg'] = 'Документ с таким наименованием уже есть!';
				return $form;
			} else {
				$form['id'] = $ds->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создан документ № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании документа!';
				}
			}
		}
		return $form;
	}
}
