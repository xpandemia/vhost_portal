<?php

namespace frontend\controllers;

use tinyframe\core\Controller as Controller;
use frontend\models\Model_Kladr as Model_Kladr;

class Controller_Kladr extends Controller
{
	/*
		Kladr actions
	*/

	public $code;

	public function __construct()
	{
		// model
		$this->model = new Model_Kladr();
		// code
		if (isset($_POST['code'])) {
			$this->code = htmlspecialchars($_POST['code']);
		}
		else {
			$this->code = null;
		}
	}

	/**
     * Prints all regions JSON.
     *
     * @return void
     */
	public function actionRegionAllJSON()
	{
		echo $this->model->getByLevelJSON($this->model::REGION);
	}

	/**
     * Prints all areas JSON.
     *
     * @return void
     */
	public function actionAreaAllJSON()
	{
		echo $this->model->getByLevelJSON($this->model::AREA);
	}

	/**
     * Prints areas by region JSON.
     *
     * @return void
     */
	public function actionAreaByRegionJSON()
	{
		echo $this->model->getAreaByRegionJSON($this->code);
	}

	/**
     * Prints cities by area JSON.
     *
     * @return void
     */
	public function actionCityByAreaJSON()
	{
		echo $this->model->getCityByAreaJSON($this->code);
	}

	/**
     * Prints cities by region JSON.
     *
     * @return void
     */
	public function actionCityByRegionJSON()
	{
		echo $this->model->getCityByRegionJSON($this->code);
	}

	/**
     * Prints locations by region JSON.
     *
     * @return void
     */
	public function actionLocationByRegionJSON()
	{
		echo $this->model->getLocationByRegionJSON($this->code);
	}

	/**
     * Prints locations by area JSON.
     *
     * @return void
     */
	public function actionLocationByAreaJSON()
	{
		echo $this->model->getLocationByAreaJSON($this->code);
	}

	/**
     * Prints locations by city JSON.
     *
     * @return void
     */
	public function actionLocationByCityJSON()
	{
		echo $this->model->getLocationByCityJSON($this->code);
	}

	/**
     * Prints streets by region JSON.
     *
     * @return void
     */
	public function actionStreetByRegionJSON()
	{
		echo $this->model->getStreetByRegionJSON($this->code);
	}

	/**
     * Prints streets by city JSON.
     *
     * @return void
     */
	public function actionStreetByCityJSON()
	{
		echo $this->model->getStreetByCityJSON($this->code);
	}

	/**
     * Prints streets by location JSON.
     *
     * @return void
     */
	public function actionStreetByLocationJSON()
	{
		echo $this->model->getStreetByLocationJSON($this->code);
	}

	/**
     * Prints houses by street JSON.
     *
     * @return void
     */
	public function actionHouseByStreetJSON()
	{
		echo $this->model->getHouseByStreetJSON($this->code);
	}

	/**
     * Prints postcode by code JSON.
     *
     * @return void
     */
	public function actionPostcodeByCodeJSON()
	{
		echo $this->model->getPostcodeByCodeJSON($this->code);
	}

	public function __destruct()
	{
		$this->model = null;
	}
}
