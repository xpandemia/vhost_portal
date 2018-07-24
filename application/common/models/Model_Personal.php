<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('FIRSTNAME_HELP', 'Имя должно содержать <strong>'.MSG_ALPHA_RUS.'</strong>, и быть не более <strong>50</strong> символов длиной.');
define('MIDDLENAME_HELP', 'Отчество должно содержать <strong>'.MSG_ALPHA_RUS.'</strong>, и быть не более <strong>50</strong> символов длиной.');
define('LASTNAME_HELP', 'Фамилия должна содержать <strong>'.MSG_ALPHA_RUS.'</strong>, и быть не более <strong>50</strong> символов длиной.');
define('BIRTHPLACE_HELP', 'Место рождения должно содержать <strong>'.MSG_TEXT_RUS.'</strong>, и быть не более <strong>240</strong> символов длиной.');

define('FIRSTNAME_PLC', 'Имя');
define('MIDDLENAME_PLC', 'Отчество');
define('LASTNAME_PLC', 'Фамилия');
define('BIRTHPLACE_PLC', 'Место рождения');

class Model_Personal extends Db_Helper
{
	/*
		Personal data processing
	*/

	const TABLE_NAME = 'personal';

	public $id;
	public $id_user;
	public $id_resume;
	public $name_first;
	public $name_middle;
	public $name_last;
	public $sex;
	public $birth_dt;
	public $birth_place;
	public $citizenship;
	public $beneficiary;
	public $dt_created;
	public $dt_updated;
	public $guid;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Personal rules.
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
				'id_resume' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_resume
								],
				'name_first' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->name_first
								],
				'name_middle' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->name_middle
								],
				'name_last' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->name_last
								],
				'sex' => [
						'required' => 1,
						'insert' => 1,
						'update' => 1,
						'value' => $this->sex
						],
				'birth_dt' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->birth_dt
							],
				'birth_place' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->birth_place
								],
				'citizenship' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->citizenship
								],
				'beneficiary' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->beneficiary
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
				'guid' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->guid
							]
				];
	}

	/**
     * Gets personal by resume.
     *
     * @return array
     */
	public function getByResume()
	{
		return $this->rowSelectOne(self::TABLE_NAME.'.id, id_resume, name_first, name_middle, name_last, sex, birth_dt, birth_place, dict_countries.description as citizenship, '.self::TABLE_NAME.'.citizenship as id_citizenship, dt_created, dt_updated, '.self::TABLE_NAME.'.guid, code1s',
								self::TABLE_NAME.' INNER JOIN dict_countries ON '.self::TABLE_NAME.'.citizenship = dict_countries.id',
								'id_resume = :id_resume',
								[':id_resume' => $this->id_resume]);
	}

	/**
     * Gets personal by user.
     *
     * @return array
     */
	public function getByUser()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_user = :id_user',
								[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets FIO by user.
     *
     * @return array
     */
	public function getFioByUser()
	{
		return $this->rowSelectOne('name_first, name_middle, name_last',
								self::TABLE_NAME.' INNER JOIN resume ON '.self::TABLE_NAME.'.id_resume = resume.id',
								'resume.id_user = :id_user',
								[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets code 1s by user.
     *
     * @return array
     */
	public function getCode1sByUser()
	{
		return $this->rowSelectOne('code1s',
								self::TABLE_NAME.' INNER JOIN resume ON '.self::TABLE_NAME.'.id_resume = resume.id',
								'resume.id_user = :id_user',
								[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets citizenship by user.
     *
     * @return array
     */
	public function getCitizenshipByUser()
	{
		return $this->rowSelectOne('dict_countries.code, dict_countries.description, dict_countries.abroad',
								self::TABLE_NAME.' INNER JOIN resume ON '.self::TABLE_NAME.'.id_resume = resume.id'.
								' INNER JOIN dict_countries ON '.self::TABLE_NAME.'.citizenship = dict_countries.id',
								'resume.id_user = :id_user',
								[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Saves personal data to database.
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
     * Changes all personal data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
