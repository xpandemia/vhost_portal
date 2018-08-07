<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictEductypes extends Db_Helper
{
	/*
		Dictionary education types processing
	*/

	const TABLE_NAME = 'dict_eductypes';

	public $id;
	public $isfolder;
	public $parent_key;
	public $code;
	public $description;
	public $guid;
	public $active;

	const EDUCTYPES_DIPLOMA = ['000000001', '000000003', '000000004', '000000006'];

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Education types rules.
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
				'isfolder' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->isfolder
							],
				'parent_key' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->parent_key
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
				'guid' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->guid
							],
				'active' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->active
							]
				];
	}

	/**
     * Gets all education types.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, 'isfolder = :isfolder', [':isfolder' => 0]);
	}

	/**
     * Gets education type by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'code = :code', [':code' => $this->code]);
	}

	/**
     * Gets educations.
     *
     * @return array
     */
	public function getEducs()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'active = :active',
									[':active' => 1]);
	}

	/**
     * Saves education type data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->active = 1;
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes education type isfolder.
     *
     * @return boolean
     */
	public function changeIsfolder()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'isfolder = :isfolder',
								[':isfolder' => $this->isfolder],
								['id' => $this->id]);
	}

	/**
     * Changes education type parent.
     *
     * @return boolean
     */
	public function changeParent()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'parent_key = :parent_key',
								[':parent_key' => $this->parent_key],
								['id' => $this->id]);
	}

	/**
     * Changes education type code.
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
     * Changes education type description.
     *
     * @return boolean
     */
	public function changeDescription()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'description = :description',
								[':description' => $this->description],
								['id' => $this->id]);
	}

	/**
     * Removes all education types.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads education types.
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
				$log->msg = 'Удалено видов образования - '.$rows_del.'.';
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
			$eductype = $this->getByCode();

            if($property->DeletionMark == 'false') {
				$this->isfolder = ((string)$property->IsFolder == 'false') ? 0 : 1;
				$this->parent_key = (string)$property->Parent_Key;
				$this->description = (string)$property->Description;
				$this->guid = (string)$property->Ref_Key;
					if ($eductype == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создан новый вид образования с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении вида образования с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $eductype['id'];
						// isfolder
						if ($eductype['isfolder'] != $this->isfolder) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак каталога вида образования с GUID ['.$this->guid.'].';
								$log->value_old = $eductype['isfolder'];
								$log->value_new = $this->isfolder;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака каталога вида образования с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// parent_key
						if ($eductype['parent_key'] != $this->parent_key) {
							if ($this->changeParent()) {
								$log->msg = 'Изменён родитель вида образования с GUID ['.$this->guid.'].';
								$log->value_old = $eductype['parent_key'];
								$log->value_new = $this->parent_key;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении родителя вида образования с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code
						if ($eductype['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код вида образования с GUID ['.$this->guid.'].';
								$log->value_old = $eductype['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода вида образования с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($eductype['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование вида образования с GUID ['.$this->guid.'].';
								$log->value_old = $eductype['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования вида образования с GUID ['.$this->guid.']!';
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
