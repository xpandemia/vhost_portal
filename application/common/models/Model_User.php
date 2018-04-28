<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('USERNAME_HELP', 'Логин должен содержать <b>только латинские буквы</b>, и быть не более <b>45</b> символов длиной.');
define('EMAIL_HELP', 'Адрес электронной почты должен быть в формате <b>user@domain</b>, содержать <b>только латинские буквы</b> и не более <b>45</b> символов длиной.');
define('PWD_HELP', 'Пароль должен содержать <b>только латинские буквы и цифры</b>, и быть <b>6-10</b> символов длиной.');
define('PWD_CONFIRM_HELP', 'Пароль должен содержать <b>только латинские буквы и цифры</b>, и быть <b>6-10</b> символов длиной.');

define('USERNAME_PLC', 'Логин');
define('EMAIL_PLC', 'user@domain');
define('PWD_PLC', 'Пароль');
define('PWD_CONFIRM_PLC', 'Повторите пароль');

class Model_User extends Db_Helper
{
	/*
		Users processing
	*/

	const TABLE_NAME = 'user';

	const ROLE_GUEST = 0;
	const ROLE_MANAGER = 1;
	const ROLE_ADMIN = 2;

	const STATUS_NOTACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

	public $id;
	public $username;
	public $email;
	public $pwd_hash;
	public $activation;
	public $pwd_token;
	public $role;
	public $status;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * User rules.
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
				'username' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->username
								],
				'email' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->email
							],
				'pwd_hash' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->pwd_hash
								],
				'activation' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->activation
								],
				'pwd_token' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->pwd_token
								],
				'role' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->role
							],
				'status' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->status
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
								]
				];
	}

	/**
     * Gets user by name.
     *
     * @return array
     */
	public function getByUsername()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'username = :username', [':username' => $this->username]);
	}

	/**
     * Gets user by email.
     *
     * @return array
     */
	public function getByEmail()
	{
		return $this->rowSelectOne('*', self::TABLE_NAME, 'email = :email', [':email' => $this->email]);
	}

	/**
     * Checks if username exists.
     *
     * @return boolean
     */
	public function existsUsername()
	{
		$row = $this->rowSelectOne('id', self::TABLE_NAME, 'username = :username', [':username' => $this->username]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if email exists.
     *
     * @return boolean
     */
	public function existsEmail()
	{
		$row = $this->rowSelectOne('id', self::TABLE_NAME, 'email = :email', [':email' => $this->email]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves user data to database.
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
     * Changes user status.
     *
     * @return boolean
     */
	public function changeStatus()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		return $this->rowUpdate(self::TABLE_NAME,
								'status = :status, dt_updated = :dt_updated',
								[':status' => $this->status, ':dt_updated' => $this->dt_updated],
								['id' => $this->id]);
	}

	/**
     * Changes user password token.
     *
     * @return boolean
     */
	public function changePwdToken()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		return $this->rowUpdate(self::TABLE_NAME,
								'pwd_token = :pwd_token, dt_updated = :dt_updated',
								[':pwd_token' => $this->pwd_token, ':dt_updated' => $this->dt_updated],
								['id' => $this->id]);
	}

	/**
     * Changes user password.
     *
     * @return boolean
     */
	public function changePwd()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		return $this->rowUpdate(self::TABLE_NAME,
								'pwd_hash = :pwd_hash, dt_updated = :dt_updated',
								[':pwd_hash' => $this->pwd_hash, ':dt_updated' => $this->dt_updated],
								['id' => $this->id]);
	}

	/**
     * Sets user session.
     *
     * @return void
     */
	public function setUser()
	{
		switch (LOGON) {
			case 'login':
				$_SESSION[APP_CODE][self::TABLE_NAME.'_id'] = $this->id;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_name'] = $this->username;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_email'] = $this->email;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_role'] = $this->role;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
				break;
			case 'cas':
				$_SESSION[APP_CODE][self::TABLE_NAME.'_id'] = $this->id;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_email'] = $this->email;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_role'] = $this->role;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
				break;
			default:
				$_SESSION[APP_CODE][self::TABLE_NAME.'_id'] = $this->id;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_name'] = $this->username;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_email'] = $this->email;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_role'] = $this->role;
				$_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
		}
	}

	/**
     * Unsets user session.
     *
     * @return void
     */
	public function unsetUser()
	{
		unset($_SESSION[APP_CODE][self::TABLE_NAME.'_id']);
		unset($_SESSION[APP_CODE][self::TABLE_NAME.'_name']);
		unset($_SESSION[APP_CODE][self::TABLE_NAME.'_email']);
		unset($_SESSION[APP_CODE][self::TABLE_NAME.'_role']);
		unset($_SESSION[APP_CODE][self::TABLE_NAME.'_status']);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
