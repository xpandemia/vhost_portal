<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ForeignLangs extends Db_Helper
{
	/*
		Foreign languages processing
	*/

	const TABLE_NAME = 'foreign_langs';

	public $id;
	public $id_user;
	public $id_resume;
	public $numb;
	public $id_lang;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Foreign languages rules.
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
				'numb' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->numb
							],
				'id_lang' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->id_lang
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
				];
	}

	/**
     * Gets foreign languages by ID.
     *
     * @return array
     */
	public function get()
	{
		$doc_educ = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Gets foreign languages by resume.
     *
     * @return array
     */
	public function getByResume()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'id_resume = :id_resume',
									[':id_resume' => $this->id_resume]);
	}

	/**
     * Gets foreign languages by resume for List.
     *
     * @return array
     */
	public function getByResumeList()
	{
		return $this->rowSelectAll('numb, code, description',
									'foreign_langs INNER JOIN dict_foreign_langs ON foreign_langs.id_lang = dict_foreign_langs.id',
									'id_resume = :id_resume',
									[':id_resume' => $this->id_resume]);
	}

	/**
     * Gets first foreign language by resume.
     *
     * @return array
     */
	public function getFirstByUser()
	{
		return $this->rowSelectOne('*',
								'foreign_langs INNER JOIN resume ON foreign_langs.id_resume = resume.id',
								'resume.id_user = :id_user AND numb = :numb',
								[':id_user' => $_SESSION[APP_CODE]['user_id'],
								':numb' => 1]);
	}

	/**
     * Checks if lang exists.
     *
     * @return boolean
     */
	public function existsLang()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_user = :id_user AND id_lang = :id_lang',
									[':id_user' => $_SESSION[APP_CODE]['user_id'],
									':id_lang' => $this->id_lang]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves foreign language data to database.
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
     * Changes all foreign language data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	/**
     * Removes foreign language.
     *
     * @return integer
     */
	public function clear()
	{
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
