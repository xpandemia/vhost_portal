<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_ApplicationPlaces extends Db_Helper
{
	/*
		Application places processing
	*/

	const TABLE_NAME = 'application_places';

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
								'required' => 0,
								'insert' => 1,
								'update' => 1,
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
		return ['spec' => [
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
     * Gets first high specialities for application.
     *
     * @return array
     */
	public function getSpecsFirstForApp()
	{
		return $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_name, eduform_name, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name is null',
									[':pid' => $this->pid]);
	}

	/**
     * Gets first high specialities UNIQUE for application.
     *
     * @return array
     */
	public function getSpecialityFirstForApp()
	{
		return $this->rowSelectAll('DISTINCT speciality_code, speciality_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name is null',
									[':pid' => $this->pid]);
	}

	/**
     * Gets first high finances UNIQUE for application.
     *
     * @return array
     */
	public function getFinanceFirstForApp()
	{
		return $this->rowSelectAll('DISTINCT finance_code, finance_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name is null',
									[':pid' => $this->pid]);
	}

	/**
     * Gets first high eduforms UNIQUE for application.
     *
     * @return array
     */
	public function getEduformFirstForApp()
	{
		return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name is null',
									[':pid' => $this->pid]);
	}

	/**
     * Gets first high edulevels UNIQUE for application.
     *
     * @return array
     */
	public function getEdulevelFirstForApp()
	{
		return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name is null',
									[':pid' => $this->pid]);
	}

	/**
     * Gets second high specialities for application.
     *
     * @return array
     */
	public function getSpecsSecondForApp()
	{
		return $this->rowSelectAll('dict_speciality.id, speciality_name, profil_name, finance_name, eduform_name, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name = :eduprogram_name',
									[':pid' => $this->pid,
									':eduprogram_name' => 'Высшее']);
	}

	/**
     * Gets second high specialities UNIQUE for application.
     *
     * @return array
     */
	public function getSpecialitySecondForApp()
	{
		return $this->rowSelectAll('DISTINCT speciality_code, speciality_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name = :eduprogram_name',
									[':pid' => $this->pid,
									':eduprogram_name' => 'Высшее']);
	}

	/**
     * Gets second high finances UNIQUE for application.
     *
     * @return array
     */
	public function getFinanceSecondForApp()
	{
		return $this->rowSelectAll('DISTINCT finance_code, finance_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name = :eduprogram_name',
									[':pid' => $this->pid,
									':eduprogram_name' => 'Высшее']);
	}

	/**
     * Gets second high eduforms UNIQUE for application.
     *
     * @return array
     */
	public function getEduformSecondForApp()
	{
		return $this->rowSelectAll('DISTINCT eduform_code, eduform_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name = :eduprogram_name',
									[':pid' => $this->pid,
									':eduprogram_name' => 'Высшее']);
	}

	/**
     * Gets second high edulevels UNIQUE for application.
     *
     * @return array
     */
	public function getEdulevelSecondForApp()
	{
		return $this->rowSelectAll('DISTINCT edulevel_code, edulevel_name',
									'dict_speciality INNER JOIN admission_campaign ON dict_speciality.campaign_code = admission_campaign.code'.
									' INNER JOIN application ON admission_campaign.id = application.id_campaign',
									'application.id = :pid AND dict_speciality.eduprogram_name = :eduprogram_name',
									[':pid' => $this->pid,
									':eduprogram_name' => 'Высшее']);
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

	public function __destruct()
	{
		$this->db = null;
	}
}
