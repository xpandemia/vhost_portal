<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\Help_Helper as Help_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Resume as Resume;
use common\models\Model_Kladr as Model_Kladr;
use common\models\Model_ForeignLangs as ForeignLangs;
use common\models\Model_DictForeignLangs as DictForeignLangs;
use frontend\models\Model_Resume as Model_Resume;

	// check resume
	if ((!isset($data['id']) || empty($data['id'])) && (!isset($data['status']) || empty($data['status']))) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка анкеты!\nСвяжитесь с администратором."));
	} else {
		if ($data['status'] == Resume::STATUS_CREATED && empty($data['dt_updated'])) {
			$data['personal_vis'] = true;
		} else {
			$data['personal_vis'] = false;
		}
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
	//$pageData->addCSS("sadf"); //это проверка того, что отладчик запустился
        echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(RESUME['ctr'], RESUME['act'], RESUME['id'], RESUME['hdr'], 2, '/images/logo_bsu_transparent.gif');
		/* help */
		echo '<button type="button" class="btn btn-info" data-toggle="modal" data-target="#helpResume">Инструкция</button>';
		/* status */
		echo Model_Resume::showStatus($data['status']);
		/* personal data */
		echo Form_Helper::setFormHeaderSub('Личные данные');
		// name_last
		echo Form_Helper::setFormInput(['label' => LASTNAME_PLC,
										'control' => 'name_last',
										'type' => 'text',
										'class' => $data['name_last_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => LASTNAME_PLC,
										'value' => $data['name_last'],
										'success' => $data['name_last_scs'],
										'error' => $data['name_last_err'],
										'help' => LASTNAME_HELP]);
		// name_first
		echo Form_Helper::setFormInput(['label' => FIRSTNAME_PLC,
										'control' => 'name_first',
										'type' => 'text',
										'class' => $data['name_first_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => FIRSTNAME_PLC,
										'value' => $data['name_first'],
										'success' => $data['name_first_scs'],
										'error' => $data['name_first_err'],
										'help' => FIRSTNAME_HELP]);
		// name_middle
		echo Form_Helper::setFormInput(['label' => MIDDLENAME_PLC,
										'control' => 'name_middle',
										'type' => 'text',
										'class' => $data['name_middle_cls'],
										'required' => 'no',
										'placeholder' => MIDDLENAME_PLC,
										'value' => $data['name_middle'],
										'success' => $data['name_middle_scs'],
										'error' => $data['name_middle_err'],
										'help' => MIDDLENAME_HELP]);
		// sex
		echo Form_Helper::setFormRadio(['label' => 'Пол',
										'control' => 'sex',
										'required' => 'yes',
										'required_style' => 'StarUp',
										'radio' => [
													'male' => ['1' => 'Мужской'],
													'female' => ['0' => 'Женский'],
													],
										'value' => $data['sex'],
										'error' => $data['sex_err']]);
		// birth_dt
		echo Form_Helper::setFormInput(['label' => 'Дата рождения',
										'control' => 'birth_dt',
										'type' => 'text',
										'class' => $data['birth_dt_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['birth_dt'],
										'success' => $data['birth_dt_scs'],
										'error' => $data['birth_dt_err']]);
		// agreement
		echo Form_Helper::setFormFile(['label' => 'Согласие родителей/опекунов',
										'control' => 'agreement',
										'required' => 'yes',
										'required_style' => 'StarUp',
										'data' => $data,
										'sample' => 'https://www.bsu.edu.ru/abitur/rules/doc/',
										'home_ctr' => RESUME['ctr'],
										'home_hdr' => RESUME['hdr'],
										'home_act' => 'Index',
										'ext' => FILES_EXT_SCANS]);
		// birth_place
		echo Form_Helper::setFormInput(['label' => BIRTHPLACE_PLC,
										'control' => 'birth_place',
										'type' => 'text',
										'class' => $data['birth_place_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => BIRTHPLACE_PLC,
										'value' => $data['birth_place'],
										'success' => $data['birth_place_scs'],
										'error' => $data['birth_place_err'],
										'help' => BIRTHPLACE_HELP]);
		// citizenship
		echo Form_Helper::setFormSelectListDB(['label' => 'Гражданство',
												'control' => 'citizenship',
												'class' => $data['citizenship_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictCountries',
												'model_method' => 'getAll',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['citizenship'],
												'success' => $data['citizenship_scs'],
												'error' => $data['citizenship_err']]); ?>
		<div class="form-check">
			<div class="col">
				<input type="checkbox" class="form-check-input" id="citizenship_not" name="citizenship_not">
				<label class="font-weight-bold" for="citizenship_not">Не имею гражданства</label>
			</div>
		</div><br>
		<?php
		// beneficiary
		echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЕсли Вы имеете льготы, то Вам разрешена только личная подача документов."), 'alert-warning');
		echo Form_Helper::setFormCheckbox(['label' => 'Имею льготы',
										'control' => 'beneficiary',
										'class' => $data['beneficiary_cls'],
										'value' => $data['beneficiary'],
										'success' => $data['beneficiary_scs'],
										'error' => $data['beneficiary_err']]);
		/* contacts */
		echo Form_Helper::setFormHeaderSub('Контактная информация');
		// email
		echo Form_Helper::setFormInput(['label' => CONTACT_EMAIL['name'],
										'control' => 'email',
										'type' => 'email',
										'class' => $data['email_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => CONTACT_EMAIL['plc'],
										'value' => $data['email'],
										'success' => $data['email_scs'],
										'error' => $data['email_err'],
										'help' => CONTACT_EMAIL['help']]);
		// phones
		echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nПожалуйста, укажите хотя бы мобильный или домаший номер телефона."), 'alert-warning');
		// phone mobile
		echo Form_Helper::setFormInput(['label' => 'Номер мобильного телефона',
										'control' => 'phone_mobile',
										'type' => 'text',
										'class' => $data['phone_mobile_cls'],
										'required' => 'no',
										'value' => $data['phone_mobile'],
										'success' => $data['phone_mobile_scs'],
										'error' => $data['phone_mobile_err']]);
		// phone home
		echo Form_Helper::setFormInput(['label' => CONTACT_PHONE_HOME['name'],
										'control' => 'phone_home',
										'type' => 'text',
										'class' => $data['phone_home_cls'],
										'required' => 'no',
										'placeholder' => CONTACT_PHONE_HOME['plc'],
										'value' => $data['phone_home'],
										'success' => $data['phone_home_scs'],
										'error' => $data['phone_home_err'],
										'help' => CONTACT_PHONE_HOME['help']]);
		// phone add
		echo Form_Helper::setFormInput(['label' => CONTACT_PHONE_ADD['name'],
										'control' => 'phone_add',
										'type' => 'text',
										'class' => $data['phone_add_cls'],
										'required' => 'no',
										'placeholder' => CONTACT_PHONE_ADD['plc'],
										'value' => $data['phone_add'],
										'success' => $data['phone_add_scs'],
										'error' => $data['phone_add_err'],
										'help' => CONTACT_PHONE_ADD['help']]);
		/* passport */
		echo Form_Helper::setFormHeaderSub('Документ, удостоверяющий личность');
		echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nПожалуйста, при наличии паспорта, указывайте паспортные данные."), 'alert-warning');
		// type
		echo Form_Helper::setFormSelectListDB(['label' => 'Тип документа',
												'control' => 'passport_type',
												'class' => $data['passport_type_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictDoctypes',
												'model_method' => 'getPassportsBsu',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['passport_type'],
												'success' => $data['passport_type_scs'],
												'error' => $data['passport_type_err']]);
		// series
		echo Form_Helper::setFormInput(['label' => 'Серия',
										'control' => 'series',
										'type' => 'text',
										'class' => $data['series_cls'],
										'required' => 'no',
										'value' => $data['series'],
										'success' => $data['series_scs'],
										'error' => $data['series_err']]);
		// numb
		echo Form_Helper::setFormInput(['label' => 'Номер',
										'control' => 'numb',
										'type' => 'text',
										'class' => $data['numb_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['numb'],
										'success' => $data['numb_scs'],
										'error' => $data['numb_err']]);
		// dt_issue
		echo Form_Helper::setFormInput(['label' => 'Дата выдачи',
										'control' => 'dt_issue',
										'type' => 'text',
										'class' => $data['dt_issue_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['dt_issue'],
										'success' => $data['dt_issue_scs'],
										'error' => $data['dt_issue_err']]);
		// unit_name
		echo Form_Helper::setFormInput(['label' => UNITNAME_PLC,
										'control' => 'unit_name',
										'type' => 'text',
										'class' => $data['unit_name_cls'],
										'required' => 'no',
										'placeholder' => UNITNAME_PLC,
										'value' => $data['unit_name'],
										'success' => $data['unit_name_scs'],
										'error' => $data['unit_name_err'],
										'help' => UNITNAME_HELP]);
		// unit_code
		echo Form_Helper::setFormInput(['label' => 'Код подразделения',
										'control' => 'unit_code',
										'type' => 'text',
										'class' => $data['unit_code_cls'],
										'required' => 'no',
										'value' => $data['unit_code'],
										'success' => $data['unit_code_scs'],
										'error' => $data['unit_code_err']]);
		// dt_end
		echo Form_Helper::setFormInput(['label' => 'Дата окончания действия',
										'control' => 'dt_end',
										'type' => 'text',
										'class' => $data['dt_end_cls'],
										'required' => 'no',
										'value' => $data['dt_end'],
										'success' => $data['dt_end_scs'],
										'error' => $data['dt_end_err']]);
		/* old passport */
		echo Form_Helper::setFormCheckbox(['label' => 'В случае несовпадения введённых данных и данных на момент сдачи ЕГЭ, рекомендуем указать дополнительные реквизиты старого документа, удостоверяющего личность',
												'control' => 'passport_old_yes',
												'class' => $data['passport_old_yes_cls'],
												'value' => $data['passport_old_yes'],
												'success' => $data['passport_old_yes_scs'],
												'error' => $data['passport_old_yes_err']]); ?>
		<br>
		<div class="form-group" id="passport_old_div">
			<?php
				echo HTML_Helper::setLabel('font-weight-bold font-italic', '', 'Старый документ, удостоверяющий личность');
				echo HTML_Helper::setAlert('Пожалуйста, при наличии старого паспорта, указывайте старые паспортные данные.', 'alert-warning');
				// type
				echo Form_Helper::setFormSelectListDB(['label' => 'Тип документа',
														'control' => 'passport_type_old',
														'class' => $data['passport_type_old_cls'],
														'required' => 'yes',
														'required_style' => 'StarUp',
														'model_class' => 'common\\models\\Model_DictDoctypes',
														'model_method' => 'getPassportsBsu',
														'model_field' => 'code',
														'model_field_name' => 'description',
														'value' => $data['passport_type_old'],
														'success' => $data['passport_type_old_scs'],
														'error' => $data['passport_type_old_err']]);
				// series
				echo Form_Helper::setFormInput(['label' => 'Серия',
												'control' => 'series_old',
												'type' => 'text',
												'class' => $data['series_old_cls'],
												'required' => 'no',
												'value' => $data['series_old'],
												'success' => $data['series_old_scs'],
												'error' => $data['series_old_err']]);
				// numb
				echo Form_Helper::setFormInput(['label' => 'Номер',
												'control' => 'numb_old',
												'type' => 'text',
												'class' => $data['numb_old_cls'],
												'required' => 'no',
												'value' => $data['numb_old'],
												'success' => $data['numb_old_scs'],
												'error' => $data['numb_old_err']]);
				// dt_issue
				echo Form_Helper::setFormInput(['label' => 'Дата выдачи',
												'control' => 'dt_issue_old',
												'type' => 'text',
												'class' => $data['dt_issue_old_cls'],
												'required' => 'no',
												'value' => $data['dt_issue_old'],
												'success' => $data['dt_issue_old_scs'],
												'error' => $data['dt_issue_old_err']]);
				// unit_name
				echo Form_Helper::setFormInput(['label' => UNITNAME_PLC,
												'control' => 'unit_name_old',
												'type' => 'text',
												'class' => $data['unit_name_old_cls'],
												'required' => 'no',
												'placeholder' => UNITNAME_PLC,
												'value' => $data['unit_name_old'],
												'success' => $data['unit_name_old_scs'],
												'error' => $data['unit_name_old_err'],
												'help' => UNITNAME_HELP]);
				// unit_code
				echo Form_Helper::setFormInput(['label' => 'Код подразделения',
												'control' => 'unit_code_old',
												'type' => 'text',
												'class' => $data['unit_code_old_cls'],
												'required' => 'no',
												'value' => $data['unit_code_old'],
												'success' => $data['unit_code_old_scs'],
												'error' => $data['unit_code_old_err']]);
				// dt_end
				echo Form_Helper::setFormInput(['label' => 'Дата окончания действия',
												'control' => 'dt_end_old',
												'type' => 'text',
												'class' => $data['dt_end_old_cls'],
												'required' => 'no',
												'value' => $data['dt_end_old'],
												'success' => $data['dt_end_old_scs'],
												'error' => $data['dt_end_old_err']]);
				// passport_old
				echo Form_Helper::setFormFile(['label' => 'Ранее выданные паспорта',
												'control' => 'passport_old',
												'required' => 'yes',
												'required_style' => 'StarUp',
												'data' => $data,
												'home_ctr' => RESUME['ctr'],
												'home_hdr' => RESUME['hdr'],
												'home_act' => 'Index',
												'ext' => FILES_EXT_SCANS]);
			?>
		</div>
		<?php
		/* addresses */
		/* registration address */
		echo Form_Helper::setFormHeaderSub('Адрес регистрации');
		// country (registration)
		echo Form_Helper::setFormSelectListDB(['label' => 'Страна',
												'control' => 'country_reg',
												'class' => $data['country_reg_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictCountries',
												'model_method' => 'getAll',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['country_reg'],
												'success' => $data['country_reg_scs'],
												'error' => $data['country_reg_err']]); ?>
		<div class="form-group" id="kladr_reg">
			<?php
				// region (registration)
				if (isset($data['country_reg']) && $data['country_reg'] == '643') {
					echo Form_Helper::setFormSelectListKladr(['label' => 'Регион',
															'control' => 'region_reg',
															'model_class' => 'common\\models\\Model_Kladr',
															'model_method' => 'getRegionAll',
															'value' => $data['region_reg']]);
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Регион', 'control' => 'region_reg']);
				}
				// area (registration)
				if (isset($data['area_reg']) && !empty($data['area_reg'])) {
					echo Form_Helper::setFormSelectListKladr(['label' => 'Район',
															'control' => 'area_reg',
															'model_class' => 'common\\models\\Model_Kladr',
															'model_method' => 'getAreaByRegion',
															'model_filter' => 'region',
															'model_filter_val' => $data['region_reg'],
															'value' => $data['area_reg']]);
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Район', 'control' => 'area_reg']);
				}
				// city (registration)
				if (isset($data['city_reg']) && !empty($data['city_reg'])) {
					if (isset($data['area_reg']) && !empty($data['area_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Город',
																'control' => 'city_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getCityByArea',
																'model_filter' => 'area',
																'model_filter_val' => $data['area_reg'],
																'value' => $data['city_reg']]);
					} else {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Город',
																'control' => 'city_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getCityByRegion',
																'model_filter' => 'region',
																'model_filter_val' => $data['region_reg'],
																'value' => $data['city_reg']]);
					}
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Город', 'control' => 'city_reg']);
				}
				// location (registration)
				if (isset($data['location_reg']) && !empty($data['location_reg'])) {
					if (isset($data['city_reg']) && !empty($data['city_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Населённый пункт',
																'control' => 'location_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getLocationByCity',
																'model_filter' => 'city',
																'model_filter_val' => $data['city_reg'],
																'value' => $data['location_reg']]);
					} elseif (isset($data['area_reg']) && !empty($data['area_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Населённый пункт',
																'control' => 'location_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getLocationByArea',
																'model_filter' => 'area',
																'model_filter_val' => $data['area_reg'],
																'value' => $data['location_reg']]);
					} elseif (isset($data['region_reg']) && !empty($data['region_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Населённый пункт',
																'control' => 'location_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getLocationByRegion',
																'model_filter' => 'region',
																'model_filter_val' => $data['region_reg'],
																'value' => $data['location_reg']]);
					} else {
						echo Form_Helper::setFormSelectListBlank(['label' => 'Населённый пункт', 'control' => 'location_reg']);
					}
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Населённый пункт', 'control' => 'location_reg']);
				}
				// street (registration)
				if (isset($data['street_reg']) && !empty($data['street_reg'])) {
					if (isset($data['location_reg']) && !empty($data['location_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Улица',
																'control' => 'street_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getStreetByLocation',
																'model_filter' => 'location',
																'model_filter_val' => $data['location_reg'],
																'value' => $data['street_reg']]);
					} elseif (isset($data['city_reg']) && !empty($data['city_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Улица',
																'control' => 'street_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getStreetByCity',
																'model_filter' => 'city',
																'model_filter_val' => $data['city_reg'],
																'value' => $data['street_reg']]);
					} elseif (isset($data['region_reg']) && !empty($data['region_reg'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Улица',
																'control' => 'street_reg',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getStreetByRegion',
																'model_filter' => 'region',
																'model_filter_val' => $data['region_reg'],
																'value' => $data['street_reg']]);
					} else {
						echo Form_Helper::setFormSelectListBlank(['label' => 'Улица', 'control' => 'street_reg']);
					}
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Улица', 'control' => 'street_reg']);
				}
				// house (registration)
				echo Form_Helper::setFormInputText(['label' => 'Дом',
													'control' => 'house_reg',
													'value' => $data['house_reg']]
													);
				// building (registration)
				echo Form_Helper::setFormInputText(['label' => 'Корпус',
													'control' => 'building_reg',
													'value' => $data['building_reg']]
													);
				// flat (registration)
				echo Form_Helper::setFormInputText(['label' => 'Квартира',
													'control' => 'flat_reg',
													'value' => $data['flat_reg']]
													);
				// postcode (registration)
				echo Form_Helper::setFormInputText(['label' => 'Индекс',
													'control' => 'postcode_reg',
													'value' => $data['postcode_reg']]
													);
			?>
		</div>
		<div class="form-check">
			<div class="col">
				<input type="checkbox" class="form-check-input" id="kladr_reg_not" name="kladr_reg_not">
				<label class="font-weight-bold" for="kladr_reg_not">Не нашёл адрес регистрации в КЛАДРе</label>
			</div>
		</div><br>
		<div class="form-check">
			<div class="col">
				<input type="checkbox" class="form-check-input" id="homeless_reg" name="homeless_reg">
				<label class="font-weight-bold" for="homeless_reg">Не имею адреса регистрации</label>
			</div>
		</div><br>
		<?php
		// address string (registration)
		echo Form_Helper::setFormInput(['label' => ADRREG['name'],
										'control' => 'address_reg',
										'type' => 'text',
										'class' => $data['address_reg_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => ADRREG['plc'],
										'value' => $data['address_reg'],
										'success' => $data['address_reg_scs'],
										'error' => $data['address_reg_err'],
										'help' => ADRREG['help']]);

		/* residential address */
		echo Form_Helper::setFormHeaderSub('Адрес проживания'); ?>
		<div class="form-check">
			<div class="col">
				<input type="checkbox" class="form-check-input" id="address_reg_clone_flag" name="address_reg_clone_flag">
				<label class="font-weight-bold" for="address_reg_clone_flag">Адрес проживания совпадает с адресом регистрации</label>
			</div>
		</div><br>
		<?php
		// country (residential)
		echo Form_Helper::setFormSelectListDB(['label' => 'Страна',
												'control' => 'country_res',
												'class' => $data['country_res_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictCountries',
												'model_method' => 'getAll',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['country_res'],
												'success' => $data['country_res_scs'],
												'error' => $data['country_res_err']]); ?>
		<div class="form-group" id="kladr_res">
			<?php
				// region (residential)
				if (isset($data['country_res']) && $data['country_res'] == '643') {
					echo Form_Helper::setFormSelectListKladr(['label' => 'Регион',
															'control' => 'region_res',
															'model_class' => 'common\\models\\Model_Kladr',
															'model_method' => 'getRegionAll',
															'value' => $data['region_res']]);
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Регион', 'control' => 'region_res']);
				}
				// area (residential)
				if (isset($data['area_res']) && !empty($data['area_res'])) {
					echo Form_Helper::setFormSelectListKladr(['label' => 'Район',
															'control' => 'area_res',
															'model_class' => 'common\\models\\Model_Kladr',
															'model_method' => 'getAreaByRegion',
															'model_filter' => 'region',
															'model_filter_val' => $data['region_res'],
															'value' => $data['area_res']]);
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Район', 'control' => 'area_res']);
				}
				// city (residential)
				if (isset($data['city_res']) && !empty($data['city_res'])) {
					if (isset($data['area_res']) && !empty($data['area_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Город',
																'control' => 'city_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getCityByArea',
																'model_filter' => 'area',
																'model_filter_val' => $data['area_res'],
																'value' => $data['city_res']]);
					} else {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Город',
																'control' => 'city_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getCityByRegion',
																'model_filter' => 'region',
																'model_filter_val' => $data['region_res'],
																'value' => $data['city_res']]);
					}
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Город', 'control' => 'city_res']);
				}
				// location (residential)
				if (isset($data['location_res']) && !empty($data['location_res'])) {
					if (isset($data['city_res']) && !empty($data['city_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Населённый пункт',
																'control' => 'location_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getLocationByCity',
																'model_filter' => 'city',
																'model_filter_val' => $data['city_res'],
																'value' => $data['location_res']]);
					} elseif (isset($data['area_res']) && !empty($data['area_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Населённый пункт',
																'control' => 'location_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getLocationByArea',
																'model_filter' => 'area',
																'model_filter_val' => $data['area_res'],
																'value' => $data['location_res']]);
					} elseif (isset($data['region_res']) && !empty($data['region_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Населённый пункт',
																'control' => 'location_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getLocationByRegion',
																'model_filter' => 'region',
																'model_filter_val' => $data['region_res'],
																'value' => $data['location_res']]);
					} else {
						echo Form_Helper::setFormSelectListBlank(['label' => 'Населённый пункт', 'control' => 'location_res']);
					}
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Населённый пункт', 'control' => 'location_res']);
				}
				// street (residential)
				if (isset($data['street_res']) && !empty($data['street_res'])) {
					if (isset($data['location_res']) && !empty($data['location_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Улица',
																'control' => 'street_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getStreetByLocation',
																'model_filter' => 'location',
																'model_filter_val' => $data['location_res'],
																'value' => $data['street_res']]);
					} elseif (isset($data['city_res']) && !empty($data['city_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Улица',
																'control' => 'street_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getStreetByCity',
																'model_filter' => 'city',
																'model_filter_val' => $data['city_res'],
																'value' => $data['street_res']]);
					} elseif (isset($data['region_res']) && !empty($data['region_res'])) {
						echo Form_Helper::setFormSelectListKladr(['label' => 'Улица',
																'control' => 'street_res',
																'model_class' => 'common\\models\\Model_Kladr',
																'model_method' => 'getStreetByRegion',
																'model_filter' => 'region',
																'model_filter_val' => $data['region_res'],
																'value' => $data['street_res']]);
					} else {
						echo Form_Helper::setFormSelectListBlank(['label' => 'Улица', 'control' => 'street_res']);
					}
				} else {
					echo Form_Helper::setFormSelectListBlank(['label' => 'Улица', 'control' => 'street_res']);
				}
				// house (residential)
				echo Form_Helper::setFormInputText(['label' => 'Дом',
													'control' => 'house_res',
													'value' => $data['house_res']]
													);
				// building (residential)
				echo Form_Helper::setFormInputText(['label' => 'Корпус',
													'control' => 'building_res',
													'value' => $data['building_res']]
													);
				// flat (residential)
				echo Form_Helper::setFormInputText(['label' => 'Квартира',
													'control' => 'flat_res',
													'value' => $data['flat_res']]
													);
				// postcode (residential)
				echo Form_Helper::setFormInputText(['label' => 'Индекс',
													'control' => 'postcode_res',
													'value' => $data['postcode_res']]
													);
			?>
		</div>
		<div class="form-check">
			<div class="col">
				<input type="checkbox" class="form-check-input" id="kladr_res_not" name="kladr_reg_not">
				<label class="font-weight-bold" for="kladr_res_not">Не нашёл адрес проживания в КЛАДРе</label>
			</div>
		</div><br>
		<div class="form-check">
			<div class="col">
				<input type="checkbox" class="form-check-input" id="homeless_res" name="homeless_res">
				<label class="font-weight-bold" for="homeless_res">Не имею адреса проживания</label>
			</div>
		</div><br>
		<?php
		// address string (residential)
		echo Form_Helper::setFormInput(['label' => ADRRES['name'],
										'control' => 'address_res',
										'type' => 'text',
										'class' => $data['address_res_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => ADRRES['plc'],
										'value' => $data['address_res'],
										'success' => $data['address_res_scs'],
										'error' => $data['address_res_err'],
										'help' => ADRRES['help']]);
		/* foreign languages */
		echo Form_Helper::setFormHeaderSub('Знание иностранных языков');
		echo HTML_Helper::setButton(null, 'btn_lang_add', 'Добавить иностранный язык');
		echo '<p></p>';
		$i = 1;
		foreach (array_filter($data, function ($var)
							        {
							            return(substr($var, 0, 4) == 'lang');
							        }, ARRAY_FILTER_USE_KEY) as $key => $value)
        {
			$lang = new DictForeignLangs();
			$lang_arr = $lang->getAll();
			if ($lang_arr) {
				echo '<div class="form-group row" id="div_lang'.$i.'">';
				echo '<div class="col col-sm-3">';
				echo '<label class="font-weight-bold" for="lang'.$i.'">Иностранный язык №'.$i.'</label>';
				echo '</div>';
				echo '<div class="col col-sm-9">';
				echo '<select class="form-control" id="lang'.$i.'" name="lang'.$i.'">';
				foreach ($lang_arr as $lang_row) {
					echo '<option value="'.$lang_row['code'].'"'.
						(($data['lang'.$i] === $lang_row['code']) ? ' selected' : '').'>'.
						$lang_row['description'].
						'</option>';
				}
				echo '</select>';
				echo '</div>';
				echo '</div>';
			} else {
				echo 'Ошибка справочника иностранных языков!';
			}
			$i++;
		}
		echo HTML_Helper::setButton(null, 'btn_lang_remove', 'Удалить иностранный язык');
		/* scans */
		echo Form_Helper::setFormHeaderSub('Скан-копии');
		echo Form_Helper::setFormFileListDB(['required' => 'required',
											'required_style' => 'StarUp',
											'model_class' => 'common\\models\\Model_DictScans',
											'model_method' => 'getByDocument',
											'model_filter' => 'doc_code',
											'model_filter_var' => 'resume',
											'model_field' => 'scan_code',
											'model_field_name' => 'scan_name',
											'data' => $data,
											'home_ctr' => RESUME['ctr'],
											'home_hdr' => RESUME['hdr'],
											'home_act' => 'Index',
											'ext' => FILES_EXT_SCANS]);
		// personal
		if ($data['personal_vis'] == true) {
			echo Form_Helper::setFormHeaderSub('Согласие');
			echo Form_Helper::setFormCheckbox(['label' => 'Я прочитал и принимаю <a href="" data-toggle="modal" data-target="#personalText">&laquo;Согласие на обработку персональных данных&raquo;</a>',
												'control' => 'personal',
												'class' => $data['personal_cls'],
												'value' => $data['personal'],
												'success' => $data['personal_scs'],
												'error' => $data['personal_err']]);
		} ?>
		<!-- controls -->
		<br>
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-info', 'btn_save', 'Сохранить', 'Сохраняет данные анкеты');
					echo HTML_Helper::setHrefButton(RESUME['ctr'], 'Send', 'btn btn-success', 'Отправить', 'Отправляет данные анкеты на проверку модератору');
					echo HTML_Helper::setHrefButton(RESUME['ctr'], 'Recall', 'btn btn-warning', 'Отозвать', 'Отзывает данные анкеты');
					echo HTML_Helper::setHrefButton(RESUME['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Сбрасывает данные анкеты');
					echo HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную');
				?>
			</div>
		</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>

<div class="modal fade" id="personalText" tabindex="-1" role="dialog" aria-labelledby="pdModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Согласие на обработку персональных данных</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body text-justify">
				<p>
					Пользователь, регистрируясь на данном сайте, принимает Согласие на обработку персональных данных (далее – Согласие). Принятием (акцептом) оферты настоящего Согласия является регистрация Пользователя на Сайте. Пользователь даёт своё согласие УНИВЕРСИТЕТУ, на обработку своих персональных данных со следующими условиями:
				</p>
				<ol>
					<li>
						<p>
							Согласие даётся на обработку следующих моих персональных данных, необходимых в связи с поступлением в УНИВЕРСИТЕТ и в целях содействия в осуществлении учебной, научной деятельности, обеспечения личной безопасности, учета результатов  исполнения  договорных  обязательств, пользования  предусмотренными законодательством РФ льготами, а также наиболее полного исполнения ОПЕРАТОРОМ обязательств и компетенций в соответствии с законодательством РФ:
						</p>
						<ul>
							<li>фамилия, имя, отчество, а также фамилия, имя, отчество – в случае их изменения, дата их изменения;</li>
							<li>гражданство;</li>
							<li>дата и место рождения, пол, паспортные и биографические данные;</li>
							<li>адрес регистрации и проживания, номера телефонов, адрес электронной почты;</li>
							<li>семейное и социальное положение;</li>
							<li>уровень образования, профессия, квалификация, стаж работы, должности и места трудовой деятельности и обучения, характеристики, аттестации, резюме;</li>
							<li>сведения, содержащиеся в документах медицинского освидетельствования;</li>
							<li>сведения о воинском учете;</li>
							<li>сведения о социальных льготах, о назначении и получении стипендий и других выплат;</li>
							<li>идентификационный номер налогоплательщика;</li>
							<li>номер страхового свидетельства обязательного пенсионного страхования, свидетельства о постановке на учет в налоговом органе;</li>
							<li>сведения, создаваемые и получаемые ОПЕРАТОРОМ в период поступления и обучения СУБЪЕКТА в УНИВЕРСИТЕТ, содержащиеся в личных делах, приказах, выписках из них, трудовых договорах, заявлениях, материалах служебных расследований, проверок и других документах.</li>
						</ul>
						<p>
							Под обработкой персональных данных следует понимать любое действие (операцию) или совокупность действий (операций), совершаемых с использованием средств автоматизации или без использования таких средств с персональными данными, включая  сбор, запись, систематизацию, накопление, хранение, уточнение (обновление, изменение), извлечение, использование, обезличивание, блокирование, удаление, уничтожение персональных данных, а также передачу (распространение, предоставление, доступ). Передачу моих персональных данных осуществлять только с моего письменного согласия или на основании действующего законодательства РФ.
						</p>
					</li>
					<li>
						<p>
							Согласие даётся на опубликование в общедоступных источниках в целях обеспечения процесса организационно-учебной,  научной,  производственной  деятельности  и  оперативности доведения до СУБЪЕКТА сведений, связанных с его поступлением и обучением в УНИВЕРСИТЕТЕТЕ в общедоступных  источниках  (сайтах,  справочниках,  досках  объявлений,  приказах, распоряжениях, других документах) следующих моих персональных данных:
						</p>
						<ul>
							<li>фамилия, имя, отчество;</li>
							<li>дата рождения;</li>
							<li>институт (филиал), факультет (отделение), номер учебной группы обучения;</li>
							<li>форма  обучения,  направление  подготовки,  специальность,  образовательная программа;</li>
							<li>номер студенческого билета (зачетной книжки);</li>
							<li>контактный телефон и адрес электронной почты;</li>
							<li>сведения  о  результатах  вступительных  испытаний,  текущей  успеваемости  и промежуточной  аттестации,  предоставлении  академического  отпуска,  отпуска  по  беременности и родам, переводе, мерах дисциплинарного взыскания;</li>
							<li>рейтинговые данные;</li>
							<li>сведения об участии в совете обучающихся, иных органах управления;</li>
							<li>сведения о результатах в области науки, творчества, физической культуры, спорта;</li>
							<li>сведения о стипендиях и иных выплатах, государственных, общественных наградах;</li>
							<li>сведения  об  участии  в  конкурсах,  соревнованиях,  фестивалях,  конференциях, форумах и т.п.</li>
						</ul>
					</li>
					<li>
						<p>
							Согласие даётся на передачу третьим лицам сведений о документе об образовании, выданном УНИВЕРСИТЕТОМ, факте обучения в УНИВЕРСИТЕТЕ, периоде обучения, результатах  обучения, направлении подготовки, специальности (в целях проверки сведений о моём образовании). Согласие на обработку персональных данных действительно в течение проведения приёмной компании, а в случае приёма в УНИВЕРСИТЕТ – бессрочно.
						</p>
					</li>
					<p>
						Я уведомлен(а) о своём праве отозвать согласие путём подачи ОПЕРАТОРУ письменного заявления. Подтверждаю, что я ознакомлен(а) с Федеральным законом от 27.07.2006 No152-ФЗ "О персональных данных", с Положением о порядке обработки и защите персональных данных работников и обучающихся УНИВЕРСИТЕТА, с моими правами и обязанностями в области защиты персональных данных, в том числе с возможными последствиями в случае моего отказа от согласия на обработку персональных данных.
					</p>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="helpResume" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Анкета (инструкция)</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-justify">
				<?php echo Help_Helper::resume_help(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		formInit();
		formEvents();
	});
</script>

<script>
	// form init
	function formInit()
	{
		// agreement
		if (getAge($('#birth_dt').val()) < 18) {
			$('#agreement_div').show();
		} else {
			$('#agreement_div').hide();
		}
		// citizenship
		if ($('#citizenship').val() == '000') {
			$('#citizenship').prop('disabled', true);
		} else {
			$('#citizenship').prop('disabled', false);
		}
		setCitizenship($('#citizenship').val());
		// passport
		setPassport();
		// old passport yes
		if ($('#passport_old_yes').prop('checked')) {
			$('#passport_old_div').show();
		} else {
			$('#passport_old_div').hide();
		}
		// old passport
		setPassportOld();
		// address registration
		var country_reg = $('#country_reg').val();
		if (country_reg == '') {
			$('#kladr_reg').hide();
			$('#address_reg').prop('disabled', true);
			$('#address_reg_clone').hide();
		} else {
			switch (country_reg) {
				case '000':
					$('#kladr_reg').hide();
					$('#homeless_reg').prop('checked', true);
					$('#address_reg').prop('disabled', true);
					break;
				case '643':
					$('#kladr_reg').show();
					$('#homeless_reg').prop('checked', false);
					$('#address_reg').prop('disabled', true);
					break;
				default:
					$('#kladr_reg').hide();
					$('#homeless_reg').prop('checked', false);
					$('#address_reg').prop('disabled', false);
					$('#address_reg_clone').show();
			}
		}
		if ($('#region_reg').val() == '' && jQuery.isEmptyObject($('#area_reg').val()) && jQuery.isEmptyObject($('#city_reg').val()) && jQuery.isEmptyObject($('#location_reg').val()) && jQuery.isEmptyObject($('#street_reg').val()) && $('#house_reg').val() == '' && $('#building_reg').val() == '' && $('#flat_reg').val() == '' && $('#postcode_reg').val() == '' && $('#address_reg').val() != '') {
			$('#kladr_reg_not').prop('checked', true);
		} else {
			$('#kladr_reg_not').prop('checked', false);
		}
		if ($('#kladr_reg_not').prop('checked')) {
			$('#kladr_reg').hide();
			$('#address_reg').prop('disabled', false);
		} else {
			$('#kladr_reg').show();
			$('#address_reg').prop('disabled', true);
		}
		// address residential
		var country_res = $('#country_res').val();
		if (country_res == '') {
			$('#kladr_res').hide();
			$('#address_res').prop('disabled', true);
		} else {
			switch (country_res) {
				case '000':
					$('#kladr_res').hide();
					$('#homeless_res').prop('checked', true);
					$('#address_res').prop('disabled', true);
					break;
				case '643':
					$('#kladr_res').show();
					$('#homeless_res').prop('checked', false);
					$('#address_res').prop('disabled', true);
					break;
				default:
					$('#kladr_res').hide();
					$('#homeless_res').prop('checked', false);
					$('#address_res').prop('disabled', false);
					$('#address_res_clone').show();
			}
		}
		if ($('#region_res').val() == '' && jQuery.isEmptyObject($('#area_res').val()) && jQuery.isEmptyObject($('#city_res').val()) && jQuery.isEmptyObject($('#location_res').val()) && jQuery.isEmptyObject($('#street_res').val()) && $('#house_res').val() == '' && $('#building_res').val() == '' && $('#flat_res').val() == '' && $('#postcode_res').val() == '' && $('#address_res').val() != '') {
			$('#kladr_res_not').prop('checked', true);
		} else {
			$('#kladr_res_not').prop('checked', false);
		}
		if ($('#kladr_res_not').prop('checked')) {
			$('#kladr_res').hide();
			$('#address_res').prop('disabled', false);
		} else {
			$('#kladr_res').show();
			$('#address_res').prop('disabled', true);
		}
		// address registration clone
		if ($('#address_reg').val() != '' && $('#address_reg').val() == $('#address_res').val()) {
			$('#address_reg_clone_flag').prop('checked', true);
			CountryResHide();
			AddressResHide();
		} else {
			$('#address_reg_clone_flag').prop('checked', false);
			CountryResShow();
			if ($('#address_res').val() == '') {
				AddressResHide();
			} else {
				AddressResShow();
			}
		}
		// KLADR res
		if ($('#address_reg').val() != '' && $('#address_res').val() == '' && !$('#address_reg_clone_flag').prop('checked')) {
			cloneAddressRegistration();
		}
	}
</script>

<script>
	// form events
	function formEvents()
	{
		// agreement
		$('#birth_dt').change(function() {
			if (getAge($('#birth_dt').val()) < 18) {
				$('#agreement_div').show();
			} else {
				$('#agreement_div').hide();
			}
		});
		// citizenship
		$('#citizenship').change(function() {
			setCitizenship($('#citizenship').val());
			$('#passport_type').val('');
			unsetPassport();
			setPassport();
		});
		// no citizenship
		$('#citizenship_not').change(function() {
			if ($('#citizenship_not').prop('checked')) {
				$('#citizenship').val('000');
				$('#citizenship').prop('disabled', true);
			} else {
				$('#citizenship').prop('disabled', false);
				if ($('#citizenship').val() == '000') {
					$('#citizenship').val('');
				}
			}
			setCitizenship($('#citizenship').val());
			$('#passport_type').val('');
			unsetPassport();
			setPassport();
		});
		// passport
		$('#passport_type').change(function() {
			$('#series').val('');
			$('#numb').val('');
			$('#dt_issue').val('');
			$('#unit_name').val('');
			$('#unit_code').val('');
			$('#dt_end').val('');
			unsetPassport();
			setPassport();
		});
		// old passport yes
		$('#passport_old_yes').change(function() {
			if ($('#passport_old_yes').prop('checked')) {
				$('#passport_old_div').show();
			} else {
				$('#passport_old_div').hide();
			}
		});
		// old passport
		$('#passport_type_old').change(function() {
			$('#series_old').val('');
			$('#numb_old').val('');
			$('#dt_issue_old').val('');
			$('#unit_name_old').val('');
			$('#unit_code_old').val('');
			$('#dt_end_old').val('');
			setPassportOld();
		});

		// contry_reg change
		$('#country_reg').change(function() {
			var country_reg = $('#country_reg').val();
			var country_reg_name = $('#country_reg :selected').text();
			if (country_reg == '643') {
				// MOTHER LAND
				$('#kladr_reg').show();
				$('#house_reg').prop('disabled', true);
				$('#house_reg').val('');
				$('#building_reg').prop('disabled', true);
				$('#building_reg').val('');
				$('#flat_reg').prop('disabled', true);
				$('#flat_reg').val('');
				$('#postcode_reg').prop('disabled', true);
				$('#postcode_reg').val('');
				$('#address_reg').prop('disabled', true);
				$('#address_reg').val('');

				getKladrAJAX('/frontend/Kladr/RegionAllJSON', null, '#region_reg');

			    $('#area_reg').empty();
			    $('#city_reg').empty();
			    $('#location_reg').empty();
			    $('#street_reg').empty();
			} else {
				// FOREIGN LAND
				$('#kladr_reg').hide();
				$('#address_reg').prop('disabled', false);
				$('#address_reg').val('');
			}
			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// region_reg change
		$('#region_reg').change(function() {
			var region_reg = $('#region_reg').val();
			var region_reg_name = $('#region_reg :selected').text();

			getKladrAJAX('/frontend/Kladr/AreaByRegionJSON', region_reg, '#area_reg');
			getKladrAJAX('/frontend/Kladr/CityByRegionJSON', region_reg, '#city_reg');
			getKladrAJAX('/frontend/Kladr/LocationByRegionJSON', region_reg, '#location_reg');
			getKladrAJAX('/frontend/Kladr/StreetByRegionJSON', region_reg, '#street_reg');

		    $('#location_reg').empty();
		    $('#street_reg').empty();
		    $('#house_reg').val('');
		    $('#building_reg').val('');
		    $('#flat_reg').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region_reg, '#postcode_reg');
		    var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
		    $('#address_reg').val(postcode_reg + region_reg_name);

		    $('#address_reg_clone').show();
		    if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
				CountryResShow();
				AddressResShow();
			}
		    if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// area_reg change
		$('#area_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg = $('#area_reg').val();
			var area_reg_name = $('#area_reg :selected').text();

			getKladrAJAX('/frontend/Kladr/CityByAreaJSON', area_reg, '#city_reg');
			getKladrAJAX('/frontend/Kladr/LocationByAreaJSON', area_reg, '#location_reg');

			$('#street_reg').empty();
		    $('#house_reg').val('');
		    $('#building_reg').val('');
		    $('#flat_reg').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area_reg, '#postcode_reg');
		    var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
		    $('#address_reg').val(postcode_reg + region_reg_name + ', ' + area_reg_name);

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// city_reg change
		$('#city_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var city_reg = $('#city_reg').val();
			var city_reg_name = $('#city_reg :selected').text();

			$('#location_reg').empty();

			getKladrAJAX('/frontend/Kladr/LocationByCityJSON', city_reg, '#location_reg');
			getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city_reg, '#street_reg');

			$('#house_reg').val('');
		    $('#building_reg').val('');
		    $('#flat_reg').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', city_reg, '#postcode_reg');
		    var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
			$('#address_reg').val(postcode_reg + region_reg_name + ', ' + city_reg_name);

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// location_reg change
		$('#location_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg_name = $('#area_reg :selected').text();
			var city_reg_name = $('#city_reg :selected').text();
			var location_reg = $('#location_reg').val();
			var location_reg_name = $('#location_reg :selected').text();

			getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', location_reg, '#street_reg');

			$('#house_reg').val('');
		    $('#building_reg').val('');
		    $('#flat_reg').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', location_reg, '#postcode_reg');
		    var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
			if (city_reg_name == '') {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + area_reg_name + ', ' + location_reg_name);
			} else {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + city_reg_name + ', ' + location_reg_name);
			}

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// street_reg change
		$('#street_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg_name = $('#area_reg :selected').text();
			var city_reg_name = $('#city_reg :selected').text();
			var location_reg_name = $('#location_reg :selected').text();
			var street_reg = $('#street_reg').val();
			var street_reg_name = $('#street_reg :selected').text();

			$('#house_reg').prop('disabled', false);
			$('#house_reg').val('');
		    $('#building_reg').prop('disabled', false);
		    $('#building_reg').val('');
			getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', street_reg, '#postcode_reg');
		    var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
			if (city_reg_name == '') {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + area_reg_name + ', ' + location_reg_name + ', ' + street_reg_name);
			} else {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + city_reg_name + ', ' + street_reg_name);
			}

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// house_reg change
		$('#house_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg_name = $('#area_reg :selected').text();
			var city_reg_name = $('#city_reg :selected').text();
			var location_reg_name = $('#location_reg :selected').text();
			var street_reg_name = $('#street_reg :selected').text();
			var house_reg = $('#house_reg').val();

			$('#flat_reg').prop('disabled', false);
			$('#flat_reg').val('');
			var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
			if (city_reg_name == '') {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + area_reg_name + ', ' + location_reg_name + ', ' + street_reg_name + ', дом ' + house_reg);
			} else {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + city_reg_name + ', ' + street_reg_name + ', дом ' + house_reg);
			}

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// building_reg change
		$('#building_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg_name = $('#area_reg :selected').text();
			var city_reg_name = $('#city_reg :selected').text();
			var location_reg_name = $('#location_reg :selected').text();
			var street_reg_name = $('#street_reg :selected').text();
			var house_reg = $('#house_reg').val();
			var building_reg = $('#building_reg').val();

			$('#flat_reg').prop('disabled', false);
			$('#flat_reg').val('');
			var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
			if (city_reg_name == '') {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + area_reg_name + ', ' + location_reg_name + ', ' + street_reg_name + ', дом ' + house_reg + ', корпус ' + building_reg);
			} else {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + city_reg_name + ', ' + street_reg_name + ', дом ' + house_reg + ', корпус ' + building_reg);
			}

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// flat_reg change
		$('#flat_reg').change(function() {
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg_name = $('#area_reg :selected').text();
			var city_reg_name = $('#city_reg :selected').text();
			var location_reg_name = $('#location_reg :selected').text();
			var street_reg_name = $('#street_reg :selected').text();
			var house_reg = $('#house_reg').val();
			var building_reg = $('#building_reg').val();
			var flat_reg = $('#flat_reg').val();
			var postcode_reg = $('#postcode_reg').val();
		    if (postcode_reg != '') {
				postcode_reg = postcode_reg + ', ';
			}
			if (city_reg_name == '') {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + area_reg_name + ', ' + location_reg_name + ', ' + street_reg_name + ', дом ' + house_reg + ', корпус ' + building_reg + ', квартира ' + flat_reg);
			} else {
				$('#address_reg').val(postcode_reg + region_reg_name + ', ' + city_reg_name + ', ' + street_reg_name + ', дом ' + house_reg + ', корпус ' + building_reg + ', квартира ' + flat_reg);
			}

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// postcode_reg change
		$('#postcode_reg').change(function() {
			var address_reg;
			var region_reg_name = $('#region_reg :selected').text();
			var area_reg_name = $('#area_reg :selected').text();
			var city_reg_name = $('#city_reg :selected').text();
			var location_reg_name = $('#location_reg :selected').text();
			var street_reg_name = $('#street_reg :selected').text();
			var house_reg = $('#house_reg').val();
			var building_reg = $('#building_reg').val();
			var flat_reg = $('#flat_reg').val();
			var postcode_reg = $('#postcode_reg').val();
			if (postcode_reg != '') {
				address_reg = postcode_reg + ', ';
			} else {
				address_reg = '';
			}
			// region, area, city, location, street
			if (city_reg_name == '') {
				address_reg = address_reg + region_reg_name + ', ' + area_reg_name + ', ' + location_reg_name + ', ' + street_reg_name;
			} else {
				address_reg = address_reg + region_reg_name + ', ' + city_reg_name + ', ' + street_reg_name;
			}
			// house
			if (house_reg != '') {
				address_reg = address_reg + ', дом ' + house_reg;
			}
			// building
			if (building_reg != '') {
				address_reg = address_reg + ', корпус ' + building_reg;
			}
			// flat
			if (flat_reg != '') {
				address_reg = address_reg + ', квартира ' + flat_reg;
			}

			$('#address_reg').val(address_reg);

			$('#address_reg_clone').show();
			if ($('#address_res').val() != '') {
				$('#address_reg_clone_flag').prop('checked', false);
			}
			if ($('#address_reg_clone_flag').prop('checked')) {
				cloneAddressRegistration();
			}
		});

		// kladr_reg not found
		$('#kladr_reg_not').change(function() {
			$('#homeless_reg').prop('checked', false)
			if ($('#kladr_reg_not').prop('checked')) {
				$('#kladr_reg').hide();
				$('#address_reg').val('');
				$('#address_reg').prop('disabled', false);
			} else {
				$('#kladr_reg').show();
				$('#address_reg').val('');
				$('#address_reg').prop('disabled', true);
			}
		});

		// homeless_reg
		$('#homeless_reg').change(function() {
			$('#kladr_reg_not').prop('checked', false)
			$('#address_reg').prop('disabled', true);
			if ($('#homeless_reg').prop('checked')) {
				$('#country_reg').val('000');
				$('#kladr_reg').hide();
				$('#address_reg').val('Не имею адреса регистрации.');
			} else {
				$('#country_reg').val('');
				$('#kladr_reg').hide();
				$('#address_reg').val('');
			}
		});

		// address_reg_clone
		$('#address_reg_clone_flag').change(function() {
			if ($('#address_reg_clone_flag').prop('checked')) {
				// clone registration address
				cloneAddressRegistration();
			} else {
				// clear residential address
				CountryResShow();
				AddressResShow();
				$('#country_res').val('');
				$('#region_res').empty();
				$('#area_res').empty();
				$('#city_res').empty();
				$('#location_res').empty();
				$('#street_res').empty();
				$('#house_res').val('');
				$('#building_res').val('');
				$('#flat_res').val('');
				$('#postcode_res').val('');
				$('#address_res').val('');
				$('#address_res').prop('disabled', false);
			}
		});

		// contry_res change
		$('#country_res').change(function() {
			var country_res = $('#country_res').val();
			var country_res_name = $('#country_res :selected').text();
			if (country_res == '643') {
				// MOTHER LAND
				$('#kladr_res').show();
				$('#house_res').prop('disabled', true);
				$('#house_res').val('');
				$('#building_res').prop('disabled', true);
				$('#building_res').val('');
				$('#flat_res').prop('disabled', true);
				$('#flat_res').val('');
				$('#postcode_res').prop('disabled', true);
				$('#postcode_res').val('');
				$('#address_res').prop('disabled', true);
				$('#address_res').val('');

				getKladrAJAX('/frontend/Kladr/RegionAllJSON', null, '#region_res');

			    $('#area_res').empty();
			    $('#city_res').empty();
			    $('#location_res').empty();
			    $('#street_res').empty();
			} else {
				// FOREIGN LAND
				$('#kladr_res').hide();
				$('#address_res').prop('disabled', false);
				$('#address_res').val('');
			}
			$('#address_reg_clone').show();
			$('#address_reg_clone_flag').prop('checked', false);
		});

		// region_res change
		$('#region_res').change(function() {
			var region_res = $('#region_res').val();
			var region_res_name = $('#region_res :selected').text();

			getKladrAJAX('/frontend/Kladr/AreaByRegionJSON', region_res, '#area_res');
		    getKladrAJAX('/frontend/Kladr/CityByRegionJSON', region_res, '#city_res');
		    getKladrAJAX('/frontend/Kladr/LocationByRegionJSON', region_res, '#location_res');
			getKladrAJAX('/frontend/Kladr/StreetByRegionJSON', region_res, '#street_res');

		    $('#location_res').empty();
		    $('#street_res').empty();
		    $('#house_res').val('');
		    $('#building_res').val('');
		    $('#flat_res').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', region_res, '#postcode_res');
		    var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			$('#address_res').val(postcode_res + region_res_name);

		    $('#address_reg_clone_flag').prop('checked', false);
		});

		// area_res change
		$('#area_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var area_res = $('#area_res').val();
			var area_res_name = $('#area_res :selected').text();

			getKladrAJAX('/frontend/Kladr/CityByAreaJSON', area_res, '#city_res');
			getKladrAJAX('/frontend/Kladr/LocationByAreaJSON', area_res, '#location_res');

			$('#street_res').empty();
		    $('#house_res').val('');
		    $('#building_res').val('');
		    $('#flat_res').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', area_res, '#postcode_res');
		    var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			$('#address_res').val(postcode_res + region_res_name + ', ' + area_res_name);

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// city_res change
		$('#city_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var city_res = $('#city_res').val();
			var city_res_name = $('#city_res :selected').text();

			$('#location_res').empty();

			getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city_res, '#street_res');

			$('#house_res').val('');
		    $('#building_res').val('');
		    $('#flat_res').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', city_res, '#postcode_res');
		    var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			$('#address_res').val(postcode_res + region_res_name + ', ' + city_res_name);

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// location_res change
		$('#location_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var area_res_name = $('#area_res :selected').text();
			var city_res_name = $('#city_res :selected').text();
			var location_res = $('#location_res').val();
			var location_res_name = $('#location_res :selected').text();

			getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', location_res, '#street_res');

			$('#house_res').val('');
		    $('#building_res').val('');
		    $('#flat_res').val('');
		    getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', location_res, '#postcode_res');
		    var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			if (city_res_name == '') {
				$('#address_res').val(postcode_res + region_res_name + ', ' + area_res_name + ', ' + location_res_name);
			} else {
				$('#address_res').val(postcode_res + region_res_name + ', ' + city_res_name + ', ' + location_res_name);
			}

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// street_res change
		$('#street_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var area_res_name = $('#area_res :selected').text();
			var city_res_name = $('#city_res :selected').text();
			var location_res_name = $('#location_res :selected').text();
			var street_res = $('#street_res').val();
			var street_res_name = $('#street_res :selected').text();

			$('#house_res').prop('disabled', false);
			$('#house_res').val('');
			$('#building_res').prop('disabled', false);
			$('#building_res').val('');
			getPostcodeAJAX('/frontend/Kladr/PostcodeByCodeJSON', street_res, '#postcode_res');
		    var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			if (city_res_name == '') {
				$('#address_res').val(postcode_res + region_res_name + ', ' + area_res_name + ', ' + location_res_name + ', ' + street_res_name);
			} else {
				$('#address_res').val(postcode_res + region_res_name + ', ' + city_res_name + ', ' + street_res_name);
			}

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// house_res change
		$('#house_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var area_res_name = $('#area_res :selected').text();
			var city_res_name = $('#city_res :selected').text();
			var location_res_name = $('#location_res :selected').text();
			var street_res_name = $('#street_res :selected').text();
			var house_res = $('#house_res').val();

			$('#flat_res').prop('disabled', false);
			$('#flat_res').val('');
			var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			if (city_res_name == '') {
				$('#address_res').val(postcode_res + region_res_name + ', ' + area_res_name + ', ' + location_res_name + ', ' + street_res_name + ', дом ' + house_res);
			} else {
				$('#address_res').val(postcode_res + region_res_name + ', ' + city_res_name + ', ' + street_res_name + ', дом ' + house_res);
			}

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// building_res change
		$('#building_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var area_res_name = $('#area_res :selected').text();
			var city_res_name = $('#city_res :selected').text();
			var location_res_name = $('#location_res :selected').text();
			var street_res_name = $('#street_res :selected').text();
			var house_res = $('#house_res').val();
			var building_res = $('#building_res').val();

			$('#flat_res').prop('disabled', false);
			$('#flat_res').val('');
			var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			if (city_res_name == '') {
				$('#address_res').val(postcode_res + region_res_name + ', ' + area_res_name + ', ' + location_res_name + ', ' + street_res_name + ', дом ' + house_res + ', корпус ' + building_res);
			} else {
				$('#address_res').val(postcode_res + region_res_name + ', ' + city_res_name + ', ' + street_res_name + ', дом ' + house_res + ', корпус ' + building_res);
			}

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// flat_res change
		$('#flat_res').change(function() {
			var region_res_name = $('#region_res :selected').text();
			var area_res_name = $('#area_res :selected').text();
			var city_res_name = $('#city_res :selected').text();
			var location_res_name = $('#location_res :selected').text();
			var street_res_name = $('#street_res :selected').text();
			var house_res = $('#house_res').val();
			var building_res = $('#building_res').val();
			var flat_res = $('#flat_res').val();
			var postcode_res = $('#postcode_res').val();
		    if (postcode_res != '') {
				postcode_res = postcode_res + ', ';
			}
			if (city_res_name == '') {
				$('#address_res').val(postcode_res + region_res_name + ', ' + area_res_name + ', ' + location_res_name + ', ' + street_res_name + ', дом ' + house_res + ', корпус ' + building_res + ', квартира ' + flat_res);
			} else {
				$('#address_res').val(postcode_res + region_res_name + ', ' + city_res_name + ', ' + street_res_name + ', дом ' + house_res + ', корпус ' + building_res + ', квартира ' + flat_res);
			}

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// postcode_res change
		$('#postcode_res').change(function() {
			var address_res;
			var region_res_name = $('#region_res :selected').text();
			var area_res_name = $('#area_res :selected').text();
			var city_res_name = $('#city_res :selected').text();
			var location_res_name = $('#location_res :selected').text();
			var street_res_name = $('#street_res :selected').text();
			var house_res = $('#house_res').val();
			var building_res = $('#building_res').val();
			var flat_res = $('#flat_res').val();
			var postcode_res = $('#postcode_res').val();
			if (postcode_res != '') {
				address_res = postcode_res + ', ';
			} else {
				address_res = '';
			}
			// region, area, city, location, street
			if (city_res_name == '') {
				address_res = address_res + region_res_name + ', ' + area_res_name + ', ' + location_res_name + ', ' + street_res_name;
			} else {
				address_res = address_res + region_res_name + ', ' + city_res_name + ', ' + street_res_name;
			}
			// house
			if (house_res != '') {
				address_res = address_res + ', дом ' + house_res;
			}
			// building
			if (building_res != '') {
				address_res = address_res + ', корпус ' + building_res;
			}
			// flat
			if (flat_res != '') {
				address_res = address_res + ', квартира ' + flat_res;
			}

			$('#address_res').val(address_res);

			$('#address_reg_clone_flag').prop('checked', false);
		});

		// kladr_res not found
		$('#kladr_res_not').change(function() {
			$('#homeless_res').prop('checked', false)
			if ($('#kladr_res_not').prop('checked')) {
				$('#country_res').val('');
				$('#kladr_res').hide();
				$('#address_res').val('');
				$('#address_res').prop('disabled', false);
			} else {
				$('#kladr_res').show();
				$('#address_res').val('');
				$('#address_res').prop('disabled', true);
			}
		});

		// homeless_res
		$('#homeless_res').change(function() {
			$('#kladr_res_not').prop('checked', false)
			$('#address_res').prop('disabled', true);
			if ($('#homeless_res').prop('checked')) {
				$('#country_res').val('000');
				$('#kladr_res').hide();
				$('#address_res').val('Не имею адреса проживания.');
			} else {
				$('#country_res').val('');
				$('#kladr_res').hide();
				$('#address_res').val('');
			}
		});

		// lang add click
		$('#btn_lang_add').click(function() {
			var langs = $("select[id^='lang']").length;
			if (langs == 0) {
				$('#btn_lang_add').after('<div class="form-group row" id="div_lang1"><div class="col col-sm-3"><label class="font-weight-bold" for="lang1">Иностранный язык №1</label></div><div class="col col-sm-9"><select class="form-control" id="lang1" name="lang1"></select></div></div>');
				getLangAJAX('/frontend/DictForeignLangs/ForeignLangsJSON', '#lang1');
			} else {
				var lang_numb = langs + 1;
				var lang = 'lang'+lang_numb;
				$('#div_lang'+langs).after('<div class="form-group row" id="div_lang'+lang_numb+'"><div class="col col-sm-3"><label class="font-weight-bold" for="'+lang+'">Иностранный язык №'+lang_numb+'</label></div><div class="col col-sm-9"><select class="form-control" id="'+lang+'" name="'+lang+'"></select></div></div>');
				getLangAJAX('/frontend/DictForeignLangs/ForeignLangsJSON', '#'+lang);
			}
		});

		// lang remove click
		$('#btn_lang_remove').click(function() {
			var langs = $("select[id^='lang']").length;
			if (langs > 0) {
				$('#div_lang'+langs).remove();
			}
		});

		// submit click
		$('#btn_save').click(function() {
			$('#citizenship').prop('disabled', false);
			$('#address_reg').prop('disabled', false);
			$('#address_res').prop('disabled', false);
		});
	}

	function setCitizenship(citizenship, renew)
	{
		var passport_type = $('#passport_type').val();
		switch (citizenship) {
			case '':
				$('#citizenship_not').prop('checked', false);
				disablePassport(true);
				break;
			case '000':
				$('#citizenship_not').prop('checked', true);
				disablePassport(false);
				getPassportAJAX('/frontend/DictDoctypes/PassportsBsuJSON', '#passport_type', passport_type);
				break;
			case '643':
				$('#citizenship_not').prop('checked', false);
				disablePassport(false);
				getPassportAJAX('/frontend/DictDoctypes/PassportsRussianJSON', '#passport_type', passport_type);
				break;
			default:
				$('#citizenship_not').prop('checked', false);
				disablePassport(false);
				getPassportAJAX('/frontend/DictDoctypes/PassportsForeignJSON', '#passport_type', passport_type);
				break;
		}
	}

	function setPassport()
	{
		if ($('#passport_type').val() != '') {
			switch ($('#passport_type').val()) {
				// Паспорт РФ
				case '000000047':
					$("label[for='series']").html('Серия*');
					$('#series').mask('9999');
					$("label[for='numb']").html('Номер*');
					$('#numb').mask('999999');
					$("label[for='dt_issue']").html('Дата выдачи*');
					$("label[for='unit_name']").html('Наименование подразделения*');
					$("label[for='unit_code']").html('Код подразделения*');
					$('#unit_code').mask('999-999');
					$("label[for='dt_end']").html('Дата окончания действия');
                    //TODO паша добавил скрытие поля
                    $("label[for='dt_end']").hide();
                    $("#dt_end").hide();
						$("label[for='passport_face']").html('Первая страница паспорта*');
						$('#passport_face_div').show();
						$("label[for='passport_reg']").html('Страница паспорта с регистрацией*');
						$('#passport_reg_div').show();
						$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
						$('#passport_foreign_face_div').hide();
						$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
						$('#passport_foreign_reg_div').hide();
						$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
						$('#passport_foreign_rus_div').hide();
						$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
						$('#passport_pforeign_face_div').hide();
						$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
						$('#passport_pforeign_rus_div').hide();
						$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
						$('#residency_foreign_face_div').hide();
						$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
						$('#residency_foreign_reg_div').hide();
						$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
						$('#id_russian_div').hide();
						$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_face_div').hide();
						$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_back_div').hide();
						$("label[for='certificate_birth']").html('Свидетельство о рождении');
						$('#certificate_birth_div').hide();
						$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
						$('#certificate_pbirth_div').hide();
					break;
				// Паспорт иностранного гражданина
				case '000000049':
					$("label[for='series']").html('Серия*');
					$('#series').unmask();
					$("label[for='numb']").html('Номер*');
					$('#numb').unmask();
					$("label[for='dt_issue']").html('Дата выдачи*');
					$("label[for='unit_name']").html('Наименование подразделения*');
					$("label[for='unit_code']").html('Код подразделения');
					$('#unit_code').unmask();
					$("label[for='dt_end']").html('Дата окончания действия');
					//TODO паша добавил скрытие поля
                            $("label[for='dt_end']").hide();
                            $("#dt_end").hide();
						$("label[for='passport_face']").html('Первая страница паспорта');
						$('#passport_face_div').hide();
						$("label[for='passport_reg']").html('Страница паспорта с регистрацией');
						$('#passport_reg_div').hide();
						$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина*');
						$('#passport_foreign_face_div').show();
						$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
						$('#passport_foreign_reg_div').show();
						$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
						$('#passport_foreign_rus_div').show();
						$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
						$('#passport_pforeign_face_div').show();
						$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
						$('#passport_pforeign_rus_div').show();
						$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
						$('#residency_foreign_face_div').hide();
						$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
						$('#residency_foreign_reg_div').hide();
						$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
						$('#id_russian_div').hide();
						$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_face_div').hide();
						$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_back_div').hide();
						$("label[for='certificate_birth']").html('Свидетельство о рождении');
						$('#certificate_birth_div').show();
						$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
						$('#certificate_pbirth_div').show();
					break;
				// Вид на жительство иностранного гражданина
                case '000000075':
					$("label[for='series']").html('Серия*');
					$('#series').unmask();
					$("label[for='numb']").html('Номер*');
					$('#numb').unmask();
					$("label[for='dt_issue']").html('Дата выдачи*');
					$("label[for='unit_name']").html('Наименование подразделения*');
					$("label[for='unit_code']").html('Код подразделения');
					$('#unit_code').unmask();
					$("label[for='dt_end']").html('Дата окончания действия*');
                    //TODO паша добавил отображение поля
                    $("label[for='dt_end']").show();
                    $("#dt_end").show();
					$('#unit_code').unmask();
						$("label[for='passport_face']").html('Первая страница паспорта');
						$('#passport_face_div').hide();
						$("label[for='passport_reg']").html('Страница паспорта с регистрацией');
						$('#passport_reg_div').hide();
						$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
						$('#passport_foreign_face_div').hide();
						$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
						$('#passport_foreign_reg_div').hide();
						$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
						$('#passport_foreign_rus_div').hide();
						$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
						$('#passport_pforeign_face_div').show();
						$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
						$('#passport_pforeign_rus_div').show();
						$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина*');
						$('#residency_foreign_face_div').show();
						$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина*');
						$('#residency_foreign_reg_div').show();
						$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
						$('#id_russian_div').hide();
						$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_face_div').hide();
						$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_back_div').hide();
						$("label[for='certificate_birth']").html('Свидетельство о рождении');
						$('#certificate_birth_div').show();
						$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
						$('#certificate_pbirth_div').show();
					break;
				// Временное удостоверение личности гражданина РФ
				case '000000202':
					$("label[for='series']").html('Серия');
					$('#series').unmask();
					$("label[for='numb']").html('Номер*');
					$('#numb').unmask();
					$("label[for='dt_issue']").html('Дата выдачи*');
					$("label[for='unit_name']").html('Наименование подразделения*');
					$("label[for='unit_code']").html('Код подразделения');
					$('#unit_code').unmask();
					$("label[for='dt_end']").html('Дата окончания действия*');
                    //TODO паша добавил отображение поля
                    $("label[for='dt_end']").show();
                    $("#dt_end").show();
						$("label[for='passport_face']").html('Первая страница паспорта');
						$('#passport_face_div').hide();
						$("label[for='passport_reg']").html('Страница паспорта с регистрацией');
						$('#passport_reg_div').hide();
						$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
						$('#passport_foreign_face_div').hide();
						$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
						$('#passport_foreign_reg_div').hide();
						$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
						$('#passport_foreign_rus_div').hide();
						$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
						$('#passport_pforeign_face_div').hide();
						$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
						$('#passport_pforeign_rus_div').hide();
						$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
						$('#residency_foreign_face_div').hide();
						$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
						$('#residency_foreign_reg_div').hide();
						$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ*');
						$('#id_russian_div').show();
						$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_face_div').hide();
						$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_back_div').hide();
						$("label[for='certificate_birth']").html('Свидетельство о рождении');
						$('#certificate_birth_div').hide();
						$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
						$('#certificate_pbirth_div').hide();
					break;
				// Удостоверение личности иностранного гражданина
				case '000000223':
					$("label[for='series']").html('Серия');
					$('#series').unmask();
					$("label[for='numb']").html('Номер*');
					$('#numb').unmask();
					$("label[for='dt_issue']").html('Дата выдачи*');
					$("label[for='unit_name']").html('Наименование подразделения');
					$("label[for='unit_code']").html('Код подразделения');
					$('#unit_code').unmask();
					$("label[for='dt_end']").html('Дата окончания действия*');
                    //TODO паша добавил отображение поля
                    $("label[for='dt_end']").show();
                    $("#dt_end").show();
						$("label[for='passport_face']").html('Первая страница паспорта');
						$('#passport_face_div').hide();
						$("label[for='passport_reg']").html('Страница паспорта с регистрацией');
						$('#passport_reg_div').hide();
						$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
						$('#passport_foreign_face_div').hide();
						$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
						$('#passport_foreign_reg_div').hide();
						$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
						$('#passport_foreign_rus_div').hide();
						$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
						$('#passport_pforeign_face_div').show();
						$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
						$('#passport_pforeign_rus_div').show();
						$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
						$('#residency_foreign_face_div').hide();
						$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
						$('#residency_foreign_reg_div').hide();
						$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
						$('#id_russian_div').hide();
						$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина*');
						$('#id_foreign_face_div').show();
						$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина*');
						$('#id_foreign_back_div').show();
						$("label[for='certificate_birth']").html('Свидетельство о рождении');
						$('#certificate_birth_div').hide();
						$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
						$('#certificate_pbirth_div').show();
					break;
				// Свидетельство о рождении, выданное уполномоченным органом иностранного государства
				case '000000226':
					$("label[for='series']").html('Серия*');
					$('#series').unmask();
					$("label[for='numb']").html('Номер*');
					$('#numb').unmask();
					$("label[for='dt_issue']").html('Дата выдачи*');
					$("label[for='unit_name']").html('Наименование подразделения*');
					$("label[for='unit_code']").html('Код подразделения');
					$('#unit_code').unmask();
					$("label[for='dt_end']").html('Дата окончания действия');
                    //TODO паша добавил скрытие поля
                    $("label[for='dt_end']").hide();
                    $("#dt_end").hide();
						$("label[for='passport_face']").html('Первая страница паспорта');
						$('#passport_face_div').hide();
						$("label[for='passport_reg']").html('Страница паспорта с регистрацией');
						$('#passport_reg_div').hide();
						$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
						$('#passport_foreign_face_div').hide();
						$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
						$('#passport_foreign_reg_div').hide();
						$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
						$('#passport_foreign_rus_div').hide();
						$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
						$('#passport_pforeign_face_div').show();
						$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
						$('#passport_pforeign_rus_div').show();
						$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
						$('#residency_foreign_face_div').hide();
						$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
						$('#residency_foreign_reg_div').hide();
						$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
						$('#id_russian_div').hide();
						$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_face_div').hide();
						$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
						$('#id_foreign_back_div').hide();
						$("label[for='certificate_birth']").html('Свидетельство о рождении*');
						$('#certificate_birth_div').show();
						$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
						$('#certificate_pbirth_div').show();
					break;
			}
		} else {
			$("label[for='series']").html('Серия');
			$('#series').unmask();
			$("label[for='numb']").html('Номер');
			$('#numb').unmask();
			$("label[for='dt_issue']").html('Дата выдачи');
			$("label[for='unit_name']").html('Наименование подразделения');
			$("label[for='unit_code']").html('Код подразделения');
			$('#unit_code').unmask();
			$("label[for='dt_end']").html('Дата окончания действия');
            //TODO паша добавил скрытие поля
            $("label[for='dt_end']").hide();
            $("#dt_end").hide();
				$("label[for='passport_face']").html('Первая страница паспорта');
				$('#passport_face_div').hide();
				$("label[for='passport_reg']").html('Страница паспорта с регистрацией');
				$('#passport_reg_div').hide();
				$("label[for='passport_foreign_face']").html('Первая страница паспорта иностранного гражданина');
				$('#passport_foreign_face_div').hide();
				$("label[for='passport_foreign_reg']").html('Страница паспорта иностранного гражданина с регистрацией по месту жительства');
				$('#passport_foreign_reg_div').hide();
				$("label[for='passport_foreign_rus']").html('Страница паспорта иностранного гражданина с информацией на русском языке');
				$('#passport_foreign_rus_div').hide();
				$("label[for='passport_pforeign_face']").html('Первая страница паспорта родителя иностранного гражданина');
				$('#passport_pforeign_face_div').hide();
				$("label[for='passport_pforeign_rus']").html('Страница паспорта родителя иностранного гражданина с информацией на русском языке');
				$('#passport_pforeign_rus_div').hide();
				$("label[for='residency_foreign_face']").html('Первая страница вида на жительство иностранного гражданина');
				$('#residency_foreign_face_div').hide();
				$("label[for='residency_foreign_reg']").html('Страница с регистрацией по месту пребывания вида на жительство иностранного гражданина');
				$('#residency_foreign_reg_div').hide();
				$("label[for='id_russian']").html('Временное удостоверение личности гражданина РФ');
				$('#id_russian_div').hide();
				$("label[for='id_foreign_face']").html('Лицевая сторона удостоверения личности иностранного гражданина');
				$('#id_foreign_face_div').hide();
				$("label[for='id_foreign_back']").html('Оборотная сторона удостоверения личности иностранного гражданина');
				$('#id_foreign_back_div').hide();
				$("label[for='certificate_birth']").html('Свидетельство о рождении');
				$('#certificate_birth_div').hide();
				$("label[for='certificate_pbirth']").html('Свидетельство о рождении родителя');
				$('#certificate_pbirth_div').hide();
		}
	}

	function unsetPassport()
	{
		$("label[for='series']").html('Серия');
		$('#series').val('');
		$("label[for='numb']").html('Номер');
		$('#numb').val('');
		$("label[for='dt_issue']").html('Дата выдачи');
		$('#dt_issue').val('');
		$("label[for='unit_name']").html('Наименование подразделения');
		$('#unit_name').val('');
		$("label[for='unit_code']").html('Код подразделения');
		$('#unit_code').val('');
		$("label[for='dt_end']").html('Дата окончания действия');
		$('#dt_end').val('');
	}

	function disablePassport(disable)
	{
		$('#passport_type').prop('disabled', disable);
		$('#series').prop('disabled', disable);
		$('#numb').prop('disabled', disable);
		$('#dt_issue').prop('disabled', disable);
		$('#unit_name').prop('disabled', disable);
		$('#unit_code').prop('disabled', disable);
		$('#dt_end').prop('disabled', disable);
		$('#passport_old_yes').prop('disabled', disable);
	}

	function setPassportOld()
	{
		if ($('#passport_type_old').val() == '000000047') {
			$("label[for='series_old']").html('Серия*');
			$('#series_old').mask('9999');
			$("label[for='numb_old']").html('Номер*');
			$('#numb_old').mask('999999');
			$("label[for='unit_code_old']").html('Код подразделения*');
			$('#unit_code_old').mask('999-999');
		} else {
			$("label[for='series_old']").html('Серия');
			$('#series_old').unmask();
			$("label[for='numb_old']").html('Номер*');
			$('#numb_old').unmask();
			$("label[for='unit_code_old']").html('Код подразделения');
			$('#unit_code_old').unmask();
		}
		$("label[for='dt_issue_old']").html('Дата выдачи*');
		$("label[for='unit_name_old']").html('Наименование подразделения');
		$("label[for='dt_end_old']").html('Дата окончания действия');
	}

	function getPassportAJAX(url, select, val)
	{
		startLoadingAnimation();
		$.ajax({
	      url: url,
	      type: 'POST',
	      data: {format: 'json'},
		  dataType: 'json',
		  success: function(result) {
	        $(select).empty();
            $(select).append('<option></option>');
	        $.each(result, function(key, value){
	            if (val == value.code) {
					$(select).append('<option value="' + value.code + '" selected>' + value.description + '</option>');
				} else {
					$(select).append('<option value="' + value.code + '">' + value.description + '</option>');
				}
	        });
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
	}

	function getKladrAJAX(url, code, select, value_test)
	{
		startLoadingAnimation();
		$.ajax({
	      url: url,
	      type: 'POST',
	      data: {format: 'json'},
		  dataType: 'json',
		  data: {code: code},
	      success: function(result) {
			$(select).empty();
            $(select).append('<option></option>');
			if (!jQuery.isEmptyObject(result)) {
				$.each(result, function(key, value){
		            if (value_test == value.kladr_code) {
						$(select).append('<option value="' + value.kladr_code + '" selected>' + value.kladr_name + ' ' + value.kladr_abbr + '</option>');
					} else {
						$(select).append('<option value="' + value.kladr_code + '">' + value.kladr_name + ' ' + value.kladr_abbr + '</option>');
					}
		        });
			}
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
	}

	function getPostcodeAJAX(url, code, control)
	{
		var postcode;
		startLoadingAnimation();
		$.ajax({
	      async: false,
	      url: url,
	      type: 'POST',
	      data: {format: 'json'},
		  dataType: 'json',
		  data: {code: code},
	      success: function(result) {
			if (!jQuery.isEmptyObject(result) && result.length) {
				$(control).val(result);
			} else {
				$(control).val('');
			}
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
	}

	function getLangAJAX(url, select)
	{
		startLoadingAnimation();
		$.ajax({
	      url: url,
	      type: 'POST',
	      data: {format: 'json'},
		  dataType: 'json',
		  success: function(result) {
	        $(select).empty();
            $(select).append('<option></option>');
	        $.each(result, function(key, value){
	            $(select).append('<option value="' + value.code + '">' + value.description + '</option>');
	        });
	      },
	      error: function(xhr, status, error) {
		      console.log('Request Failed: ' + status + ' ' + error + ' ' + xhr.status + ' ' + xhr.statusText);
		  }
	    });
	    stopLoadingAnimation();
	    $(select).val('');
	}

	function startLoadingAnimation()
	{
	  var imgObj = $("#loadImg");
	  imgObj.show();

	  var centerY = $(window).scrollTop() + ($(window).height() + imgObj.height())/2;
	  var centerX = $(window).scrollLeft() + ($(window).width() + imgObj.width())/2;

	  imgObj.offset({ top:centerY, left:centerX });
	}

	function stopLoadingAnimation()
	{
	  $("#loadImg").hide();
	}

	function cloneAddressRegistration()
	{
		var country_reg = $('#country_reg').val();
		var address_reg = $('#address_reg').val();
		if (country_reg != '' && address_reg != '') {
			$('#country_res').val(country_reg);
			if (country_reg == '643') {
				$('#kladr_res').show();
				$('#address_res').prop('disabled', true);
				$('#address_res').val(address_reg);
				// renew kladr_res
				var region_reg = $('#region_reg').val();
				var area_reg = $('#area_reg').val();
				var city_reg = $('#city_reg').val();
				var location_reg = $('#location_reg').val();
				var street_reg = $('#street_reg').val();
				if (region_reg != '' && !jQuery.isEmptyObject(region_reg)) {
					getKladrAJAX('/frontend/Kladr/RegionAllJSON', null, '#region_res', region_reg);
					// area
					if (area_reg != '' && !jQuery.isEmptyObject(area_reg)) {
						getKladrAJAX('/frontend/Kladr/AreaByRegionJSON', region_reg, '#area_res', area_reg);
						if (city_reg != '' && !jQuery.isEmptyObject(city_reg)) {
							getKladrAJAX('/frontend/Kladr/CityByAreaJSON', area_reg, '#city_res', city_reg);
							if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
								getKladrAJAX('/frontend/Kladr/LocationByCityJSON', city_reg, '#location_res', city_reg);
							} else {
								$('#location_res').empty();
								if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
									getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city_reg, '#street_res', street_reg);
								} else {
									$('#street_res').empty();
								}
							}
						} else {
							$('#city_res').empty();
							if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
								getKladrAJAX('/frontend/Kladr/LocationByAreaJSON', area_reg, '#location_res', location_reg);
								if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
									getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', location_reg, '#street_res', street_reg);
								} else {
									$('#street_res').empty();
								}
							} else {
								$('#location_res').empty();
							}
						}
					} else {
						$('#area_res').empty();
						// city
						if (city_reg != '' && !jQuery.isEmptyObject(city_reg)) {
							getKladrAJAX('/frontend/Kladr/CityByRegionJSON', region_reg, '#city_res', city_reg);
							if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
								getKladrAJAX('/frontend/Kladr/LocationByCityJSON', city_reg, '#location_res', city_reg);
							} else {
								$('#location_res').empty();
								if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
									getKladrAJAX('/frontend/Kladr/StreetByCityJSON', city_reg, '#street_res', street_reg);
								} else {
									$('#street_res').empty();
								}
							}
						} else {
							$('#city_res').empty();
						}
						// location
						if (location_reg != '' && !jQuery.isEmptyObject(location_reg)) {
							getKladrAJAX('/frontend/Kladr/LocationByRegionJSON', region_reg, '#location_res', location_reg);
							if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
								getKladrAJAX('/frontend/Kladr/StreetByLocationJSON', street_reg, '#street_res', street_reg);
							} else {
								$('#street_res').empty();
							}
						} else {
							$('#location_res').empty();
							if (street_reg != '' && !jQuery.isEmptyObject(street_reg)) {
								getKladrAJAX('/frontend/Kladr/StreetByRegionJSON', region_reg, '#street_res', street_reg);
							} else {
								$('#street_res').empty();
							}
						}
					}
				} else {
					$('#region_res').empty();
					$('#area_res').empty();
					$('#city_res').empty();
					$('#location_res').empty();
					$('#street_res').empty();
				}
				$('#house_res').val($('#house_reg').val());
				$('#building_res').val($('#building_reg').val());
				$('#flat_res').val($('#flat_reg').val());
				$('#postcode_res').val($('#postcode_reg').val());
				CountryResHide();
				AddressResHide();
			} else {
				$('#kladr_res').hide();
				$('#address_res').prop('disabled', false);
				$('#address_res').val(address_reg);
			}
		}
	}

	function CountryResShow() {
		$("label[for='country_res']").show();
		$('#country_res').show();
	}

	function CountryResHide() {
		$("label[for='country_res']").hide();
		$('#country_res').hide();
	}

	function AddressResShow() {
		$('#kladr_res').show();
		$('#kladr_res_not').show();
		$("label[for='kladr_res_not']").show();
		$('#homeless_res').show();
		$("label[for='homeless_res']").show();
	}

	function AddressResHide() {
		$('#kladr_res').hide();
		$('#kladr_res_not').hide();
		$("label[for='kladr_res_not']").hide();
		$('#homeless_res').hide();
		$("label[for='homeless_res']").hide();
	}

	function getAge(dateString) {
		var day = parseInt(dateString.substring(0,2));
		var month = parseInt(dateString.substring(3,5));
		var year = parseInt(dateString.substring(6,10));
		var today = new Date();
		var birthDate = new Date(year, month - 1, day);
		var age = today.getFullYear() - birthDate.getFullYear();
		var m = today.getMonth() - birthDate.getMonth();
		if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
			age--;
		}
		return age;
    }
</script>

<script>
	$(function(){
	  $("#birth_dt").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#phone_mobile").mask("+7(999) 999-99-99");
	  $("#dt_issue").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#dt_end").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#dt_issue_old").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#dt_end_old").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	  $("#postcode_reg").mask("999999");
	  $("#postcode_res").mask("999999");
	});
</script>
