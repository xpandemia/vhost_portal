<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\XML_Helper as XML_Helper;
//use common\models\Model_DictCitizenship as Model_DictCitizenship;
use common\models\Model_DictionaryManager as DictionaryManager;

class Model_DictionaryManager extends Model
{
	/*
		Dictionary manager processing
	*/

	/**
     * Login rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'dictionary' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Выберите справочник!'],
								'success' => 'Справочник выбран.'
                               ],
            ];
	}

	/**
     * Checks login data.
     *
     * @return array
     */
	public function renew($form)
	{
		set_time_limit(0);
		ini_set("memory_limit", "128M");
		// get dictionary
		$dicts = new DictionaryManager();
        $dicts->id = $form['dictionary'];
        $row = $dicts->getById();
        if ($row) {
			// get xml
			$xml = XML_Helper::loadXml(HOST_1C, $row['dict_code'], $row['dict_filter'], USER_1C, PASSWORD_1C);
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
		} else {
			$form['error_msg'] = 'Ошибка при получении параметров справочника!';
		}
		return $form;
	}
}
