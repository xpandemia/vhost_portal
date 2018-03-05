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
	public function getUserByName()
	{
		return $this->rowSelect('*', 'user', 'username = :username', [':username' => $this->username]);
	}

	/**
     * Gets user by email.
     *
     * @return array
     */
	public function getUserByEmail()
	{
		return $this->rowSelect('*', 'user', 'email = :email', [':email' => $this->email]);
	}

	/**
     * Checks if username exists.
     *
     * @return boolean
     */
	public function existsUsername()
	{
		$row = $this->rowSelect('id', 'user', 'username = :username', [':username' => $this->username]);
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
		$row = $this->rowSelect('id', 'user', 'email = :email', [':email' => $this->email]);
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
								'user',
								':username, :email, :pwd_hash, :activation, :status, :dt_created',
								[':username' => $this->username, ':email' => $this->email, ':pwd_hash' => $this->pwd_hash, ':activation' => $this->activation, ':status' => $this->status, ':dt_created' => $this->dt_created]);
	}

	/**
     * Changes user status.
     *
     * @return boolean
     */
	public function changeStatus()
	{
		return $this->rowUpdate('user',
								'status = :status, dt_updated = :dt_updated',
								[':status' => $this->status, ':dt_updated' => date('Y-m-d H:i:s')]);
	}

	/**
     * Changes user password token.
     *
     * @return boolean
     */
	public function changePwdToken()
	{
		return $this->rowUpdate('user',
								'pwd_token = :pwd_token, dt_updated = :dt_updated',
								[':pwd_token' => $this->pwd_token, ':dt_updated' => date('Y-m-d H:i:s')]);
	}

	/**
     * Changes user password.
     *
     * @return boolean
     */
	public function changePwd()
	{
		return $this->rowUpdate('user',
								'pwd_hash = :pwd_hash, dt_updated = :dt_updated',
								[':pwd_hash' => $this->pwd_hash, ':dt_updated' => date('Y-m-d H:i:s')]);
	}

	/**
     * Sets user session.
     *
     * @return void
     */
	public function setUser()
	{
		$_SESSION[APP_CODE]['user_id'] = $this->id;
		$_SESSION[APP_CODE]['user_name'] = $this->username;
		$_SESSION[APP_CODE]['user_role'] = $this->role;
		$_SESSION[APP_CODE]['user_status'] = $this->status;
	}

	/**
     * Unsets user session.
     *
     * @return void
     */
	public function unsetUser()
	{
		unset($_SESSION[APP_CODE]['user_id']);
		unset($_SESSION[APP_CODE]['user_name']);
		unset($_SESSION[APP_CODE]['user_role']);
		unset($_SESSION[APP_CODE]['user_status']);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
