<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_Langs as Langs;

class Model_Langs extends Model
{
	/*
		Langs processing
	*/

	/**
     * Langs rules.
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
                            'pattern' => ['value' => PATTERN_CODE, 'msg' => 'Для кода можно использовать '.MSG_CODE.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинный код!'],
                            'success' => 'Код заполнен верно.'
                           ],
                'name_original' => [
		                            'type' => 'text',
		                            'class' => 'form-control',
		                            'required' => ['default' => '', 'msg' => 'Наименование на родном языке обязательно для заполнения!'],
		                            'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинное наименование на родном языке!'],
		                            'success' => 'Наименование на родном языке заполнено верно.'
		                           ],
	            'name_eng' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Наименование на английском языке обязательно для заполнения!'],
	                            'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для наименования на английском языке можно использовать '.MSG_ALPHA.'!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинное наименование на английском языке!'],
	                            'success' => 'Наименование на английском языке заполнено верно.'
	                           ],
	             'name_rus' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Наименование на русском языке обязательно для заполнения!'],
	                            'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для наименования на русском языке можно использовать '.MSG_ALPHA_RUS.'!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинное наименование на русском языке!'],
	                            'success' => 'Наименование на русском языке заполнено верно.'
	                           ]
            ];
	}

	/**
     * Gets lang from database.
     *
     * @return array
     */
	public function get($id)
	{
		$lang = new Langs();
		$lang->id = $id;
		return $lang->get();
	}

	/**
     * Deletes lang from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$lang = new Langs();
		$lang->id = $form['id'];
		if ($lang->clear() > 0) {
			$form['success_msg'] = 'Язык № '.$lang->id.' удалён.';
		} else {
			$form['error_msg'] = 'Ошибка удаления языка № '.$lang->id.'! Свяжитесь с администратором.';
		}
		return $form;
	}

	/**
     * Checks lang data.
     *
     * @return array
     */
	public function check($form)
	{
		$lang = new Langs();
		$lang->code = $form['code'];
		$lang->name_original = $form['name_original'];
		$lang->name_eng = $form['name_eng'];
		$lang->name_rus = $form['name_rus'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$lang->id = $form['id'];
			$lang_row = $lang->get();
			if ($lang->existsCodeExcept()) {
				$form['error_msg'] = 'Такой язык уже есть!';
				return $form;
			} else {
				if ($lang->changeAll()) {
					$form['success_msg'] = 'Изменён язык № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении языка № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($lang->existsCode()) {
				$form['error_msg'] = 'Такой язык уже есть!';
				return $form;
			} else {
				$form['id'] = $lang->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создан язык № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании языка!';
				}
			}
		}
		return $form;
	}
}
