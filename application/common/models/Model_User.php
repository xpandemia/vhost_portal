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
			return TRUE;
		} else {
			return FALSE;
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
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
     * Saves user data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('username, email, pwd_hash, activation, status, dt_created',
								self::TABLE_NAME,
								':username, :email, :pwd_hash, :activation, :status, :dt_created',
								[':username' => $this->username,
								':email' => $this->email,
								':pwd_hash' => $this->pwd_hash,
								':activation' => $this->activation,
								':status' => $this->status,
								':dt_created' => $this->dt_created]);
	}

	/**
     * Changes user status.
     *
     * @return boolean
     */
	public function changeStatus()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'status = :status, dt_updated = :dt_updated',
								[':status' => $this->status, ':dt_updated' => date('Y-m-d H:i:s')],
								['id' => $this->id]);
	}

	/**
     * Changes user password token.
     *
     * @return boolean
     */
	public function changePwdToken()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'pwd_token = :pwd_token, dt_updated = :dt_updated',
								[':pwd_token' => $this->pwd_token, ':dt_updated' => date('Y-m-d H:i:s')],
								['id' => $this->id]);
	}

	/**
     * Changes user password.
     *
     * @return boolean
     */
	public function changePwd()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'pwd_hash = :pwd_hash, dt_updated = :dt_updated',
								[':pwd_hash' => $this->pwd_hash, ':dt_updated' => date('Y-m-d H:i:s')],
								['id' => $this->id]);
	}

	/**
     * Sets user session.
     *
     * @return void
     */
	public function setUser()
	{
		$_SESSION[APP_CODE][self::TABLE_NAME.'_id'] = $this->id;
		$_SESSION[APP_CODE][self::TABLE_NAME.'_name'] = $this->username;
		$_SESSION[APP_CODE][self::TABLE_NAME.'_email'] = $this->email;
		$_SESSION[APP_CODE][self::TABLE_NAME.'_role'] = $this->role;
		$_SESSION[APP_CODE][self::TABLE_NAME.'_status'] = $this->status;
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
