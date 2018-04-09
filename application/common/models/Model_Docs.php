<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Docs extends Db_Helper
{
	/*
		Docs processing
	*/

	const TABLE_NAME = 'docs';

	public $doc_code;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets doc by code.
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

	public function __destruct()
	{
		$this->db = null;
	}
}
