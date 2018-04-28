<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_AdmissionCampaign extends Db_Helper
{
	/*
		Admission campaigns processing
	*/

	const TABLE_NAME = 'admission_campaign';

	public $id;
	public $code;
	public $description;
	public $max_spec;
	public $max_spec_type;
	public $receipt_allowed;
	public $archive;

	public $university;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Admission campaigns rules.
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
				'code' => [
							'required' => 1,
							'insert' => 1,
							'update' => 1,
							'value' => $this->code
							],
				'description' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->description
								],
				'max_spec' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->max_spec
								],
				'max_spec_type' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->max_spec_type
									],
				'receipt_allowed' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->receipt_allowed
									],
				'archive' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->archive
							]
				];
	}

	/**
     * Gets all admission campaigns.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets one admission campaign.
     *
     * @return array
     */
	public function getOne()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'code = :code',
								[':code' => $this->code]);
	}

	/**
     * Gets admission campaign by ID.
     *
     * @return array
     */
	public function getById()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id = :id',
								[':id' => $this->id]);
	}

	/**
     * Gets admission campaign by code.
     *
     * @return array
     */
	public function getByCode()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'code = :code',
								[':code' => $this->code]);
	}

	/**
     * Gets admission campaigns by university.
     *
     * @return array
     */
	public function getByUniversity()
	{
		switch ($this->university) {
			case 'БелГУ':
				return $this->rowSelectAll('code, description',
											self::TABLE_NAME,
											'description not like :code AND receipt_allowed = :receipt_allowed',
											[':code' => '%СОФ%',
											':receipt_allowed' => 1]);
			case 'СОФ':
				return $this->rowSelectAll('code, description',
											self::TABLE_NAME,
											'description like :code AND receipt_allowed = :receipt_allowed',
											[':code' => '%СОФ%',
											':receipt_allowed' => 1]);
			default:
				return $this->rowSelectAll('code, description',
											self::TABLE_NAME,
											'receipt_allowed = :receipt_allowed',
											[':receipt_allowed' => 1]);
		}
	}

	/**
     * Saves admission campaign data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all admission campaign data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME,
								$prepare['fields'],
								$prepare['params'],
								['code' => $this->code]);
	}

	/**
     * Removes all admission campaigns.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads admission campaigns.
     *
     * @return array
     */
	public function load($xml, $id_dict, $dict_name, $clear_load)
	{
		$result['success_msg'] = null;
		$result['error_msg'] = null;
		if ($xml === false || !isset($xml->return->PK)) {
            $result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
        $PK = $xml->return->PK;
        if (!is_array($PK)) {
            $PK = [$PK];
        }
		if(sizeof($PK) == 0) {
			$result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
        $log = new Model_DictionaryManagerLog();
		$log->id_dict = $id_dict;
		$log->id_user = $_SESSION[APP_CODE]['user_id'];
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено приёмных кампаний - '.$rows_del.'.';
				$log->value_old = null;
				$log->value_new = null;
				$log->save();
			} else {
				$rows_del = 0;
			}
		$rows_ins = 0;
		$rows_upd = 0;
		foreach ($PK as $string_pk) {
            $this->code = (string)$string_pk->IdPK;
            $this->description = (string)$string_pk->Description;
            $this->max_spec = (int)$string_pk->MaximalSpeciality;
            $this->max_spec_type = (string)$string_pk->MaximalSpecialityType;
            if ((boolean)$string_pk->ReceptionAllowed == true) {
                $this->receipt_allowed = 1;
            } else {
				$this->receipt_allowed = 0;
			}
            $pk = $this->getOne();
            if ($pk == null) {
				if ($this->save()) {
					$log->msg = 'Создана приёмная кампания с кодом "'.$this->code.'".';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$rows_ins++;
				} else {
					$result['error_msg'] = 'Ошибка при приёмной кампании с кодом " '.$this->code.'"!';
					return $result;
				}
			} else {
				// update
				$upd = 0;
				if ($this->changeAll()) {
					$log->msg = 'Изменена приёмная кампания с кодом "'.$this->code.'"".';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$upd = 1;
				} else {
					$result['error_msg'] = 'Ошибка при изменении приёмной кампании с кодом "'.$this->code.'"!';
					return $result;
				}
			}
        }
        if ($rows_del == 0 && $rows_ins == 0 && $rows_upd == 0) {
			$result['success_msg'] = 'Справочник "'.$dict_name.'" не нуждается в обновлении.';
		} else {
			$result['success_msg'] = nl2br("В справочнике \"$dict_name\":\n----- удалено записей - $rows_del\n----- добавлено записей - $rows_ins\n----- обновлено записей - $rows_upd");
		}
        return $result;
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
