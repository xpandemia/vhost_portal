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
	public $id_user;
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
     * Scans rules.
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
				'id_user' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_user
							],
				'id_doc' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_doc
							],
				'id_row' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_row
							],
				'id_scans' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_scans
								],
				'file_data' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->file_data
								],
				'file_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->file_name
								],
				'file_type' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->file_type
								],
				'file_size' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->file_size
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
     * Gets scan by document/scan.
     *
     * @return array
     */
	public function getByDocScan($doc_code, $doc_id, $scan_code)
	{
		return $this->rowSelectOne('scans.*',
								'scans INNER JOIN docs ON scans.id_doc = docs.id'.
								' INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
								'docs.doc_code = :doc_code AND scans.id_row = :id_row AND dict_scans.scan_code = :scan_code',
								[':doc_code' => $doc_code,
								':id_row' => $doc_id,
								':scan_code' => $scan_code]);
	}

	/**
     * Gets scans by document row.
     *
     * @return array
     */
	public function getByDocrow($doc_code)
	{
		return $this->rowSelectAll('scans.*',
								'scans INNER JOIN docs ON scans.id_doc = docs.id',
								'docs.doc_code = :doc_code AND scans.id_row = :id_row',
								[':doc_code' => $doc_code,
								':id_row' => $this->id_row]);
	}

	/**
     * Gets scans by document row FULL.
     *
     * @return array
     */
	public function getByDocrowFull($doc_code, $id_row)
	{
		$result = [];
		$scans = new Model_DictScans();
		$scans->doc_code = $doc_code;
		$scans_arr = $scans->getByDocument();
		if ($scans_arr) {
			foreach ($scans_arr as $scans_row) {
				$row = $this->rowSelectOne('scans.id, file_data, file_type',
											'scans INNER JOIN docs ON scans.id_doc = docs.id'.
											' INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
											'id_row = :id_row AND doc_code = :doc_code AND scan_code = :scan_code',
											[':id_row' => $id_row,
											':doc_code' => $scans->doc_code,
											':scan_code' => $scans_row['scan_code']]);
				if (!is_array($row)) {
					$scan = [$scans_row['scan_code'].'_id' => null, $scans_row['scan_code'] => null, $scans_row['scan_code'].'_type' => null];
				} else {
					$scan = [$scans_row['scan_code'].'_id' => $row['id'], $scans_row['scan_code'] => $row['file_data'], $scans_row['scan_code'].'_type' => $row['file_type']];
				}
				$result = array_merge($result, $scan);
			}
		}
		return $result;
	}

	/**
     * Saves scan data to database.
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

	/**
     * Removes scans by document.
     *
     * @return integer
     */
	public function clearbyDoc($doc_code)
	{
		if (!empty($doc_code)) {
			$docs = new Model_Docs();
			$docs->doc_code = $doc_code;
			$docs_row = $docs->getByCode();
			if ($docs_row) {
				$this->id_doc = $docs_row['id'];
				return $this->rowDelete(self::TABLE_NAME,
									'id_doc = :id_doc AND id_row = :id_row',
									[':id_doc' => $this->id_doc,
									':id_row' => $this->id_row]);
			} else {
				throw new \RuntimeException('Параметры скан-копии не найдены!');
			}
		} else {
			throw new \InvalidArgumentException('Код документа не указан!');
		}
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
