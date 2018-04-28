<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ApplicationStatus extends Db_Helper
{
	/*
		Application status log processing
	*/

	const TABLE_NAME = 'application_status';

	public $id;
	public $id_application;
	public $numb;
	public $numb1c;
	public $status;
	public $comment;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Application rules.
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
				'id_application' => [
									'required' => 1,
									'insert' => 1,
									'update' => 0,
									'value' => $this->id_application
									],
				'numb' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'numb1c' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb1c
							],
				'status' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->status
							],
				'comment' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->comment
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
     * Saves application status log data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
