<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictCountries extends Db_Helper
{
	/*
		Dictionary countries processing
	*/

	const TABLE_NAME = 'dict_countries';

	const ABROAD_HOME = 0;
	const ABROAD_HOME_NAME = 'Нет';
	const ABROAD_NEAR = 1;
	const ABROAD_NEAR_NAME = 'Ближнее';
	const ABROAD_FAR = 2;
	const ABROAD_FAR_NAME = 'Дальнее';

	const ABROAD_LIST = [
						['code' => 0, 'description' => 'Нет'],
						['code' => 1, 'description' => 'Ближнее'],
						['code' => 2, 'description' => 'Дальнее']
						];

	public $id;
	public $code;
	public $description;
	public $fullname;
	public $abroad;
	public $code_alpha2;
	public $code_alpha3;
	public $guid;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Countries rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
				'id' => [
						'required' => 1,
						'insert' => 0,
						'update' => 0,
						'value' => $this->id
						],
				'code' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->code
							],
				'description' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->description
								],
				'fullname' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->fullname
								],
				'abroad' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->abroad
							],
				'code_alpha2' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->code_alpha2
								],
				'code_alpha3' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->code_alpha3
								],
				'guid' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->guid
							]
				];
	}

	/**
     * Countries grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'id' => [
						'name' => '№',
						'type' => 'int'
						],
				'description' => [
								'name' => 'Наименование',
								'type' => 'string'
								],
				'fullname' => [
								'name' => 'Полное наименование',
								'type' => 'string'
								],
				'abroad' => [
							'name' => 'Зарубежье',
							'type' => 'string'
							],
				'code_alpha2' => [
								'name' => 'Альфа-2',
								'type' => 'string'
								],
				'code_alpha3' => [
								'name' => 'Альфа-3',
								'type' => 'string'
								]
				];
	}

	/**
     * Gets all countries for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll('id, description, fullname, getCountryAbroadName(abroad) as abroad, code_alpha2, code_alpha3',
									self::TABLE_NAME,
									null,
									null,
									'description ASC');
	}

	/**
     * Gets all countries.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, null, null, 'description');
	}

	/**
     * Gets country by ID.
     *
     * @return array
     */
	public function get()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id = :id',
								[':id' => $this->id]);
	}

	/**
     * Gets country by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'code = :code',
								[':code' => $this->code]);
	}

	/**
     * Gets country by description.
     *
     * @return array
     */
	public function getByDescription()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'description = :description',
								[':description' => $this->description]);
	}

	/**
     * Checks if GUID exists.
     *
     * @return boolean
     */
	public function existsGuid()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'guid = :guid',
									[':guid' => $this->guid]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if GUID exists except this ID.
     *
     * @return boolean
     */
	public function existsGuidExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'guid = :guid AND id <> :id',
									[':guid' => $this->guid, ':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if code exists.
     *
     * @return boolean
     */
	public function existsCode()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code',
									[':code' => $this->code]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if code exists except this ID.
     *
     * @return boolean
     */
	public function existsCodeExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code AND id <> :id',
									[':code' => $this->code, ':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if description exists.
     *
     * @return boolean
     */
	public function existsDescription()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'description = :description',
									[':description' => $this->description]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if description exists except this ID.
     *
     * @return boolean
     */
	public function existsDescriptionExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'description = :description AND id <> :id',
									[':description' => $this->description, ':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if country used in citizenship.
     *
     * @return boolean
     */
	public function existsCitizenship()
	{
		$arr = $this->rowSelectAll('dict_countries.id',
									'personal INNER JOIN dict_countries ON personal.citizenship = dict_countries.id',
									'dict_countries.id = :id',
									[':id' => $this->id]);
		if (!empty($arr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if country used in address.
     *
     * @return boolean
     */
	public function existsAddress()
	{
		$arr = $this->rowSelectAll('dict_countries.id',
									'address INNER JOIN dict_countries ON address.id_country = dict_countries.id',
									'dict_countries.id = :id',
									[':id' => $this->id]);
		if (!empty($arr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves country data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all country data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	/**
     * Changes code.
     *
     * @return boolean
     */
	public function changeCode()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'code = :code',
								[':code' => $this->code],
								['id' => $this->id]);
	}

	/**
     * Changes description.
     *
     * @return boolean
     */
	public function changeName()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'description = :description',
								[':description' => $this->description],
								['id' => $this->id]);
	}

	/**
     * Changes fullname.
     *
     * @return boolean
     */
	public function changeFullName()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'fullname = :fullname',
								[':fullname' => $this->fullname],
								['id' => $this->id]);
	}

	/**
     * Changes abroad.
     *
     * @return boolean
     */
	public function changeAbroad()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'abroad = :abroad',
								[':abroad' => $this->abroad],
								['id' => $this->id]);
	}

	/**
     * Changes code alpha2.
     *
     * @return boolean
     */
	public function changeAlpha2()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'code_alpha2 = :code_alpha2',
								[':code_alpha2' => $this->code_alpha2],
								['id' => $this->id]);
	}

	/**
     * Changes code alpha3.
     *
     * @return boolean
     */
	public function changeAlpha3()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'code_alpha3 = :code_alpha3',
								[':code_alpha3' => $this->code_alpha3],
								['id' => $this->id]);
	}

	/**
     * Removes all countries.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Removes country.
     *
     * @return integer
     */
	public function clear()
	{
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
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
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено стран - '.$rows_del.'.';
				$log->value_old = null;
				$log->value_new = null;
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
			$this->code = (string)$property->Code;
			$country = $this->getByCode();

            if($property->DeletionMark == 'false') {
				$this->description = (string)$property->Description;
				$this->fullname = (string)$property->НаименованиеПолное;
				$this->code_alpha2 = (string)$property->КодАльфа2;
				$this->code_alpha3 = (string)$property->КодАльфа3;
				$this->guid = (string)$property->Ref_Key;
					if ($country == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создана новая страна с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении страны с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $country['id'];
						// code
						if ($country['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код страны с GUID ['.$this->guid.'].';
								$log->value_old = $country['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($country['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование страны с GUID ['.$this->guid.'].';
								$log->value_old = $country['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// full name
						if ($country['fullname'] != $this->fullname) {
							if ($this->changeFullName()) {
								$log->msg = 'Изменено полное наименование страны с GUID ['.$this->guid.'].';
								$log->value_old = $country['fullname'];
								$log->value_new = $this->fullname;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении полного наименования страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code alpha 2
						if ($country['code_alpha2'] != $this->code_alpha2) {
							if ($this->changeAlpha2()) {
								$log->msg = 'Изменён код альфа-2 страны с GUID ['.$this->guid.'].';
								$log->value_old = $country['code_alpha2'];
								$log->value_new = $this->code_alpha2;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода альфа-2 страны с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code alpha 3
						if ($country['code_alpha3'] != $this->code_alpha3) {
							if ($this->changeAlpha3()) {
								$log->msg = 'Изменён код альфа-3 страны с GUID ['.$this->guid.'].';
								$log->value_old = $country['code_alpha3'];
								$log->value_new = $this->code_alpha3;
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
