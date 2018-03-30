<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictionaryManager extends Db_Helper
{
	/*
		Dictionary manager processing
	*/

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
		return $this->rowSelectAll('*', 'dictionary_manager');
	}

	/**
     * Gets dictionary by code.
     *
     * @return array
     */
	public function getById()
	{
		return $this->rowSelectOne('*', 'dictionary_manager', 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
