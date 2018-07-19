<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_EductypesDoctypes extends Db_Helper
{
	/*
		Eductypes doctypes processing
	*/

	const TABLE_NAME = 'eductypes_doctypes';

	public $id;
	public $id_eductype;
	public $id_doctype;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Eductypes doctypes rules.
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
				'id_eductype' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->id_eductype
									],
				'id_doctype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_doctype
								],
				'dt_created' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->dt_created
								]
				];
	}

	/**
     * Eductypes doctypes grid.
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
				'educ_type' => [
								'name' => 'Вид образования',
								'type' => 'string'
								],
				'doc_type' => [
								'name' => 'Тип документа',
								'type' => 'string'
								],
				'dt_created' => [
								'name' => 'Дата создания',
								'type' => 'date'
								]
				];
	}

	/**
     * Gets eductypes doctypes by ID.
     *
     * @return array
     */
	public function get()
	{
		$ed = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($ed) {
			$educ_type = $this->rowSelectOne('code as educ_type',
											'dict_eductypes',
											'id = :id',
											[':id' => $ed['id_eductype']]);
			if (!is_array($educ_type)) {
				$educ_type = ['educ_type' => null];
			}
			$doc_type = $this->rowSelectOne('code as doc_type',
											'dict_doctypes',
											'id = :id',
											[':id' => $ed['id_doctype']]);
			if (!is_array($doc_type)) {
				$doc_type = ['doc_type' => null];
			}
			$result = array_merge($ed, $educ_type, $doc_type);
			return $result;
		} else {
			return null;
		}
	}

	/**
     * Gets eductypes doctypes for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("eductypes_doctypes.id, dict_eductypes.description as educ_type, dict_doctypes.description as doc_type, date_format(dt_created, '%d.%m.%Y') as dt_created",
									'eductypes_doctypes INNER JOIN dict_eductypes ON eductypes_doctypes.id_eductype = dict_eductypes.id'.
									' INNER JOIN dict_doctypes ON eductypes_doctypes.id_doctype = dict_doctypes.id',
									null,
									null,
									'dict_eductypes.description ASC, dict_doctypes.description ASC');
	}

	/**
     * Checks if eductypes doctypes exists.
     *
     * @return boolean
     */
	public function exists()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_eductype = :id_eductype AND id_doctype = :id_doctype',
									[':id_eductype' => $this->id_eductype,
									':id_doctype' => $this->id_doctype]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if eductypes doctypes exists except this ID.
     *
     * @return boolean
     */
	public function existsExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_eductype = :id_eductype AND id_doctype = :id_doctype and id <> :id',
									[':id_eductype' => $this->id_eductype,
									':id_doctype' => $this->id_doctype,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves eductypes doctypes data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all eductypes doctypes data.
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
     * Removes eductypes doctypes.
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
