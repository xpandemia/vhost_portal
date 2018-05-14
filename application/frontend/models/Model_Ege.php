<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_Ege as Ege;
use common\models\Model_Personal as Model_Personal;

class Model_Ege extends Model
{
	/*
		Ege processing
	*/

	/**
     * Ege rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'description' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для описания можно использовать только русские буквы и цифры!'],
	                            'width' => ['format' => 'string', 'min' => 0, 'max' => 100, 'msg' => 'Слишком длинное описание!'],
	                            'success' => 'Описание заполнено верно.'
	                           ],
                'reg_year' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'required' => ['default' => '', 'msg' => 'Год сдачи обязателен для заполнения!'],
	                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для года сдачи можно использовать только цифры!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 4, 'msg' => 'Слишком длинный год сдачи!'],
	                            'unique' => ['class' => 'common\\models\\Model_Ege', 'method' => 'ExistsRegyear', 'msg' => 'Такой год сдачи уже есть!'],
	                            'compared' => ['value' => date('Y'), 'type' => '<=', 'msg' => 'Год сдачи должен быть меньше или равен '.date('Y').'.'],
	                            'success' => 'Год сдачи заполнен верно.'
	                           ]
	            ];
	}

	/**
     * Validates resume advanced.
     *
     * @return array
     */
	public function validateFormAdvanced($form)
	{
		// reg_year
		if (date('Y') - $form['reg_year'] > 4) {
			$form = $this->setFormErrorField($form, 'reg_year', 'Неактуальный год сдачи ЕГЭ!');
			return $form;
		}
		return $form;
	}

	/**
     * Gets ege from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ege = new Ege();
		$ege->id = $id;
		return $ege->get();
	}

	/**
     * Deletes ege from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$ege = new Ege();
		$ege->id = $form['id'];
		if ($ege->clear() > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks ege data.
     *
     * @return array
     */
	public function check($form)
	{
		$ege = new Ege();
		$ege->id_user = $_SESSION[APP_CODE]['user_id'];
		if (empty($form['description'])) {
			$personal = new Model_Personal();
			$fio = $personal->getFioByUser();
			if ($fio) {
				$ege->description = $fio['name_last'].' '.$fio['name_first'].', результаты ЕГЭ за '.$form['reg_year'].' год';
			} else {
				$form['error_msg'] = 'Не найдены личные данные пользователя!';
				return $form;
			}
		} else {
			$ege->description = $form['description'];
		}
		$ege->reg_year = $form['reg_year'];
		if ($ege->save() > 0) {
			$form['error_msg'] = null;
			$form['success_msg'] = 'Создан новый документ ЕГЭ.';
		} else {
			$form['error_msg'] = 'Ошибка при создании документа ЕГЭ!';
		}
		return $form;
	}
}
