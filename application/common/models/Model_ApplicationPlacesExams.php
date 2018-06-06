<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ApplicationPlacesExams extends Db_Helper
{
	/*
		Application places exams processing
	*/

	const TABLE_NAME = 'application_places_exams';

	public $id;
	public $pid;
	public $id_user;
	public $id_test;
	public $id_discipline;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Application places exams rules.
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
				'id_test' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_test
							],
				'id_discipline' => [
									'required' => 1,
									'insert' => 1,
									'update' => 0,
									'value' => $this->id_discipline
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
     * Gets exams by place.
     *
     * @return array
     */
	public function getExamsByPlace()
	{
		return $this->rowSelectAll('application_places_exams.id, dict_testing_scopes.code as test_code, dict_discipline.code as discipline_code',
									'application_places_exams INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id'.
									' INNER JOIN dict_discipline ON application_places_exams.id_discipline = dict_discipline.id',
									'application_places_exams.pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets full exams by place.
     *
     * @return array
     */
	public function getExamsByPlaceFull()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets exams by application.
     *
     * @return array
     */
	public function getExamsByApplication()
	{
		return $this->rowSelectAll('DISTINCT dict_testing_scopes.code, dict_testing_scopes.description, dict_discipline.code as discipline_code, dict_discipline.discipline_name',
									'application_places_exams INNER JOIN application_places ON application_places_exams.pid = application_places.id'.
									' INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id'.
									' INNER JOIN dict_discipline ON application_places_exams.id_discipline = dict_discipline.id',
									'application_places.pid = :pid',
									[':pid' => $this->pid],
									'dict_discipline.discipline_name');
	}

	/**
     * Saves application places exams data to database.
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
     * Changes testing scope.
     *
     * @return boolean
     */
	public function changeTest()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'id_test = :id_test',
								[':id_test' => $this->id_test],
								['id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
