<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

define('ADRREG', array(
					'name' => 'Адрес регистрации',
					'plc' => 'Индекс, Область, Район, Город или Населённый пункт, Улица, Дом, Корпус, Квартира',
					'help' => 'Адрес регистрации должно содержать <b>только цифры, русские буквы, тире, точки, запятые или пробелы</b>, и быть не более <b>255</b> символов длиной.'));
define('ADRRES', array(
					'name' => 'Адрес проживания',
					'plc' => 'Индекс, Область, Район, Город или Населённый пункт, Улица, Дом, Корпус, Квартира',
					'help' => 'Адрес проживания должно содержать <b>только цифры, русские буквы, тире, точки, запятые или пробелы</b>, и быть не более <b>255</b> символов длиной.'));

class Model_Address extends Db_Helper
{
	/*
		Address processing
	*/

	const TABLE_NAME = 'address';

	const TYPE_REG = 0;
	const TYPE_RES = 1;

	public $id;
	public $id_resume;
	public $id_country;
	public $type;
	public $kladr;
	public $region;
	public $area;
	public $city;
	public $location;
	public $street;
	public $house;
	public $building;
	public $flat;
	public $postcode;
	public $adr;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
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
     * @return boolean
     */
	public function save()
	{
		return $this->rowInsert('id_resume, id_country, type, kladr, region, area, city, location, street, house, building, flat, postcode, adr, dt_created',
								self::TABLE_NAME,
								':id_resume, :id_country, :type, :kladr, :region, :area, :city, :location, :street, :house, :building, :flat, :postcode, :adr, :dt_created',
								[':id_resume' => $this->id_resume,
								':id_country' => $this->id_country,
								':type' => $this->type,
								':kladr' => $this->kladr,
								':region' => $this->region,
								':area' => $this->area,
								':city' => $this->city,
								':location' => $this->location,
								':street' => $this->street,
								':house' => $this->house,
								':building' => $this->building,
								':flat' => $this->flat,
								':postcode' => $this->postcode,
								':adr' => $this->adr,
								':dt_created' => $this->dt_created]);
	}

	/**
     * Changes all address data.
     *
     * @return boolean
     */
	public function changeAll()
	{
		return $this->rowUpdate(self::TABLE_NAME,
								'id_resume = :id_resume, id_country = :id_country, type = :type, region = :region, area = :area, city = :city, location = :location, street = :street, house = :house, building = :building, flat = :flat, postcode = :postcode, adr = :adr, dt_updated = :dt_updated',
								[':id_resume' => $this->id_resume,
								':id_country' => $this->id_country,
								':type' => $this->type,
								':region' => $this->region,
								':area' => $this->area,
								':city' => $this->city,
								':location' => $this->location,
								':street' => $this->street,
								':house' => $this->house,
								':building' => $this->building,
								':flat' => $this->flat,
								':postcode' => $this->postcode,
								':adr' => $this->adr,
								':dt_updated' => date('Y-m-d H:i:s')],
								['id' => $this->id]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
