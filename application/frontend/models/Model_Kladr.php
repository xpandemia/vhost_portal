<?php

namespace frontend\models;

use tinyframe\core\helpers\Db_Helper as Db_Helper;
use common\models\Model_Kladr as Model_Kladr_Data;

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
		$this->kladr = new Model_Kladr_Data();
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
											'kladr.level = :level',
											[':level' => $level]);
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
		// get kladr
		$kladr = $this->kladr->getByCode($region);
		$code_region = $kladr['code_region'];
		// get areas by region
		$area = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region',
										[':level' => self::AREA, ':code_region' => $code_region]);
		foreach ($area as $value) {
			$area_json[] = ['kladr_code' => $value['kladr_code'],
							'kladr_name' => $value['kladr_name'],
							'kladr_abbr' => $value['kladr_abbr']];
		}
		return json_encode($area_json);
	}

	/**
     * Gets cities by region JSON.
     *
     * @return JSON
     */
	public function getCityByRegionJSON($region) : string
	{
		// get kladr
		$kladr = $this->kladr->getByCode($region);
		$code_region = $kladr['code_region'];
		// get cities by region
		$city = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region',
										[':level' => self::CITY, ':code_region' => $code_region]);
		foreach ($city as $value) {
			$city_json[] = ['kladr_code' => $value['kladr_code'],
							'kladr_name' => $value['kladr_name'],
							'kladr_abbr' => $value['kladr_abbr']];
		}
		return json_encode($city_json);
	}

	/**
     * Gets locations by city JSON.
     *
     * @return JSON
     */
	public function getLocationByCityJSON($city) : string
	{
		// get kladr
		$kladr = $this->kladr->getByCode($city);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		$code_city = $kladr['code_city'];
		// get locations by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area AND code_city = :code_city',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => $code_area,
											':code_city' => $code_city]);
		foreach ($location as $value) {
			$location_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
		}
		return json_encode($location_json);
	}

	/**
     * Gets locations by area JSON.
     *
     * @return JSON
     */
	public function getLocationByAreaJSON($area) : string
	{
		// get kladr
		$kladr = $this->kladr->getByCode($area);
		$code_region = $kladr['code_region'];
		$code_area = $kladr['code_area'];
		// get locations by area
		$location = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
											'kladr',
											'level = :level AND code_region = :code_region AND code_area = :code_area',
											[':level' => self::LOCATION,
											':code_region' => $code_region,
											':code_area' => $code_area]);
		foreach ($location as $value) {
			$location_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
		}
		return json_encode($location_json);
	}

	/**
     * Gets streets by city JSON.
     *
     * @return JSON
     */
	public function getStreetByCityJSON($city) : string
	{
		// get kladr
		$kladr = $this->kladr->getByCode($city);
		$code_region = $kladr['code_region'];
		$code_city = $kladr['code_city'];
		// get streets by city
		$street = $this->db->rowSelectAll('kladr_code, kladr_name, kladr_abbr',
										'kladr',
										'level = :level AND code_region = :code_region AND code_city = :code_city',
										[':level' => self::STREET,
										':code_region' => $code_region,
										':code_city' => $code_city]);
		foreach ($street as $value) {
			$street_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
		}
		return json_encode($street_json);
	}

	/**
     * Gets streets by location JSON.
     *
     * @return JSON
     */
	public function getStreetByLocationJSON($location) : string
	{
		// get kladr
		$kladr = $this->kladr->getByCode($location);
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
		foreach ($street as $value) {
			$street_json[] = ['kladr_code' => $value['kladr_code'],
								'kladr_name' => $value['kladr_name'],
								'kladr_abbr' => $value['kladr_abbr']];
		}
		return json_encode($street_json);
	}
}
