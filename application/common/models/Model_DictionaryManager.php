<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictionaryManager extends Db_Helper
{
	/*
		Dictionary manager processing
	*/

	const TABLE_NAME = 'dictionary_manager';

	public $id;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets all dictionaries.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets dictionary by id.
     *
     * @return array
     */
	public function getById()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id = :id',
								[':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
