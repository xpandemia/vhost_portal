<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictCitizenship extends Db_Helper
{
	/*
		Dictionary citizenship processing
	*/

	const TABLE_NAME = 'dict_citizenship';

	public $id;
	public $citizenship_code;
	public $citizenship_name;
	public $guid;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets all citizenships.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets citizenship by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'citizenship_code = :citizenship_code',
								[':citizenship_code' => $this->citizenship_code]);
	}

	/**
     * Gets citizenship by name.
     *
     * @return array
     */
	public function getByName()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'citizenship_name = :citizenship_name',
								[':citizenship_name' => $this->citizenship_name]);
	}

	/**
     * Saves citizenship data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('citizenship_code, citizenship_name, guid',
								self::TABLE_NAME,
								':citizenship_code, :citizenship_name, :guid',
								[':citizenship_code' => $this->citizenship_code, ':citizenship_name' => $this->citizenship_name, ':guid' => $this->guid]);
	}

	/**
     * Changes citizenship code.
     *
     * @return boolean
     */
	public function changeCode()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'citizenship_code = :citizenship_code',
								[':citizenship_code' => $this->citizenship_code],
								['id' => $this->id]);
	}

	/**
     * Changes citizenship name.
     *
     * @return boolean
     */
	public function changeName()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'citizenship_name = :citizenship_name',
								[':citizenship_name' => $this->citizenship_name],
								['id' => $this->id]);
	}

	/**
     * Removes all citizenships.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads citizenships.
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
				$log->msg = 'Удалено гражданств - '.$rows_del.'.';
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
			$this->citizenship_code = (string)$property->Code;
			$citizenship = $this->getByCode();

            if($property->DeletionMark == 'false') {
				$this->citizenship_name = (string)$property->Description;
				$this->guid = (string)$property->Ref_Key;
					if ($citizenship == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создано новое гражданство с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении гражданства с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $citizenship['id'];
						// code
						if ($citizenship['citizenship_code'] != $this->citizenship_code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код гражданства с GUID ['.$this->guid.'].';
								$log->value_old = $citizenship['citizenship_code'];
								$log->value_new = $this->citizenship_code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода гражданства с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// name
						if ($citizenship['citizenship_name'] != $this->citizenship_name) {
							if ($this->changeName()) {
								$log->msg = 'Изменено наименование гражданства с GUID ['.$this->guid.'].';
								$log->value_old = $citizenship['citizenship_name'];
								$log->value_new = $this->citizenship_name;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования гражданства с GUID ['.$this->guid.']!';
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
