<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictScans extends Db_Helper
{
	/*
		Dictionary scans processing
	*/

	const TABLE_NAME = 'dict_scans';

	public $id;
	public $id_doc;
	public $numb;
	public $scan_code;
	public $scan_name;
	public $required;
	public $main;

	public $doc_code;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary scans rules.
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
				'id_doc' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->id_doc
							],
				'numb' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'scan_code' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->scan_code
								],
				'scan_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->scan_name
								],
				'required' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->required
								],
				'main' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->main
							]
				];
	}

	/**
     * Dictionary scans grid.
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
				'docs' => [
						'name' => 'Документ',
						'type' => 'string'
						],
				'numb' => [
							'name' => 'Номер пп',
							'type' => 'int'
							],
				'scan_code' => [
								'name' => 'Код',
								'type' => 'string'
								],
				'scan_name' => [
								'name' => 'Наименование',
								'type' => 'string'
								],
				'required' => [
								'name' => 'Обязательность',
								'type' => 'string'
								],
				'main' => [
							'name' => 'Основная группа',
							'type' => 'string'
							]
				];
	}

	/**
     * Gets all dictionary scans for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("dict_scans.id, docs.doc_name as docs, numb, scan_code, scan_name, if(required = 0, 'Нет', 'Да') as required, if(main = 0, 'Нет', 'Да') as main",
									'dict_scans INNER JOIN docs ON dict_scans.id_doc = docs.id',
									null,
									null,
									'docs, numb, id ASC');
	}

	/**
     * Syncs dictionary scans numbs for document.
     *
     * @return void
     */
	public function syncNumbs()
	{
		if ($this->numb > 0) {
			$numb = $this->numb;
			$scans_arr = rowSelectAll('id, numb',
										self::TABLE_NAME,
										'id_doc = :id_doc AND main = :main',
										[':id_doc' => $this->id_doc,
										':main' => 1],
										'numb ASC');
			if ($scans_arr) {
				foreach ($scans_arr as $scans_row) {
					if ($scans_row['numb'] > $numb) {
						$this->id = $scans_row['id'];
						$this->numb = $scans_row['numb'] + 1;
						$this->changeNumb();
					}
				}
			}
		}
	}

	/**
     * Gets all dictionary scans.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets dictionary scans by document.
     *
     * @return array
     */
	public function getByDocument()
	{
		return $this->rowSelectAll(self::TABLE_NAME.'.id, scan_code, scan_name, required',
									self::TABLE_NAME.' INNER JOIN docs ON '.self::TABLE_NAME.'.id_doc = docs.id',
									'doc_code = :doc_code AND main = :main',
									[':doc_code' => $this->doc_code,
									':main' => 1],
									'numb ASC');
	}

	/**
     * Gets required dictionary scans by document.
     *
     * @return array
     */
	public function getByDocumentRequired()
	{
		return $this->rowSelectAll(self::TABLE_NAME.'.id, scan_code, scan_name, required',
									self::TABLE_NAME.' INNER JOIN docs ON '.self::TABLE_NAME.'.id_doc = docs.id',
									'doc_code = :doc_code AND required = :required AND main = :main',
									[':doc_code' => $this->doc_code,
									':required' => 1,
									':main' => 1],
									'numb ASC');
	}

	/**
     * Gets dictionary scans by ID.
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
     * Gets dictionary scans by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne(self::TABLE_NAME.'.id, scan_code, scan_name, required',
								self::TABLE_NAME.' INNER JOIN docs ON '.self::TABLE_NAME.'.id_doc = docs.id',
								'doc_code = :doc_code AND scan_code = :scan_code',
								[':doc_code' => $this->doc_code,
								':scan_code' => $this->scan_code]);
	}

	/**
     * Gets dictionary scans max numb by document.
     *
     * @return int
     */
	public function getNumbMax() : int
	{
		$scans_row = $this->rowSelectOne('max(numb) as numb',
										self::TABLE_NAME,
										'id_doc = :id_doc AND main = :main',
										[':id_doc' => $this->id_doc,
										':main' => 1]);
		if ($scans_row) {
			return $scans_row['numb'];
		} else {
			return 0;
		}
	}

	/**
     * Checks if dictionary scans code exists.
     *
     * @return boolean
     */
	public function existsCode()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_doc = :id_doc AND scan_code = :scan_code',
									[':id_doc' => $this->id_doc,
									':scan_code' => $this->scan_code]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary scans code exists except this ID.
     *
     * @return boolean
     */
	public function existsCodeExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_doc = :id_doc AND scan_code = :scan_code AND id <> :id',
									[':id_doc' => $this->id_doc,
									':scan_code' => $this->scan_code,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary scans name exists.
     *
     * @return boolean
     */
	public function existsName()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_doc = :id_doc AND scan_name = :scan_name',
									[':id_doc' => $this->id_doc,
									':scan_name' => $this->scan_name]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary scans name exists except this ID.
     *
     * @return boolean
     */
	public function existsNameExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_doc = :id_doc AND scan_name = :scan_name AND id <> :id',
									[':id_doc' => $this->id_doc,
									':scan_name' => $this->scan_name,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary scans used in scans.
     *
     * @return boolean
     */
	public function existsScans()
	{
		$arr = $this->rowSelectAll('scans.id',
									'scans INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
									'dict_scans.id = :id',
									[':id' => $this->id]);
		if (!empty($arr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves dictionary scans data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all dictionary scans data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME,
								$prepare['fields'],
								$prepare['params'],
								['id' => $this->id]);
	}

	/**
     * Changes dictionary scans numb.
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
     * Removes dictionary scans.
     *
     * @return integer
     */
	public function clear()
	{
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
