<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictCountries extends Db_Helper
{
	/*
		Dictionary countries processing
	*/

	public $country_code;
	public $country_name;
	public $country_fullname;
	public $code_alpha2;
	public $code_alpha3;
	public $guid;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets all countries.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', 'dict_countries');
	}

	/**
     * Gets country by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', 'dict_countries', 'country_code = :country_code', [':country_code' => $this->country_code]);
	}

	/**
     * Gets country by name.
     *
     * @return array
     */
	public function getByName()
	{
		return $this->rowSelectOne('*', 'dict_countries', 'country_name = :country_name', [':country_name' => $this->country_name]);
	}

	/**
     * Saves citizenship data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('country_code, country_name, country_fullname, code_alpha2, code_alpha3, guid',
								'dict_countries',
								':country_code, :country_name, :country_fullname, :code_alpha2, :code_alpha3, :guid',
								[':country_code' => $this->country_code,
								':country_name' => $this->country_name,
								':country_fullname' => $this->country_fullname,
								':code_alpha2' => $this->code_alpha2,
								':code_alpha3' => $this->code_alpha3,
								':guid' => $this->guid]);
	}

	/**
     * Changes country code.
     *
     * @return boolean
     */
	public function changeCode()
	{
		return $this->rowUpdate('dict_countries',
								'country_code = :country_code',
								[':country_code' => $this->country_code]);
	}

	/**
     * Changes country name.
     *
     * @return boolean
     */
	public function changeName()
	{
		return $this->rowUpdate('dict_countries',
								'country_name = :country_name',
								[':country_name' => $this->country_name]);
	}

	/**
     * Changes country fullname.
     *
     * @return boolean
     */
	public function changeFullName()
	{
		return $this->rowUpdate('dict_countries',
								'country_fullname = :country_fullname',
								[':country_fullname' => $this->country_fullname]);
	}

	/**
     * Changes country alpha2.
     *
     * @return boolean
     */
	public function changeAlpha2()
	{
		return $this->rowUpdate('dict_countries',
								'code_alpha2 = :code_alpha2',
								[':code_alpha2' => $this->code_alpha2]);
	}

	/**
     * Changes country alpha3.
     *
     * @return boolean
     */
	public function changeAlpha3()
	{
		return $this->rowUpdate('dict_countries',
								'code_alpha3 = :code_alpha3',
								[':code_alpha3' => $this->code_alpha3]);
	}

	/**
     * Removes all countries.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete('dict_countries');
	}

	/**
     * Loads countries.
     *
     * @return array
     */
	public function load($properties, $id_dict, $dict_name, $clear_load)
	{
		$result['success_msg'] = null;
		$result['error_msg'] = null;
		$log = new Model_DictionaryManagerLog();
		$log->id_dict = $id_dict;
		$log->id_user = $_SESSION[APP_CODE]['user_id'];
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено стран - '.$rows_del.'.';
				$log->dt_created = date('Y-m-d H:i:s');
				$log->save();
			} else {
				$rows_del = 0;
			}
		if(sizeof($properties) == 0) {
			$result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
		$rows_ins = 0;
		$rows_upd = 0;
		foreach($properties as $property) {
			$this->country_code = (string)$property->Code;
			$country = $this->getByCode();

            if($property->DeletionMark == "false") {
				$this->country_name = (string)$property->Description;
				//var_dump($this->country_name);
				//exit();
				$this->country_fullname = (string)$property->НаименованиеПолное;
				$this->code_alpha2 = (string)$property->КодАльфа2;
				$this->code_alpha3 = (string)$property->КодАльфа3;
				$this->guid = (string)$property->Ref_Key;
					if ($country == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создана новая страна с GUID ['.$this->guid.'].';
							$log->dt_created = date('Y-m-d H:i:s');
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении страны с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						// code
						if ($country['country_code'] != $this->country_code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код страны с GUID ['.$this->guid.'].';
								$log->value_old = $kladr['country_code'];
								$log->value_new = $this->country_code;
								$log->dt_created = date('Y-m-d H:i:s');
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// name
						if ($country['country_name'] != $this->country_name) {
							if ($this->changeName()) {
								$log->msg = 'Изменено наименование страны с GUID ['.$this->guid.'].';
								$log->value_old = $kladr['country_name'];
								$log->value_new = $this->country_name;
								$log->dt_created = date('Y-m-d H:i:s');
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// full name
						if ($country['country_fullname'] != $this->country_fullname) {
							if ($this->changeFullName()) {
								$log->msg = 'Изменено полное наименование страны с GUID ['.$this->guid.'].';
								$log->value_old = $kladr['country_fullname'];
								$log->value_new = $this->country_fullname;
								$log->dt_created = date('Y-m-d H:i:s');
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении полного наименования страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// alpha 2
						if ($country['code_alpha2'] != $this->code_alpha2) {
							if ($this->changeAlpha2()) {
								$log->msg = 'Изменён код альфа-2 страны с GUID ['.$this->guid.'].';
								$log->value_old = $kladr['code_alpha2'];
								$log->value_new = $this->code_alpha2;
								$log->dt_created = date('Y-m-d H:i:s');
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода альфа-2 страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// alpha 3
						if ($country['code_alpha3'] != $this->code_alpha3) {
							if ($this->changeAlpha3()) {
								$log->msg = 'Изменён код альфа-3 страны с GUID ['.$this->guid.'].';
								$log->value_old = $kladr['code_alpha3'];
								$log->value_new = $this->code_alpha3;
								$log->dt_created = date('Y-m-d H:i:s');
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода альфа-3 страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// counter
						if ($upd == 1) {
							$rows_upd++;
						}
					}
			}
        }
        if ($rows_del == 0 && $rows_ins == 0 && $rows_upd == 0) {
			$result['success_msg'] = 'Справочник "'.$dict_name.'" не нуждается в обновлении.';
		} else {
			$result['success_msg'] = nl2br("В справочнике \"$dict_name\":\n----- удалено записей - $rows_del\n----- добавлено записей - $rows_ins\n----- обновлено записей - $rows_upd");
		}
        return $result;
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
