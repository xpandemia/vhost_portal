<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Resume as Model_Resume_Data;
use common\models\Model_Personal as Model_Personal;
use common\models\Model_DictCitizenship as Model_DictCitizenship;
use common\models\Model_Address as Model_Address;
use common\models\Model_DictCountries as Model_DictCountries;

class Model_Resume extends Model
{
	/*
		Resume processing
	*/

	/**
     * Resume rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'name_first' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Имя обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для имени можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное имя!'],
                                'success' => 'Имя заполнено верно.'
                               ],
                'name_middle' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для отчества можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинное отчество!'],
                                'success' => 'Отчество заполнено верно.'
                               ],
                'name_last' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Фамилия обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA_RUS, 'msg' => 'Для фамилии можно использовать только русские буквы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 50, 'msg' => 'Слишком длинная фамилия!'],
                                'success' => 'Фамилия заполнена верно.'
                               ],
                'sex' => [
                            'type' => 'radio',
                            'class' => 'form-check-input',
                            'required' => ['default' => '', 'msg' => 'Пол обязателен для заполнения!'],
                            'success' => 'Пол заполнен верно.'
                           ],
                'birth_dt' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Дата рождения обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата рождения должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
                                'success' => 'Дата рождения заполнена верно.'
                               ],
				'birth_place' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Место рождения обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_TEXT_RUS, 'msg' => 'Для места рождения можно использовать только русские буквы, тире, точки, запятые и пробелы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 240, 'msg' => 'Слишком длинное место рождения!'],
                                'success' => 'Место рождения заполнено верно.'
                               ],
                'citizenship' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Гражданство обязательно для заполнения!'],
								'success' => 'Гражданство заполнено верно.'
                               ],
				'country_reg' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Страна регистрации обязательна для заполнения!'],
								'success' => 'Страна регистрации заполнена верно.'
                               ],
                'address_reg' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Адрес регистрации обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для адреса регистрации можно использовать только цифры, русские буквы, тире, точки, запятые и пробелы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 255, 'msg' => 'Слишком длинный адрес регистрации!'],
                                'success' => 'Адрес регистрации заполнен верно.'
                               ],
                'country_res' => [
								'type' => 'selectlist',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Страна проживания обязательна для заполнения!'],
								'success' => 'Страна проживания заполнена верно.'
                               ],
                'address_res' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Адрес проживания обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для адреса проживания можно использовать только цифры, русские буквы, тире, точки, запятые и пробелы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 255, 'msg' => 'Слишком длинный адрес проживания!'],
                                'success' => 'Адрес проживания заполнен верно.'
                               ],
                'personal' => [
                                'type' => 'checkbox',
                                'class' => 'form-check-input',
                                'required' => ['default' => '', 'msg' => 'Необходимо согласие на обработку персональных данных!'],
                                'success' => 'Получено согласие на обработку персональных данных.'
                               ]
            ];
	}

	/**
     * Get registration address.
     *
     * @return array
     */
	public function getAddressReg($form)
	{
		if (isset($_POST['homeless_reg'])) {
			$form['kladr_reg'] = 0;
			$form['region_reg'] = null;
			$form['area_reg'] = null;
			$form['city_reg'] = null;
			$form['location_reg'] = null;
			$form['street_reg'] = null;
			$form['house_reg'] = null;
			$form['building_reg'] = null;
			$form['flat_reg'] = null;
			$form['postcode_reg'] = null;
		} else {
			$form['kladr_reg'] = (isset($_POST['kladr_reg_not'])) ? 0 : 1;
			$form['region_reg'] = (isset($_POST['region_reg'])) ? $_POST['region_reg'] : null;
			$form['area_reg'] = (isset($_POST['area_reg'])) ? $_POST['area_reg'] : null;
			$form['city_reg'] = (isset($_POST['city_reg'])) ? $_POST['city_reg'] : null;
			$form['location_reg'] = (isset($_POST['location_reg'])) ? $_POST['location_reg'] : null;
			$form['street_reg'] = (isset($_POST['street_reg'])) ? $_POST['street_reg'] : null;
			$form['house_reg'] = (isset($_POST['house_reg'])) ? $_POST['house_reg'] : null;
			$form['building_reg'] = (isset($_POST['building_reg'])) ? $_POST['building_reg'] : null;
			$form['flat_reg'] = (isset($_POST['flat_reg'])) ? $_POST['flat_reg'] : null;
			$form['postcode_reg'] = (isset($_POST['postcode_reg'])) ? $_POST['postcode_reg'] : null;
		}
		return $form;
	}

