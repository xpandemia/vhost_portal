<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictForeignLangs as DictForeignLangs;

class Model_DictForeignLangs extends Model
{
	/*
		Dictionary foreign languages processing
	*/

	public $lang;

	public function __construct()
	{
		$this->lang = new DictForeignLangs();
	}

	/**
     * Gets foreign languages JSON.
     *
     * @return JSON
     */
	public function getForeignLangsJSON() : string
	{
		$lang = $this->lang->getAll();
			foreach ($lang as $value) {
				$lang_json[] = ['code' => $value['code'],
								'description' => $value['description']];
			}
			return json_encode($lang_json);
	}
}
