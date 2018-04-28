<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictSpeciality extends Db_Helper
{
	/*
		Dictionary speciality processing
	*/

	const TABLE_NAME = 'dict_speciality';

	public $id;
	public $faculty_code;
	public $faculty_name;
	public $speciality_code;
	public $speciality_name;
	public $profil_code;
	public $profil_name;
	public $edulevel_code;
	public $edulevel_name;
	public $eduform_code;
	public $eduform_name;
	public $eduprogram_code;
	public $eduprogram_name;
	public $finance_code;
	public $finance_name;
	public $group_code;
	public $group_name;
	public $speciality_human_code;
	public $campaign_code;
	public $detail_group_name;
	public $detail_group_code;
	public $receipt_allowed;
	public $archive;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Dictionary speciality rules.
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
				'faculty_code' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->faculty_code
									],
				'faculty_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->faculty_name
								],
				'speciality_code' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->speciality_code
									],
				'speciality_name' => [
									'required' => 1,
									'insert' => 1,
									'update' => 1,
									'value' => $this->speciality_name
									],
				'profil_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->profil_code
								],
				'profil_name' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->profil_name
								],
				'edulevel_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->edulevel_code
									],
				'edulevel_name' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->edulevel_name
									],
				'eduform_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->eduform_code
									],
				'eduform_name' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->eduform_name
									],
				'eduprogram_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->eduprogram_code
									],
				'eduprogram_name' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->eduprogram_name
									],
				'finance_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->finance_code
									],
				'finance_name' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->finance_name
									],
				'group_code' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->group_code
								],
				'group_name' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->group_name
								],
				'speciality_human_code' => [
											'required' => 1,
											'insert' => 1,
											'update' => 1,
											'value' => $this->speciality_human_code
											],
				'campaign_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->campaign_code
									],
				'detail_group_name' => [
										'required' => 0,
										'insert' => 1,
										'update' => 1,
										'value' => $this->detail_group_name
										],
				'detail_group_code' => [
										'required' => 0,
										'insert' => 1,
										'update' => 1,
										'value' => $this->detail_group_code
										],
				'receipt_allowed' => [
									'required' => 0,
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
     * Gets all specialities.
     *
     * @return array
     */
	public function getAll()
	{
		return $this->rowSelectAll('*', self::TABLE_NAME);
	}

	/**
     * Gets one speciality.
     *
     * @return array
     */
	public function getOne()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'faculty_code = :faculty_code AND speciality_code = :speciality_code AND profil_code = :profil_code AND edulevel_code = :edulevel_code AND eduform_code = :eduform_code AND eduprogram_code = :eduprogram_code AND campaign_code = :campaign_code',
								[':faculty_code' => $this->faculty_code,
								':speciality_code' => $this->speciality_code,
								':profil_code' => $this->profil_code,
								':edulevel_code' => $this->edulevel_code,
								':eduform_code' => $this->eduform_code,
								':eduprogram_code' => $this->eduprogram_code,
								':campaign_code' => $this->campaign_code]);
	}

	/**
     * Gets speciality by ID.
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
     * Saves speciality data to database.
     *
     * @return integer
     */
	public function save()
	{
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all speciality data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME,
								$prepare['fields'],
								$prepare['params'],
								['faculty_code' => $this->faculty_code,
								'speciality_code' => $this->speciality_code,
								'profil_code' => $this->profil_code,
								'edulevel_code' => $this->edulevel_code,
								'eduform_code' => $this->eduform_code,
								'eduprogram_code' => $this->eduprogram_code,
								'campaign_code' => $this->campaign_code]);
	}

	/**
     * Removes all specialities.
     *
     * @return integer
     */
	public function clearAll()
	{
		return $this->rowDelete(self::TABLE_NAME);
	}

	/**
     * Loads specialities.
     *
     * @return array
     */
	public function load($xml, $id_dict, $dict_name, $clear_load)
	{
		$result['success_msg'] = null;
		$result['error_msg'] = null;
		if ($xml === false || !isset($xml->return->StringsPlanPriema) || !isset($xml->return->StringsPlanPriema->StringPlanPriema)) {
            $result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
        if (isset($result->return->UniversalResponse->Complete) && $result->return->UniversalResponse->Complete == "0") {
            $result['error_msg'] = 'Ошибка при выполнении метода GetStringsPriema: '.$xml->return->UniversalResponse->Description;
            return $result;
        }
		$StringPlanPriema = $xml->return->StringsPlanPriema->StringPlanPriema;
        if (!is_array($StringPlanPriema)) {
            $StringPlanPriema = [$StringPlanPriema];
        }
        if(sizeof($StringPlanPriema) == 0) {
			$result['error_msg'] = 'Не удалось получить данные справочника "'.$dict_name.'"!';
			return $result;
        }
		$log = new Model_DictionaryManagerLog();
		$log->id_dict = $id_dict;
		$log->id_user = $_SESSION[APP_CODE]['user_id'];
			if ($clear_load == 1) {
				// clear
				$rows_del = $this->$clear_load();
				$log->msg = 'Удалено направлений подготовки - '.$rows_del.'.';
				$log->value_old = null;
				$log->value_new = null;
				$log->save();
			} else {
				$rows_del = 0;
			}
		$rows_ins = 0;
		$rows_upd = 0;
		foreach ($StringPlanPriema as $string_priema) {
            $this->faculty_code = (string)$string_priema->FacultetCode;
            $this->faculty_name = (string)$string_priema->FacultetName;
            $this->speciality_code = (string)$string_priema->SpecialityCode;
            $this->speciality_name = (string)$string_priema->SpecialityName;
            $this->profil_code = (string)$string_priema->ProfilCode;
            $this->profil_name = (string)$string_priema->ProfilName;
            $this->edulevel_code = (string)$string_priema->EducationLevelCode;
            $this->edulevel_name = (string)$string_priema->EducationLevelName;
            $this->eduform_code = (string)$string_priema->EducationFormCode;
            $this->eduform_name = (string)$string_priema->EducationFormName;
            $this->eduprogram_code = (string)$string_priema->EducationProgramCode;
            $this->eduprogram_name = (string)$string_priema->EducationProgramName;
            $this->finance_code = (string)$string_priema->FinanceCode;
            $this->finance_name = (string)$string_priema->FinanceName;
            $this->group_code = (string)$string_priema->GroupCode;
            $this->group_name = (string)$string_priema->GroupName;
            $this->speciality_human_code = (string)$string_priema->SpecialityCodeOKSO;
            $this->campaign_code = (string)$string_priema->IdPK;
            $this->detail_group_code = (string)$string_priema->DetailGroupCode;
            $this->detail_group_name = (string)$string_priema->DetailGroupName;
            $spec = $this->getOne();
            if ($spec == null) {
				// insert
				if ($this->save()) {
					$log->msg = 'Создано направление подготовки на факультет '.$this->faculty_code.' по специальности '.$this->speciality_code.'.';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$rows_ins++;
				} else {
					$result['error_msg'] = 'Ошибка при сохранении направление подготовки на факультет '.$this->faculty_code.' по специальности '.$this->speciality_code.'!';
					return $result;
				}
			} else {
				// update
				$upd = 0;
				if ($this->changeAll()) {
					$log->msg = 'Изменено направление подготовки на факультет '.$this->faculty_code.' по специальности '.$this->speciality_code.'.';
					$log->value_old = null;
					$log->value_new = null;
					$log->save();
					$upd = 1;
				} else {
					$result['error_msg'] = 'Ошибка при изменении направление подготовки на факультет '.$this->faculty_code.' по специальности '.$this->speciality_code.'!';
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
