<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Application extends Db_Helper
{
	/*
		Application processing
	*/

	const TABLE_NAME = 'application';

	const TYPE_NEW = 1;
	const TYPE_CHANGE = 2;
	const TYPE_RECALL = 3;

	const STATUS_CREATED = 0;
    const STATUS_SENDED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_RECALLED = 4;
    const STATUS_CHANGED = 5;

	public $id;
	public $id_user;
	public $id_university;
	public $id_campaign;
	public $id_docseduc;
	public $id_app;
	public $type;
	public $status;
	public $numb;
	public $numb1c;
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
				'id_user' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_user
							],
				'id_university' => [
									'required' => 1,
									'insert' => 1,
									'update' => 0,
									'value' => $this->id_university
									],
				'id_campaign' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_campaign
								],
				'id_docseduc' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_docseduc
								],
				'id_app' => [
							'required' => 0,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_app
							],
				'type' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->type
							],
				'status' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->status
							],
				'numb' => [
							'required' => 0,
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
				'dt_created' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->dt_created
								]
				];
	}

	/**
     * Applications grid.
     *
     * @return array
     */
	public function grid()
	{
		return ['numb' => [
							'name' => 'Номер',
							'type' => 'int'
							],
				'reason' => [
							'name' => 'Основание',
							'type' => 'string'
							],
				'type' => [
							'name' => 'Тип',
							'type' => 'string'
							],
				'status' => [
							'name' => 'Состояние',
							'type' => 'string'
							],
				'university' => [
								'name' => 'Место поступления',
								'type' => 'string'
								],
				'campaign' => [
								'name' => 'Приёмная кампания',
								'type' => 'string'
								],
				'docs_educ' => [
								'name' => 'Документ об образовании',
								'type' => 'string'
								]
				];
	}

	/**
     * Generates application numb.
     *
     * @return string
     */
	public function generateNumb()
	{
		if (isset($this->id) && !empty($this->id)) {
			return str_pad('', 11 - strlen($this->id), '0').$this->id;
		} else {
			return str_pad('', 11, '0');
		}
	}

	/**
     * Gets applications by user for GRID.
     *
     * @return array
     */
	public function getByUserGrid()
	{
		return $this->rowSelectAll("application.id,".
									" dict_university.code as university,".
									" admission_campaign.description as campaign,".
									" concat(dict_doctypes.description, ' № ', docs_educ.series, '-', docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as docs_educ,".
									" reason.numb as reason,".
									" getAppTypeName(application.type) as type,".
									" getAppStatusName(application.status) as status,".
									" application.numb",
									'application INNER JOIN dict_university ON application.id_university = dict_university.id'.
									' INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id'.
									' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
									' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id'.
									' LEFT OUTER JOIN application reason ON application.id_app = reason.id',
									'application.id_user = :id_user',
									[':id_user' => $this->id_user]);
	}

	/**
     * Gets application spec.
     *
     * @return array
     */
	public function getSpec()
	{
		$result = [];
		$app = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($app) {
			$scan = new Model_Scans();
			$scan_arr = $scan->getByDocrowFull('application', $this->id);
			$result = array_merge($app, $scan_arr);
		}
		return $result;
	}

	/**
     * Saves application data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->status = self::STATUS_CREATED;
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all application data.
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
     * Changes application numb.
     *
     * @return boolean
     */
	public function changeNumb()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'numb = :numb',
								[':numb' => $this->numb],
								['id' => $this->id]);
	}

	/**
     * Removes application.
     *
     * @return integer
     */
	public function clear()
	{
		$scans = new Model_Scans();
		$scans->id_row = $this->id;
		$scans->clearbyDoc('application');
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
