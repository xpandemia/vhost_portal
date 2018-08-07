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
		$de = new DictEge();
		$de->id = $id;
		$de_row = $de->get();
		return ['id' => $de_row['id'], 'discipline' => $de_row['code']];
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
		$de = new DictEge();
		$de->id = $form['id'];
		if ($de->existsEge()) {
			$form['error_msg'] = 'Удалять дисциплины ЕГЭ, которые используются в результатах ЕГЭ, нельзя!';
		} else {
			if ($de->clear() > 0) {
				$form['success_msg'] = 'Дисциплина ЕГЭ № '.$de->id.' удалена.';
			} else {
				$form['error_msg'] = 'Ошибка удаления дисциплины ЕГЭ № '.$de->id.'! Свяжитесь с администратором.';
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
			if ($de->existsEge()) {
				$form['error_msg'] = 'Удалять дисциплины ЕГЭ, которые используются в результатах ЕГЭ, нельзя!';
			} elseif ($de->existsExcept()) {
				$form['error_msg'] = 'Такая дисциплина ЕГЭ уже есть!';
				return $form;
			} else {
				if ($de->changeAll()) {
					$form['success_msg'] = 'Изменена дисциплина ЕГЭ № '.$form['id'].'.';
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
