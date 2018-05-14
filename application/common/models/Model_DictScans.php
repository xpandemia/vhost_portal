<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictScans extends Db_Helper
{
	/*
		Dictionary scans processing
	*/

	const TABLE_NAME = 'dict_scans';

	public $doc_code;
	public $scan_code;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets all scans.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets scans by document.
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
								'numb');
	}

	/**
     * Gets scan by code.
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

	public function __destruct()
	{
		$this->db = null;
	}
}
