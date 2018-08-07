<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictTestingScopes extends Db_Helper
{
	/*
		Dictionary testing scopes processing
	*/

	const TABLE_NAME = 'dict_testing_scopes';

	public $id;
	public $isfolder;
	public $parent_key;
	public $code;
	public $description;
	public $guid;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Testing scopes rules.
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
							]
				];
	}

	/**
     * Gets all testing scopes.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, 'isfolder = :isfolder', [':isfolder' => 0]);
	}

	/**
     * Gets testing scope by GUID.
     *
     * @return array
     */
	public function getByGuid()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'guid = :guid', [':guid' => $this->guid]);
	}

	/**
     * Gets testing scope by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'code = :code', [':code' => $this->code]);
	}

	/**
     * Gets EGE.
     *
     * @return array
     */
	public function getEge()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'description = :description',
									[':description' => 'ЕГЭ']);
	}

	/**
     * Gets EXAM.
     *
     * @return array
     */
	public function getExam()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'description = :description',
									[':description' => 'Экзамен']);
	}

	/**
     * Gets TEST.
     *
     * @return array
     */
	public function getTest()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'description = :description',
									[':description' => 'Тестирование']);
	}

	/**
     * Gets entrance exams.
     *
     * @return array
     */
	public function getEntranceExams()
	{
		return $this->rowSelectAll('code, description',
									self::TABLE_NAME,
									'description in (:description1, :description2)',
									[':description1' => 'ЕГЭ',
									':description2' => 'Экзамен']);
	}

	/**
     * Saves testing scope data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes testing scope isfolder.
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
     * Changes testing scope parent.
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
     * Changes testing scope code.
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
     * Changes testing scope description.
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
     * Removes all testing scopes.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads testing scopes.
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
				$log->msg = 'Удалено видов контроля - '.$rows_del.'.';
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
			$test = $this->getByGuid();

            if($property->DeletionMark == 'false') {
				$this->isfolder = ((string)$property->IsFolder == 'false') ? 0 : 1;
				$this->parent_key = (string)$property->Parent_Key;
				$this->code = (string)$property->Code;
				$this->description = (string)$property->Description;
					if ($test == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создан новый вид контроля с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении вида контроля с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $test['id'];
						// isfolder
						if ($test['isfolder'] != $this->isfolder) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак каталога вида контроля с GUID ['.$this->guid.'].';
								$log->value_old = $test['isfolder'];
								$log->value_new = $this->isfolder;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака каталога вида контроля с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// parent_key
						if ($test['parent_key'] != $this->parent_key) {
							if ($this->changeParent()) {
								$log->msg = 'Изменён родитель вида контроля с GUID ['.$this->guid.'].';
								$log->value_old = $test['parent_key'];
								$log->value_new = $this->parent_key;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении родителя вида контроля с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code
						if ($test['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код вида контроля с GUID ['.$this->guid.'].';
								$log->value_old = $test['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода вида контроля с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($test['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование вида контроля с GUID ['.$this->guid.'].';
								$log->value_old = $test['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования вида контроля с GUID ['.$this->guid.']!';
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
