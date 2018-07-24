<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictEge as DictEge;
use common\models\Model_DictDiscipline as DictDiscipline;

class Model_DictEge extends Model
{
	/*
		Dictionary ege processing
	*/

	/**
     * Dictionary ege rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'discipline' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Дисциплина обязательна для заполнения!'],
								'success' => 'Дисциплина выбрана.'
                               ]
            ];
	}

	/**
     * Gets dictionary ege from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ed = new DictEge();
		$ed->id = $id;
		$ed_row = $ed->get();
		return ['id' => $ed_row['id'], 'discipline' => $ed_row['code']];
	}

	/**
     * Deletes dictionary ege from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$ed = new DictEge();
		$ed->id = $form['id'];
		if ($ed->existsEge()) {
			$form['error_msg'] = 'Удалять дисциплины ЕГЭ, которые используются в результатах ЕГЭ, нельзя!';
		} else {
			if ($ed->clear() > 0) {
				$form['success_msg'] = 'Дисциплина ЕГЭ № '.$docs->id.' удалена.';
			} else {
				$form['error_msg'] = 'Ошибка удаления дисциплины ЕГЭ № '.$docs->id.'! Свяжитесь с администратором.';
			}
		}
		return $form;
	}

	/**
     * Checks dictionary ege data.
     *
     * @return array
     */
	public function check($form)
	{
		$de = new DictEge();
		$de->code = $form['discipline'];
			$dsp = new DictDiscipline();
			$dsp->code = $form['discipline'];
			$dsp_row = $dsp->getDescriptionByCode();
		$de->description = $dsp_row['discipline_name'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$de->id = $form['id'];
			if ($ed->existsEge()) {
				$form['error_msg'] = 'Удалять дисциплины ЕГЭ, которые используются в результатах ЕГЭ, нельзя!';
			} elseif ($de->existsExcept()) {
				$form['error_msg'] = 'Такая дисциплина ЕГЭ уже есть!';
				return $form;
			} else {
				if ($de->changeAll()) {
					$form['success_msg'] = 'Дисциплина ЕГЭ № '.$form['id'].' успешно изменена.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении дисциплины ЕГЭ № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($de->exists()) {
				$form['error_msg'] = 'Такая дисциплина ЕГЭ уже есть!';
				return $form;
			} else {
				$form['id'] = $de->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создана дисциплина ЕГЭ № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании дисциплины ЕГЭ!';
				}
			}
		}
		return $form;
	}
}
