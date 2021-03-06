<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_Application as Application;

class Model_EgeDisciplines extends Db_Helper
{
	/*
		Ege disciplines processing
	*/

	const TABLE_NAME = 'ege_disciplines';

	public $id;
	public $pid;
	public $id_user;
	public $id_discipline;
	public $points;
	public $dt_created;
	public $dt_updated;

	public $code_discipline;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Ege disciplines rules.
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
				'id_discipline' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_discipline
								],
				'points' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->points
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
     * Ege disciplines grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'discipline' => [
								'name' => 'Дисциплина',
								'type' => 'string'
								],
				'points' => [
							'name' => 'Кол-во баллов',
							'type' => 'int'
							]
				];
	}

	/**
     * Gets ege discipline by ID.
     *
     * @return array
     */
	public function get()
	{
		$egedsp = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($egedsp) {
			$dsp = $this->rowSelectOne('code as discipline',
										'dict_ege',
										'id = :id',
										[':id' => $egedsp['id_discipline']]);
			if (!is_array($dsp)) {
				$dsp = ['discipline' => null];
			}
			return array_merge($egedsp, $dsp);
		} else {
			return null;
		}
	}

	/**
     * Gets ege disciplines for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll('ege_disciplines.id, pid, dict_ege.description as discipline, points',
									'ege_disciplines INNER JOIN dict_ege ON ege_disciplines.id_discipline = dict_ege.id',
									'pid = :pid',
									[':pid' => $this->pid],
									'dict_ege.description ASC');
	}

	/**
     * Checks if discipline exists.
     *
     * @return boolean
     */
	public function exists()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'pid = :pid AND id_discipline = :id_discipline',
									[':pid' => $this->pid,
									':id_discipline' => $this->id_discipline]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if discipline exists except this ID.
     *
     * @return boolean
     */
	public function existsExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'pid = :pid AND id_discipline = :id_discipline AND id <> :id',
									[':pid' => $this->pid,
									':id_discipline' => $this->id_discipline,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if ege discipline used in applications "GO".
     *
     * @return boolean
     */
	public function existsAppGo() : bool
	{
		$app_arr = $this->rowSelectAll('application.id',
										'application INNER JOIN application_places ON application.id = application_places.pid'.
										' INNER JOIN application_places_exams ON application_places.id = application_places_exams.pid'.
										' INNER JOIN dict_testing_scopes ON application_places_exams.id_test = dict_testing_scopes.id'.
										' INNER JOIN dict_discipline ON application_places_exams.id_discipline = dict_discipline.id'.
										' INNER JOIN dict_ege ON dict_discipline.code = dict_ege.code'.
										' INNER JOIN ege_disciplines ON dict_ege.id = ege_disciplines.id_discipline',
										'ege_disciplines.id = :id AND dict_testing_scopes.code = :test  AND ((application.status in (1,2) and application.type in (1,2)) OR application.status = 1 and application.type = 3) AND application.active = 1',
										[':id' => $this->id,
										':test' => '000000001']);
		if ($app_arr) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves ege discipline data to database.
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
     * Changes all ege discipline data.
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
     * Removes ege discipline.
     *
     * @return integer
     */
	public function clear()
	{
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Checks discipline.
     *
     * @return boolean
     */
	public function checkDiscipline()
	{
		$ret = $this->rowSelectAll('points, reg_year',
									'ege_disciplines INNER JOIN ege ON ege_disciplines.pid = ege.id'.
									' INNER JOIN dict_ege ON ege_disciplines.id_discipline = dict_ege.id',
									'dict_ege.code = :code_discipline AND ege.id_user = :id_user AND ege.reg_year >= :reg_year',
									[':code_discipline' => $this->code_discipline,
									':id_user' => $_SESSION[APP_CODE]['user_id'],
									':reg_year' => date('Y') - 6],
									'points DESC, reg_year DESC', 1);
        
        return ($ret !== FALSE) ? $ret : ['points' => '', 'reg_year' => '2020'];
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
