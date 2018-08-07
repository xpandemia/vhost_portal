<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictCountries as DictCountries;

class Model_DictCountries extends Model
{
	/*
		Dictionary countries processing
	*/

	/**
     * Dictionary countries rules.
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
                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для кода можно использовать '.MSG_NUMB.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 3, 'msg' => 'Слишком длинный код!'],
                            'success' => 'Код заполнен верно.'
                           ],
                'description' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Наименование обязательно для заполнения!'],
	                            'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для наименования можно использовать '.PATTERN_TEXT_RUS.'!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 60, 'msg' => 'Слишком длинное наименование!'],
	                            'success' => 'Наименование заполнено верно.'
	                           ],
	            'fullname' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для полного наименования можно использовать '.MSG_TEXT_RUS.'!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное полное наименование!'],
	                            'success' => 'Полное наименование заполнено верно.'
	                           ],
	            'abroad' => [
							'type' => 'selectlist',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Зарубежье обязательно для заполнения!'],
							'success' => 'Зарубежье заполнено верно.'
                           ],
                'code_alpha2' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Альфа-2 обязателен для заполнения!'],
	                            'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для альфа-2 можно использовать '.MSG_ALPHA.'!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 2, 'msg' => 'Слишком длинный альфа-2!'],
	                            'success' => 'Альфа-2 заполнен верно.'
	                            ],
                'code_alpha3' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Альфа-3 обязателен для заполнения!'],
	                            'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для альфа-3 можно использовать '.MSG_ALPHA.'!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 3, 'msg' => 'Слишком длинный альфа-3!'],
	                            'success' => 'Альфа-3 заполнен верно.'
	                            ],
	            'guid' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для глобального идентификатора можно использовать '.MSG_NUMB.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 36, 'msg' => 'Слишком длинный глобальный идентификатор!'],
                            'success' => 'Глобальный идентификатор заполнен верно.'
                           ]
            ];
	}

	/**
     * Gets country from database.
     *
     * @return array
     */
	public function get($id)
	{
		$dc = new DictCountries();
		$dc->id = $id;
		return $dc->get();
	}

	/**
     * Deletes country from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$dc = new DictCountries();
		$dc->id = $form['id'];
		if ($dc->existsCitizenship()) {
			$form['error_msg'] = 'Удалять страны, которые используются в гражданствах, нельзя!';
		} elseif ($dc->existsAddress()) {
			$form['error_msg'] = 'Удалять страны, которые используются в адресах, нельзя!';
		} else {
			if ($dc->clear() > 0) {
				$form['success_msg'] = 'Страна № '.$docs->id.' удалена.';
			} else {
				$form['error_msg'] = 'Ошибка удаления страны № '.$docs->id.'! Свяжитесь с администратором.';
			}
		}
		return $form;
	}

	/**
     * Checks dictionary countries data.
     *
     * @return array
     */
	public function check($form)
	{
		$dc = new DictCountries();
		$dc->code = $form['code'];
		$dc->description = $form['description'];
		$dc->fullname = $form['fullname'];
		$dc->abroad = $form['abroad'];
		$dc->code_alpha2 = $form['code_alpha2'];
		$dc->code_alpha3 = $form['code_alpha3'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$dc->id = $form['id'];
			$dc_row = $dc->get();
			if ($dc->existsCitizenship() && ($dc->code != $dc_row['code'] || $dc->description != $dc_row['description'])) {
				$form['error_msg'] = 'Изменять страны, которые используются в гражданствах, нельзя!';
				return $form;
			} elseif ($dc->existsAddress() && ($dc->code != $dc_row['code'] || $dc->description != $dc_row['description'])) {
				$form['error_msg'] = 'Изменять страны, которые используются в адресах, нельзя!';
				return $form;
			} elseif ($dc->existsCodeExcept() && $dc->existsDescriptionExcept() && $dc->existsGuidExcept()) {
				$form['error_msg'] = 'Такая страна уже есть!';
				return $form;
			} else {
				if ($dc->changeAll()) {
					$form['success_msg'] = 'Изменена страна № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении страны № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($dc->existsCode() && $dc->existsDescription() && $dc->existsGuid()) {
				$form['error_msg'] = 'Такая страна уже есть!';
				return $form;
			} else {
				$dc->guid = $form['guid'];
				$form['id'] = $dc->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создана страна № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании страны!';
				}
			}
		}
		return $form;
	}
}
