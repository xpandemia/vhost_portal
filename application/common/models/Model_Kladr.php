<?php

namespace common\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;

class Model_Kladr extends Db_Helper
{
	/*
		KLADR data processing
	*/

	const REGION = 1;
	const AREA = 2;
	const CITY = 3;
	const LOCATION = 4;
	const STREET = 5;

	public $region;
	public $area;
	public $city;
	public $location;

	public $db;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
	}

	/**
     * Gets kladr by code.
     *
     * @return array
     */
	public function getByCode($code) : array
	{
		$kladr = $this->db->rowSelectOne('*',
										'kladr',
										'kladr_code = :kladr_code',
										[':kladr_code' => $code]);
		return $kladr;
	}

	/**
     * Gets all regions.
     *
     * @return array
     */
	public function getRegionAll() : array
	{
		$region = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level',
										[':level' => self::REGION]);
		return $region;
	}

	/**
     * Gets areas by region.
     *
     * @return array
     */
	public function getAreaByRegion() : array
	{
		// get kladr
		$kladr = $this->getByCode($this->region);
		$code_region = $kladr['code_region'];
		// get area by region
		$area = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region',
										[':level' => self::AREA, ':code_region' => $code_region]);
		return $area;
	}

	/**
     * Gets cities by region.
     *
     * @return array
     */
	public function getCityByRegion() : array
	{
		// get kladr
		$kladr = $this->getByCode($this->region);
		$code_region = $kladr['code_region'];
		// get city by region
		$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region',
										[':level' => self::CITY, ':code_region' => $code_region]);
		return $city;
	}

	/**
     * Gets locations by area.
     *
     * @return array
     */
	public function getLocationByArea() : array
	{
		// get kladr
		$kladr = $this->getByCode($this->area);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		// get location by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => $code_area]);
		return $location;
	}

	/**
     * Gets locations by city.
     *
     * @return array
     */
	public function getLocationByCity() : array
	{
		// get kladr
		$kladr = $this->getByCode($this->city);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		$code_city = $kladr['code_city'];
		// get location by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => $code_area,
											':code_city' => $code_city]);
		return $location;
	}

	/**
     * Gets streets by city.
     *
     * @return array
     */
	public function getStreetByCity() : array
	{
		// get kladr
		$kladr = $this->getByCode($this->city);
		$code_region = $kladr['code_region'];
		$code_city = $kladr['code_city'];
		// get streets by city
		$street = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_city = :code_city',
										[':level' => self::STREET,
										':code_region' => $code_region,
										':code_city' => $code_city]);
		return $street;
	}

	/**
     * Gets streets by location.
     *
     * @return array
     */
	public function getStreetByLocation() : array
	{
		// get kladr
		$kladr = $this->getByCode($this->location);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		$code_location = $kladr['code_location'];
		// get streets by location
		$street = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_area = :code_area AND code_location = :code_location',
										[':level' => self::STREET,
										':code_region' => $code_region,
										':code_area' => $code_area,
										':code_location' => $code_location]);
		return $street;
	}
}
