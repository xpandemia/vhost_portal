<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Scans extends Db_Helper
{
	/*
		Scans processing
	*/

	const TABLE_NAME = 'scans';

	public $id;
	public $id_doc;
	public $id_row;
	public $id_scans;
	public $file_data;
	public $file_name;
	public $file_type;
	public $file_size;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets scan by ID.
     *
     * @return array
     */
	public function get()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Gets scan by document.
     *
     * @return array
     */
	public function getByDoc()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_doc = :id_doc AND id_scans = :id_scans',
								[':id_doc' => $this->id_doc,
								':id_scans' => $this->id_scans]);
	}

	/**
     * Saves scan data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('id_doc, id_row, id_scans, file_data, file_name, file_type, file_size, dt_created',
								self::TABLE_NAME,
								':id_doc, :id_row, :id_scans, :file_data, :file_name, :file_type, :file_size, :dt_created',
								[':id_doc' => $this->id_doc,
								':id_row' => $this->id_row,
								':id_scans' => $this->id_scans,
								':file_data' => $this->file_data,
								':file_name' => $this->file_name,
								':file_type' => $this->file_type,
								':file_size' => $this->file_size,
								':dt_created' => $this->dt_created]);
	}

	/**
     * Removes scan.
     *
     * @return integer
     */
	public function clear()
	{
		return $this->rowDelete(self::TABLE_NAME,
								'id = :id',
								[':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
