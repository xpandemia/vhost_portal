<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_ApplicationStatus as ApplicationStatus;
use common\models\Model_ApplicationPlaces as ApplicationPlaces;
use common\models\Model_ApplicationPlacesExams as ApplicationPlacesExams;
use common\models\Model_ApplicationAchievs as ApplicationAchievs;
use common\models\Model_Scans as Scans;

class Model_Application extends Db_Helper
{
	/*
		Application processing
	*/

	const TABLE_NAME = 'application';

	const TYPE_NEW = 1;
	const TYPE_NEW_NAME = 'Заявление на приём документов';
	const TYPE_CHANGE = 2;
	const TYPE_CHANGE_NAME = 'Заявление на изменение документов';
	const TYPE_RECALL = 3;
	const TYPE_RECALL_NAME = 'Заявление на отзыв документов';

	/*
		"GO" - sended, approved, rejected
	*/
	const STATUS_CREATED = 0;
	const STATUS_CREATED_NAME = 'Новое';
    const STATUS_SENDED = 1;
    const STATUS_SENDED_NAME = 'Отправлено';
    const STATUS_APPROVED = 2;
    const STATUS_APPROVED_NAME = 'Принято';
    const STATUS_REJECTED = 3;
    const STATUS_REJECTED_NAME = 'Отклонено';
    const STATUS_SAVED = 4;
    const STATUS_SAVED_NAME = 'Сохранено';
    const STATUS_CHANGED = 5;
    const STATUS_CHANGED_NAME = 'Изменено';
    const STATUS_RECALLED = 6;
    const STATUS_RECALLED_NAME = 'Отозвано';

