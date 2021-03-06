<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('CONTACT_EMAIL', array(
							'name' => 'Адрес эл. почты',
							'plc' => 'user@domain',
							'help' => 'Адрес электронной почты должен быть <strong>'.MSG_EMAIL_LIGHT.'</strong>, содержать <strong>'.MSG_ALPHA.'</strong> и не более <b>45</b> символов длиной.'));
define('CONTACT_PHONE_HOME', array(
									'name' => 'Номер домашнего телефона',
									'plc' => 'код города12345',
									'help' => 'Номер домашнего телефона должен содержать <strong>'.MSG_NUMB.'</strong> и быть не более <strong>45</strong> символов длиной.'));
define('CONTACT_PHONE_ADD', array(
									'name' => 'Номер дополнительного телефона',
									'plc' => '+79031234567',
									'help' => 'Номер дополнительного телефона должен содержать <strong>'.MSG_PHONE_ADD.'</strong> и быть не более <strong>45</strong> символов длиной.'));

require_once ('Model_DictFeatures.php');
require_once ('Model_DictPrivilleges.php');

class Model_Contacts extends Db_Helper
{
	/*
		Contact data processing
	*/

	const TABLE_NAME = 'contacts';

	const TYPE_EMAIL = 0;
	const TYPE_PHONE_MOBILE = 1;
	const TYPE_PHONE_HOME = 2;
	const TYPE_PHONE_ADD = 3;

	const TRANS_PHONE_MOBILE = ['+7' => '+7', '(' => '-', ')' => '-', ' ' => '', '-' => '-'];
	const TRANS_PHONE_HOME = ['(' => '', ')' => '', '-' => ''];

	public $id;
	public $id_user;
	public $id_resume;
	public $type;
	public $contact;
	public $comment;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Contacts rules.
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
				'type' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->type
							],
				'contact' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->contact
							],
				'comment' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->comment
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
     * Gets email by resume.
     *
     * @return array
     */
	public function getEmailByResume()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_resume = :id_resume AND type = :type',
								[':id_resume' => $this->id_resume, ':type' => self::TYPE_EMAIL]);
	}

	/**
     * Gets phone mobile by resume.
     *
     * @return array
     */
	public function getPhoneMobileByResume()
	{
	    $row = $this->rowSelectOne('*',
                                   self::TABLE_NAME,
                                   'id_resume = :id_resume AND type = :type',
                                   [':id_resume' => $this->id_resume, ':type' => self::TYPE_PHONE_MOBILE]);
		return $row;
	}

	/**
     * Gets phone home by resume.
     *
     * @return array
     */
	public function getPhoneHomeByResume()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_resume = :id_resume AND type = :type',
								[':id_resume' => $this->id_resume, ':type' => self::TYPE_PHONE_HOME]);
	}

	/**
     * Gets phone add by resume.
     *
     * @return array
     */
	public function getPhoneAddByResume()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_resume = :id_resume AND type = :type',
								[':id_resume' => $this->id_resume, ':type' => self::TYPE_PHONE_ADD]);
	}

	/**
     * Saves contacts data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->id_user = $_SESSION[APP_CODE]['user_id'];
		switch ($this->type) {
			case self::TYPE_PHONE_MOBILE:
				$this->contact = strtr($this->contact, self::TRANS_PHONE_MOBILE);
				break;
			case self::TYPE_PHONE_HOME:
				$this->contact = strtr($this->contact, self::TRANS_PHONE_HOME);
				break;
		}
		$this->dt_created = date('Y-m-d H:i:s');
		$this->dt_updated = null;
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all contacts data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		switch ($this->type) {
			case self::TYPE_PHONE_MOBILE:
				$this->contact = strtr($this->contact, self::TRANS_PHONE_MOBILE);
				break;
			case self::TYPE_PHONE_HOME:
				$this->contact = strtr($this->contact, self::TRANS_PHONE_HOME);
				break;
		}
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	/**
     * Removes contact.
     *
     * @return integer
     */
	public function clear()
	{
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Makes mobile phone pretty.
     *
     * @return string
     */
	public static function prettyPhoneMobile($phone_mobile)
	{
		return $phone_mobile;
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
