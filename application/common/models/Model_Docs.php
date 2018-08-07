<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Docs extends Db_Helper
{
	/*
		Documents processing
	*/

	const TABLE_NAME = 'docs';

	public $id;
	public $doc_code;
	public $doc_name;
	public $table_name;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Documents rules.
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
				'doc_code' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->doc_code
							],
				'doc_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->doc_name
								],
				'table_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->table_name
								]
				];
	}

	/**
     * Documents grid.
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
				'doc_code' => [
								'name' => 'Код',
								'type' => 'string'
								],
				'doc_name' => [
								'name' => 'Наименование',
								'type' => 'string'
								],
				'table_name' => [
								'name' => 'Таблица',
								'type' => 'string'
								]
				];
	}

	/**
     * Gets all documents.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									null,
									null,
									'doc_name ASC');
	}

	/**
     * Gets documents by ID.
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
     * Gets document by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'doc_code = :doc_code',
									[':doc_code' => $this->doc_code]);
	}

	/**
     * Checks if documents code exists.
     *
     * @return boolean
     */
	public function existsCode()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'doc_code = :doc_code',
									[':doc_code' => $this->doc_code]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if documents code exists except this ID.
     *
     * @return boolean
     */
	public function existsCodeExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'doc_code = :doc_code AND id <> :id',
									[':doc_code' => $this->doc_code,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if documents name exists.
     *
     * @return boolean
     */
	public function existsName()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'doc_name = :doc_name',
									[':doc_name' => $this->doc_name]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if documents name exists except this ID.
     *
     * @return boolean
     */
	public function existsNameExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'doc_name = :doc_name AND id <> :id',
									[':doc_name' => $this->doc_name,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if documents used in dictionary scans.
     *
     * @return boolean
     */
	public function existsDictScans()
	{
		$arr = $this->rowSelectAll('dict_scans.id',
									'docs INNER JOIN dict_scans ON docs.id = dict_scans.id_doc',
									'docs.id = :id',
									[':id' => $this->id]);
		if (!empty($arr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves documents data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all documents data.
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
     * Removes documents.
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
