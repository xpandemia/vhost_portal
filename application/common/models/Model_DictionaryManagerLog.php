<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictionaryManagerLog extends Db_Helper
{
	/*
		Dictionary manager log processing
	*/

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
     * Saves dictionary manager log data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('id_dict, id_user, msg, value_old, value_new, dt_created',
								'dictionary_manager_log',
								':id_dict, :id_user, :msg, :value_old, :value_new, :dt_created',
								[':id_dict' => $this->id_dict,
								':id_user' => $this->id_user,
								':msg' => $this->msg,
								':value_old' => $this->value_old,
								':value_new' => $this->value_new,
								':dt_created' => $this->dt_created]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
