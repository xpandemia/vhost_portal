<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Personal as Model_Personal;
use common\models\Model_DictCountries as Model_DictCountries;

class Model_Resume extends Model
{
	/*
		Resume processing
	*/

	/**
     * Resume rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'name_first' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Имя обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для имени можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное имя!'],
                                'success' => 'Имя заполнено верно.'
                               ],
                'name_middle' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для отчества можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное отчество!'],
                                'success' => 'Отчество заполнено верно.'
                               ],
                'name_last' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Фамилия обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для фамилии можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинная фамилия!'],
                                'success' => 'Фамилия заполнена верно.'
                               ],
                'sex' => [
                            'type' => 'radio',
                            'class' => 'form-check-input',
                            'required' => ['default' => '', 'msg' => 'Пол обязателен для заполнения!'],
                            'success' => 'Пол заполнен верно.'
                           ],
                'birth_dt' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Дата рождения обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата рождения должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
                                'success' => 'Дата рождения заполнена верно.'
                               ],
				'birth_place' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Место рождения обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для места рождения можно использовать только русские буквы, тире, точки, запятые и пробелы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 240, 'msg' => 'Слишком длинное место рождения!'],
                                'success' => 'Место рождения заполнено верно.'
                               ],
                'citizenship' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Гражданство обязательно для заполнения!'],
								'success' => 'Гражданство заполнено верно.'
                               ],
                'personal' => [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'required' => ['default' => '', 'msg' => 'Необходимо согласие на обработку персональных данных!'],
                                'success' => 'Получено согласие на обработку персональных данных.'
                               ]
            ];
	}

	/**
     * Checks resume data.
     *
     * @return array
     */
	public function check($form)
	{
		$personal = new Model_Personal();
		$personal->id_user = $_SESSION[APP_CODE]['user_id'];
			$personal->name_first = $form['name_first'];
			$personal->name_middle = $form['name_middle'];
			$personal->name_last = $form['name_last'];
			$personal->sex = $form['sex'];
			$personal->birth_dt = date('Y-m-d', strtotime($form['birth_dt']));
			$personal->birth_place = $form['birth_place'];
				$countries = new Model_DictCountries();
				$countries->country_name = $form['citizenship'];
				$row_country =  $countries->getCountryByName();
			$personal->citizenship = $row_country['id'];
		$row_personal = $personal->getPersonalByUser();
		if ($row_personal) {
			if ($personal->changeAll()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при изменении личных данных!';
			}
		} else {
			$personal->dt_created = date('Y-m-d H:i:s');
			if ($personal->save()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при создании личных данных!';
			}
		}
		return $form;
	}
}
