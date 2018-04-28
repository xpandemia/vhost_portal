<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ApplicationPlaces extends Db_Helper
{
	/*
		Application places processing
	*/

	const TABLE_NAME = 'application_places';

	public $id;
	public $pid;
	public $id_user;
	public $id_spec;
	public $curriculum;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Application places rules.
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
				'id_spec' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_spec
							],
				'curriculum' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->curriculum
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
     * Application places grid.
     *
     * @return array
     */
	public function grid()
	{
		return ['spec' => [
							'name' => 'Направление подготовки',
							'type' => 'string'
							]
				];
	}

	/**
     * Gets application places for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("application_places.id, concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, ''), ' ', dict_speciality.finance_name, ' ', dict_speciality.eduform_name) as spec",
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets specialities by application.
     *
     * @return array
     */
	public function getSpecsByApp()
	{
		return $this->rowSelectAll('*',
									'application_places',
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets specialities for application.
     *
     * @return array
     */
	public function getSpecsForApp()
	{
		return $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_name, eduform_name, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Saves application places data to database.
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
     * Changes all application places data.
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
     * Removes application places by application.
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