	/**
     * Get residential address.
     *
     * @return array
     */
	public function getAddressRes($form)
	{
		if (isset($_POST['homeless_res'])) {
			$form['kladr_res'] = 0;
			$form['region_res'] = null;
			$form['area_res'] = null;
			$form['city_res'] = null;
			$form['location_res'] = null;
			$form['street_res'] = null;
			$form['house_res'] = null;
			$form['building_res'] = null;
			$form['flat_res'] = null;
			$form['postcode_res'] = null;
		} else {
			$form['kladr_res'] = (isset($_POST['kladr_res_not'])) ? 0 : 1;
			$form['region_res'] = (isset($_POST['region_res'])) ? $_POST['region_res'] : null;
			$form['area_res'] = (isset($_POST['area_res'])) ? $_POST['area_res'] : null;
			$form['city_res'] = (isset($_POST['city_res'])) ? $_POST['city_res'] : null;
			$form['location_res'] = (isset($_POST['location_res'])) ? $_POST['location_res'] : null;
			$form['street_res'] = (isset($_POST['street_res'])) ? $_POST['street_res'] : null;
			$form['house_res'] = (isset($_POST['house_res'])) ? $_POST['house_res'] : null;
			$form['building_res'] = (isset($_POST['building_res'])) ? $_POST['building_res'] : null;
			$form['flat_res'] = (isset($_POST['flat_res'])) ? $_POST['flat_res'] : null;
			$form['postcode_res'] = (isset($_POST['postcode_res'])) ? $_POST['postcode_res'] : null;
		}
		return $form;
	}

	/**
     * Checks resume data.
     *
     * @return array
     */
	public function check($form)
	{
		// personal
		$personal = new Model_Personal();
		$personal->id_resume = $form['id'];
		$personal->name_first = $form['name_first'];
		$personal->name_middle = $form['name_middle'];
		$personal->name_last = $form['name_last'];
		$personal->sex = $form['sex'];
		$personal->birth_dt = date('Y-m-d', strtotime($form['birth_dt']));
		$personal->birth_place = $form['birth_place'];
				$citizenship = new Model_DictCitizenship();
				$citizenship->citizenship_name = $form['citizenship'];
				$row_citizenship =  $citizenship->getByName();
		$personal->citizenship = $row_citizenship['id'];
		$row_personal = $personal->getByResume();
		if ($row_personal) {
			if ($personal->changeAll()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при изменении личных данных!';
				return $form;
			}
		} else {
			$personal->dt_created = date('Y-m-d H:i:s');
			if ($personal->save()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при создании личных данных!';
				return $form;
			}
		}
		// address registration
		$address_reg = new Model_Address();
		$address_reg->id_resume = $form['id'];
			$country_reg = new Model_DictCountries();
			$country_reg->country_code = $form['country_reg'];
			$row_country_reg = $country_reg->getByCode();
		$address_reg->id_country = $row_country_reg['id'];
		$address_reg->type = $address_reg::TYPE_REG;
		$address_reg->kladr = $form['kladr_reg'];
		$address_reg->region = (empty($form['region_reg'])) ? null : $form['region_reg'];
		$address_reg->area = (empty($form['area_reg'])) ? null : $form['area_reg'];
		$address_reg->city = (empty($form['city_reg'])) ? null : $form['city_reg'];
		$address_reg->location = (empty($form['location_reg'])) ? null : $form['location_reg'];
		$address_reg->street = (empty($form['street_reg'])) ? null : $form['street_reg'];
		$address_reg->house = (empty($form['house_reg'])) ? null : $form['house_reg'];
		$address_reg->building = (empty($form['building_reg'])) ? null : $form['building_reg'];
		$address_reg->flat = (empty($form['flat_reg'])) ? null : $form['flat_reg'];
		$address_reg->postcode = (empty($form['postcode_reg'])) ? null : $form['postcode_reg'];
		$address_reg->adr = $form['address_reg'];
		$row_address_reg = $address_reg->getByResumeType();
		if ($row_address_reg) {
			if ($address_reg->changeAll()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при изменении адреса регистрации!';
				return $form;
			}
		} else {
			$address_reg->dt_created = date('Y-m-d H:i:s');
			if ($address_reg->save()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при создании адреса регистрации!';
				return $form;
			}
		}
		// address residential
		$address_res = new Model_Address();
		$address_res->id_resume = $form['id'];
			$country_res = new Model_DictCountries();
			$country_res->country_code = $form['country_res'];
			$row_country_res = $country_res->getByCode();
		$address_res->id_country = $row_country_res['id'];
		$address_res->type = $address_res::TYPE_RES;
		$address_res->kladr = $form['kladr_res'];
		$address_res->region = (empty($form['region_res'])) ? null : $form['region_res'];
		$address_res->area = (empty($form['area_res'])) ? null : $form['area_res'];
		$address_res->city = (empty($form['city_res'])) ? null : $form['city_res'];
		$address_res->location = (empty($form['location_res'])) ? null : $form['location_res'];
		$address_res->street = (empty($form['street_res'])) ? null : $form['street_res'];
		$address_res->house = (empty($form['house_res'])) ? null : $form['house_res'];
		$address_res->building = (empty($form['building_res'])) ? null : $form['building_res'];
		$address_res->flat = (empty($form['flat_res'])) ? null : $form['flat_res'];
		$address_res->postcode = (empty($form['postcode_res'])) ? null : $form['postcode_res'];
		$address_res->adr = $form['address_res'];
		$row_address_res = $address_res->getByResumeType();
		if ($row_address_res) {
			if ($address_res->changeAll()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при изменении адреса проживания!';
				return $form;
			}
		} else {
			$address_res->dt_created = date('Y-m-d H:i:s');
			if ($address_res->save()) {
				$form['error_msg'] = null;
			} else {
				$form['error_msg'] = 'Ошибка при создании адреса проживания!';
				return $form;
			}
		}
		// set status
		$resume = new Model_Resume_Data();
		$resume->status = $resume::STATUS_SENDED;
		$resume->changeStatus();
			return $form;
	}
}
