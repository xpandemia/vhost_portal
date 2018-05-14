<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictForeignLangs extends Db_Helper
{
	/*
		Dictionary foreign languages processing
	*/

	const TABLE_NAME = 'dict_foreign_langs';

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
     * Foreign languages rules.
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
     * Gets all foreign languages.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'isfolder = :isfolder',
									[':isfolder' => 0],
									'description');
	}

	/**
     * Gets BSU foreign languages.
     *
     * @return array
     */
	public function getBsu()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'code in (:code1, :code2, :code3)',
									[':code1' => '000000002',
									':code2' => '000000005',
									':code3' => '000000006'],
									'description');
	}

	/**
     * Gets foreign language by GUID.
     *
     * @return array
     */
	public function getByGuid()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'guid = :guid', [':guid' => $this->guid]);
	}

	/**
     * Gets foreign language by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'code = :code', [':code' => $this->code]);
	}

	/**
     * Saves foreign language data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes foreign language isfolder.
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
     * Changes foreign language parent.
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
     * Changes foreign language code.
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
     * Changes foreign language description.
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
     * Removes all foreign languages.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads foreign languages.
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
				$log->msg = 'Удалено иностранных языков - '.$rows_del.'.';
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
			$lang = $this->getByGuid();

            if($property->DeletionMark == 'false') {
				$this->isfolder = ((string)$property->IsFolder == 'false') ? 0 : 1;
				$this->parent_key = (string)$property->Parent_Key;
				$this->code = (string)$property->Code;
				$this->description = (string)$property->Description;
					if ($lang == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создан новый иностранный язык с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении иностранного языка с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						// isfolder
						if ($lang['isfolder'] != $this->isfolder) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак каталога иностранного языка с GUID ['.$this->guid.'].';
								$log->value_old = $lang['isfolder'];
								$log->value_new = $this->isfolder;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака каталога иностранного языка с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// parent_key
						if ($lang['parent_key'] != $this->parent_key) {
							if ($this->changeParent()) {
								$log->msg = 'Изменён родитель иностранного языка с GUID ['.$this->guid.'].';
								$log->value_old = $lang['parent_key'];
								$log->value_new = $this->parent_key;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении родителя иностранного языка с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code
						if ($lang['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код иностранного языка с GUID ['.$this->guid.'].';
								$log->value_old = $lang['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода иностранного языка с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($lang['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование иностранного языка с GUID ['.$this->guid.'].';
								$log->value_old = $lang['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования иностранного языка с GUID ['.$this->guid.']!';
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
