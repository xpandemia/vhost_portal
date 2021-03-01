<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\SOAP_Helper as SOAP_Helper;
use tinyframe\core\helpers\XML_Helper as XML_Helper;
use common\models\Model_DictionaryManager as DictionaryManager;

class Model_DictionaryManager extends Model
{
	/*
		Dictionary manager processing
	*/

	/**
     * Dictionary manager add rules.
     *
     * @return array
     */
	public function rules_add()
	{
		return [
                'dict_code' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Код обязателен для заполнения!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 255, 'msg' => 'Слишком длинный код!'],
                            'success' => 'Код заполнен верно.'
                           ],
                'dict_name' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для наименования можно использовать '.MSG_TEXT_RUS.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование!'],
                                'success' => 'Наименование заполнено верно.'
                               ],
                'dict_filter' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 255, 'msg' => 'Слишком длинный фильтр!'],
	                            'success' => 'Фильтр заполнен верно.'
	                           ],
	            'type' => [
							'type' => 'selectlist',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Тип обязателен для заполнения!'],
							'success' => 'Тип заполнен верно.'
                           ],
                'table_name' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование таблицы обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_CODE, 'msg' => 'Для кода можно использовать '.MSG_CODE.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование таблицы!'],
                                'success' => 'Наименование таблицы заполнен верно.'
                               ],
                'model_class' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование модели обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_PATH, 'msg' => 'Для наименования модели можно использовать '.MSG_PATH.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование модели!'],
                                'success' => 'Наименование модели заполнен верно.'
                               ],
                'clear_load' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное наименование метода очистки!'],
	                            'success' => 'Наименование метода очистки заполнено верно.'
	                           ],
	            'active' => [
							'type' => 'radio',
                            'class' => 'form-check-input',
                            'required' => ['default' => '', 'msg' => 'Флаг активности обязателен для заполнения!'],
							'success' => 'Флаг активности выбран.'
                           ]
            ];
	}

	/**
     * Dictionary manager sync rules.
     *
     * @return array
     */
	public function rules_sync()
	{
		return [
                'dictionary' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Пожалуйста, выберите справочник!'],
								'success' => 'Справочник выбран.'
                               ],
            ];
	}

	/**
     * Gets dictionary from database.
     *
     * @return array
     */
	public function get($id)
	{
		$dm = new DictionaryManager();
		$dm->id = $id;
		return $dm->getById();
	}

	/**
     * Deletes dictionary from database.
     *
     * @return array
     */
	public function delete($form) : array
	{
		$form['success_msg'] = null;
		$form['error_msg'] = null;
		$dm = new DictionaryManager();
		$dm->id = $form['id'];
		if ($dm->clear() > 0) {
			$form['success_msg'] = 'Справочник № '.$dm->id.' удалён.';
		} else {
			$form['error_msg'] = 'Ошибка удаления справочника № '.$dm->id.'! Свяжитесь с администратором.';
		}
		return $form;
	}

	/**
     * Renews dictionary data.
     *
     * @return array
     */
	public function renew($form)
	{
		set_time_limit(0);
		ini_set("memory_limit", "256M");
		// get dictionary
		$dicts = new DictionaryManager();
        $dicts->id = $form['dictionary'];
        $row = $dicts->getById();
        
        if ($row) {
			switch ($row['type']) {
				case $dicts::TYPE_ODATA:
					// get xml via OData
					$xml = XML_Helper::loadXml(ODATA_1C, $row['dict_code'], $row['dict_filter'], USER_1C, PASSWORD_1C);
					if ($xml) {
						// collect properties
						$properties = XML_Helper::getProperties($xml);
						// use model
						$model = new $row['model_class'];
						// load data
						$result = $model->load($properties, $row['id'], $row['dict_name'], $row['clear_load']);
						$form['error_msg'] = $result['error_msg'];
						$form['success_msg'] = $result['success_msg'];
					} else {
						$form['error_msg'] = 'Ошибка при загрузке справочника!';
					}
					break;
				case $dicts::TYPE_WSDL:
					$xml = SOAP_Helper::loadWsdl(WSDL_1C, $row['dict_code'], USER_1C, PASSWORD_1C);
					if ($xml) {
						// use model
						$model = new $row['model_class'];
						// load
                        $result = $model->load($xml, $row['id'], $row['dict_name'], $row['clear_load']);
						$form['error_msg'] = $result['error_msg'];
						$form['success_msg'] = $result['success_msg'];
					} else {
						$form['error_msg'] = 'Ошибка при загрузке справочника!';
					}
					break;
				default:
					$form['error_msg'] = 'Неизвестный тип справочника!';
			}
		} else {
			$form['error_msg'] = 'Ошибка при получении параметров справочника!';
		}
		return $form;
	}

	/**
     * Checks dictionary data.
     *
     * @return array
     */
	public function check($form)
	{
		$dm = new DictionaryManager();
		$dm->dict_code = $form['dict_code'];
		$dm->dict_name = $form['dict_name'];
		$dm->dict_filter = (empty($form['dict_filter'])) ? null : $form['dict_filter'];
		$dm->type = $form['type'];
		$dm->table_name = $form['table_name'];
		$dm->model_class = $form['model_class'];
		$dm->clear_load = (empty($form['clear_load'])) ? null : $form['clear_load'];
		$dm->active = $form['active'];
		if (isset($form['id']) && !empty($form['id'])) {
			// update
			$dm->id = $form['id'];
			if ($dm->existsCodeExcept()) {
				$form['error_msg'] = 'Справочник с таким кодом уже есть!';
				return $form;
			} elseif ($dm->existsNameExcept()) {
				$form['error_msg'] = 'Справочник с таким наименованием уже есть!';
				return $form;
			} else {
				if ($dm->changeAll()) {
					$form['success_msg'] = 'Изменён справочник № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при изменении справочника № '.$form['id'].'!';
				}
			}
		} else {
			// insert
			if ($dm->existsCode()) {
				$form['error_msg'] = 'Справочник с таким кодом уже есть!';
				return $form;
			} elseif ($dm->existsName()) {
				$form['error_msg'] = 'Справочник с таким наименованием уже есть!';
				return $form;
			} else {
				$form['id'] = $dm->save();
				if ($form['id'] > 0) {
					$form['success_msg'] = 'Создан справочник № '.$form['id'].'.';
				} else {
					$form['error_msg'] = 'Ошибка при создании справочника!';
				}
			}
		}
		return $form;
	}
}
