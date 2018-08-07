<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictUniversity extends Db_Helper
{
	/*
		Dictionary university processing
	*/

	const TABLE_NAME = 'dict_university';

	public $id;
	public $code;
	public $description;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary university rules.
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
								]
				];
	}

	/**
     * Dictionary university grid.
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
				'description' => [
								'name' => 'Наименование',
								'type' => 'string'
								]
				];
	}

	/**
     * Gets all dictionary university.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets dictionary university by ID.
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
     * Gets dictionary university by code.
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
     * Checks if dictionary university code exists.
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
     * Checks if dictionary university code exists except this ID.
     *
     * @return boolean
     */
	public function existsCodeExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code AND id <> :id',
									[':code' => $this->code,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary university description exists.
     *
     * @return boolean
     */
	public function existsDescription()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'description = :description',
									[':description' => $this->description]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary university description exists except this ID.
     *
     * @return boolean
     */
	public function existsDescriptionExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'description = :description AND id <> :id',
									[':description' => $this->description,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary university used in applications.
     *
     * @return boolean
     */
	public function existsApplications()
	{
		$arr = $this->rowSelectAll('application.id',
									'application INNER JOIN dict_university ON application.id_university = dict_university.id',
									'dict_university.id = :id',
									[':id' => $this->id]);
		if (!empty($arr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves dictionary university data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all dictionary university data.
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
     * Removes all dictionary university.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Removes dictionary university.
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
