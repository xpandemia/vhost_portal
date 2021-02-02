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
     * @return array | bool
     */
	public function getByCode($code)
	{
		return $this->db->rowSelectOne('*',
										'kladr',
										'kladr_code = :kladr_code',
										[':kladr_code' => $code]);
	}

	/**
     * Gets all regions.
     *
     * @return array
     */
	public function getRegionAll()
	{
		$region = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND relevance = :relevance',
										[':level' => self::REGION,
										':relevance' => '0'],
										'kladr_name ASC');
		return $region;
	}

	/**
     * Gets areas by region.
     *
     * @return array
     */
	public function getAreaByRegion()
	{
		// get kladr
		$kladr = $this->getByCode($this->region);
		$code_region = $kladr['code_region'];
		// get area by region
		$area = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND relevance = :relevance',
										[':level' => self::AREA,
										':code_region' => $code_region,
										':relevance' => '0'],
										'kladr_name ASC');
		return $area;
	}

	/**
     * Gets cities by region.
     *
     * @return array
     */
	public function getCityByRegion()
	{
		// get kladr
		$kladr = $this->getByCode($this->region);
		$code_region = $kladr['code_region'];
		// get city by region
		$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_area = :code_area AND relevance = :relevance',
										[':level' => self::CITY,
										':code_region' => $code_region,
										':code_area' => '0',
										':relevance' => '0'],
										'kladr_name ASC');
		return $city;
	}

	/**
     * Gets cities by area.
     *
     * @return array
     */
	public function getCityByArea()
	{
		// get kladr
		$kladr = $this->getByCode($this->area);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		// get city by area
		$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_area = :code_area AND relevance = :relevance',
										[':level' => self::CITY,
										':code_region' => $code_region,
										':code_area' => $code_area,
										':relevance' => '0'],
										'kladr_name ASC');
		return $city;
	}

	/**
     * Gets locations by region.
     *
     * @return array
     */
	public function getLocationByRegion()
	{
		// get kladr
		$kladr = $this->getByCode($this->region);
		$code_region = $kladr['code_region'];
		// get location by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city AND relevance = :relevance',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => '0',
											':code_city' => '0',
											':relevance' => '0'],
											'kladr_name ASC');
		return $location;
	}

	/**
     * Gets locations by area.
     *
     * @return array
     */
	public function getLocationByArea()
	{
		// get kladr
		$kladr = $this->getByCode($this->area);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		// get location by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND relevance = :relevance',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => $code_area,
											':relevance' => '0'],
											'kladr_name ASC');
		return $location;
	}

	/**
     * Gets locations by city.
     *
     * @return array
     */
	public function getLocationByCity()
	{
		// get kladr
		$kladr = $this->getByCode($this->city);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		$code_city = $kladr['code_city'];
		// get location by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city AND relevance = :relevance',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => $code_area,
											':code_city' => $code_city,
											':relevance' => '0'],
											'kladr_name ASC');
		return $location;
	}

	/**
     * Gets streets by region.
     *
     * @return array
     */
	public function getStreetByRegion()
	{
		// get kladr
		$kladr = $this->getByCode($this->region);
		$code_region = $kladr['code_region'];
		// get streets by city
		$street = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city AND code_location = :code_location AND relevance = :relevance',
										[':level' => self::STREET,
										':code_region' => $code_region,
										':code_area' => '0',
										':code_city' => '0',
										':code_location' => '0',
										':relevance' => '0'],
										'kladr_name ASC');
		return $street;
	}

	/**
     * Gets streets by city.
     *
     * @return array
     */
	public function getStreetByCity()
	{
		// get kladr
		$kladr = $this->getByCode($this->city);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		$code_city = $kladr['code_city'];
		// get streets by city
		$street = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city AND code_location = :code_location AND relevance = :relevance',
										[':level' => self::STREET,
										':code_region' => $code_region,
										':code_area' => $code_area,
										':code_city' => $code_city,
										':code_location' => '0',
										':relevance' => '0'],
										'kladr_name ASC');
		return $street;
	}

	/**
     * Gets streets by location.
     *
     * @return array
     */
	public function getStreetByLocation()
	{
		// get kladr
		$kladr = $this->getByCode($this->location);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		$code_location = $kladr['code_location'];
		// get streets by location
		$street = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_area = :code_area AND code_location = :code_location AND relevance = :relevance',
										[':level' => self::STREET,
										':code_region' => $code_region,
										':code_area' => $code_area,
										':code_location' => $code_location,
										':relevance' => '0'],
										'kladr_name ASC');
		return $street;
	}
}
