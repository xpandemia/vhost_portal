<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_DictCountries extends Db_Helper
{
	/*
		Dictionary countries processing
	*/

	public $country_name;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets all countries.
     *
     * @return array
     */
	public function getCountryAll()
	{
		return $this->rowSelect('*', 'dict_countries', '', '');
	}

	/**
     * Gets country by name.
     *
     * @return array
     */
	public function getCountryByName()
	{
		return $this->rowSelect('*', 'dict_countries', 'country_name = :country_name', [':country_name' => $this->country_name]);
	}

	/**
     * Gets country name by name.
     *
     * @return array
     */
	public function getCountryNameByName()
	{
		return $this->rowSelect('country_name', 'dict_countries', 'country_name = :country_name', [':country_name' => $this->country_name]);
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
