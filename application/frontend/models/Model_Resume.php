<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Personal as Model_Personal;

class Model_Resume extends Model
{
	/*
		Resume processing
	*/

	public $form = 'resume';

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
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное имя!']
                               ],
                'name_middle' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для отчества можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное отчество!']
                               ],
                'name_last' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Фамилия обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для фамилии можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинная фамилия!']
                               ],
                'sex' => [
                            'type' => 'radio',
                            'class' => 'form-check-input',
                            'required' => ['default' => '', 'msg' => 'Пол обязателен для заполнения!']
                           ],
                'birth_dt' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Дата рождения обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата рождения должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!']
                               ],
				'birth_place' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Место рождения обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для места рождения можно использовать только русские буквы, тире, точки, запятые и пробелы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 240, 'msg' => 'Слишком длинное место рождения!']
                               ],
                'personal' => [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'required' => ['default' => '', 'msg' => 'Необходимо согласие на обработку персональных данных!']
                               ]
            ];
	}

	/**
     * Checks resume data.
     *
     * @return boolean
     */
	public function check()
	{
		$personal = new Model_Personal();
		$personal->id_user = $_SESSION['user']['id'];
			$personal->name_first = $_SESSION[$this->form]['name_first'];
			$personal->name_middle = $_SESSION[$this->form]['name_middle'];
			$personal->name_last = $_SESSION[$this->form]['name_last'];
			$personal->sex = $_SESSION[$this->form]['sex'];
			$personal->birth_dt = date('Y-m-d', strtotime($_SESSION[$this->form]['birth_dt']));
			$personal->birth_place = $_SESSION[$this->form]['birth_place'];
		$row = $personal->getPersonalByUser();
		if ($row) {
			if ($personal->changeAll()) {
				return true;
			} else {
				$_SESSION[$this->form]['error_msg'] = 'Ошибка при изменении пользователя!';
				return false;
			}
		} else {
			$personal->dt_created = date('Y-m-d H:i:s');
			if ($personal->save()) {
				return true;
			} else {
				$_SESSION[$this->form]['error_msg'] = 'Ошибка при сохранении пользователя!';
				return false;
			}
		}
	}
}
