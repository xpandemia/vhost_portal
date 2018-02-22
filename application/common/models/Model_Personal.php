<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define("FIRSTNAME_HELP", "Имя должно содержать <b>только русские буквы</b>, и быть не более <b>50</b> символов длиной.");
define("MIDDLENAME_HELP", "Отчество должно содержать <b>только русские буквы</b>, и быть не более <b>50</b> символов длиной.");
define("LASTNAME_HELP", "Фамилия должна содержать <b>только русские буквы</b>, и быть не более <b>50</b> символов длиной.");
define("BIRTHPLACE_HELP", "Место рождения должно содержать <b>только русские буквы, тире, точки, запятые или пробелы</b>, и быть не более <b>240</b> символов длиной.");

define("FIRSTNAME_PLC", "Имя");
define("MIDDLENAME_PLC", "Отчество");
define("LASTNAME_PLC", "Фамилия");
define("BIRTHPLACE_PLC", "Место рождения");

class Model_Personal extends Db_Helper
{
	/*
		Personal data processing
	*/

	public $id;
	public $id_user;
	public $name_first;
	public $name_middle;
	public $name_last;
	public $sex;
	public $birth_dt;
	public $birth_place;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = new Db_Helper();
	}

	/**
     * Gets personal by username.
     *
     * @return array
     */
	public function getPersonalByUser()
	{
		return $this->rowSelect('*', 'personal', 'id_user = :id_user', [':id_user' => $this->id_user]);
	}

	/**
     * Saves personal data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('id_user, name_first, name_middle, name_last, sex, birth_dt, birth_place, dt_created',
								'personal',
								':id_user, :name_first, :name_middle, :name_last, :sex, :birth_dt, :birth_place, :dt_created',
								[':id_user' => $this->id_user,
								':name_first' => $this->name_first,
								':name_middle' => $this->name_middle,
								':name_last' => $this->name_last,
								':sex' => $this->sex,
								':birth_dt' => $this->birth_dt,
								':birth_place' => $this->birth_place,
								':dt_created' => $this->dt_created]);
	}

	/**
     * Changes all personal data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		return $this->rowUpdate('personal',
								'name_first = :name_first, name_middle = :name_middle, name_last = :name_last, sex = :sex, birth_dt = :birth_dt, birth_place = :birth_place, dt_updated = :dt_updated',
								[':name_first' => $this->name_first,
								':name_middle' => $this->name_middle,
								':name_last' => $this->name_last,
								':sex' => $this->sex,
								':birth_dt' => $this->birth_dt,
								':birth_place' => $this->birth_place,
								':dt_updated' => date('Y-m-d H:i:s')]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
