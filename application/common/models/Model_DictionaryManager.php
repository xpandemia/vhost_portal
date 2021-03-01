<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictionaryManager extends Db_Helper
{
	/*
		Dictionary manager processing
	*/

	const TABLE_NAME = 'dictionary_manager';

	const TYPE_ODATA = 0;
	const TYPE_ODATA_NAME = 'OData';
	const TYPE_WSDL = 1;
	const TYPE_WSDL_NAME = 'WSDL';

	const ROLE_LIST = [
						['code' => 0, 'description' => 'OData'],
						['code' => 1, 'description' => 'WSDL']
						];

	public $id;
	public $dict_code;
	public $dict_name;
	public $dict_filter;
	public $type;
	public $table_name;
	public $model_class;
	public $clear_load;
	public $active;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary manager rules.
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
				'dict_code' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dict_code
								],
				'dict_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dict_name
								],
				'dict_filter' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dict_filter
								],
				'type' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->type
							],
				'table_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->table_name
								],
				'model_class' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->model_class
								],
				'clear_load' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->clear_load
								],
				'active' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->active
							]
				];
	}

	/**
     * Dictionary manager grid.
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
				'dict_code' => [
								'name' => 'Код',
								'type' => 'string'
								],
				'dict_name' => [
								'name' => 'Наименование',
								'type' => 'string'
								],
				'dict_filter' => [
								'name' => 'Фильтр',
								'type' => 'string'
								],
				'type' => [
							'name' => 'Тип',
							'type' => 'string'
							],
				'table_name' => [
								'name' => 'Таблица',
								'type' => 'string'
								],
				'model_class' => [
								'name' => 'Модель',
								'type' => 'string'
								],
				'clear_load' => [
								'name' => 'Метод очистки',
								'type' => 'string'
								],
				'active' => [
							'name' => 'Активный',
							'type' => 'string'
							]
				];
	}

	/**
     * Gets all dictionaries for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("id, dict_code, dict_name, dict_filter, getDmTypeName(type) as type, table_name, model_class, clear_load, if(active = 0, 'Нет', 'Да') as active",
									self::TABLE_NAME);
	}

	/**
     * Gets all dictionaries.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets dictionary by id.
     *
     * @return array
     */
	public function getById()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id = :id',
								[':id' => $this->id]);
	}

	/**
     * Checks if dictionary code exists.
     *
     * @return boolean
     */
	public function existsCode()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'dict_code = :dict_code',
									[':dict_code' => $this->dict_code]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary code exists except this ID.
     *
     * @return boolean
     */
	public function existsCodeExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'dict_code = :dict_code AND id <> :id',
									[':dict_code' => $this->dict_code,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if dictionary name exists.
     *
     * @return boolean
     */
	public function existsName()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'dict_name = :dict_name',
									[':dict_name' => $this->dict_name]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function existsNameExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'dict_name = :dict_name AND id <> :id',
									[':dict_name' => $this->dict_name,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}	

	/**
     * Checks if dictionary name exists except this ID.
     *
     * @return boolean
     */
	public function existsDescriptionExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'dict_name = :dict_name AND id <> :id',
									[':dict_name' => $this->dict_name,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves dictionary data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all dictionary data.
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
     * Removes dictionary.
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
