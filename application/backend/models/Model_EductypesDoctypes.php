<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_EductypesDoctypes as EductypesDoctypes;
use common\models\Model_DictEductypes as DictEductypes;
use common\models\Model_DictDoctypes as DictDoctypes;

class Model_EductypesDoctypes extends Model
{
	/*
		Eductypes doctypes processing
	*/

	/**
     * Eductypes doctypes rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'educ_type' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Вид образования обязателен для заполнения!'],
								'success' => 'Вид образования выбран.'
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
     * Gets eductypes doctypes from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ed = new EductypesDoctypes();
		$ed->id = $id;
		return $ed->get();
	}

	/**
     * Deletes eductypes doctypes from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$ed = new EductypesDoctypes();
		$ed->id = $form['id'];
		if ($ed->clear() > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks eductypes doctypes data.
     *
     * @return array
     */
	public function check($form)
	{

		$ed = new EductypesDoctypes();
			$types = new DictEductypes();
			$types->code = $form['educ_type'];
			$types_row = $types->getByCode();
		$ed->id_eductype = $types_row['id'];
			$doctype = new DictDoctypes();
			$doctype->code = $form['doc_type'];
			$doctype_row = $doctype->getByCode();
		$ed->id_doctype = $doctype_row['id'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$ed->id = $form['id'];
			if ($ed->existsExcept()) {
				$form['error_msg'] = 'Такая связь уже есть!';
				return $form;
			} else {
				if ($ed->changeAll()) {
					$form['success_msg'] = 'Изменена связь № '.$form['id'].'.';
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
