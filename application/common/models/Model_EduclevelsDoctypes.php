<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_EduclevelsDoctypes extends Db_Helper
{
	/*
		Educlevels doctypes processing
	*/

	const TABLE_NAME = 'educlevels_doctypes';

	public $id;
	public $id_educlevel;
	public $id_doctype;
	public $pay;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Educlevels doctypes rules.
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
				'id_educlevel' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->id_educlevel
									],
				'id_doctype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_doctype
								],
				'pay' => [
						'required' => 1,
						'insert' => 1,
						'update' => 1,
						'value' => $this->pay
						],
				'dt_created' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->dt_created
								]
				];
	}

	/**
     * Educlevels doctypes grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'id' => [
						'name' => '№',
						'type' => 'int'
						],
				'educ_level' => [
								'name' => 'Уровень подготовки',
								'type' => 'string'
								],
				'doc_type' => [
								'name' => 'Тип документа',
								'type' => 'string'
								],
				'pay' => [
							'name' => 'Платное образование',
							'type' => 'string'
							],
				'dt_created' => [
								'name' => 'Дата создания',
								'type' => 'date'
								]
				];
	}

	/**
     * Gets educlevels doctypes by ID.
     *
     * @return array
     */
	public function get()
	{
		$ed = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($ed) {
			$educ_level = $this->rowSelectOne('code as educ_level',
											'dict_educlevels',
											'id = :id',
											[':id' => $ed['id_educlevel']]);
			if (!is_array($educ_level)) {
				$educ_level = ['educ_level' => null];
			}
			$doc_type = $this->rowSelectOne('code as doc_type',
											'dict_doctypes',
											'id = :id',
											[':id' => $ed['id_doctype']]);
			if (!is_array($doc_type)) {
				$doc_type = ['doc_type' => null];
			}
			$result = array_merge($ed, $educ_level, $doc_type);
			return $result;
		} else {
			return null;
		}
	}

	/**
     * Gets educlevels doctypes for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("educlevels_doctypes.id, dict_educlevels.description as educ_level, dict_doctypes.description as doc_type, if(pay = 0, 'Нет', 'Да') as pay, date_format(dt_created, '%d.%m.%Y') as dt_created",
									'educlevels_doctypes INNER JOIN dict_educlevels ON educlevels_doctypes.id_educlevel = dict_educlevels.id'.
									' INNER JOIN dict_doctypes ON educlevels_doctypes.id_doctype = dict_doctypes.id',
									null,
									null,
									'dict_educlevels.description ASC, dict_doctypes.description ASC');
	}

	/**
     * Gets pay by document campaign.
     *
     * @return array
     */
	public function getPayByDocCampaign($campaign, $docs_educ)
	{
		if (!empty($campaign) && !empty($docs_educ)) {
			return $this->rowSelectOne('DISTINCT educlevels_doctypes.pay',
										'educlevels_doctypes'.
										' INNER join docs_educ ON educlevels_doctypes.id_doctype = docs_educ.id_doctype'.
										' INNER JOIN dict_educlevels ON educlevels_doctypes.id_educlevel = dict_educlevels.id'.
										' INNER JOIN dict_speciality ON dict_educlevels.code = dict_speciality.edulevel_code'.
										' INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code',
										'admission_campaign.id = :campaign AND docs_educ.id = :docs_educ',
										[':campaign' => $campaign,
										':docs_educ' => $docs_educ]);
		} else {
			return null;
		}
	}

	/**
     * Checks if educlevels doctypes exists.
     *
     * @return boolean
     */
	public function exists()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_educlevel = :id_educlevel AND id_doctype = :id_doctype',
									[':id_educlevel' => $this->id_educlevel,
									':id_doctype' => $this->id_doctype]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks if educlevels doctypes exists except this ID.
     *
     * @return boolean
     */
	public function existsExcept()
	{
		$row = $this->rowSelectOne('id',
									self::TABLE_NAME,
									'id_educlevel = :id_educlevel AND id_doctype = :id_doctype and id <> :id',
									[':id_educlevel' => $this->id_educlevel,
									':id_doctype' => $this->id_doctype,
									':id' => $this->id]);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves educlevels doctypes data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all educlevels doctypes data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME,
								$prepare['fields'],
								$prepare['params'],
								['id' => $this->id]);
	}

	/**
     * Removes educlevels doctypes.
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
