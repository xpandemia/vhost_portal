<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('UNITNAME_HELP', 'Наименование подразделения должно содержать <b>только русские буквы, тире, точки, запятые или пробелы</b>, и быть не более <b>100</b> символов длиной.');

define('UNITNAME_PLC', 'Наименование подразделения');

class Model_Passport extends Db_Helper
{
	/*
		Passport data processing
	*/

	const TABLE_NAME = 'passport';

	public $id;
	public $id_resume;
	public $id_doctype;
	public $main;
	public $series;
	public $numb;
	public $dt_issue;
	public $unit_name;
	public $unit_code;
	public $dt_end;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Passport rules.
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
				'id_resume' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_resume
								],
				'id_doctype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_doctype
								],
				'main' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->main
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
				'dt_issue' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dt_issue
								],
				'unit_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->unit_name
								],
				'unit_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->unit_code
								],
				'dt_end' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->dt_end
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
     * Gets passport by resume.
     *
     * @return array
     */
	public function getByResume()
	{
		return $this->rowSelectOne(self::TABLE_NAME.'.*, dict_doctypes.code as passport_type',
								self::TABLE_NAME.' INNER JOIN dict_doctypes on '.self::TABLE_NAME.'.id_doctype = dict_doctypes.id',
								'id_resume = :id_resume AND main = :main',
								[':id_resume' => $this->id_resume, ':main' => $this->main]);
	}

	/**
     * Saves passport data to database.
     *
     * @return boolean
     */
	public function save()
	{
		$this->dt_created = date('Y-m-d H:i:s');
		$this->dt_updated = null;
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all passport data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
