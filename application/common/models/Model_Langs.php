<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Langs extends Db_Helper
{
	/*
		Langs processing
	*/

	const TABLE_NAME = 'langs';

	public $id;
	public $code;
	public $name_original;
	public $name_eng;
	public $name_rus;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Langs rules.
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
				'name_original' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->name_original
									],
				'name_eng' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->name_eng
								],
				'name_rus' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->name_rus
								]
				];
	}

	/**
     * Langs grid.
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
				'code' => [
							'name' => 'Код',
							'type' => 'string'
							],
				'name_original' => [
									'name' => 'Наименование на родном языке',
									'type' => 'string'
									],
				'name_eng' => [
								'name' => 'Наименование на английском языке',
								'type' => 'string'
								],
				'name_rus' => [
								'name' => 'Наименование на русском языке',
								'type' => 'string'
								]
				];
	}

	/**
     * Gets all langs for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll('id, code, name_original, name_eng, name_rus',
									self::TABLE_NAME,
									null,
									null,
									'name_eng ASC');
	}

	/**
     * Gets lang by ID.
     *
     * @return array
     */
	public function get()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Gets all langs.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets lang by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'code = :code', [':code' => $this->code]);
	}

	/**
     * Checks if code exists.
     *
     * @return boolean
     */
	public function existsCode()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code',
									[':code' => $this->code]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if code exists except this ID.
     *
     * @return boolean
     */
	public function existsCodeExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code AND id <> :id',
									[':code' => $this->code, ':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves lang data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all lang data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	/**
     * Removes lang.
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