	public $id;
	public $id_user;
	public $id_university;
	public $id_campaign;
	public $id_docseduc;
	public $id_docship;
	public $id_lang;
	public $id_app;
	public $type;
	public $status;
	public $numb;
	public $numb1s;
	public $campus;
	public $conds;
	public $remote;
	public $pay;
	public $active;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Application rules.
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
				'id_university' => [
									'required' => 1,
									'insert' => 1,
									'update' => 0,
									'value' => $this->id_university
									],
				'id_campaign' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->id_campaign
								],
				'id_docseduc' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_docseduc
								],
				'id_docship' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_docship
								],
				'id_lang' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->id_lang
							],
				'id_app' => [
							'required' => 0,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_app
							],
				'type' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->type
							],
				'status' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->status
							],
				'numb' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb
							],
				'numb1s' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->numb1s
							],
				'campus' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->campus
							],
				'conds' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->conds
							],
				'remote' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->remote
							],
				'pay' => [
						'required' => 1,
						'insert' => 1,
						'update' => 1,
						'value' => $this->pay
						],
				'active' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->active
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
     * Applications grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'numb' => [
							'name' => 'Номер',
							'type' => 'int'
							],
				'reason' => [
							'name' => 'Основание',
							'type' => 'string'
							],
				'type' => [
							'name' => 'Тип',
							'type' => 'string'
							],
				'status' => [
							'name' => 'Состояние',
							'type' => 'string'
							],
				'university' => [
								'name' => 'Место поступления',
								'type' => 'string'
								],
				'campaign' => [
								'name' => 'Приёмная кампания',
								'type' => 'string'
								],
				'docs_educ' => [
								'name' => 'Документ об образовании',
								'type' => 'string'
								]
				];
	}

	/**
     * Generates application numb.
     *
     * @return string
     */
	public function generateNumb()
	{
		if (isset($this->id) && !empty($this->id)) {
			return str_pad('', 11 - strlen($this->id), '0').$this->id;
		} else {
			return str_pad('', 11, '0');
		}
	}

	/**
     * Gets applications by user for GRID.
     *
     * @return array
     */
	public function getByUserGrid()
	{
		return $this->rowSelectAll("application.id,".
									" dict_university.code as university,".
									" admission_campaign.description as campaign,".
									" concat(dict_doctypes.description, ' № ', ifnull(concat(docs_educ.series, '-'), ''), docs_educ.numb, ' от ', date_format(dt_issue, '%d.%m.%Y')) as docs_educ,".
									" reason.numb as reason,".
									" getAppTypeName(application.type) as type,".
									" getAppStatusName(application.status) as status,".
									" application.numb",
									'application INNER JOIN dict_university ON application.id_university = dict_university.id'.
									' INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id'.
									' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
									' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id'.
									' LEFT OUTER JOIN application reason ON application.id_app = reason.id',
									'application.id_user = :id_user AND application.active = :active',
									[':id_user' => $_SESSION[APP_CODE]['user_id'],
									':active' => 1]);
	}

	/**
     * Gets application by ID.
     *
     * @return array
     */
	public function get()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id = :id',
									[':id' => $this->id]);
	}

	/**
     * Gets applications by user.
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
     * Gets application spec.
     *
     * @return array
     */
	public function getSpec()
	{
		$result = [];
		$app = $this->rowSelectOne('*', self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
		if ($app) {
			// docs shipment
			$docs_ship = $this->rowSelectOne('code as docs_ship',
											'dict_docships',
											'id = :id',
											[':id' => $app['id_docship']]);
			if (!is_array($docs_ship)) {
				$docs_ship = ['docs_ship' => null];
			}
			// scans
			$scan = new Model_Scans();
			$scan_arr = $scan->getByDocrowFull('application', $this->id);
			$result = array_merge($app, $docs_ship, $scan_arr);
		}
		return $result;
	}

	/**
     * Checks if campaign exists for user.
     *
     * @return boolean
     */
	public function existsUserCampaign() : bool
	{
		$app = $this->rowSelectAll('*',
									self::TABLE_NAME,
									'id_user = :id_user AND id_campaign = :id_campaign AND active = :active',
									[':id_user' => $_SESSION[APP_CODE]['user_id'],
									':id_campaign' => $this->id_campaign,
									':active' => 1],
									'dt_created', 1, 1);
		if ($app) {
			switch ($app['type']) {
				case self::TYPE_NEW:
					if ($app['status'] == self::STATUS_REJECTED) {
						return false;
					} else {
						return true;
					}
				case self::TYPE_CHANGE:
					return true;
				case self::TYPE_RECALL:
					if ($app['status'] == self::STATUS_APPROVED) {
						return false;
					} else {
						return true;
					}
				default:
					return true;
			}
		} else {
			return false;
		}
	}

	/**
     * Saves application data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->id_user = $_SESSION[APP_CODE]['user_id'];
		$this->status = self::STATUS_CREATED;
		$this->active = 1;
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		$id = $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
		if ($id > 0) {
			$this->id = $id;
			$this->numb = $this->generateNumb();
			$this->changeNumb();
		}
		return $id;
	}

	/**
     * Changes all application data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$this->numb = $this->generateNumb();
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME,
								$prepare['fields'],
								$prepare['params'],
								['id' => $this->id]);
	}

	/**
     * Changes application numb.
     *
     * @return boolean
     */
	public function changeNumb()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'numb = :numb',
								[':numb' => $this->numb],
								['id' => $this->id]);
	}

	/**
     * Changes application type.
     *
     * @return boolean
     */
	public function changeType()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'type = :type',
								[':type' => $this->type],
								['id' => $this->id]);
	}

	/**
     * Changes application status.
     *
     * @return boolean
     */
	public function changeStatus()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'status = :status',
								[':status' => $this->status],
								['id' => $this->id]);
	}

	/**
     * Changes application activity.
     *
     * @return boolean
     */
	public function changeActive()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'active = :active',
								[':active' => $this->active],
								['id' => $this->id]);
	}

	/**
     * Removes application.
     *
     * @return integer
     */
	public function clear()
	{
		// clear scans
		$scans = new Model_Scans();
		$scans->id_row = $this->id;
		$scans->clearbyDoc('application');
		// clear app
		return $this->rowDelete(self::TABLE_NAME, 'id = :id', [':id' => $this->id]);
	}

	/**
     * Copies application.
     *
     * @return integer
     */
	public function copy($type = null)
	{
		$app_old = $this->get();
		$this->active = 0;
		$this->changeActive();
		// new application
		$this->id_university = $app_old['id_university'];
		$this->id_campaign = $app_old['id_campaign'];
		$this->id_docseduc = $app_old['id_docseduc'];
		$this->id_docship = $app_old['id_docship'];
		$this->id_lang = $app_old['id_lang'];
		$this->id_app = $app_old['id'];
		if (empty($type)) {
			$this->type = self::TYPE_NEW;
		} else {
			$this->type = $type;
		}
		$this->campus = $app_old['campus'];
		$this->conds = $app_old['conds'];
		$this->remote = $app_old['remote'];
		$this->pay = $app_old['pay'];
		$id_old = $this->id;
		$this->save();
		if ($this->id > 0) {
			// log
			$applog = new ApplicationStatus();
			$applog->id_application = $this->id;
			$applog->create();
			// places
			$places = new ApplicationPlaces();
			$places->pid = $id_old;
			$places_arr = $places->getSpecsByApp();
			if ($places_arr) {
				foreach ($places_arr as $places_row) {
					$places->pid = $this->id;
					$places->id_spec = $places_row['id_spec'];
					$places->curriculum = $places_row['curriculum'];
					$place = $places->save();
					// exams
					$exams = new ApplicationPlacesExams();
					$exams->pid = $places_row['id'];
					$exams_arr = $exams->getExamsByPlaceFull();
					if ($exams_arr) {
						foreach ($exams_arr as $exams_row) {
							$exams->pid = $place;
							$exams->id_test = $exams_row['id_test'];
							$exams->id_discipline = $exams_row['id_discipline'];
							$exams->save();
						}
					}
				}
			}
			// achievs
			$ia = new ApplicationAchievs();
			$ia->pid = $id_old;
			$ia_arr = $ia->getByApp();
			if ($ia_arr) {
				foreach ($ia_arr as $ia_row) {
					$ia->pid = $this->id;
					$ia->id_achiev = $ia_row['id_achiev'];
					$ia->save();
				}
			}
			// scans
			$scans = new Scans();
			$scans->id_row = $id_old;
			$scans_arr = $scans->getByDocrow('application');
			if ($scans_arr) {
				foreach ($scans_arr as $scans_row) {
					$scans->id_doc = $scans_row['id_doc'];
					$scans->id_row = $this->id;
					$scans->id_scans = $scans_row['id_scans'];
					$scans->file_data = $scans_row['file_data'];
					$scans->file_name = $scans_row['file_name'];
					$scans->file_type = $scans_row['file_type'];
					$scans->file_size = $scans_row['file_size'];
					$scans->save();
				}
			}
		}
		return $this->id;
	}

	/**
     * Checks magistrature first.
     *
     * @return boolean
     */
	public function checkMagistratureFirst()
	{
		$row = $this->rowSelectOne('application.*',
									'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id'.
									' INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
									' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'application.id = :id AND left(admission_campaign.description, 12) = :description AND dict_doctypes.code in (:doc_type1, :doc_type2)',
									[':id' => $this->id,
									':description' => 'Магистратура',
									':doc_type1' => '000000022',
									':doc_type2' => '000000025']);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks high after.
     *
     * @return boolean
     */
	public function checkHighAfter()
	{
		$row = $this->rowSelectOne('application.*',
									'application INNER JOIN admission_campaign ON application.id_campaign = admission_campaign.id',
									'application.id = :id AND (left(admission_campaign.description, 10) = :description1 OR left(admission_campaign.description, 11) = :description2)',
									[':id' => $this->id,
									':description1' => 'Ординатура',
									':description2' => 'Аспирантура']);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Checks certificate.
     *
     * @return boolean
     */
	public function checkCertificate()
	{
		$row = $this->rowSelectOne('application.*',
									'application INNER JOIN docs_educ ON application.id_docseduc = docs_educ.id'.
									' INNER JOIN dict_doctypes ON docs_educ.id_doctype = dict_doctypes.id',
									'application.id = :id AND dict_doctypes.code in (:code1, :code2)',
									[':id' => $this->id,
									':code1' => '000000026',
									':code2' => '000000088']);
		if (!empty($row)) {
			return true;
		} else {
			return false;
		}
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
