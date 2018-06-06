<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictFinances extends Db_Helper
{
	/*
		Dictionary finances processing
	*/

	const TABLE_NAME = 'dict_finances';

	public $id;
	public $isfolder;
	public $parent_key;
	public $code;
	public $description;
	public $abbr;
	public $guid;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Finances rules.
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
				'abbr' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->abbr
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
     * Gets all finances.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									null,
									null,
									'description');
	}

	/**
     * Gets finance by GUID.
     *
     * @return array
     */
	public function getByGuid()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'guid = :guid',
									[':guid' => $this->guid]);
	}

	/**
     * Gets finance by code.
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
     * Saves finance data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes finance isfolder.
     *
     * @return boolean
     */
	public function changeIsfolder()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'isfolder = :isfolder',
								[':isfolder' => $this->isfolder]);
	}

	/**
     * Changes finance parent.
     *
     * @return boolean
     */
	public function changeParent()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'parent_key = :parent_key',
								[':parent_key' => $this->parent_key]);
	}

	/**
     * Changes finance code.
     *
     * @return boolean
     */
	public function changeCode()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'code = :code',
								[':code' => $this->code]);
	}

	/**
     * Changes finance description.
     *
     * @return boolean
     */
	public function changeDescription()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'description = :description',
								[':description' => $this->description]);
	}

	/**
     * Changes finance abbr.
     *
     * @return boolean
     */
	public function changeAbbr()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'abbr = :abbr',
								[':abbr' => $this->abbr]);
	}

	/**
     * Removes all finances.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads finances.
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
				$log->msg = 'Удалено оснований поступления - '.$rows_del.'.';
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
			$this->guid = (string)$property->Ref_Key;
			$finance = $this->getByGuid();

            if($property->DeletionMark == 'false') {
				$this->isfolder = ((string)$property->IsFolder == 'false') ? 0 : 1;
				$this->parent_key = (string)$property->Parent_Key;
				$this->code = (string)$property->Code;
				$this->description = (string)$property->Description;
				$this->abbr = (string)$property->СокращенноеНаименование;
					if ($finance == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создано новое основание поступления с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при создании основание поступления с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						// isfolder
						if ($finance['isfolder'] != $this->isfolder) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак каталога основания поступления с GUID ['.$this->guid.'].';
								$log->value_old = $finance['isfolder'];
								$log->value_new = $this->isfolder;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака каталога основания поступления с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// parent_key
						if ($finance['parent_key'] != $this->parent_key) {
							if ($this->changeParent()) {
								$log->msg = 'Изменён родитель основания поступления с GUID ['.$this->guid.'].';
								$log->value_old = $finance['parent_key'];
								$log->value_new = $this->parent_key;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении родителя основания поступления с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code
						if ($finance['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код основания поступления с GUID ['.$this->guid.'].';
								$log->value_old = $finance['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода основания поступления с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($finance['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование основания поступления с GUID ['.$this->guid.'].';
								$log->value_old = $finance['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования основания поступления с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// abbr
						if ($finance['abbr'] != $this->abbr) {
							if ($this->changeAbbr()) {
								$log->msg = 'Изменено сокращённое наименование основания поступления с GUID ['.$this->guid.'].';
								$log->value_old = $finance['abbr'];
								$log->value_new = $this->abbr;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении сокращённого наименования основания поступления с GUID ['.$this->guid.']!';
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
