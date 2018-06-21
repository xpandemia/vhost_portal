<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('ADRREG', array(
					'name' => 'Адрес регистрации',
					'plc' => 'Индекс, Область, Район, Город или Населённый пункт, Улица, Дом, Корпус, Квартира',
					'help' => 'Адрес регистрации должно содержать <strong>'.MSG_INFO_RUS.'</strong>, и быть не более <strong>255</strong> символов длиной.'));
define('ADRRES', array(
					'name' => 'Адрес проживания',
					'plc' => 'Индекс, Область, Район, Город или Населённый пункт, Улица, Дом, Корпус, Квартира',
					'help' => 'Адрес проживания должно содержать <strong>'.MSG_INFO_RUS.'</strong>, и быть не более <strong>255</strong> символов длиной.'));

class Model_Address extends Db_Helper
{
	/*
		Address processing
	*/

	const TABLE_NAME = 'address';

	const TYPE_REG = 0;
	const TYPE_RES = 1;

	public $id;
	public $id_user;
	public $id_resume;
	public $id_country;
	public $type;
	public $kladr;
	public $region_code;
	public $region;
	public $area_code;
	public $area;
	public $city_code;
	public $city;
	public $location_code;
	public $location;
	public $street_code;
	public $street;
	public $house;
	public $building;
	public $flat;
	public $postcode;
	public $adr;
	public $dt_created;
	public $dt_updated;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Address rules.
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
				'id_country' => [
								'required' => 1,
								'insert' => 1,
								'update' => 1,
								'value' => $this->id_country
								],
				'type' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->type
							],
				'kladr' => [
							'required' => 1,
							'insert' => 1,
							'update' => 0,
							'value' => $this->kladr
							],
				'region_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->region_code
								],
				'region' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->region
							],
				'area_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->area_code
								],
				'area' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->area
							],
				'city_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->city_code
								],
				'city' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->city
							],
				'location_code' => [
									'required' => 0,
									'insert' => 1,
									'update' => 1,
									'value' => $this->location_code
									],
				'location' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->location
								],
				'street_code' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->street_code
								],
				'street' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->street
							],
				'house' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->house
							],
				'building' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->building
								],
				'flat' => [
							'required' => 0,
							'insert' => 1,
							'update' => 1,
							'value' => $this->flat
							],
				'postcode' => [
								'required' => 0,
								'insert' => 1,
								'update' => 1,
								'value' => $this->postcode
								],
				'adr' => [
						'required' => 1,
						'insert' => 1,
						'update' => 1,
						'value' => $this->adr
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
								]
				];
	}

	/**
     * Gets address by resume/type.
     *
     * @return array
     */
	public function getByResumeType()
	{
		return $this->rowSelectOne('*',
								self::TABLE_NAME,
								'id_resume = :id_resume AND type = :type',
								[':id_resume' => $this->id_resume, ':type' => $this->type]);
	}

	/**
     * Saves address data to database.
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
     * Changes all address data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		$this->dt_updated = date('Y-m-d H:i:s');
		$prepare = $this->prepareUpdate(self::TABLE_NAME, $this->rules());
		return $this->rowUpdate(self::TABLE_NAME, $prepare['fields'], $prepare['params'], ['id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
