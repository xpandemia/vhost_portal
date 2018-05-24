<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_IndAchievs extends Db_Helper
{
	/*
		Individual achievments processing
	*/

	const TABLE_NAME = 'ind_achievs';

	public $id;
	public $id_user;
	public $id_achiev;
	public $series;
	public $numb;
	public $company;
	public $dt_issue;
	public $dt_created;
	public $dt_updated;

	public $campaign_code;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Individual achievments rules.
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
				'id_achiev' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_achiev
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
				'company' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->company
							],
				'dt_issue' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dt_issue
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
     * Individual achievments grid.
     *
     * @return array
     */
	public function grid()
	{
		return ['id' => [
						'name' => '№',
						'type' => 'int'
						],
				'achiev_type' => [
								'name' => 'Индивидуальное достижение',
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
				'company' => [
							'name' => 'Организация',
							'type' => 'string'
							]
				];
	}

	/**
     * Gets individual achievment by ID.
     *
     * @return array
     */
	public function get()
	{
		$ia = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($ia) {
			$achiev_type = $this->rowSelectOne('code as achiev_type',
												'dict_ind_achievs',
												'id = :id',
												[':id' => $ia['id_achiev']]);
			if (!is_array($achiev_type)) {
				$achiev_type = ['achiev_type' => null];
			}
			$result = array_merge($ia, $achiev_type);
			$scan = new Model_Scans();
			$scan_arr = $scan->getByDocrowFull('ind_achievs', $ia['id']);
			$result = array_merge($result, $scan_arr);
			return $result;
		} else {
			return null;
		}
	}

	/**
     * Gets individual achievments by user.
     *
     * @return array
     */
	public function getByUser()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets individual achievments by user/admission campaign.
     *
     * @return array
     */
	public function getByUserCampaign()
	{
		return $this->rowSelectAll('ind_achievs.*',
									'ind_achievs INNER JOIN dict_ind_achievs ON ind_achievs.id_achiev = dict_ind_achievs.id'.
									' INNER JOIN dict_ind_achievs_ac ON dict_ind_achievs.code = dict_ind_achievs_ac.achiev_code',
									'ind_achievs.id_user = :id_user AND dict_ind_achievs_ac.campaign_code = :campaign_code',
									[':id_user' => $_SESSION[APP_CODE]['user_id'],
									':campaign_code' => $this->campaign_code]);
	}

	/**
     * Gets individual achievments by user for GRID.
     *
     * @return array
     */
	public function getByUserGrid()
	{
		return $this->rowSelectAll("ind_achievs.id, dict_ind_achievs.description as achiev_type, series, ind_achievs.numb, date_format(dt_issue, '%d.%m.%Y') as dt_issue, company",
									'ind_achievs INNER JOIN dict_ind_achievs ON ind_achievs.id_achiev = dict_ind_achievs.id',
									'id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets individual achievment by achiev_type/series/numb.
     *
     * @return array
     */
	public function getByNumb()
	{
		if (empty($this->series)) {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id_achiev = :id_achiev AND series is null AND numb = :numb',
									[':id_achiev' => $this->id_achiev,
									':numb' => $this->numb]);
		} else {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id_achiev = :id_achiev AND series = :series AND numb = :numb',
									[':id_achiev' => $this->id_achiev,
									':series' => $this->series,
									':numb' => $this->numb]);
		}
	}

	/**
     * Saves individual achievment data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->id_user = $_SESSION[APP_CODE]['user_id'];
		$this->dt_created = date('Y-m-d H:i:s');
		$this->dt_updated = null;
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all individual achievment data.
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
     * Removes individual achievment.
     *
     * @return integer
     */
	public function clear()
	{
		$scans = new Model_Scans();
		$scans->id_row = $this->id;
		$scans->clearbyDoc('ind_achievs');
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
