<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use common\models\Model_DictDoctypes as DictDoctypes;

class Model_DictDoctypes extends Model
{
	/*
		Dictionary document types processing
	*/

	public $doc;

	public function __construct()
	{
		$this->doc = new DictDoctypes();
	}

	/**
     * Gets passports JSON.
     *
     * @return JSON
     */
	public function getPassportsJSON() : string
	{
		$doc = $this->doc->getPassports();
		foreach ($doc as $value) {
			$doc_json[] = ['code' => $value['code'],
							'description' => $value['description']];
		}
		if (!empty($doc_json)) {
			return json_encode($doc_json);
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets russian passports JSON.
     *
     * @return JSON
     */
	public function getPassportsRussianJSON() : string
	{
		$doc = $this->doc->getPassportsRussia();
		foreach ($doc as $value) {
			$doc_json[] = ['code' => $value['code'],
							'description' => $value['description']];
		}
		if (!empty($doc_json)) {
			return json_encode($doc_json);
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets foreign passports JSON.
     *
     * @return JSON
     */
	public function getPassportsForeignJSON() : string
	{
		$doc = $this->doc->getPassportsForeign();
		foreach ($doc as $value) {
			$doc_json[] = ['code' => $value['code'],
							'description' => $value['description']];
		}
		if (!empty($doc_json)) {
			return json_encode($doc_json);
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets education documents by education type code JSON.
     *
     * @return JSON
     */
	public function getDiplomasByEducCodeJSON($code_educ) : string
	{
		if (!empty($code_educ)) {
			$this->doc->code_educ = $code_educ;
			$doc = $this->doc->getDiplomasByEducCode();
			foreach ($doc as $value) {
				$doc_json[] = ['code' => $value['code'],
								'description' => $value['description']];
			}
			if (!empty($doc_json)) {
				return json_encode($doc_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}
}
