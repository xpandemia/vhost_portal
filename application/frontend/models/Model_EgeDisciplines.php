<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictEge as Model_DictEge;
use common\models\Model_EgeDisciplines as EgeDisciplines;

class Model_EgeDisciplines extends Model
{
	/*
		Ege disciplines processing
	*/

	/**
     * Ege disciplines rules.
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
								'success' => 'Дисциплина заполнена верно.'
                               ],
                'points' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Баллы обязательны для заполнения!'],
                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для баллов можно использовать только цифры!'],
                            'width' => ['format' => 'numb', 'min' => 0, 'max' => 100, 'msg' => 'Значение баллов должно быть между 0 и 100!'],
                            'success' => 'Баллы заполнены верно.'
                           ]
	            ];
	}

	/**
     * Gets ege discipline from database.
     *
     * @return array
     */
	public function get($id)
	{
		$egedsp = new EgeDisciplines();
		$egedsp->id = $id;
		return $egedsp->get();
	}

	/**
     * Deletes ege discipline from database.
     *
     * @return boolean
     */
	public function delete($form)
	{
		$egedsp = new EgeDisciplines();
		$egedsp->id = $form['id'];
		if ($egedsp->clear() > 0) {
			$_SESSION[APP_CODE]['error_msg'] = null;
			$_SESSION[APP_CODE]['success_msg'] = 'Удалена дисциплина ЕГЭ.';
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks ege discipline data.
     *
     * @return array
     */
	public function check($form)
	{
		$egedsp = new EgeDisciplines();
		$egedsp->pid = $form['pid'];
			$dsp = new Model_DictEge();
			$dsp->code = $form['discipline'];
			$row_dsp =  $dsp->getByCode();
		$egedsp->id_discipline = $row_dsp['id'];
		// check discipline
		if ($egedsp->existsDiscipline()) {
			$form['error_msg'] = 'Такая дисциплина ЕГЭ уже есть!';
			return $form;
		}
		$egedsp->points = $form['points'];
		if ($egedsp->save() > 0) {
			$_SESSION[APP_CODE]['error_msg'] = null;
			$_SESSION[APP_CODE]['success_msg'] = 'Создана новая дисциплина ЕГЭ.';
		} else {
			$_SESSION[APP_CODE]['error_msg'] = 'Ошибка при создании дисциплины ЕГЭ!';
		}
		return $form;
	}
}
