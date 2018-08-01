<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_Application as Application;

class Model_DocsEduc extends Db_Helper
{
	/*
		Education documents processing
	*/

	const TABLE_NAME = 'docs_educ';

	public $id;
	public $id_user;
	public $id_eductype;
	public $id_doctype;
	public $id_educform;
	public $series;
	public $numb;
	public $school;
	public $dt_issue;
	public $end_year;
	public $speciality;
	public $dt_created;
	public $dt_updated;

	const CERTIFICATES = ['000000026', '000000088'];
	const DIPLOMAS = ['000000022', '000000023', '000000024', '000000025', '000000046', '000000048', '000000054', '000000153'];
	const DIPLOMA_BACHELOR = '000000022';
	const DIPLOMA_SPECIALIST = '000000024';
	const DIPLOMA_SPECIALIST_DIPLOMA = '000000025';
	const DIPLOMA_MAGISTER = '000000023';
	const CLASSES_9 = ['000000088'];
	const CLASSES_11 = ['000000026'];
	const HIGH_BEFORE = ['000000026', '000000046', '000000048', '000000088'];
	const HIGH_AFTER = ['000000022', '000000023', '000000024', '000000025'];

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Education documents rules.
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
				'id_eductype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_eductype
								],
				'id_doctype' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_doctype
								],
				'id_educform' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_educform
								],
				'series' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->series
							],
				'numb' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'school' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->school
							],
				'dt_issue' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->dt_issue
								],
				'end_year' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->end_year
								],
				'speciality' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->speciality
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
     * Education documents grid.
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
				'educ_type' => [
								'name' => 'Вид образования',
								'type' => 'string'
								],
				'doc_type' => [
								'name' => 'Тип документа',
								'type' => 'string'
								],
				'series' => [
							'name' => 'Серия',
							'type' => 'string'
							],
				'numb' => [
							'name' => 'Номер',
							'type' => 'string'
							],
				'dt_issue' => [
								'name' => 'Дата выдачи',
								'type' => 'date',
								'format' => 'd.m.Y'
							],
				'school' => [
							'name' => 'Учебное заведение',
							'type' => 'string'
							],
				'end_year' => [
								'name' => 'Год окончания',
								'type' => 'int'
								]
				];
	}

	/**
     * Gets education document by ID.
     *
     * @return array
     */
	public function get()
	{
		$doc_educ = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($doc_educ) {
			$educ_type = $this->rowSelectOne('code as educ_type',
											'dict_eductypes',
											'id = :id',
											[':id' => $doc_educ['id_eductype']]);
			if (!is_array($educ_type)) {
				$educ_type = ['educ_type' => null];
			}
			$doc_type = $this->rowSelectOne('code as doc_type',
											'dict_doctypes',
											'id = :id',
											[':id' => $doc_educ['id_doctype']]);
			if (!is_array($doc_type)) {
				$doc_type = ['doc_type' => null];
			}
			$educ_form = $this->rowSelectOne('code as educ_form',
											'dict_educforms',
											'id = :id',
											[':id' => $doc_educ['id_educform']]);
			if (!is_array($educ_form)) {
				$educ_form = ['educ_form' => null];
			}
			$change_name = $this->rowSelectOne('scans.id as change_name_id, file_data as change_name, file_type as change_name_type',
											'scans INNER JOIN docs ON scans.id_doc = docs.id'.
											' INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
											'id_row = :id_row AND doc_code = :doc_code AND scan_code = :scan_code',
											[':id_row' => $doc_educ['id'],
											':doc_code' => 'docs_educ',
											':scan_code' => 'change_name']);
			if (!is_array($change_name)) {
				$change_name = ['change_name_id' => null, 'change_name' => null, 'change_name_type' => null];
			}
			$result = array_merge($doc_educ, $educ_type, $doc_type, $educ_form, $change_name);
			$scan = new Model_Scans();
			$scan_arr = $scan->getByDocrowFull('docs_educ', $doc_educ['id']);
			$result = array_merge($result, $scan_arr);
			return $result;
		} else {
			return null;
		}
	}

	/**
     * Gets education document by ID for PDF.
     *
     * @return array
     */
	public function getForPdf()
	{
		$doc_educ = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($doc_educ) {
			$educ_type = $this->rowSelectOne('description as educ_type',
											'dict_eductypes',
											'id = :id',
											[':id' => $doc_educ['id_eductype']]);
			if (!is_array($educ_type)) {
				$educ_type = ['educ_type' => null];
			}
			$doc_type = $this->rowSelectOne('code as doc_type',
											'dict_doctypes',
											'id = :id',
											[':id' => $doc_educ['id_doctype']]);
			if (!is_array($doc_type)) {
				$doc_type = ['doc_type' => null];
			}
			$educ_form = $this->rowSelectOne('description as educ_form',
											'dict_educforms',
											'id = :id',
											[':id' => $doc_educ['id_educform']]);
			if (!is_array($educ_form)) {
				$educ_form = ['educ_form' => null];
			}
			return $result = array_merge($doc_educ, $educ_type, $doc_type, $educ_form);;
		} else {
			return null;
		}
	}

	/**
     * Gets education documents by ID for single field.
     *
     * @return array
     */
	public function getForField()
	{
		return $this->rowSelectOne("concat(dict_doctypes.description, ' № ', ifnull(concat(docs_educ.series, '-'), ''), docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as docs_educ",
									'docs_educ INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'docs_educ.id = :id',
									[':id' => $this->id]);
	}

	/**
     * Gets education documents by user.
     *
     * @return array
     */
	public function getByUser()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets education documents by user for SelectList.
     *
     * @return array
     */
	public function getByUserSl()
	{
		return $this->rowSelectAll("docs_educ.id, concat(dict_doctypes.description, ' № ', ifnull(concat(docs_educ.series, '-'), ''), docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as description",
									'docs_educ INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'docs_educ.id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']],
									'description ASC');
	}

	/**
     * Gets campaign education documents by user for SelectList.
     *
     * @return array
     */
	public function getByUserSlCampaign($campaign_code)
	{
		if (!empty($campaign_code)) {
			return $this->rowSelectAll("DISTINCT docs_educ.id, concat(dict_doctypes.description, ' № ', ifnull(concat(docs_educ.series, '-'), ''), docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as description",
										'docs_educ'.
										' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id'.
										' INNER JOIN educlevels_doctypes ON educlevels_doctypes.id_doctype = docs_educ.id_doctype'.
										' INNER JOIN dict_educlevels ON educlevels_doctypes.id_educlevel = dict_educlevels.id'.
										' INNER JOIN dict_speciality ON dict_educlevels.code = dict_speciality.edulevel_code',
										'dict_speciality.campaign_code = :campaign_code and docs_educ.id_user = :id_user',
										[':campaign_code' => $campaign_code,
										':id_user' => $_SESSION[APP_CODE]['user_id']],
										'description ASC');
		} else {
			return null;
		}
	}

	/**
     * Gets education documents by user for GRID.
     *
     * @return array
     */
	public function getByUserGrid()
	{
		return $this->rowSelectAll("docs_educ.id, dict_eductypes.description as educ_type, dict_doctypes.description as doc_type, series, numb, dt_issue, school, end_year",
									'docs_educ INNER JOIN dict_eductypes ON docs_educ.id_eductype = dict_eductypes.id'.
									' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	/**
     * Gets education document by series/numb.
     *
     * @return array
     */
	public function getByNumb()
	{
		if (empty($this->series)) {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'series is null AND numb = :numb',
									[':numb' => $this->numb]);
		} else {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'series = :series AND numb = :numb',
									[':series' => $this->series,
									':numb' => $this->numb]);
		}
	}

	/**
     * Gets education document by series/numb except this ID.
     *
     * @return array
     */
	public function getByNumbExcept()
	{
		if (empty($this->series)) {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'series is null AND numb = :numb AND id <> :id',
									[':numb' => $this->numb,
									':id' => $this->id]);
		} else {
			return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'series = :series AND numb = :numb AND id <> :id',
									[':series' => $this->series,
									':numb' => $this->numb,
									':id' => $this->id]);
		}
	}

	/**
     * Checks if education document used in applications "GO".
     *
     * @return boolean
     */
	public function existsAppGo() : bool
	{
		$app_arr = $this->rowSelectAll('application.id',
										'application INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id',
										'docs_educ.id = :id AND application.status in (:status1, :status2)',
										[':id' => $this->id,
										':status1' => Application::STATUS_SENDED,
										':status2' => Application::STATUS_APPROVED]);
		if ($app_arr) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Saves education document data to database.
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
     * Changes all education document data.
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
     * Removes education document.
     *
     * @return integer
     */
	public function clear()
	{
		$scans = new Model_Scans();
		$scans->id_row = $this->id;
		$scans->clearbyDoc('docs_educ');
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
