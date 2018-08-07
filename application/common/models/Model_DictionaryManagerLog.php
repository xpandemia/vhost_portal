<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictionaryManagerLog extends Db_Helper
{
	/*
		Dictionary manager log processing
	*/

	const TABLE_NAME = 'dictionary_manager_log';

	public $id;
	public $id_dict;
	public $id_user;
	public $msg;
	public $value_old;
	public $value_new;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary manager log rules.
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
				'id_dict' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_dict
							],
				'id_user' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_user
							],
				'msg' => [
						'required' => 1,
						'insert' => 1,
						'update' => 0,
						'value' => $this->msg
						],
				'value_old' => [
								'required' => 0,
								'insert' => 1,
								'update' => 0,
								'value' => $this->value_old
								],
				'value_new' => [
								'required' => 0,
								'insert' => 1,
								'update' => 0,
								'value' => $this->value_new
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
     * Dictionary manager log grid.
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
				'msg' => [
							'name' => 'Запись',
							'type' => 'string'
							],
				'value_old' => [
								'name' => 'Старое значение',
								'type' => 'string'
								],
				'value_new' => [
								'name' => 'Новое значения',
								'type' => 'string'
								],
				'user' => [
							'name' => 'Администратор',
							'type' => 'string'
							],
				'dt_created' => [
								'name' => 'Дата создания',
								'type' => 'date',
								'format' => 'd.m.Y H:i:s'
								]
				];
	}

	/**
     * Gets all dictionary logs by dictionary for GRID.
     *
     * @return array
     */
	public function getGridByDict()
	{
		return $this->rowSelectAll('dictionary_manager_log.id, msg, value_old, value_new, user.username as user, dictionary_manager_log.dt_created',
									'dictionary_manager_log INNER JOIN user ON dictionary_manager_log.id_user = user.id',
									'id_dict = :id_dict',
									[':id_dict' => $this->id_dict]);
	}

	/**
     * Saves dictionary manager log data to database.
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

	public function __destruct()
	{
		$this->db = null;
	}
}
