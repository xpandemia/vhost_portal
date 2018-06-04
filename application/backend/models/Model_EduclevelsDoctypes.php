<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_EduclevelsDoctypes as EduclevelsDoctypes;
use common\models\Model_DictEduclevels as DictEduclevels;
use common\models\Model_DictDoctypes as DictDoctypes;

class Model_EduclevelsDoctypes extends Model
{
	/*
		Educlevels doctypes processing
	*/

	/**
     * Educlevels doctypes rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'educ_level' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Уровень подготовки обязателен для заполнения!'],
								'success' => 'Уровень подготовки выбран.'
                               ],
                'doc_type' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Тип документа для заполнения!'],
								'success' => 'Тип документа выбран.'
                               ],
                'pay' => [
						'type' => 'checkbox',
                        'class' => 'form-check-input',
                        'success' => 'Получена информация о платном обучении.'
                       ]
            ];
	}

	/**
     * Gets educlevels doctypes from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ed = new EduclevelsDoctypes();
		$ed->id = $id;
		return $ed->get();
	}

	/**
     * Deletes educlevels doctypes from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$ed = new EduclevelsDoctypes();
		$ed->id = $form['id'];
		if ($ed->clear() > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks educlevels doctypes data.
     *
     * @return array
     */
	public function check($form)
	{
		$ed = new EduclevelsDoctypes();
			$levels = new DictEduclevels();
			$levels->code = $form['educ_level'];
			$levels_row = $levels->getByCode();
		$ed->id_educlevel = $levels_row['id'];
			$doctype = new DictDoctypes();
			$doctype->code = $form['doc_type'];
			$doctype_row = $doctype->getByCode();
		$ed->id_doctype = $doctype_row['id'];
		$ed->pay = (($form['pay'] == 'checked') ? 1 : 0);
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$ed->id = $form['id'];
			if ($ed->existsExcept()) {
				$form['error_msg'] = 'Такая связь уже есть!';
				return $form;
			} else {
				if ($ed->changeAll()) {
					$form['success_msg'] = 'Связь № '.$form['id'].' успешно изменена.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении связи № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($ed->exists()) {
				$form['error_msg'] = 'Такая связь уже есть!';
				return $form;
			} else {
				$form['id'] = $ed->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создана связь № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании связи!';
				}
			}
		}
		return $form;
	}
}
