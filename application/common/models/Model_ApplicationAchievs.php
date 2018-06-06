<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ApplicationAchievs extends Db_Helper
{
	/*
		Application achievments processing
	*/

	const TABLE_NAME = 'application_achievs';

	public $id;
	public $pid;
	public $id_user;
	public $id_achiev;
	public $dt_created;

	const IA_GTO = '000000004';
	const IA_MEDAL_CERTIFICATE = '000000003';
	const IA_MEDAL_DIPLOMA = '000000022';
	const IA_CONTEST_RUS = '000000005';
	const IA_CONTEST_BSU = '000000002';
	const IA_SPORTMASTER = '000000023';

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Application achievments rules.
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
				'pid' => [
						'required' => 1,
						'insert' => 1,
						'update' => 0,
						'value' => $this->pid
						],
				'id_user' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_user
							],
				'id_achiev' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_achiev
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
     * Application achievments grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'achiev' => [
							'name' => 'Индивидуальное достижение',
							'type' => 'string'
							],
				'doc' => [
							'name' => 'Документ',
							'type' => 'string'
							],
				'company' => [
							'name' => 'Выдан',
							'type' => 'string'
							],
				'dt_issue' => [
								'name' => 'Дата выдачи',
								'type' => 'date'
								]
				];
	}

	/**
     * Gets application achievments for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("application_achievs.id, dict_ind_achievs.description as achiev, concat('№ ', ifnull(concat(ind_achievs.series, '-'), ''), ind_achievs.numb) as doc, company, date_format(dt_issue, '%d.%m.%Y') as dt_issue",
									'application_achievs INNER JOIN ind_achievs ON application_achievs.id_achiev = ind_achievs.id'.
									' INNER JOIN dict_ind_achievs ON ind_achievs.id_achiev = dict_ind_achievs.id',
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets application achievments by application.
     *
     * @return array
     */
	public function getByApp()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets application achievments by application for PDF.
     *
     * @return array
     */
	public function getByAppForPdf()
	{
		return $this->rowSelectAll('dict_ind_achievs.code',
									self::TABLE_NAME.' INNER JOIN ind_achievs ON '.self::TABLE_NAME.'.id_achiev = ind_achievs.id'.
									' INNER JOIN dict_ind_achievs ON ind_achievs.id_achiev = dict_ind_achievs.id',
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Saves application achievments data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->id_user = $_SESSION[APP_CODE]['user_id'];
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all application achievments data.
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
     * Removes application achievments by application.
     *
     * @return integer
     */
	public function clearByApplication()
	{
		return $this->rowDelete(self::TABLE_NAME,
								'pid = :pid',
								[':pid' => $this->pid]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
