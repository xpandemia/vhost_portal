<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Calc_Helper as Calc_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\Files_Helper as Files_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_Personal as Model_Personal;
use common\models\Model_Contacts as Model_Contacts;
use common\models\Model_Passport as Model_Passport;
use common\models\Model_DictDoctypes as Model_DictDoctypes;
use common\models\Model_Address as Model_Address;
use common\models\Model_DictCountries as Model_DictCountries;
use common\models\Model_DictScans as Model_DictScans;
use common\models\Model_Scans as Scans;
use common\models\Model_Docs as Model_Docs;
use common\models\Model_DictForeignLangs as DictForeignLangs;
use common\models\Model_ForeignLangs as ForeignLangs;

include ROOT_DIR.'/application/frontend/models/Model_Scans.php';

use PDO;

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
		$rules = [
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
                                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата рождения больше текущей даты или равна ей!'],
                                'success' => 'Дата рождения заполнена верно.'
                               ],
                'agreement' => [
								'type' => 'file',
								'class' => 'form-control',
								'success' => 'Скан-копия "Согласие родителей/опекунов" заполнено верно.'
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
                'email' => [
                            'type' => 'email',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть в формате user@domain'],
                            'width' => ['format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!'],
                            'success' => 'Адрес эл. почты заполнен верно.'
                           ],
                'phone_mobile' => [
		                            'type' => 'text',
		                            'class' => 'form-control',
		                            'success' => 'Номер мобильного телефона заполнен верно.'
		                           ],
		        'phone_home' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'pattern' => ['value' => PATTERN_PHONE_HOME, 'msg' => 'Номер домашнего телефона должен содержать только цифры и тире!'],
	                            'success' => 'Номер домашнего телефона заполнен верно.'
	                           ],
		        'phone_add' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'pattern' => ['value' => PATTERN_PHONE_ADD, 'msg' => 'Номер домашнего телефона должен содержать только цифры и тире!'],
	                            'success' => 'Номер дополнительного телефона заполнен верно.'
	                           ],
                'passport_type' => [
									'type' => 'selectlist',
	                                'class' => 'form-control',
	                                'required' => ['default' => '', 'msg' => 'Тип документа обязателен для заполнения!'],
									'success' => 'Тип документа заполнен верно.'
	                               ],
	            'series' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!'],
                            'success' => 'Серия заполнена верно.'
                           ],
                'numb' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Номер обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для номера можно использовать только цифры!'],
                            'width' => ['format' => 'string', 'min' => 1, 'max' => 15, 'msg' => 'Слишком длинный номер!'],
                            'success' => 'Номер заполнен верно.'
                           ],
                'dt_issue' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Дата выдачи обязательна для заполнения!'],
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
                                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!'],
                                'success' => 'Дата выдачи заполнена верно.'
                               ],
                'unit_name' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Наименование подразделения обязательно для заполнения!'],
                                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования подразделения можно использовать только русские буквы, тире, точки, запятые и пробелы!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование подразделения!'],
                                'success' => 'Наименование подразделения заполнено верно.'
                               ],
                'unit_code' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'success' => 'Код подразделения заполнена верно.'
	                           ],
	            'dt_end' => [
                            'type' => 'date',
                            'format' => 'd.m.Y',
                            'class' => 'form-control',
                            'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата окончания действия должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
                            'compared' => ['value' => date('d.m.Y'), 'type' => '>', 'msg' => 'Дата окончания действия меньше текущей даты или равна ей!'],
                            'success' => 'Дата окончания действия заполнена верно.'
                           ],
                'passport_old_yes' => [
			                            'type' => 'checkbox',
			                            'class' => 'form-check-input',
			                            'success' => ''
			                           ],
                'passport_type_old' => [
										'type' => 'selectlist',
		                                'class' => 'form-control',
		                                'success' => 'Тип документа заполнен верно.'
		                               ],
		        'series_old' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 10, 'msg' => 'Слишком длинная серия!'],
	                            'success' => 'Серия заполнена верно.'
	                           ],
                'numb_old' => [
	                            'type' => 'text',
	                            'class' => 'form-control',
	                            'pattern' => ['value' => PATTERN_NUMB, 'msg' => 'Для номера можно использовать только цифры!'],
	                            'width' => ['format' => 'string', 'min' => 1, 'max' => 15, 'msg' => 'Слишком длинный номер!'],
	                            'success' => 'Номер заполнен верно.'
	                           ],
                'dt_issue_old' => [
                                'type' => 'date',
                                'format' => 'd.m.Y',
                                'class' => 'form-control',
                                'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата выдачи должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
                                'compared' => ['value' => date('d.m.Y'), 'type' => '<', 'msg' => 'Дата выдачи больше текущей даты или равна ей!'],
                                'success' => 'Дата выдачи заполнена верно.'
                               ],
                'unit_name_old' => [
	                                'type' => 'text',
	                                'class' => 'form-control',
	                                'pattern' => ['value' => PATTERN_INFO_RUS, 'msg' => 'Для наименования подразделения можно использовать только русские буквы, тире, точки, запятые и пробелы!'],
	                                'width' => ['format' => 'string', 'min' => 1, 'max' => 100, 'msg' => 'Слишком длинное наименование подразделения!'],
	                                'success' => 'Наименование подразделения заполнено верно.'
	                               ],
                'unit_code_old' => [
		                            'type' => 'text',
		                            'class' => 'form-control',
		                            'success' => 'Код подразделения заполнена верно.'
		                           ],
	            'dt_end_old' => [
	                            'type' => 'date',
	                            'format' => 'd.m.Y',
	                            'class' => 'form-control',
	                            'pattern' => ['value' => PATTERN_DATE_STRONG, 'msg' => 'Дата окончания действия должна быть в фомате ДД.ММ.ГГГГ и только 20-го, 21-го вв!'],
	                            'compared' => ['value' => date('d.m.Y'), 'type' => '>', 'msg' => 'Дата окончания действия меньше текущей даты или равна ей!'],
	                            'success' => 'Дата окончания действия заполнена верно.'
	                           ],
	            'passport_old' => [
									'type' => 'file',
									'class' => 'form-control',
									'success' => 'Скан-копия "Сведения о ранее выданных паспортах" заполнена верно.'
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
                               ]
                ];
        $scans = Model_Scans::createRules('resume');
        $rules = array_merge($rules, $scans);
        $rules['personal'] = [
                            'type' => 'checkbox',
                            'class' => 'form-check-input',
                            'required' => ['default' => '', 'msg' => 'Необходимо согласие на обработку персональных данных!'],
                            'success' => 'Получено согласие на обработку персональных данных.'
                           ];
		return $rules;
	}

	/**
     * Shows status.
     *
     * @return string
     */
	public static function showStatus($status)
	{
		switch ($status) {
			case Resume::STATUS_CREATED:
				return '<div class="alert alert-info">Состояние анкеты: СОЗДАНА</div>';
			case Resume::STATUS_SENDED:
				return '<div class="alert alert-primary">Состояние анкеты: ОТПРАВЛЕНА</div>';
			case Resume::STATUS_APPROVED:
				return '<div class="alert alert-success">Состояние анкеты: ОДОБРЕНА</div>';
			case Resume::STATUS_REJECTED:
				return '<div class="alert alert-danger">Состояние анкеты: ОТКЛОНЕНА</div>';
			default:
				return '<div class="alert alert-warning">Состояние анкеты: НЕИЗВЕСТНО</div>';
		}
	}

	/**
     * Validates resume advanced.
     *
     * @return array
     */
	public function validateFormAdvanced($form)
	{
		// birth_dt
		if (Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') <= 12) {
			$form = $this->setFormErrorField($form, 'birth_dt', 'Ваш возраст меньше или равен 12 лет!');
			return $form;
		}
		// dt_issue
		if (date('Y-m-d', strtotime($form['dt_issue'])) <= date('Y-m-d', strtotime($form['birth_dt']))) {
			$form = $this->setFormErrorField($form, 'dt_issue', 'Дата выдачи документа, удостоверяющего личность, меньше или равна дате рождения!');
			return $form;
		}
		// phones
		if (empty($form['phone_mobile']) && empty($form['phone_home']) && empty($form['phone_add'])) {
			$form = $this->setFormError($form, 'Необходимо заполнить хотя бы один номер телефона в контактной информации!');
			return $form;
		}
		return $form;
	}

	/**
     * Validates agreement.
     *
     * @return array
     */
	public function validateAgreement($form)
	{
		if (!empty($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') < 18 && empty($form['agreement_name'])) {
			$form = $this->setFormErrorFile($form, 'agreement', 'Скан-копия "Согласие родителей/опекунов" обязательна для заполнения!');
		}
		return $form;
	}

	/**
     * Validates passport.
     *
     * @return array
     */
	public function validatePassport($form)
	{
		if (!empty($form['passport_type'])) {
			if ($form['passport_type'] == '000000047') {
				// series
				if (empty($form['series'])) {
					$form = $this->setFormErrorField($form, 'series', 'Серия обязательна для заполнения!');
				}
				// unit_code
				if (empty($form['unit_code'])) {
					$form = $this->setFormErrorField($form, 'unit_code', 'Код подразделения обязателен для заполнения!');
				}
			} else {
				// dt_end
				if (empty($form['dt_end'])) {
					$form = $this->setFormErrorField($form, 'dt_end', 'Дата окончания действия обязательна для заполнения!');
				}
			}
		}
		return $form;
	}

	/**
     * Validates old passport.
     *
     * @return array
     */
	public function validatePassportOld($form)
	{
		if ($form['passport_old_yes'] == 'checked') {
			// type
			if (empty($form['passport_type_old'])) {
				$form = $this->setFormErrorField($form, 'passport_type_old', 'Тип документа обязателен для заполнения!');
			} else {
				if ($form['passport_type_old'] == '000000047') {
					// series
					if (empty($form['series_old'])) {
						$form = $this->setFormErrorField($form, 'series_old', 'Серия обязательна для заполнения!');
					}
					// unit_code
					if (empty($form['unit_code_old'])) {
						$form = $this->setFormErrorField($form, 'unit_code_old', 'Код подразделения обязателен для заполнения!');
					}
					// passport_old
					if (empty($form['passport_old'])) {
						$form = $this->setFormErrorFile($form, 'passport_old', 'Скан-копия "Сведения о ранее выданных паспортах" обязательна для заполнения!');
					}
				}
				// numb
				if (empty($form['numb_old'])) {
					$form = $this->setFormErrorField($form, 'numb_old', 'Номер обязателен для заполнения!');
				}
				// dt_issue
				if (empty($form['dt_issue_old'])) {
					$form = $this->setFormErrorField($form, 'dt_issue_old', 'Дата выдачи обязательна для заполнения!');
				} elseif (date('Y-m-d', strtotime($form['dt_issue_old'])) <= date('Y-m-d', strtotime($form['birth_dt']))) {
					$form = $this->setFormErrorField($form, 'dt_issue_old', 'Дата выдачи старого документа, удостоверяющего личность, меньше или равна дате рождения!', 1);	
				} elseif (date('Y-m-d', strtotime($form['dt_issue'])) <= date('Y-m-d', strtotime($form['dt_issue_old']))) {
					$form = $this->setFormErrorField($form, 'dt_issue_old', 'Дата выдачи документа, удостоверяющего личность, меньше или равна дате выдачи старого документа, удостоверяющего личность!', 1);
				}
			}
		}
		return $form;
	}

	/**
     * Sets registration address.
     *
     * @return array
     */
	public function setAddressReg($form)
	{
		$address = new Model_Address();
		$address->id_resume = $form['id'];
		$address->type = $address::TYPE_REG;
		$row = $address->getByResumeType();
		if ($row) {
			$form['region_reg'] = $row['region'];
			$form['area_reg'] = $row['area'];
			$form['city_reg'] = $row['city'];
			$form['location_reg'] = $row['location'];
			$form['street_reg'] = $row['street'];
			$form['house_reg'] = $row['house'];
			$form['building_reg'] = $row['building'];
			$form['flat_reg'] = $row['flat'];
			$form['postcode_reg'] = $row['postcode'];
		} else {
			$form['region_reg'] = null;
			$form['area_reg'] = null;
			$form['city_reg'] = null;
			$form['location_reg'] = null;
			$form['street_reg'] = null;
			$form['house_reg'] = null;
			$form['building_reg'] = null;
			$form['flat_reg'] = null;
			$form['postcode_reg'] = null;
		}
		return $form;
	}

	/**
     * Sets residential address.
     *
     * @return array
     */
	public function setAddressRes($form)
	{
		$address = new Model_Address();
		$address->id_resume = $form['id'];
		$address->type = $address::TYPE_RES;
		$row = $address->getByResumeType();
		if ($row) {
			$form['region_res'] = $row['region'];
			$form['area_res'] = $row['area'];
			$form['city_res'] = $row['city'];
			$form['location_res'] = $row['location'];
			$form['street_res'] = $row['street'];
			$form['house_res'] = $row['house'];
			$form['building_res'] = $row['building'];
			$form['flat_res'] = $row['flat'];
			$form['postcode_res'] = $row['postcode'];
		} else {
			$form['region_res'] = null;
			$form['area_res'] = null;
			$form['city_res'] = null;
			$form['location_res'] = null;
			$form['street_res'] = null;
			$form['house_res'] = null;
			$form['building_res'] = null;
			$form['flat_res'] = null;
			$form['postcode_res'] = null;
		}
		return $form;
	}

	/**
     * Resets registration address.
     *
     * @return array
     */
	public function resetAddressReg($form)
	{
		$form['region_reg'] = null;
		$form['area_reg'] = null;
		$form['city_reg'] = null;
		$form['location_reg'] = null;
		$form['street_reg'] = null;
		$form['house_reg'] = null;
		$form['building_reg'] = null;
		$form['flat_reg'] = null;
		$form['postcode_reg'] = null;
		return $form;
	}

	/**
     * Resets residential address.
     *
     * @return array
     */
	public function resetAddressRes($form)
	{
		$form['region_res'] = null;
		$form['area_res'] = null;
		$form['city_res'] = null;
		$form['location_res'] = null;
		$form['street_res'] = null;
		$form['house_res'] = null;
		$form['building_res'] = null;
		$form['flat_res'] = null;
		$form['postcode_res'] = null;
		return $form;
	}

	/**
     * Gets registration address.
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
			$form['region_reg'] = (isset($_POST['region_reg'])) ? htmlspecialchars($_POST['region_reg']) : null;
			$form['area_reg'] = (isset($_POST['area_reg'])) ? htmlspecialchars($_POST['area_reg']) : null;
			$form['city_reg'] = (isset($_POST['city_reg'])) ? htmlspecialchars($_POST['city_reg']) : null;
			$form['location_reg'] = (isset($_POST['location_reg'])) ? htmlspecialchars($_POST['location_reg']) : null;
			$form['street_reg'] = (isset($_POST['street_reg'])) ? htmlspecialchars($_POST['street_reg']) : null;
			$form['house_reg'] = (isset($_POST['house_reg'])) ? htmlspecialchars($_POST['house_reg']) : null;
			$form['building_reg'] = (isset($_POST['building_reg'])) ? htmlspecialchars($_POST['building_reg']) : null;
			$form['flat_reg'] = (isset($_POST['flat_reg'])) ? htmlspecialchars($_POST['flat_reg']) : null;
			$form['postcode_reg'] = (isset($_POST['postcode_reg'])) ? htmlspecialchars($_POST['postcode_reg']) : null;
		}
		return $form;
	}

	/**
     * Gets residential address.
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
			$form['region_res'] = (isset($_POST['region_res'])) ? htmlspecialchars($_POST['region_res']) : null;
			$form['area_res'] = (isset($_POST['area_res'])) ? htmlspecialchars($_POST['area_res']) : null;
			$form['city_res'] = (isset($_POST['city_res'])) ? htmlspecialchars($_POST['city_res']) : null;
			$form['location_res'] = (isset($_POST['location_res'])) ? htmlspecialchars($_POST['location_res']) : null;
			$form['street_res'] = (isset($_POST['street_res'])) ? htmlspecialchars($_POST['street_res']) : null;
			$form['house_res'] = (isset($_POST['house_res'])) ? htmlspecialchars($_POST['house_res']) : null;
			$form['building_res'] = (isset($_POST['building_res'])) ? htmlspecialchars($_POST['building_res']) : null;
			$form['flat_res'] = (isset($_POST['flat_res'])) ? htmlspecialchars($_POST['flat_res']) : null;
			$form['postcode_res'] = (isset($_POST['postcode_res'])) ? htmlspecialchars($_POST['postcode_res']) : null;
		}
		return $form;
	}

	/**
     * Sets foreign languages.
     *
     * @return array
     */
	public function setForeignLangs($form)
	{
		$langs = new ForeignLangs();
		$langs->id_resume = $form['id'];
		$langs_arr = $langs->getByResumeList();
		if ($langs_arr) {
			foreach ($langs_arr as $langs_row) {
				$form['lang'.$langs_row['numb']] = $langs_row['code'];
			}
		}
		return $form;
	}

	/**
     * Resets foreign languages.
     *
     * @return array
     */
	public function resetForeignLangs($form)
	{
		foreach (array_filter($form, function ($var)
							        {
							            return(substr($var, 0, 4) == 'lang');
							        }, ARRAY_FILTER_USE_KEY) as $key => $value)
        {
			unset($form[$key]);
		}
		return $form;
	}

	/**
     * Gets foreign languages.
     *
     * @return array
     */
	public function getForeignLangs($form)
	{
		foreach (array_filter($_POST, function ($var)
							        {
							            return(substr($var, 0, 4) == 'lang');
							        }, ARRAY_FILTER_USE_KEY) as $key => $value)
        {
			$form[$key] = $value;
		}
		return $form;
	}

	/**
     * Unsets resume files.
     *
     * @return array
     */
	public function unsetScans($form)
	{
		// agreement
		if (isset($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') < 18) {
			if (empty($form['agreement_name'])) {
				$form['agreement_err'] = 'Скан-копия "Согласие родителей/опекунов" обязательна для заполнения!';
				$form['agreement_scs'] = null;
				$form['validate'] = false;
			}
		}
		// passport_old
		if ($form['passport_old_yes'] == 'checked' && $form['passport_type_old'] == '000000047') {
			if (empty($form['agreement_name'])) {
				$form['passport_old_err'] = 'Скан-копия "Сведения о ранее выданных паспортах" обязательна для заполнения!';
				$form['passport_old_scs'] = null;
				$form['validate'] = false;
			}
		}
		// main
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = 'resume';
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			$docs = new Model_Docs();
			$docs->doc_code = 'resume';
			$docs_row = $docs->getByCode();
			$scans = new Scans();
			foreach ($dict_scans_arr as $dict_scans_row) {
				if ($dict_scans_row['required'] == 1) {
					$scans->id_doc = $docs_row['id'];
					$scans->id_scans = $dict_scans_row['id'];
					if (!$scans->getByDoc()) {
						$form[$dict_scans_row['scan_code'].'_id'] = null;
						$form[$dict_scans_row['scan_code']] = null;
						$form[$dict_scans_row['scan_code'].'_id'] = null;
						$form[$dict_scans_row['scan_code'].'_name'] = null;
						$form[$dict_scans_row['scan_code'].'_type'] = null;
						$form[$dict_scans_row['scan_code'].'_size'] = null;
						$form[$dict_scans_row['scan_code'].'_scs'] = null;
						$form[$dict_scans_row['scan_code'].'_err'] = 'Скан-копия "'.ucfirst($dict_scans_row['scan_name']).'" обязательна для заполнения!';
					}
				}
			}
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
		/* personal */
		$personal = new Model_Personal();
		$personal->id_user = $_SESSION[APP_CODE]['user_id'];
		$personal->id_resume = $form['id'];
		$personal->name_first = $form['name_first'];
		$personal->name_middle = $form['name_middle'];
		$personal->name_last = $form['name_last'];
		$personal->sex = $form['sex'];
		$personal->birth_dt = date('Y-m-d', strtotime($form['birth_dt']));
		$personal->birth_place = $form['birth_place'];
				$citizenship = new Model_DictCountries();
				$citizenship->code = $form['citizenship'];
				$row_citizenship =  $citizenship->getByCode();
		$personal->citizenship = $row_citizenship['id'];
		$row_personal = $personal->getByResume();
		if ($row_personal) {
			$personal->id = $row_personal['id'];
			if (!$personal->changeAll()) {
				$form['error_msg'] = 'Ошибка при изменении личных данных!';
				return $form;
			}
		} else {
			if ($personal->save() == 0) {
				$form['error_msg'] = 'Ошибка при создании личных данных!';
				return $form;
			}
		}
		/* agreement */
		if (!empty($form['birth_dt']) && Calc_Helper::getAge($form['birth_dt'], 'd.m.Y') < 18) {
			$form = Model_Scans::push('resume', 'agreement', $form);
		}
		/* contacts */
		$contacts = new Model_Contacts();
		$contacts->id_user = $_SESSION[APP_CODE]['user_id'];
		$contacts->id_resume = $form['id'];
		// email
		$contacts->type = (int) $contacts::TYPE_EMAIL;
		$contacts->contact = $form['email'];
		$row_contacts = $contacts->getEmailByResume();
		if ($row_contacts) {
			$contacts->id = $row_contacts['id'];
			if (!$contacts->changeAll()) {
				$form['error_msg'] = 'Ошибка при изменении адреса эл. почты!';
				return $form;
			}
		} else {
			if ($contacts->save() == 0) {
				$form['error_msg'] = 'Ошибка при создании адреса эл. почты!';
				return $form;
			}
		}
		// phone mobile
		$contacts->type = $contacts::TYPE_PHONE_MOBILE;
		if (!empty($form['phone_mobile'])) {
			$contacts->contact = $form['phone_mobile'];
			$row_contacts = $contacts->getPhoneMobileByResume();
			if ($row_contacts) {
				$contacts->id = $row_contacts['id'];
				if (!$contacts->changeAll()) {
					$form['error_msg'] = 'Ошибка при изменении номера мобильного телефона!';
					return $form;
				}
			} else {
				if ($contacts->save() == 0) {
					$form['error_msg'] = 'Ошибка при создании номера мобильного телефона!';
					return $form;
				}
			}
		} else {
			$row_contacts = $contacts->getPhoneMobileByResume();
			if ($row_contacts) {
				$contacts->id = $row_contacts['id'];
				$contacts->clear();
			}
		}
		// phone home
		$contacts->type = $contacts::TYPE_PHONE_HOME;
		if (!empty($form['phone_home'])) {
			$contacts->contact = $form['phone_home'];
			$row_contacts = $contacts->getPhoneHomeByResume();
			if ($row_contacts) {
				$contacts->id = $row_contacts['id'];
				if (!$contacts->changeAll()) {
					$form['error_msg'] = 'Ошибка при изменении номера домашнего телефона!';
					return $form;
				}
			} else {
				if ($contacts->save() == 0) {
					$form['error_msg'] = 'Ошибка при создании номера домашнего телефона!';
					return $form;
				}
			}
		} else {
			$row_contacts = $contacts->getPhoneHomeByResume();
			if ($row_contacts) {
				$contacts->id = $row_contacts['id'];
				$contacts->clear();
			}
		}
		// phone add
		$contacts->type = $contacts::TYPE_PHONE_ADD;
		if (!empty($form['phone_add'])) {
			$contacts->contact = $form['phone_add'];
			$row_contacts = $contacts->getPhoneAddByResume();
			if ($row_contacts) {
				$contacts->id = $row_contacts['id'];
				if (!$contacts->changeAll()) {
					$form['error_msg'] = 'Ошибка при изменении номера дополнительного телефона!';
					return $form;
				}
			} else {
				if ($contacts->save() == 0) {
					$form['error_msg'] = 'Ошибка при создании номера дополнительного телефона!';
					return $form;
				}
			}
		} else {
			$row_contacts = $contacts->getPhoneAddByResume();
			if ($row_contacts) {
				$contacts->id = $row_contacts['id'];
				$contacts->clear();
			}
		}
		/* passports */
		// new passport
		$passport = new Model_Passport();
		$passport->id_user = $_SESSION[APP_CODE]['user_id'];
		$passport->id_resume = $form['id'];
			$passport_type = new Model_DictDoctypes();
			$passport_type->code = $form['passport_type'];
			$row_passport_type = $passport_type->getByCode();
		$passport->id_doctype = $row_passport_type['id'];
		$passport->main = 1;
		$passport->series = (empty($form['series'])) ? null : $form['series'];
		$passport->numb = $form['numb'];
		$passport->dt_issue = date('Y-m-d', strtotime($form['dt_issue']));
		$passport->unit_name = $form['unit_name'];
		$passport->unit_code = (empty($form['unit_code'])) ? null : $form['unit_code'];
		$passport->dt_end = (empty($form['dt_end'])) ? null : date('Y-m-d', strtotime($form['dt_end']));
		$row_passport = $passport->getByResume();
		if ($row_passport) {
			if (!$passport->changeAll()) {
				$form['error_msg'] = 'Ошибка при изменении данных документа, удостоверяющего личность!';
				return $form;
			}
		} else {
			if ($passport->save() == 0) {
				$form['error_msg'] = 'Ошибка при создании данных документа, удостоверяющего личность!';
				return $form;
			}
		}
		// old passport
		if ($form['passport_old_yes'] == 'checked') {
			$passport_type = new Model_DictDoctypes();
			$passport_type->code = $form['passport_type_old'];
			$row_passport_type = $passport_type->getByCode();
			$passport->id_doctype = $row_passport_type['id'];
				$passport->main = 0;
				$passport->series = (empty($form['series_old'])) ? null : $form['series_old'];
				$passport->numb = $form['numb_old'];
				$passport->dt_issue = date('Y-m-d', strtotime($form['dt_issue_old']));
				$passport->unit_name = (empty($form['unit_name_old'])) ? null : $form['unit_name_old'];
				$passport->unit_code = (empty($form['unit_code_old'])) ? null : $form['unit_code_old'];
				$passport->dt_end = (empty($form['dt_end_old'])) ? null : date('Y-m-d', strtotime($form['dt_end_old']));
				$row_passport = $passport->getByResume();
				if ($row_passport) {
					if (!$passport->changeAll()) {
						$form['error_msg'] = 'Ошибка при изменении данных старого документа, удостоверяющего личность!';
						return $form;
					}
				} else {
					if ($passport->save() == 0) {
						$form['error_msg'] = 'Ошибка при создании данных старого документа, удостоверяющего личность!';
						return $form;
					}
				}
		}
		/* addresses */
		// address registration
		$address_reg = new Model_Address();
		$address_reg->id_user = $_SESSION[APP_CODE]['user_id'];
		$address_reg->id_resume = $form['id'];
			$country_reg = new Model_DictCountries();
			$country_reg->code = $form['country_reg'];
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
			if ($row_address_reg['id_country'] != $address_reg->id_country || $row_address_reg['adr'] != $address_reg->adr) {
				$address_reg->id = $row_address_reg['id'];
				if (!$address_reg->changeAll()) {
					$form['error_msg'] = 'Ошибка при изменении адреса регистрации!';
					return $form;
				}
			}
		} else {
			if ($address_reg->save() == 0) {
				$form['error_msg'] = 'Ошибка при создании адреса регистрации!';
				return $form;
			}
		}
		// address residential
		$address_res = new Model_Address();
		$address_res->id_user = $_SESSION[APP_CODE]['user_id'];
		$address_res->id_resume = $form['id'];
			$country_res = new Model_DictCountries();
			$country_res->code = $form['country_res'];
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
			if ($row_address_res['id_country'] != $address_res->id_country || $row_address_res['adr'] != $address_res->adr) {
				$address_res->id = $row_address_res['id'];
				if (!$address_res->changeAll()) {
					$form['error_msg'] = 'Ошибка при изменении адреса проживания!';
					return $form;
				}
			}
		} else {
			if ($address_res->save() == 0) {
				$form['error_msg'] = 'Ошибка при создании адреса проживания!';
				return $form;
			}
		}
		/* foreign languages */
		$langs = new ForeignLangs();
		$lang = new DictForeignLangs();
		$langs->id_user = $_SESSION[APP_CODE]['user_id'];
		$langs->id_resume = $form['id'];
		$langs_arr = $langs->getByResume();
		$i = 1;
		if ($langs_arr) {
			foreach ($langs_arr as $langs_row) {
				$langs->id = $langs_row['id'];
				if (array_key_exists('lang'.$langs_row['numb'], $form)) {
					// update
					$lang->code = $form['lang'.$langs_row['numb']];
					$lang_row = $lang->getByCode();
					if ($lang_row) {
						$langs->id_lang = $lang_row['id'];
						if (!$langs->changeAll()) {
							$form['error_msg'] = 'Ошибка при изменении иностранного языка!';
							return $form;
						}
					} else {
						$form['error_msg'] = 'Ошибка при поиске иностранного языка!';
						return $form;
					}
				} else {
					// delete
					$langs->clear();
				}
				unset($form['lang'.$langs_row['numb']]);
				$i++;
			}
		}
		// insert
		foreach (array_filter($form, function ($var)
							        {
							            return(substr($var, 0, 4) == 'lang');
							        }, ARRAY_FILTER_USE_KEY) as $key => $value)
        {
			$lang->code = $value;
			$lang_row = $lang->getByCode();
			if ($lang_row) {
				$langs->numb = $i;
				$langs->id_lang = $lang_row['id'];
				if ($langs->save() == 0) {
					$form['error_msg'] = 'Ошибка при создании иностранного языка!';
					return $form;
				}
			} else {
				$form['error_msg'] = 'Ошибка при поиске иностранного языка!';
				return $form;
			}
			$i++;
		}
		/* scans */
		$dict_scans = new Model_DictScans();
		$dict_scans->doc_code = 'resume';
		$dict_scans_arr = $dict_scans->getByDocument();
		if ($dict_scans_arr) {
			foreach ($dict_scans_arr as $dict_scans_row) {
				$form = Model_Scans::push($dict_scans->doc_code, $dict_scans_row['scan_code'], $form);
				if (!empty($form['error_msg'])) {
					return $form;
				}
			}
		}
		return $form;
	}
}
