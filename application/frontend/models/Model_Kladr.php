<?php

namespace frontend\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_Kladr as Kladr;

class Model_Kladr extends Db_Helper
{
	/*
		KLADR processing
	*/

	const REGION = 1;
	const AREA = 2;
	const CITY = 3;
	const LOCATION = 4;
	const STREET = 5;

	public $db;
	public $kladr;

	public function __construct()
	{
		$this->db = Db_Helper::getInstance();
		$this->kladr = new Kladr();
	}

	/**
     * Gets kladrs by level JSON.
     *
     * @return JSON
     */
	public function getByLevelJSON($level) : string
	{
		if (empty($level)) {
			$err[] = ['action' => 'getByLevelJSON', 'error' => 'Input variables errors!'];
			return json_encode($err);
		} else {
			$kladr = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'kladr.level = :level AND relevance = :relevance',
											[':level' => $level,
											':relevance' => '0'],
											'kladr_name');
		}
			foreach ($kladr as $value) {
				$kladr_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
			}
			return json_encode($kladr_json);
	}

	/**
     * Gets areas by region JSON.
     *
     * @return JSON
     */
	public function getAreaByRegionJSON($region) : string
	{
		if (!empty($region)) {
			// get kladr
			$kladr = $this->kladr->getByCode($region);
			$code_region = $kladr['code_region'];
			// get areas by region
			$area = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND relevance = :relevance',
											[':level' => self::AREA,
											':code_region' => $code_region,
											':relevance' => '0'],
											'kladr_name');
			foreach ($area as $value) {
				$area_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($area_json)) {
				return json_encode($area_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets cities by region JSON.
     *
     * @return JSON
     */
	public function getCityByRegionJSON($region) : string
	{
		if (!empty($region)) {
			// get kladr
			$kladr = $this->kladr->getByCode($region);
			$code_region = $kladr['code_region'];
			// get cities by region
			$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND relevance = :relevance',
											[':level' => self::CITY,
											':code_region' => $code_region,
											':code_area' => '0',
											':relevance' => '0'],
											'kladr_name');
			foreach ($city as $value) {
				$city_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($city_json)) {
				return json_encode($city_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets cities by area JSON.
     *
     * @return JSON
     */
	public function getCityByAreaJSON($area) : string
	{
		if (!empty($area)) {
			// get kladr
			$kladr = $this->kladr->getByCode($area);
			$code_region = $kladr['code_region'];
			$code_area = $kladr['code_area'];
			// get cities by area
			$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND relevance = :relevance',
											[':level' => self::CITY,
											':code_region' => $code_region,
											':code_area' => $code_area,
											':relevance' => '0'],
											'kladr_name');
			foreach ($city as $value) {
				$city_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($city_json)) {
				return json_encode($city_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets locations by region JSON.
     *
     * @return JSON
     */
	public function getLocationByRegionJSON($region) : string
	{
		if (!empty($region)) {
			// get kladr
			$kladr = $this->kladr->getByCode($region);
			$code_region = $kladr['code_region'];
			// get cities by region
			$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city AND relevance = :relevance',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => '0',
											':code_city' => '0',
											':relevance' => '0'],
											'kladr_name');
			foreach ($city as $value) {
				$city_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($city_json)) {
				return json_encode($city_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets locations by city JSON.
     *
     * @return JSON
     */
	public function getLocationByCityJSON($city) : string
	{
		if (!empty($city)) {
			// get kladr
			$kladr = $this->kladr->getByCode($city);
			$code_region = $kladr['code_region'];
			$code_area = $kladr['code_area'];
			$code_city = $kladr['code_city'];
			// get locations by area
			$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
												'kladr',
												'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city AND relevance = :relevance',
												[':level' => self::LOCATION,
												':code_region' => $code_region,
												':code_area' => $code_area,
												':code_city' => $code_city,
												':relevance' => '0'],
												'kladr_name');
			foreach ($location as $value) {
				$location_json[] = ['kladr_code' => $value['kladr_code'],
									'kladr_name' => $value['kladr_name'],
									'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($location_json)) {
				return json_encode($location_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets locations by area JSON.
     *
     * @return JSON
     */
	public function getLocationByAreaJSON($area) : string
	{
		if (!empty($area)) {
			// get kladr
			$kladr = $this->kladr->getByCode($area);
			$code_region = $kladr['code_region'];
			$code_area = $kladr['code_area'];
			// get locations by area
			$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
												'kladr',
												'level = :level AND code_region = :code_region AND code_area = :code_area AND relevance = :relevance',
												[':level' => self::LOCATION,
												':code_region' => $code_region,
												':code_area' => $code_area,
												':relevance' => '0'],
												'kladr_name');
			foreach ($location as $value) {
				$location_json[] = ['kladr_code' => $value['kladr_code'],
									'kladr_name' => $value['kladr_name'],
									'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($location_json)) {
				return json_encode($location_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets streets by region JSON.
     *
     * @return JSON
     */
	public function getStreetByRegionJSON($region) : string
	{
		if (!empty($region)) {
			// get kladr
			$kladr = $this->kladr->getByCode($region);
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
											'kladr_name');
			foreach ($street as $value) {
				$street_json[] = ['kladr_code' => $value['kladr_code'],
									'kladr_name' => $value['kladr_name'],
									'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($street_json)) {
				return json_encode($street_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets streets by city JSON.
     *
     * @return JSON
     */
	public function getStreetByCityJSON($city) : string
	{
		if (!empty($city)) {
			// get kladr
			$kladr = $this->kladr->getByCode($city);
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
											'kladr_name');
			foreach ($street as $value) {
				$street_json[] = ['kladr_code' => $value['kladr_code'],
									'kladr_name' => $value['kladr_name'],
									'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($street_json)) {
				return json_encode($street_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets streets by location JSON.
     *
     * @return JSON
     */
	public function getStreetByLocationJSON($location) : string
	{
		if (!empty($location)) {
			// get kladr
			$kladr = $this->kladr->getByCode($location);
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
											'kladr_name');
			foreach ($street as $value) {
				$street_json[] = ['kladr_code' => $value['kladr_code'],
									'kladr_name' => $value['kladr_name'],
									'kladr_abbr' => $value['kladr_abbr']];
			}
			if (!empty($street_json)) {
				return json_encode($street_json);
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}

	/**
     * Gets postcode by code JSON.
     *
     * @return JSON
     */
	public function getPostcodeByCodeJSON($code) : string
	{
		if (!empty($code)) {
			// get kladr
			$kladr = $this->kladr->getByCode($code);
			if ($kladr && !empty($kladr['postcode'])) {
				return json_encode(trim($kladr['postcode']));
			} else {
				return json_encode(null);
			}
		} else {
			return json_encode(null);
		}
	}
}
