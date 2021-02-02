<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictEge extends Db_Helper
{
	/*
		Dictionary ege processing
	*/

	const TABLE_NAME = 'dict_ege';

	public $id;
	public $code;
	public $description;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary ege rules.
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
				'code' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->code
							],
				'description' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->description
								]
				];
	}

	/**
     * Educlevels doctypes grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'id' => [
						'name' => '№',
						'type' => 'int'
						],
				'code' => [
							'name' => 'Код',
							'type' => 'string'
							],
				'description' => [
								'name' => 'Наименование',
								'type' => 'string'
								]
				];
	}

	/**
     * Gets all dictionary ege for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, null, null, 'id ASC');
	}

	/**
     * Gets all dictionary ege.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME, null, null, 'description ASC');
	}

	/**
     * Gets dictionary ege by ID.
     *
     * @return array
     */
	public function get()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id = :id',
									[':id' => $this->id]);
	}

	/**
     * Gets dictionary ege by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'code = :code',
									[':code' => $this->code]);
	}

	/**
     * Gets dictionary ege by description.
     *
     * @return array
     */
	public function getByDescription()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'description = :description',
									[':description' => $this->description]);
	}

	/**
     * Checks if dictionary ege exists.
     *
     * @return boolean
     */
	public function exists()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code AND description = :description',
									[':code' => $this->code,
									':description' => $this->description]);
		if (!empty($row)) {
			return true;
		}
        
        return false;
    }

	/**
     * Checks if dictionary ege exists except this ID.
     *
     * @return boolean
     */
	public function existsExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'code = :code AND description = :description and id <> :id',
									[':code' => $this->code,
									':description' => $this->description,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		}
        
        return false;
    }

	/**
     * Checks if dictionary ege used in ege.
     *
     * @return boolean
     */
	public function existsEge()
	{
		$arr = $this->rowSelectAll('id',
									'ege_discipline INNER JOIN dict_ege ON ege_discipline.id_discipline = dict_ege.id',
									'dict_ege.id = :id',
									[':id' => $this->id]);
		if (!empty($arr)) {
			return true;
		}
        
        return false;
    }

	/**
     * Saves dictionary ege data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all dictionary ege data.
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
     * Removes all dictionary ege.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Removes dictionary ege.
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
