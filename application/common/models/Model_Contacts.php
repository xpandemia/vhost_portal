<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('CONTACT_EMAIL', array(
							'name' => 'Адрес эл. почты',
							'plc' => 'user@domain',
							'help' => 'Адрес электронной почты должен быть в формате <b>user@domain</b>, содержать <b>только латинские буквы</b> и не более <b>45</b> символов длиной.'));

class Model_Contacts extends Db_Helper
{
	/*
		Contact data processing
	*/

	const TABLE_NAME = 'contacts';

	const TYPE_EMAIL = 0;
	const TYPE_PHONE = 1;

	public $id;
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
     * Gets phone by resume.
     *
     * @return array
     */
	public function getPhoneByResume()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_resume = :id_resume AND type = :type',
								[':id_resume' => $this->id_resume, ':type' => self::TYPE_PHONE]);
	}

	/**
     * Saves contacts data to database.
     *
     * @return boolean
     */
	public function save()
	{
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
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
