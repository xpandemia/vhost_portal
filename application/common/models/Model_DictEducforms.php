<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictEducforms extends Db_Helper
{
	/*
		Dictionary education forms processing
	*/

	const TABLE_NAME = 'dict_educforms';

	public $id;
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
     * Education forms rules.
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
     * Gets all education forms.
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
     * Gets education form by GUID.
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
     * Gets education form by code.
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
     * Saves education form data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes education form code.
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
     * Changes education form description.
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
     * Changes education form abbr.
     *
     * @return boolean
     */
	public function changeAbbr()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'abbr = :abbr',
								[':abbr' => $this->abbr],
								['id' => $this->id]);
	}

	/**
     * Removes all education forms.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads education forms.
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
				$log->msg = 'Удалено форм обучения - '.$rows_del.'.';
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
			$educform = $this->getByGuid();

            if($property->DeletionMark == 'false') {
				$this->code = (string)$property->Code;
				$this->description = (string)$property->Description;
				$this->abbr = (string)$property->СокращенноеНаименование;
					if ($educform == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создана новая форма обучения с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при создании формы обучения с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $educform['id'];
						// code
						if ($educform['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код формы обучения с GUID ['.$this->guid.'].';
								$log->value_old = $educform['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода формы обучения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($educform['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование формы обучения с GUID ['.$this->guid.'].';
								$log->value_old = $educform['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования формы обучения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// abbr
						if ($educform['abbr'] != $this->abbr) {
							if ($this->changeAbbr()) {
								$log->msg = 'Изменено сокращённое наименование формы обучения с GUID ['.$this->guid.'].';
								$log->value_old = $educform['abbr'];
								$log->value_new = $this->abbr;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении сокращённого наименования формы обучения с GUID ['.$this->guid.']!';
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
