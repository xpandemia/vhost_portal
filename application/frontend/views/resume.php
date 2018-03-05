<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check resume
	if (!isset($data['is_edit'])) {
		Basic_Helper::redirect(APP_NAME, 401, BEHAVIOR.'/Main', 'Index');
	} else {
		if ($data['is_edit'] === true) {
			$data['personal_vis'] = false;
		}
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php echo Form_Helper::setFormBegin('Resume/Resume', 'form_personal', RESUME_HDR); ?>

		<!-- name_first -->
		<?php echo Form_Helper::setFormInput(['label' => FIRSTNAME_PLC,
											'control' => 'name_first',
											'type' => 'text',
											'class' => $data['name_first_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'placeholder' => FIRSTNAME_PLC,
											'value' => $data['name_first'],
											'success' => $data['name_first_scs'],
											'error' => $data['name_first_err'],
											'help' => FIRSTNAME_HELP]); ?>
		<!-- name_middle -->
		<?php echo Form_Helper::setFormInput(['label' => MIDDLENAME_PLC,
											'control' => 'name_middle',
											'type' => 'text',
											'class' => $data['name_middle_cls'],
											'required' => 'no',
											'placeholder' => MIDDLENAME_PLC,
											'value' => $data['name_middle'],
											'success' => $data['name_middle_scs'],
											'error' => $data['name_middle_err'],
											'help' => MIDDLENAME_HELP]); ?>
		<!-- name_last -->
		<?php echo Form_Helper::setFormInput(['label' => LASTNAME_PLC,
											'control' => 'name_last',
											'type' => 'text',
											'class' => $data['name_last_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'placeholder' => LASTNAME_PLC,
											'value' => $data['name_last'],
											'success' => $data['name_last_scs'],
											'error' => $data['name_last_err'],
											'help' => LASTNAME_HELP]); ?>
		<!-- sex -->
		<?php echo Form_Helper::setFormRadio(['label' => 'Пол',
											'control' => 'sex',
											'required' => 'yes',
											'required_style' => 'StarUp',
											'radio' => [
														'male' => ['1' => 'Мужской'],
														'female' => ['0' => 'Женский'],
														],
											'value' => $data['sex'],
											'error' => $data['sex_err']]); ?>
		<!-- birth_dt -->
		<?php echo Form_Helper::setFormInput(['label' => 'Дата рождения',
											'control' => 'birth_dt',
											'type' => 'text',
											'class' => $data['birth_dt_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'value' => $data['birth_dt'],
											'success' => $data['birth_dt_scs'],
											'error' => $data['birth_dt_err']]); ?>
		<!-- birth_place -->
		<?php echo Form_Helper::setFormInput(['label' => BIRTHPLACE_PLC,
											'control' => 'birth_place',
											'type' => 'text',
											'class' => $data['birth_place_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'placeholder' => BIRTHPLACE_PLC,
											'value' => $data['birth_place'],
											'success' => $data['birth_place_scs'],
											'error' => $data['birth_place_err'],
											'help' => BIRTHPLACE_HELP]); ?>
		<!-- citizenship -->
		<?php echo Form_Helper::setFormSelectListDB(['label' => 'Гражданство',
													'control' => 'citizenship',
													'class' => $data['citizenship_cls'],
													'required' => 'yes',
													'required_style' => 'StarUp',
													'model_class' => 'common\\models\\Model_DictCountries',
													'model_method' => 'getCountryAll',
													'model_field' => 'country_name',
													'value' => $data['citizenship'],
													'success' => $data['citizenship_scs'],
													'error' => $data['citizenship_err']]); ?>
		<!-- personal -->
		<?php if ($data['personal_vis'] == true) {
				echo Form_Helper::setFormCheckbox(['label' => 'Я даю согласие на обработку своих персональных данных в соответствии с Федеральным законом РФ от 27 июля 2006 г. №152-ФЗ "О персональных данных"',
													'control' => 'personal',
													'class' => $data['personal_cls'],
													'value' => $data['personal'],
													'success' => $data['personal_scs'],
													'error' => $data['personal_err']]);
				} ?>

		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
					echo HTML_Helper::setHrefButton('Resume/Reset', 'btn btn-danger', 'Очистить');
					echo HTML_Helper::setHrefButton('Main/Index', 'btn btn-primary', 'На главную');
				?>
			</div>
		</div>

	<?php
		echo Form_Helper::setFormEnd();
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
	?>
</div>

<script>
	$(function(){
	  $("#birth_dt").mask("99.99.9999", {placeholder: "ДД.ММ.ГГГГ" });
	});
</script>
