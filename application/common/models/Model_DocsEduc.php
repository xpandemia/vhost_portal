<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DocsEduc extends Db_Helper
{
	/*
		Education documents processing
	*/

	const TABLE_NAME = 'docs_educ';

	public $id;
	public $id_user;
	public $id_eductype;
	public $id_doctype;
	public $series;
	public $numb;
	public $school;
	public $dt_issue;
	public $end_year;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Education documents rules.
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
				'id_eductype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_eductype
								],
				'id_doctype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_doctype
								],
				'series' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->series
							],
				'numb' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'school' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->school
							],
				'dt_issue' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dt_issue
								],
				'end_year' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->end_year
								],
				'dt_created' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->dt_created
								],
				'dt_updated' => [
								'required' => 0,
								'insert' => 0,
								'update' => 1,
								'value' => $this->dt_updated
								],
				];
	}

	/**
     * Education documents grid.
     *
     * @return array
     */
	public function grid()
	{
		return ['id' => [
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
				'series' => [
							'name' => 'Серия',
							'type' => 'string'
							],
				'numb' => [
							'name' => 'Номер',
							'type' => 'string'
							],
				'dt_issue' => [
								'name' => 'Дата выдачи',
								'type' => 'date'
							],
				'school' => [
							'name' => 'Учебное заведение',
							'type' => 'string'
							],
				'end_year' => [
								'name' => 'Год окончания',
								'type' => 'int'
								]
				];
	}

	/**
     * Gets education document by ID.
     *
     * @return array
     */
	public function get()
	{
		$doc_educ = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($doc_educ) {
			$educ_type = $this->rowSelectOne('code as educ_type',
											'dict_eductypes',
											'id = :id',
											[':id' => $doc_educ['id_eductype']]);
			if (!is_array($educ_type)) {
				$educ_type = ['educ_type' => null];
			}
			$doc_type = $this->rowSelectOne('code as doc_type',
											'dict_doctypes',
											'id = :id',
											[':id' => $doc_educ['id_doctype']]);
			if (!is_array($doc_type)) {
				$doc_type = ['doc_type' => null];
			}
			$change_name = $this->rowSelectOne('scans.id as change_name_id, file_data as change_name, file_type as change_name_type',
											'scans INNER JOIN docs ON scans.id_doc = docs.id'.
											' INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
											'id_row = :id_row AND doc_code = :doc_code AND scan_code = :scan_code',
											[':id_row' => $doc_educ['id'],
											':doc_code' => 'docs_educ',
											':scan_code' => 'change_name']);
			if (!is_array($change_name)) {
				$change_name = ['change_name_id' => null, 'change_name' => null, 'change_name_type' => null];
			}
			$result = array_merge($doc_educ, $educ_type, $doc_type, $change_name);
			$scan = new Model_Scans();
			$scan_arr = $scan->getByDocrowFull('docs_educ', $doc_educ['id']);
			$result = array_merge($result, $scan_arr);
			return $result;
		} else {
			return null;
		}
	}

	/**
     * Gets education documents by user.
     *
     * @return array
     */
	public function getByUser()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'id_user = :id_user',
									[':id_user' => $this->id_user]);
	}

	/**
     * Gets education documents by user for Select List.
     *
     * @return array
     */
	public function getByUserSl()
	{
		return $this->rowSelectAll("docs_educ.id, concat(dict_doctypes.description, ' № ', ifnull(concat(docs_educ.series, '-'), ''), docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as description",
									'docs_educ INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'docs_educ.id_user = :id_user',
									[':id_user' => $this->id_user]);
	}

	/**
     * Gets education documents by user for GRID.
     *
     * @return array
     */
	public function getByUserGrid()
	{
		return $this->rowSelectAll("docs_educ.id, dict_eductypes.description as educ_type, dict_doctypes.description as doc_type, series, numb, date_format(dt_issue, '%d.%m.%Y') as dt_issue, school, end_year",
									'docs_educ INNER JOIN dict_eductypes ON docs_educ.id_eductype = dict_eductypes.id'.
									' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'id_user = :id_user',
									[':id_user' => $this->id_user]);
	}

	/**
     * Gets education document by doctype/series/numb.
     *
     * @return array
     */
	public function getByNumb()
	{
		if (empty($this->series)) {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id_doctype = :id_doctype AND series is null AND numb = :numb',
									[':id_doctype' => $this->id_doctype,
									':numb' => $this->numb]);
		} else {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id_doctype = :id_doctype AND series = :series AND numb = :numb',
									[':id_doctype' => $this->id_doctype,
									':series' => $this->series,
									':numb' => $this->numb]);
		}
	}

	/**
     * Saves education document data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->dt_created = date('Y-m-d H:i:s');
		$this->dt_updated = null;
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all education document data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	/**
     * Removes education document.
     *
     * @return integer
     */
	public function clear()
	{
		$scans = new Model_Scans();
		$scans->id_row = $this->id;
		$scans->clearbyDoc('docs_educ');
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
