<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictUniversity as DictUniversity;

class Model_DictUniversity extends Model
{
	/*
		Dictionary university processing
	*/

	/**
     * Dictionary university rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'code' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Код обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для кода можно использовать '.MSG_ALPHA_RUS.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный код!'],
                            'success' => 'Код заполнен верно.'
                           ],
                'description' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для наименования можно использовать '.MSG_TEXT_RUS.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование!'],
                                'success' => 'Наименование заполнено верно.'
                               ]
            ];
	}

	/**
     * Gets dictionary university from database.
     *
     * @return array
     */
	public function get($id)
	{
		$du = new DictUniversity();
		$du->id = $id;
		return $du->get();
	}

	/**
     * Deletes dictionary university from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$du = new DictUniversity();
		$du->id = $form['id'];
		if ($du->existsApplications()) {
			$form['error_msg'] = 'Удалять места поступления, которые используются в справочнике скан-копий, нельзя!';
		} else {
			if ($du->clear() > 0) {
				$form['success_msg'] = 'Место поступления № '.$du->id.' удалено.';
			} else {
				$form['error_msg'] = 'Ошибка удаления места поступления № '.$du->id.'! Свяжитесь с администратором.';
			}
		}
		return $form;
	}

	/**
     * Checks dictionary university data.
     *
     * @return array
     */
	public function check($form)
	{
		$du = new DictUniversity();
		$du->code = $form['code'];
		$du->description = $form['description'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$du->id = $form['id'];
			if ($du->existsApplications()) {
				$form['error_msg'] = 'Изменять места поступления, которые используются в справочнике скан-копий, нельзя!';
				return $form;
			} elseif ($du->existsCodeExcept()) {
				$form['error_msg'] = 'Место поступления с таким кодом уже есть!';
				return $form;
			} elseif ($du->existsDescriptionExcept()) {
				$form['error_msg'] = 'Место поступления с таким наименованием уже есть!';
				return $form;
			} else {
				if ($du->changeAll()) {
					$form['success_msg'] = 'Изменено место поступления № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении места поступления № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($du->existsCode()) {
				$form['error_msg'] = 'Место поступления с таким кодом уже есть!';
				return $form;
			} elseif ($du->existsDescription()) {
				$form['error_msg'] = 'Место поступления с таким наименованием уже есть!';
				return $form;
			} else {
				$form['id'] = $du->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создано место поступления № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании места поступления!';
				}
			}
		}
		return $form;
	}
}
