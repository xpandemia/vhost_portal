<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictIndAchievs extends Db_Helper
{
	/*
		Dictionary individual achievments processing
	*/

	const TABLE_NAME = 'dict_ind_achievs';

	public $id;
	public $code;
	public $description;
	public $numb;
	public $abbr;
	public $confirm;
	public $guid;
	public $archive;

	const IA_BSU = ['000000002', '000000003', '000000004', '000000005', '000000010', '000000015', '000000017', '000000022', '000000023', '000000031', '000000032', '000000035', '000000039', '000000040', '000000041', '000000042', '000000044', '000000045', '000000046', '000000047', '000000048', '000000049'];

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Individual achievments rules.
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
				'numb' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'abbr' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->abbr
							],
				'confirm' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->confirm
							],
				'guid' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->guid
							],
				'archive' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->archive
							]
				];
	}

	/**
     * Gets all individual achievments.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'archive = :archive',
									[':archive' => 0],
									'description');
	}

	/**
     * Gets individual achievment by GUID.
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
     * Gets individual achievment by code.
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
     * Saves individual achievment data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes individual achievment code.
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
     * Changes individual achievment description.
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
     * Changes individual achievment numb.
     *
     * @return boolean
     */
	public function changeNumb()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'numb = :numb',
								[':numb' => $this->numb],
								['id' => $this->id]);
	}

	/**
     * Changes individual achievment abbr.
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
     * Changes individual achievment confirm.
     *
     * @return boolean
     */
	public function changeConfirm()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'confirm = :confirm',
								[':confirm' => $this->confirm],
								['id' => $this->id]);
	}

	/**
     * Removes all individual achievments.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads individual achievments.
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
				$log->msg = 'Удалено индивидуальных достижений - '.$rows_del.'.';
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
			$ia = $this->getByGuid();

            if($property->DeletionMark == 'false') {
				$this->code = (string)$property->Code;
				$this->description = (string)$property->Description;
				$this->numb = (string)$property->НомерИД;
				$this->abbr = (string)$property->СокращенноеНаименование;
				$this->confirm = ((string)$property->ТребуетсяПодтверждающийДокумент == 'false') ? 0 : 1;
				$this->archive = (in_array($this->code, self::IA_BSU)) ? 0 : 1;
					if ($ia == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создано новое индивидуальное достижение с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении индивидуального достижения с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $ia['id'];
						// code
						if ($ia['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($ia['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// numb
						if ($ia['numb'] != $this->numb) {
							if ($this->changeNumb()) {
								$log->msg = 'Изменён номер ИД индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['numb'];
								$log->value_new = $this->numb;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении номера ИД индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// abbr
						if ($ia['abbr'] != $this->abbr) {
							if ($this->changeAbbr()) {
								$log->msg = 'Изменено сокращённое наименование индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['abbr'];
								$log->value_new = $this->abbr;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении сокращённого наименования индивидуального достижения с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// confirm
						if ($ia['confirm'] != $this->confirm) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак подтверждающего документа индивидуального достижения с GUID ['.$this->guid.'].';
								$log->value_old = $ia['confirm'];
								$log->value_new = $this->confirm;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака подтверждающего документа индивидуального достижения с GUID ['.$this->guid.']!';
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
