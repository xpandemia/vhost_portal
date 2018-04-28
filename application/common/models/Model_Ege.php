<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Ege extends Db_Helper
{
	/*
		Ege processing
	*/

	const TABLE_NAME = 'ege';

	public $id;
	public $id_user;
	public $description;
	public $reg_year;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Ege rules.
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
				'description' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->description
								],
				'reg_year' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->reg_year
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
     * Ege grid.
     *
     * @return array
     */
	public function grid()
	{
		return ['id' => [
						'name' => '№',
						'type' => 'int'
						],
				'description' => [
								'name' => 'Описание',
								'type' => 'string'
								],
				'reg_year' => [
								'name' => 'Год сдачи',
								'type' => 'int'
								]
				];
	}

	/**
     * Gets ege by ID.
     *
     * @return array
     */
	public function get()
	{
		$doc_educ = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Gets ege by user.
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
     * Gets ege by user for GRID.
     *
     * @return array
     */
	public function getByUserGrid()
	{
		return $this->rowSelectAll('id, description, reg_year',
									self::TABLE_NAME,
									'id_user = :id_user',
									[':id_user' => $this->id_user]);
	}

	/**
     * Checks if reg_year exists.
     *
     * @return boolean
     */
	public function existsRegyear()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_user = :id_user AND reg_year = :reg_year',
									[':id_user' => $_SESSION[APP_CODE]['user_id'],
									':reg_year' => $this->reg_year]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves ege data to database.
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
     * Changes all ege data.
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
     * Removes ege.
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
