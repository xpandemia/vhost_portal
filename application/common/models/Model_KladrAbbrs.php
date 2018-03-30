<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_KladrAbbrs extends Db_Helper
{
	/*
		KLADR abbrs processing
	*/

	public $abbr_code;
	public $abbr_name;
	public $level;
	public $abbr;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets KLADR abbr by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', 'kladr_abbrs', 'abbr_code = :abbr_code', [':abbr_code' => $this->abbr_code]);
	}

	/**
     * Saves KLADR abbr data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('abbr_code, abbr_name, level, abbr',
								'kladr_abbrs',
								':abbr_code, :abbr_name, :level, :abbr',
								[':abbr_code' => $this->abbr_code, ':abbr_name' => $this->abbr_name, ':level' => $this->level, ':abbr' => $this->abbr]);
	}

	/**
     * Changes KLADR abbr name.
     *
     * @return boolean
     */
	public function changeName()
	{
		return $this->rowUpdate('kladr_abbrs',
								'abbr_name = :abbr_name',
								[':abbr_name' => $this->abbr_name]);
	}

	/**
     * Changes KLADR abbr level.
     *
     * @return boolean
     */
	public function changeLevel()
	{
		return $this->rowUpdate('kladr_abbrs',
								'level = :level',
								[':level' => $this->level]);
	}

	/**
     * Changes KLADR abbr abbr.
     *
     * @return boolean
     */
	public function changeAbbr()
	{
		return $this->rowUpdate('kladr_abbrs',
								'abbr = :abbr',
								[':abbr' => $this->abbr]);
	}

	/**
     * Removes all KLADR abbrs.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete('kladr_abbrs');
	}

	/**
     * Loads KLADR abbrs.
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
				$log->msg = 'Удалено адресных сокращений - '.$rows_del.'.';
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
			$this->abbr_code = (string)$property->Код;
			$abbr = $this->getByCode();

            $this->abbr_name = (string)$property->Наименование;
			$this->level = (string)$property->Уровень;
			$this->abbr = (string)$property->Сокращение;
				if ($abbr == null) {
					// insert
					if ($this->save()) {
						$log->msg = 'Создано новое адресное сокращение с кодом ['.$this->abbr_code.'].';
						$log->dt_created = date('Y-m-d H:i:s');
						$log->save();
						$rows_ins++;
					} else {
						$result['error_msg'] = 'Ошибка при сохранении адресного сокращения с кодом ['.$this->abbr_code.']!';
						return $result;
					}
				} else {
					// update
					$upd = 0;
					// name
					if ($abbr['abbr_name'] != $this->abbr_name) {
						if ($this->changeName()) {
							$log->msg = 'Изменено наименование адресного сокращения с кодом ['.$this->abbr_code.'].';
							$log->value_old = $kladr['abbr_name'];
							$log->value_new = $this->abbr_name;
							$log->dt_created = date('Y-m-d H:i:s');
							$log->save();
							$upd = 1;
						} else {
							$result['error_msg'] = 'Ошибка при изменении наименования адресного сокращения с кодом ['.$this->abbr_code.']!';
							return $result;
						}
					}
					// level
					if ($abbr['level'] != $this->level) {
						if ($this->changeLevel()) {
							$log->msg = 'Изменён уровень адресного сокращения с кодом ['.$this->abbr_code.'].';
							$log->value_old = $kladr['level'];
							$log->value_new = $this->level;
							$log->dt_created = date('Y-m-d H:i:s');
							$log->save();
							$upd = 1;
						} else {
							$result['error_msg'] = 'Ошибка при изменении уровня адресного сокращения с кодом ['.$this->abbr_code.']!';
							return $result;
						}
					}
					// abbr
					if ($abbr['abbr'] != $this->abbr) {
						if ($this->changeAbbr()) {
							$log->msg = 'Изменено сокращения адресного сокращения с кодом ['.$this->abbr_code.'].';
							$log->value_old = $kladr['abbr'];
							$log->value_new = $this->abbr;
							$log->dt_created = date('Y-m-d H:i:s');
							$log->save();
							$upd = 1;
						} else {
							$result['error_msg'] = 'Ошибка при изменении сокращения адресного сокращения с кодом ['.$this->abbr_code.']!';
							return $result;
						}
					}
					// counter
					if ($upd == 1) {
						$rows_upd++;
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
