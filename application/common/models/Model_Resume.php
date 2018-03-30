<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Resume extends Db_Helper
{
	/*
		Resume data processing
	*/

	const STATUS_CREATED = 0;
    const STATUS_SENDED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

	public $id_user;
	public $dt_created;
	public $id_approver;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets resume by user.
     *
     * @return array
     */
	public function getByUser()
	{
		return $this->rowSelectOne('resume.id, id_user, status, name_first, name_middle, name_last, sex, birth_dt, birth_place,'.
								' dict_citizenship.citizenship_name as citizenship, personal.citizenship as id_citizenship, personal.guid,'.
								'(SELECT adr FROM address WHERE id_resume = resume.id AND type = 0) as address_reg,'.
								'(SELECT country_code
								FROM address INNER JOIN dict_countries ON address.id_country = dict_countries.id
								WHERE id_resume = resume.id AND type = 0) as country_reg,'.
								'(SELECT adr FROM address WHERE id_resume = resume.id AND type = 1) as address_res,'.
								'(SELECT country_code
								FROM address INNER JOIN dict_countries ON address.id_country = dict_countries.id
								WHERE id_resume = resume.id AND type = 1) as country_res',
								'resume LEFT OUTER JOIN personal ON resume.id = personal.id_resume'.
								' LEFT OUTER JOIN dict_citizenship ON personal.citizenship = dict_citizenship.id',
								'id_user = :id_user',
								[':id_user' => $this->id_user]);
	}

	/**
     * Saves resume data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('id_user, status, dt_created',
								'resume',
								':id_user, :status, :dt_created',
								[':id_user' => $this->id_user,
								':status' => self::STATUS_CREATED,
								':dt_created' => $this->dt_created]);
	}

	/**
     * Changes resume status.
     *
     * @return boolean
     */
	public function changeStatus()
	{
		if (self::STATUS_APPROVED) {
			return $this->rowUpdate('resume',
								'status = :status, dt_updated = :dt_updated, dt_approve = :dt_approve, id_approver = :id_approver',
								[':status' => $this->status,
								':dt_updated' => date('Y-m-d H:i:s'),
								':dt_approve' => date('Y-m-d H:i:s'),
								':id_approver' => $this->id_approver]);
		} else {
			return $this->rowUpdate('resume',
								'status = :status, dt_updated = :dt_updated',
								[':status' => $this->status,
								':dt_updated' => date('Y-m-d H:i:s')]);
		}
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
