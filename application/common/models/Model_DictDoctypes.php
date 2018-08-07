<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictDoctypes extends Db_Helper
{
	/*
		Dictionary document types processing
	*/

	const TABLE_NAME = 'dict_doctypes';

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
     * Document types rules.
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
     * Gets all document types.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, 'isfolder = :isfolder', [':isfolder' => 0]);
	}

	/**
     * Gets document type by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'code = :code', [':code' => $this->code]);
	}

	/**
     * Gets passports.
     *
     * @return array
     */
	public function getPassports()
	{
		return $this->rowSelectAll('d1.*',
									'dict_doctypes d1 INNER JOIN dict_doctypes d2 ON d1.parent_key = d2.guid',
									'd2.isfolder = :isfolder AND d2.description = :description',
									[':isfolder' => 1,
									':description' => 'Паспорта']);
	}

	/**
     * Gets passports BSU.
     *
     * @return array
     */
	public function getPassportsBsu()
	{
		return $this->rowSelectAll('d1.*',
									'dict_doctypes d1 INNER JOIN dict_doctypes d2 ON d1.parent_key = d2.guid',
									'd2.isfolder = :isfolder AND d2.description = :description AND d1.code in (:code1, :code2, :code3, :code4, :code5, :code6, :code7)',
									[':isfolder' => 1,
									':description' => 'Паспорта',
									':code1' => '000000047',
									':code2' => '000000049',
									':code3' => '000000075',
									':code4' => '000000087',
									':code5' => '000000202',
									':code6' => '000000223',
									':code7' => '000000226']);
	}

	/**
     * Gets russian passports.
     *
     * @return array
     */
	public function getPassportsRussia()
	{
		return $this->rowSelectAll('*',
									'dict_doctypes',
									'code in (:code1, :code2)',
									[':code1' => '000000047',
									':code2' => '000000202']);
	}

	/**
     * Gets foreign passports.
     *
     * @return array
     */
	public function getPassportsForeign()
	{
		return $this->rowSelectAll('*',
									'dict_doctypes',
									'code in (:code1, :code2, :code3, :code4, :code5)',
									[':code1' => '000000049',
									':code2' => '000000075',
									':code3' => '000000087',
									':code4' => '000000223',
									':code5' => '000000226']);
	}

	/**
     * Gets education documents.
     *
     * @return array
     */
	public function getDiplomas()
	{
		return $this->rowSelectAll('d1.*',
									'dict_doctypes d1 INNER JOIN dict_doctypes d2 ON d1.parent_key = d2.guid',
									'd2.isfolder = :isfolder AND d2.description = :description',
									[':isfolder' => 1,
									':description' => 'Документы об образовании'],
									'd1.description');
	}

	/**
     * Gets education documents by education type code.
     *
     * @return array
     */
	public function getDiplomasByEducCode()
	{
		return $this->rowSelectAll('dict_doctypes.*',
									'dict_doctypes INNER JOIN eductypes_doctypes ON dict_doctypes.id = eductypes_doctypes.id_doctype'.
									' INNER JOIN dict_eductypes ON eductypes_doctypes.id_eductype = dict_eductypes.id',
									'dict_eductypes.code = :code',
									[':code' => $this->code_educ]);
	}

	/**
     * Saves document type data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes document type isfolder.
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
     * Changes document type parent.
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
     * Changes document type code.
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
     * Changes document type description.
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
     * Removes all document types.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads document types.
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
				$log->msg = 'Удалено типов документов - '.$rows_del.'.';
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
			$doctype = $this->getByCode();

            if($property->DeletionMark == 'false') {
				$this->isfolder = ((string)$property->IsFolder == 'false') ? 0 : 1;
				$this->parent_key = (string)$property->Parent_Key;
				$this->description = (string)$property->Description;
				$this->guid = (string)$property->Ref_Key;
					if ($doctype == null) {
						// insert
						if ($this->save()) {
							$log->msg = 'Создан новый тип документа с GUID ['.$this->guid.'].';
							$log->value_old = null;
							$log->value_new = null;
							$log->save();
							$rows_ins++;
						} else {
							$result['error_msg'] = 'Ошибка при сохранении типа документа с GUID ['.$this->guid.']!';
							return $result;
						}
					} else {
						// update
						$upd = 0;
						$this->id = $doctype['id'];
						// isfolder
						if ($doctype['isfolder'] != $this->isfolder) {
							if ($this->changeIsfolder()) {
								$log->msg = 'Изменён признак каталога типа документа с GUID ['.$this->guid.'].';
								$log->value_old = $doctype['isfolder'];
								$log->value_new = $this->isfolder;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении признака каталога типа документа с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// parent_key
						if ($doctype['parent_key'] != $this->parent_key) {
							if ($this->changeParent()) {
								$log->msg = 'Изменён родитель типа документа с GUID ['.$this->guid.'].';
								$log->value_old = $doctype['parent_key'];
								$log->value_new = $this->parent_key;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении родителя типа документа с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// code
						if ($doctype['code'] != $this->code) {
							if ($this->changeCode()) {
								$log->msg = 'Изменён код типа документа с GUID ['.$this->guid.'].';
								$log->value_old = $doctype['code'];
								$log->value_new = $this->code;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении кода типа документа с GUID ['.$this->guid.']!';
								return $result;
							}
						}
						// description
						if ($doctype['description'] != $this->description) {
							if ($this->changeDescription()) {
								$log->msg = 'Изменено наименование типа документа с GUID ['.$this->guid.'].';
								$log->value_old = $doctype['description'];
								$log->value_new = $this->description;
								$log->save();
								$upd = 1;
							} else {
								$result['error_msg'] = 'Ошибка при изменении наименования типа документа с GUID ['.$this->guid.']!';
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
