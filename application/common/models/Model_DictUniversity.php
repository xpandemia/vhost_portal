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
     * Gets all dictionary university.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
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

	public function __destruct()
	{
		$this->db = null;
	}
}
