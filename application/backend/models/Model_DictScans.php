<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictScans as DictScans;

class Model_DictScans extends Model
{
	/*
		Dictionary scans processing
	*/

	/**
     * Dictionary scans rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'id_doc' => [
							'type' => 'selectlist',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Документ обязателен для заполнения!'],
							'success' => 'Документ выбран.'
                           ],
                'numb' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для номера пп можно использовать '.MSG_NUMB.'!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 2, 'msg' => 'Слишком длинный номер пп!'],
                            'success' => 'Номер пп заполнен верно.'
                           ],
                'scan_code' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Код обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_CODE, 'msg' => 'Для кода можно использовать '.MSG_CODE.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 30, 'msg' => 'Слишком длинный код!'],
                                'success' => 'Код заполнен верно.'
                               ],
                'scan_name' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования можно использовать '.MSG_INFO_RUS.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование!'],
                                'success' => 'Наименование заполнено верно.'
                               ],
                'required' => [
								'type' => 'radio',
	                            'class' => 'form-check-input',
	                            'required' => ['default' => '', 'msg' => 'Флаг обязательности обязателен для заполнения!'],
								'success' => 'Флаг обязательности выбран.'
	                           ],
	            'main' => [
							'type' => 'radio',
                            'class' => 'form-check-input',
                            'required' => ['default' => '', 'msg' => 'Флаг основной группы обязателен для заполнения!'],
							'success' => 'Флаг основной группы выбран.'
                           ]
            ];
	}

	/**
     * Gets dictionary scans from database.
     *
     * @return array
     */
	public function get($id)
	{
		$ds = new DictScans();
		$ds->id = $id;
		return $ds->get();
	}

	/**
     * Deletes dictionary scans from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$ds = new DictScans();
		$ds->id = $form['id'];
		if ($ds->existsScans()) {
			$form['error_msg'] = 'Удалять скан-копии, которые используются в хранилище, нельзя!';
		} else {
			if ($ds->clear() > 0) {
				$form['success_msg'] = 'Скан-копия № '.$ds->id.' удалена.';
			} else {
				$form['error_msg'] = 'Ошибка удаления скан-копии № '.$ds->id.'! Свяжитесь с администратором.';
			}
		}
		return $form;
	}

	/**
     * Checks dictionary scans data.
     *
     * @return array
     */
	public function check($form)
	{
		$ds = new DictScans();
		$ds->id_doc = $form['id_doc'];
		if (isset($form['numb']) && !empty($form['numb'])) {
			if ($form['main'] === 1) {
				if ($form['numb'] === 0) {
					$form['error_msg'] = 'В основной группе номер пп не может быть равен нулю!';
					return $form;
				} else {
					$numb_max = $ds->getNumbMax();
					if ($form['numb'] > $numb_max + 1) {
						$ds->numb = $numb_max + 1;
						$sync = 0;
					} else {
						$ds->numb = $form['numb'];
						$sync = 1;
					}
				}
			} else {
				$sync = 0;
				$ds->numb = 0;
			}
		} else {
			if ($form['main'] === 1) {
				$form['error_msg'] = 'В основной группе номер пп обязателен для заполнения!';
				return $form;
			} else {
				$sync = 0;
				$ds->numb = 0;
			}
		}
		$ds->scan_code = $form['scan_code'];
		$ds->scan_name = $form['scan_name'];
		$ds->required = $form['required'];
		$ds->main = $form['main'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$ds->id = $form['id'];
			if ($ds->existsScans()) {
				$form['error_msg'] = 'Изменять скан-копии, которые используются в хранилище, нельзя!';
				return $form;
			} elseif ($ds->existsCodeExcept()) {
				$form['error_msg'] = 'Скан-копия с таким кодом уже есть!';
				return $form;
			} elseif ($ds->existsNameExcept()) {
				$form['error_msg'] = 'Скан-копия с таким наименованием уже есть!';
				return $form;
			} else {
				if ($ds->changeAll()) {
					$form['success_msg'] = 'Изменена скан-копия № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении скан-копии № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($ds->existsCode()) {
				$form['error_msg'] = 'Скан-копия с таким кодом уже есть!';
				return $form;
			} elseif ($ds->existsName()) {
				$form['error_msg'] = 'Скан-копия с таким наименованием уже есть!';
				return $form;
			} else {
				$form['id'] = $ds->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создана скан-копия № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании скан-копии!';
				}
			}
		}
		if ($sync === 1) {
			$ds->syncNumbs();
		}
		return $form;
	}
}
