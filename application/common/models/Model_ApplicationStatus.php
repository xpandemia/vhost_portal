<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_Application as Application;

class Model_ApplicationStatus extends Db_Helper
{
	/*
		Application status log processing
	*/

	const TABLE_NAME = 'application_status';

	public $id;
	public $id_application;
	public $numb;
	public $numb1s;
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
				'numb1s' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb1s
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
     * Gets the last application status log data.
     *
     * @return integer
     */
	public function getLast()
	{
		$res = $this->rowSelectAll('*',
									self::TABLE_NAME,
									'id_application = :id_application',
									[':id_application' => $this->id_application],
									'dt_created DESC', 1);
		return $res;
	}

	/**
     * Creates application status log data.
     *
     * @return integer
     */
	public function create()
	{
		$app = new Application();
		$app->id = $this->id_application;
		$app_row = $app->get();
		if ($app_row) {
			$this->numb = $app_row['numb'];
			$this->numb1s = $app_row['numb1s'];
			$this->status = $app_row['status'];
			return $this->save();
		} else {
			return 0;
		}
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

	/**
     * Removes application log.
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
