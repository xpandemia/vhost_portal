<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_Personal as Model_Personal;
use common\models\Model_Contacts as Model_Contacts;
use common\models\Model_DictScans as Model_DictScans;

class Model_Resume extends Db_Helper
{
	/*
		Resume data processing
	*/

	const TABLE_NAME = 'resume';

	const STATUS_CREATED = 0;
	const STATUS_CREATED_NAME = 'Новая';
	const STATUS_SAVED = 1;
	const STATUS_SAVED_NAME = 'Сохранена';
    const STATUS_SENDED = 2;
    const STATUS_SENDED_NAME = 'Отправлена';
    const STATUS_APPROVED = 3;
    const STATUS_APPROVED_NAME = 'Принята';
    const STATUS_REJECTED = 4;
    const STATUS_REJECTED_NAME = 'Отклонена';

	public $id;
	public $id_user;
	public $status;
	public $app;
	public $dt_created;
	public $dt_updated;
	public $dt_approve;
	public $id_approver;
	public $comment;

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
				'status' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->status
							],
				'app' => [
						'required' => 1,
						'insert' => 1,
						'update' => 1,
						'value' => $this->app
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
				'dt_approve' => [
								'required' => 0,
								'insert' => 0,
								'update' => 1,
								'value' => $this->dt_approve
								],
				'id_approver' => [
								'required' => 0,
								'insert' => 0,
								'update' => 1,
								'value' => $this->id_approver
								],
				'comment' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->comment
							],
				];
	}

	/**
     * Gets resume by user.
     *
     * @return array
     */
	public function getByUser()
	{
		$resume = $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']]);
		if ($resume) {
			$personal = $this->rowSelectOne('name_first, name_middle, name_last, sex, birth_dt, birth_place, dict_countries.code as citizenship, dict_countries.description as citizenship_name, beneficiary',
											'personal INNER JOIN dict_countries ON personal.citizenship = dict_countries.id',
											'id_resume = :id_resume',
											[':id_resume' => $resume['id']]);
			if (!is_array($personal)) {
				$personal = ['name_first' => null, 'name_middle' => null, 'name_last' => null, 'sex' => null, 'birth_dt' => null, 'birth_place' => null, 'citizenship' => null, 'citizenship_name' => null, 'beneficiary' => null];
			}
			$agreement = $this->rowSelectOne('scans.id as agreement_id, file_data as agreement, file_type as agreement_type',
											'scans INNER JOIN docs ON scans.id_doc = docs.id'.
											' INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
											'id_row = :id_row AND doc_code = :doc_code AND scan_code = :scan_code',
											[':id_row' => $resume['id'],
											':doc_code' => 'resume',
											':scan_code' => 'agreement']);
			if (!is_array($agreement)) {
				$agreement = ['agreement_id' => null, 'agreement' => null, 'agreement_type' => null];
			}
			$email = $this->rowSelectOne('contact as email',
										'contacts',
										'id_resume = :id_resume AND type = :type',
										[':id_resume' => $resume['id'], ':type' => Model_Contacts::TYPE_EMAIL]);
			if (!is_array($email)) {
				$email = ['email' => null];
			}
			$phone_mobile = $this->rowSelectOne('contact as phone_mobile',
												'contacts',
												'id_resume = :id_resume AND type = :type',
												[':id_resume' => $resume['id'], ':type' => Model_Contacts::TYPE_PHONE_MOBILE]);
			if (!is_array($phone_mobile)) {
				$phone_mobile = ['phone_mobile' => null];
			} else {
				$phone_mobile['phone_mobile'] = Model_Contacts::prettyPhoneMobile($phone_mobile['phone_mobile']);
			}
			$phone_home = $this->rowSelectOne('contact as phone_home',
												'contacts',
												'id_resume = :id_resume AND type = :type',
												[':id_resume' => $resume['id'], ':type' => Model_Contacts::TYPE_PHONE_HOME]);
			if (!is_array($phone_home)) {
				$phone_home = ['phone_home' => null];
			}
			$phone_add = $this->rowSelectOne('contact as phone_add',
												'contacts',
												'id_resume = :id_resume AND type = :type',
												[':id_resume' => $resume['id'], ':type' => Model_Contacts::TYPE_PHONE_ADD]);
			if (!is_array($phone_add)) {
				$phone_add = ['phone_add' => null];
			}
			$passport = $this->rowSelectOne('dict_doctypes.code as passport_type, dict_doctypes.description as passport_type_name, series, numb, dt_issue, unit_name, unit_code, dt_end',
											'passport INNER JOIN dict_doctypes ON passport.id_doctype = dict_doctypes.id',
											'id_resume = :id_resume AND main = :main',
											[':id_resume' => $resume['id'], ':main' => 1]);
			if (!is_array($passport)) {
				$passport = ['passport_type' => null, 'series' => null, 'numb' => null, 'dt_issue' => null, 'unit_name' => null, 'unit_code' => null, 'dt_end' => null];
			}
			$passport_old = $this->rowSelectOne('dict_doctypes.code as passport_type_old, series as series_old, numb as numb_old, dt_issue as dt_issue_old, unit_name as unit_name_old, unit_code as unit_code_old, dt_end as dt_end_old',
												'passport INNER JOIN dict_doctypes ON passport.id_doctype = dict_doctypes.id',
												'id_resume = :id_resume AND main = :main',
												[':id_resume' => $resume['id'], ':main' => 0]);
			if (!is_array($passport_old)) {
				$passport_old = ['passport_type_old' => null, 'series_old' => null, 'numb_old' => null, 'dt_issue_old' => null, 'unit_name_old' => null, 'unit_code_old' => null, 'dt_end_old' => null];
			}
			$address_reg = $this->rowSelectOne('dict_countries.code as country_reg, adr as address_reg',
												'address INNER JOIN dict_countries ON address.id_country = dict_countries.id',
												'id_resume = :id_resume AND type = :type',
												[':id_resume' => $resume['id'], ':type' => 0]);
			if (!is_array($address_reg)) {
				$address_reg = ['country_reg' => null, 'address_reg' => null];
			}
			$address_res = $this->rowSelectOne('dict_countries.code as country_res, adr as address_res',
												'address INNER JOIN dict_countries ON address.id_country = dict_countries.id',
												'id_resume = :id_resume AND type = :type',
												[':id_resume' => $resume['id'], ':type' => 1]);
			if (!is_array($address_res)) {
				$address_res = ['country_res' => null, 'address_res' => null];
			}
			$result = array_merge($resume, $personal, $agreement, $email, $phone_mobile, $phone_home, $phone_add, $passport, $passport_old, $address_reg, $address_res);
			// scans
			$scans = new Model_DictScans();
			$scans->doc_code = 'resume';
			$scans_arr = $scans->getByDocument();
			if ($scans_arr) {
				foreach ($scans_arr as $scans_row) {
					$row = $this->rowSelectOne('scans.id, file_data, file_type',
												'scans INNER JOIN docs ON scans.id_doc = docs.id'.
												' INNER JOIN dict_scans ON scans.id_scans = dict_scans.id',
												'id_row = :id_row AND doc_code = :doc_code AND scan_code = :scan_code',
												[':id_row' => $resume['id'],
												':doc_code' => $scans->doc_code,
												':scan_code' => $scans_row['scan_code']]);
					if (!is_array($row)) {
						$scan = [$scans_row['scan_code'].'_id' => null, $scans_row['scan_code'] => null, $scans_row['scan_code'].'_type' => null];
					} else {
						$scan = [$scans_row['scan_code'].'_id' => $row['id'], $scans_row['scan_code'] => $row['file_data'], $scans_row['scan_code'].'_type' => $row['file_type']];
					}
					$result = array_merge($result, $scan);
				}
			}
			return $result;
		} else {
			return null;
		}
	}

	/**
     * Saves resume data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->id_user = $_SESSION[APP_CODE]['user_id'];
		$this->status = self::STATUS_CREATED;
		$this->app = 1;
		$this->dt_created = date('Y-m-d H:i:s');
		$this->dt_updated = null;
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes resume status.
     *
     * @return boolean
     */
	public function changeStatus()
	{
		if (self::STATUS_APPROVED) {
			return $this->rowUpdate(self::TABLE_NAME,
								'status = :status, dt_updated = :dt_updated, dt_approve = :dt_approve, id_approver = :id_approver',
								[':status' => $this->status,
								':dt_updated' => date('Y-m-d H:i:s'),
								':dt_approve' => date('Y-m-d H:i:s'),
								':id_approver' => $this->id_approver],
								['id' => $this->id]);
		} else {
			return $this->rowUpdate(self::TABLE_NAME,
								'status = :status, dt_updated = :dt_updated',
								[':status' => $this->status,
								':dt_updated' => date('Y-m-d H:i:s')],
								['id' => $this->id]);
		}
	}

	/**
     * Changes resume app.
     *
     * @return boolean
     */
	public function changeApp()
	{
		return $this->rowUpdate(self::TABLE_NAME,
							'app = :app, dt_updated = :dt_updated',
							[':app' => $this->app,
							':dt_updated' => date('Y-m-d H:i:s')],
							['id' => $this->id]);
	}

	/**
     * Checks resume by user.
     *
     * @return boolean
     */
	public function checkByUser()
	{
		return $this->rowSelectOne('*',
									self::TABLE_NAME,
									'id_user = :id_user',
									[':id_user' => $_SESSION[APP_CODE]['user_id']]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
