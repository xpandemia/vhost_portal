<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_DictScans as Model_DictScans;

class Model_Resume extends Db_Helper
{
	/*
		Resume data processing
	*/

	const TABLE_NAME = 'resume';

	const STATUS_CREATED = 0;
    const STATUS_SENDED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

	public $id;
	public $id_user;
	public $dt_created;
	public $id_approver;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets resume by user.
     *
     * @return array
     */
	public function getByUser()
	{
		$fields = self::TABLE_NAME.'.id, id_user, status, name_first, name_middle, name_last, sex, birth_dt, birth_place,'.
				' dict_citizenship.citizenship_name as citizenship, personal.citizenship as id_citizenship, personal.guid,'.
				" (SELECT scans.id".
				" FROM scans INNER JOIN docs ON scans.id_doc = docs.id".
				" INNER JOIN dict_scans ON scans.id_scans = dict_scans.id".
				" WHERE id_row = ".self::TABLE_NAME.".id AND docs.doc_code = '".self::TABLE_NAME."' AND dict_scans.scan_code = 'agreement') as agreement_id,".
				" (SELECT file_data".
				" FROM scans INNER JOIN docs ON scans.id_doc = docs.id".
				" INNER JOIN dict_scans ON scans.id_scans = dict_scans.id".
				" WHERE id_row = ".self::TABLE_NAME.".id AND docs.doc_code = '".self::TABLE_NAME."' AND dict_scans.scan_code = 'agreement') as agreement,".
				" (SELECT file_type".
				" FROM scans INNER JOIN docs ON scans.id_doc = docs.id".
				" INNER JOIN dict_scans ON scans.id_scans = dict_scans.id".
				" WHERE id_row = ".self::TABLE_NAME.".id AND docs.doc_code = '".self::TABLE_NAME."' AND dict_scans.scan_code = 'agreement') as agreement_type,".
				' (SELECT contact FROM contacts WHERE id_resume = '.self::TABLE_NAME.'.id and type = 0) as email,'.
				' (SELECT contact FROM contacts WHERE id_resume = '.self::TABLE_NAME.'.id and type = 1) as phone,'.
				' dict_doctypes.code as passport_type,'.
				' passport.series, passport.numb, passport.dt_issue, passport.unit_name, passport.unit_code, passport.dt_end,'.
				' (SELECT dict_doctypes.code
				FROM passport LEFT OUTER JOIN dict_doctypes ON id_doctype = dict_doctypes.id
				WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as passport_type_old,'.
				' (SELECT series FROM passport WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as series_old,'.
				' (SELECT numb FROM passport WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as numb_old,'.
				' (SELECT dt_issue FROM passport WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as dt_issue_old,'.
				' (SELECT unit_name FROM passport WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as unit_name_old,'.
				' (SELECT unit_code FROM passport WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as unit_code_old,'.
				' (SELECT dt_end FROM passport WHERE id_resume = '.self::TABLE_NAME.'.id AND main = 0) as dt_end_old,'.
				' (SELECT adr FROM address WHERE id_resume = '.self::TABLE_NAME.'.id AND type = 0) as address_reg,'.
				' (SELECT country_code
				FROM address INNER JOIN dict_countries ON address.id_country = dict_countries.id
				WHERE id_resume = '.self::TABLE_NAME.'.id AND type = 0) as country_reg,'.
				' (SELECT adr FROM address WHERE id_resume = '.self::TABLE_NAME.'.id AND type = 1) as address_res,'.
				' (SELECT country_code
				FROM address INNER JOIN dict_countries ON address.id_country = dict_countries.id
				WHERE id_resume = '.self::TABLE_NAME.'.id AND type = 1) as country_res';
		$scans = new Model_DictScans();
		$scans->doc_code = 'resume';
		$scans_arr = $scans->getByDocument();
		if ($scans_arr) {
			foreach ($scans_arr as $scans_row) {
				$fields .= ", (SELECT scans.id".
							" FROM scans INNER JOIN docs ON scans.id_doc = docs.id".
								" INNER JOIN dict_scans ON scans.id_scans = dict_scans.id".
							" WHERE id_row = ".self::TABLE_NAME.".id AND docs.doc_code = '".self::TABLE_NAME."' AND dict_scans.scan_code = '".$scans_row['scan_code']."') as ".$scans_row['scan_code']."_id";
				$fields .= ", (SELECT file_data".
							" FROM scans INNER JOIN docs ON scans.id_doc = docs.id".
								" INNER JOIN dict_scans ON scans.id_scans = dict_scans.id".
							" WHERE id_row = ".self::TABLE_NAME.".id AND docs.doc_code = '".self::TABLE_NAME."' AND dict_scans.scan_code = '".$scans_row['scan_code']."') as ".$scans_row['scan_code'];
				$fields .= ", (SELECT file_type".
							" FROM scans INNER JOIN docs ON scans.id_doc = docs.id".
								" INNER JOIN dict_scans ON scans.id_scans = dict_scans.id".
							" WHERE id_row = ".self::TABLE_NAME.".id AND docs.doc_code = '".self::TABLE_NAME."' AND dict_scans.scan_code = '".$scans_row['scan_code']."') as ".$scans_row['scan_code']."_type";
			}
		}
		return $this->rowSelectOne($fields,
								self::TABLE_NAME.' LEFT OUTER JOIN personal ON '.self::TABLE_NAME.'.id = personal.id_resume'.
								' LEFT OUTER JOIN dict_citizenship ON personal.citizenship = dict_citizenship.id'.
								' LEFT OUTER JOIN passport ON resume.id = passport.id_resume'.
								' LEFT OUTER JOIN dict_doctypes ON passport.id_doctype = dict_doctypes.id',
								'id_user = :id_user AND passport.main = :passport',
								[':id_user' => $this->id_user,
								':passport' => 1]);
	}

	/**
     * Saves resume data to database.
     *
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('id_user, status, dt_created',
								self::TABLE_NAME,
								':id_user, :status, :dt_created',
								[':id_user' => $this->id_user,
								':status' => self::STATUS_CREATED,
								':dt_created' => $this->dt_created]);
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

	public function __destruct()
	{
		$this->db = null;
	}
}
