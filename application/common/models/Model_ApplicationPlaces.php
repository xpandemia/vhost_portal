<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ApplicationPlaces extends Db_Helper
{
	/*
		Application places processing
	*/

	const TABLE_NAME = 'application_places';

	const FREE = 'Бюджетная основа';
	const PAY = 'Полное возмещение затрат';
	const PURPOSE = 'Целевой прием';

	public $id;
	public $pid;
	public $id_user;
	public $id_spec;
	public $curriculum;
	public $dt_created;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Application places rules.
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
				'pid' => [
						'required' => 1,
						'insert' => 1,
						'update' => 0,
						'value' => $this->pid
						],
				'id_user' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_user
							],
				'id_spec' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->id_spec
							],
				'curriculum' => [
								'required' => 1,
								'insert' => 1,
								'update' => 0,
								'value' => $this->curriculum
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
     * Application places grid.
     *
     * @return array
     */
	public function grid()
	{
		return [
				'spec' => [
							'name' => 'Направление подготовки',
							'type' => 'string'
							]
				];
	}

	/**
     * Gets application places for GRID.
     *
     * @return array
     */
	public function getGrid()
	{
		return $this->rowSelectAll("application_places.id, concat(dict_speciality.speciality_name, ' ', ifnull(dict_speciality.profil_name, ''), ' ', dict_speciality.finance_name, ' ', dict_speciality.eduform_name) as spec",
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets specialities by application.
     *
     * @return array
     */
	public function getSpecsByApp()
	{
		return $this->rowSelectAll('*',
									self::TABLE_NAME,
									'pid = :pid',
									[':pid' => $this->pid]);
	}

	/**
     * Gets special specialities based on 9 classes for application.
     *
     * @return array
     */
	public function getSpecsSpecial9ForApp($pay)
	{
		$conds = $this->CondsSpecial9Educ($pay);
		$params = $this->ParamsSpecial9Educ($pay);
		return $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_name, eduform_name, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':eduprogram_name' => 'среднее (основное,общее)',
									':stage_numb' => 1],
									'speciality_name, profil_name');
	}

	/**
     * Gets special specialities based on 9 classes UNIQUE for application.
     *
     * @return array
     */
	public function getSpecialitySpecial9ForApp($pay)
	{
		$conds = $this->CondsSpecial9Educ($pay);
		$params = $this->ParamsSpecial9Educ($pay);
		return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':eduprogram_name' => 'среднее (основное,общее)',
									':stage_numb' => 1],
									'speciality_name');
	}

	/**
     * Gets special finances based on 9 classes UNIQUE for application.
     *
     * @return array
     */
	public function getFinanceSpecial9ForApp($pay)
	{
		$conds = $this->CondsSpecial9Educ($pay);
		$params = $this->ParamsSpecial9Educ($pay);
		return $this->rowSelectAll('DISTINCT finance_code, finance_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':eduprogram_name' => 'среднее (основное,общее)',
									':stage_numb' => 1],
									'speciality_name');
	}

	/**
     * Gets special eduforms based on 9 classes UNIQUE for application.
     *
     * @return array
     */
	public function getEduformSpecial9ForApp($pay)
	{
		$conds = $this->CondsSpecial9Educ($pay);
		$params = $this->ParamsSpecial9Educ($pay);
		return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':eduprogram_name' => 'среднее (основное,общее)',
									':stage_numb' => 1],
									'speciality_name');
	}

	/**
     * Gets special edulevels based on 9 classes UNIQUE for application.
     *
     * @return array
     */
	public function getEdulevelSpecial9ForApp($pay)
	{
		$conds = $this->CondsSpecial9Educ($pay);
		$params = $this->ParamsSpecial9Educ($pay);
		return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'speciality_name');
	}

	/**
     * Gets first high specialities for application.
     *
     * @return array
     */
	public function getSpecsFirstForApp($pay)
	{
		$conds = $this->CondsHighEducFirst($pay);
		$params = $this->ParamsHighEducFirst($pay);
		return $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_name, eduform_name, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name = :finance_name AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':stage_numb' => 1],
									'speciality_name, profil_name');
	}

	/**
     * Gets first high specialities UNIQUE for application.
     *
     * @return array
     */
	public function getSpecialityFirstForApp($pay)
	{
		$conds = $this->CondsHighEducFirst($pay);
		$params = $this->ParamsHighEducFirst($pay);
		return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':stage_numb' => 1],
									'speciality_name');
	}

	/**
     * Gets first high finances UNIQUE for application.
     *
     * @return array
     */
	public function getFinanceFirstForApp($pay)
	{
		$conds = $this->CondsHighEducFirst($pay);
		$params = $this->ParamsHighEducFirst($pay);
		return $this->rowSelectAll('DISTINCT finance_code, finance_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':stage_numb' => 1],
									'finance_name');
	}

	/**
     * Gets first high eduforms UNIQUE for application.
     *
     * @return array
     */
	public function getEduformFirstForApp($pay)
	{
		$conds = $this->CondsHighEducFirst($pay);
		$params = $this->ParamsHighEducFirst($pay);
		return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null)',
									[':pid' => $this->pid,
									':group_beneficiary' => 0,
									':finance_name' => $finance_name,
									':stage_numb' => 1],
									'eduform_name');
	}

	/**
     * Gets first high edulevels UNIQUE for application.
     *
     * @return array
     */
	public function getEdulevelFirstForApp($pay)
	{
		$conds = $this->CondsHighEducFirst($pay);
		$params = $this->ParamsHighEducFirst($pay);
		return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'edulevel_name');
	}

	/**
     * Gets second high specialities for application.
     *
     * @return array
     */
	public function getSpecsSecondForApp($pay)
	{
		$conds = $this->CondsHighEducSecond($pay);
		$params = $this->ParamsHighEducSecond($pay);
		return $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_name, eduform_name, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'speciality_name, profil_name');
	}

	/**
     * Gets second high specialities UNIQUE for application.
     *
     * @return array
     */
	public function getSpecialitySecondForApp($pay)
	{
		$conds = $this->CondsHighEducSecond($pay);
		$params = $this->ParamsHighEducSecond($pay);
		return $this->rowSelectAll('DISTINCT speciality_code, speciality_name, profil_code, profil_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'speciality_name');
	}

	/**
     * Gets second high finances UNIQUE for application.
     *
     * @return array
     */
	public function getFinanceSecondForApp($pay)
	{
		$conds = $this->CondsHighEducSecond($pay);
		$params = $this->ParamsHighEducSecond($pay);
		return $this->rowSelectAll('DISTINCT finance_code, finance_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'finance_name');
	}

	/**
     * Gets second high eduforms UNIQUE for application.
     *
     * @return array
     */
	public function getEduformSecondForApp($pay)
	{
		$conds = $this->CondsHighEducSecond($pay);
		$params = $this->ParamsHighEducSecond($pay);
		return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'eduform_name');
	}

	/**
     * Gets second high edulevels UNIQUE for application.
     *
     * @return array
     */
	public function getEdulevelSecondForApp($pay)
	{
		$conds = $this->CondsHighEducSecond($pay);
		$params = $this->ParamsHighEducSecond($pay);
		return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									$conds,
									$params,
									'edulevel_name');
	}

	/**
     * Gets specialities for bachelor and specialist.
     *
     * @return array
     */
	public function getByAppForBachelorSpec()
	{
		return $this->rowSelectAll('application_places.*',
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid AND edulevel_name in (:edulevel_name1, :edulevel_name2)',
									[':pid' => $this->pid,
									':edulevel_name1' => 'Бакалавр',
									':edulevel_name2' => 'Специалист']);
	}

	/**
     * Gets specialities for medical certificate (A1 group).
     *
     * @return array
     */
	public function getByAppForMedicalA1()
	{
		return $this->rowSelectAll('application_places.*',
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid AND speciality_name like :speciality_name AND profil_name = :profil_name',
									[':pid' => $this->pid,
									'speciality_name' => '44.03.01 Педагогическое образование%',
									':profil_name' => 'Физическая культура']);
	}

	/**
     * Gets specialities for medical certificate (A2 group).
     *
     * @return array
     */
	public function getByAppForMedicalA2()
	{
		return $this->rowSelectAll('application_places.*',
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid AND speciality_name in (:speciality_name1, :speciality_name2, :speciality_name3, :speciality_name4, :speciality_name5, :speciality_name6, :speciality_name7)',
									[':pid' => $this->pid,
									':speciality_name1' => '49.03.01 Физическая культура',
									':speciality_name2' => '38.05.02 Таможенное дело',
									':speciality_name3' => '31.05.01 Лечебное дело',
									':speciality_name4' => '31.05.02 Педиатрия',
									':speciality_name5' => '31.05.03 Стоматология',
									':speciality_name6' => '33.05.01 Фармация',
									':speciality_name7' => '32.05.01 Медико-профилактическое дело']);
	}

	/**
     * Gets specialities for medical certificate (B1 group).
     *
     * @return array
     */
	public function getByAppForMedicalB1()
	{
		return $this->rowSelectAll('application_places.*',
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid AND eduform_name = :eduform_name AND speciality_name in (:speciality_name1, :speciality_name2, :speciality_name3, :speciality_name4, :speciality_name5, :speciality_name6, :speciality_name7)',
									[':pid' => $this->pid,
									':eduform_name' => 'Очная',
									':speciality_name1' => '21.05.02 Прикладная геология',
									':speciality_name2' => '21.05.04 Горное дело и направлениям подготовки',
									':speciality_name3' => '44.03.01 Педагогическое образование',
									':speciality_name4' => '44.03.05 Педагогическое образование',
									':speciality_name5' => '44.03.02 Психолого-педагогическое образование',
									':speciality_name6' => '44.03.03 Специальное (дефектологическое) образование',
									':speciality_name7' => '19.03.04 Технология продукции и организация общественного питания']);
	}

	/**
     * Gets specialities for medical certificate (C1 group).
     *
     * @return array
     */
	public function getByAppForMedicalC1()
	{
		return $this->rowSelectAll('application_places.*',
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid AND edulevel_name = :edulevel_name AND speciality_name in (:speciality_name1, :speciality_name2, :speciality_name3, :speciality_name4, :speciality_name5, :speciality_name6, :speciality_name7, :speciality_name8, :speciality_name9, :speciality_name10, :speciality_name11)',
									[':pid' => $this->pid,
									':edulevel_name' => 'СПО',
									':speciality_name1' => '31.02.01 Лечебное дело',
									':speciality_name2' => '31.02.02 Акушерское дело',
									':speciality_name3' => '31.02.03 Лабораторная диагностика',
									':speciality_name4' => '31.02.05 Стоматология ортопедическая',
									':speciality_name5' => '31.02.06 Стоматология профилактическая',
									':speciality_name6' => '32.02.02 Медико-профилактическое дело',
									':speciality_name7' => '33.02.01 Фармация',
									':speciality_name8' => '34.02.01 Сестринское дело',
									':speciality_name9' => '34.02.02 Медицинский массаж (для обучения лиц с ограниченными возможностями здоровья по зрению)',
									':speciality_name10' => '44.02.01 Дошкольное образование',
									':speciality_name11' => '44.02.02 Преподавание в начальных классах']);
	}

	/**
     * Gets specialities for payed online education.
     *
     * @return array
     */
	public function getByAppForPayedOnline()
	{
		return $this->rowSelectAll('application_places.*',
									'application_places INNER JOIN dict_speciality ON application_places.id_spec = dict_speciality.id',
									'pid = :pid AND finance_name = :finance_name AND eduform_name = :eduform_name',
									[':pid' => $this->pid,
									':finance_name' => 'Полное возмещение затрат',
									':eduform_name' => 'Заочная']);
	}

	/**
     * Saves application places data to database.
     *
     * @return integer
     */
	public function save()
	{
		$this->id_user = $_SESSION[APP_CODE]['user_id'];
		$this->dt_created = date('Y-m-d H:i:s');
		$prepare = $this->prepareInsert(self::TABLE_NAME, $this->rules());
		return $this->rowInsert($prepare['fields'], self::TABLE_NAME, $prepare['conds'], $prepare['params']);
	}

	/**
     * Changes all application places data.
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
     * Removes application places by application.
     *
     * @return integer
     */
	public function clearByApplication()
	{
		return $this->rowDelete(self::TABLE_NAME,
								'pid = :pid',
								[':pid' => $this->pid]);
	}

	/**
     * Returns conditions for special education based on 9 classes.
     *
     * @return string
     */
	public function CondsSpecial9Educ($pay) : string
	{
		if ($pay == 1) {
			return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name = :finance_name AND eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)';
		} else {
			return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)';
		}
	}

	/**
     * Returns conditions for the first high education.
     *
     * @return string
     */
	public function CondsHighEducFirst($pay) : string
	{
		if ($pay == 1) {
			return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name = :finance_name AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null)';
		} else {
			return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND dict_speciality.eduprogram_name is null AND (stage_numb = :stage_numb OR stage_numb is null)';
		}
	}

	/**
     * Returns conditions for the second high education.
     *
     * @return string
     */
	public function CondsHighEducSecond($pay) : string
	{
		if ($pay == 1) {
			return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name = :finance_name AND dict_speciality.eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)';
		} else {
			return 'application.id = :pid AND group_beneficiary = :group_beneficiary AND finance_name <> :finance_name AND dict_speciality.eduprogram_name = :eduprogram_name AND (stage_numb = :stage_numb OR stage_numb is null)';
		}
	}

	/**
     * Returns parameters for special education based on 9 classes.
     *
     * @return array
     */
	public function ParamsSpecial9Educ($pay) : array
	{
		if ($pay == 1) {
			return [':pid' => $this->pid,
					':group_beneficiary' => 0,
					':finance_name' => self::PAY,
					':eduprogram_name' => 'среднее (основное,общее)',
					':stage_numb' => 1];
		} else {
			return [':pid' => $this->pid,
					':group_beneficiary' => 0,
					':finance_name' => self::PURPOSE,
					':eduprogram_name' => 'среднее (основное,общее)',
					':stage_numb' => 1];
		}
	}

	/**
     * Returns parameters for the first high education.
     *
     * @return array
     */
	public function ParamsHighEducFirst($pay) : array
	{
		if ($pay == 1) {
			return [':pid' => $this->pid,
					':group_beneficiary' => 0,
					':finance_name' => self::PAY,
					':stage_numb' => 1];
		} else {
			return [':pid' => $this->pid,
					':group_beneficiary' => 0,
					':finance_name' => self::PURPOSE,
					':stage_numb' => 1];
		}
	}

	/**
     * Returns parameters for the second high education.
     *
     * @return array
     */
	public function ParamsHighEducSecond($pay) : array
	{
		if ($pay == 1) {
			return [':pid' => $this->pid,
					':group_beneficiary' => 0,
					':finance_name' => self::PAY,
					':eduprogram_name' => 'Высшее',
					':stage_numb' => 1];
		} else {
			return [':pid' => $this->pid,
					':group_beneficiary' => 0,
					':finance_name' => self::PURPOSE,
					':eduprogram_name' => 'Высшее',
					':stage_numb' => 1];
		}
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
