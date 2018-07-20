<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictEge extends Db_Helper
{
	/*
		Dictionary ege processing
	*/

	const TABLE_NAME = 'dict_ege';

	public $id;
	public $code;
	public $description;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary ege rules.
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
     * Gets all dictionary ege.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, null, null, 'description');
	}

	/**
     * Gets dictionary ege by code.
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
     * Gets dictionary ege by description.
     *
     * @return array
     */
	public function getByDescription()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'description = :description',
								[':description' => $this->description]);
	}

	/**
     * Saves dictionary ege data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all dictionary ege data.
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
     * Removes all dictionary ege.
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
